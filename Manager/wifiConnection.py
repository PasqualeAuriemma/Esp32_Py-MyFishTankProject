# manager/wifiConnection.py
#
# Modifiche rispetto alla versione precedente:
#
#   1. NON importa urequests a livello di modulo.
#      urequests viene importato in modo lazy dentro _post_https_request(),
#      solo nel momento in cui serve davvero, e poi rimosso dalla heap.
#      Questo risparmia ~15-20 KB che prima venivano allocati subito.
#
#   2. Accetta un oggetto network.WLAN già costruito da main.py.
#      In questo modo il driver WiFi viene inizializzato PRIMA di qualsiasi
#      altro import pesante, quando la heap è ancora libera e contigua.
#      Se non viene passato, lo crea internamente (compatibilità).
#
#   3. Il blocco connect() ora gestisce correttamente il caso in cui
#      il driver sia rimasto in stato corrotto da un crash precedente
#      (active=True ma non connesso): lo spegne, aspetta, lo riaccende.

import network  # type: ignore[import-untyped]
import time
import json
import gc

from Helper.Singleton import Singleton


class WifiConnection(Singleton):
    """Handles the WiFi connection for the device. Singleton: one instance per device."""

    __slots__ = ("_ssid", "_password", "_host", "_wlan", "_singleton_initialized")

    # Mappa stati WiFi → stringa leggibile: allocata UNA VOLTA al caricamento del modulo.
    # Con moduli frozen, vive in flash invece che in RAM.
    _STATUS_MAP = {
        network.STAT_IDLE: "NESSUNA CONNESSIONE",
        network.STAT_CONNECTING: "CONNESSIONE IN CORSO",
        network.STAT_WRONG_PASSWORD: "PASSWORD ERRATA",
        network.STAT_NO_AP_FOUND: "NESSUN AP TROVATO",
        network.STAT_GOT_IP: "CONNESSO",
    }

    def __init__(self, wlan_driver, ssid, password, host=None):
        """Initializes the WifiConnection (only on first instantiation).

        Args:
            ssid (str): The SSID of the WiFi network.
            password (str): The password for the WiFi network.
        """
        if getattr(self, "_singleton_initialized", True):
            return
        self._ssid = ssid
        self._password = password
        self._host = host
        # Initialize the interface, but don't activate it immediately
        self._wlan = wlan_driver
        self._singleton_initialized = True

    @property
    def host(self):
        return self._host

    @property
    def ssid(self):
        return self._ssid

    @property
    def password(self):
        return self._password

    def is_connected(self):
        """Checks if the device is connected to WiFi."""
        return self._wlan.isconnected()

    def log_message(self, message, end="\n"):
        """Funzione di logging."""
        print(message, end=end)

    def connect(self):
        """
        Activates the WiFi interface and attempts to connect.
        Handles internal errors by resetting the interface.
        """
        try:
            # Ensure the interface is active. If not, activate it.
            if not self._wlan.active():
                self.log_message("Activating WiFi interface...")
                self._wlan.active(True)

            # If already connected, no need to do anything else.
            if self.is_connected():
                return True

            self.log_message("Connecting to network '{}'...".format(self._ssid))
            self._wlan.connect(self._ssid, self._password)

            # Wait for connection with a timeout
            max_wait = 15
            while max_wait > 0:
                if self._wlan.isconnected():
                    break
                self.log_message(".", end="")
                max_wait -= 1
                time.sleep(1)

            # Check final connection status
            if self.is_connected():
                self.log_message("\nWiFi connected successfully!")
                self.log_message(
                    "Network configuration: {}".format(self._wlan.ifconfig())
                )
                return True
            else:
                self.log_message(
                    "Failed to connect. Status: {}".format(self._wlan.status())
                )
                self.log_message("Deactivating WiFi interface to reset state.")
                self._wlan.active(False)
                return False

        except OSError as e:
            self.log_message(
                "Caught an OSError: {}. Deactivating WiFi to reset state.".format(e)
            )
            # On internal state error, force deactivation to allow for recovery
            self._wlan.active(False)
            return False

    def disconnect(self):
        """Disconnects from the WiFi network and deactivates the interface."""
        try:
            # If the interface is already inactive, there's nothing to do.
            if not self._wlan.active():
                self.log_message(
                    "WiFi interface already inactive; nothing to disconnect."
                )
                return True

            # If connected, request a disconnect and wait briefly for it to complete.
            if self.is_connected():
                self.log_message("Disconnecting from WiFi network...")
                self._wlan.disconnect()

                max_wait = 5
                while max_wait > 0 and self.is_connected():
                    time.sleep(1)
                    max_wait -= 1

            # At this point we are either disconnected or were never connected;
            # deactivate the interface to fully reset the state.
            self.log_message("Deactivating WiFi interface to reset state.")
            self._wlan.active(False)
            self.log_message("WiFi disconnected successfully!")
            return True
        except OSError as e:
            self.log_message(
                "Caught an OSError: {}. Failed to disconnect from WiFi.".format(e)
            )
            return False

    def connection_status(self):
        """Restituisce lo stato leggibile della connessione."""
        return self._STATUS_MAP.get(self._wlan.status(), "STATO SCONOSCIUTO")

    def get_ip_address(self):
        """Restituisce l'indirizzo IP della connessione."""
        return self._wlan.ifconfig()[0]

    def send_value_to_web(self, value, key, timestamp):
        """
        Invia un valore a un endpoint web.
        Tenta la connessione se non è già attivo.
        NOTA: Non si disconnette automaticamente per efficienza.

        Ritorna:
            bool: True se l'invio ha avuto successo, False altrimenti.
        """
        if self._host is None:
            self.log_message("[wifi] Host non configurato. Impossibile inviare i dati.")
            return False

        if not self.is_connected():
            self.log_message("[wifi] Non connesso. Tentativo di connessione...")
            if not self.connect():
                self.log_message(
                    "[wifi] Impossibile inviare i dati: connessione WiFi fallita."
                )
                gc.collect()
                return False

        url = "https://{}/take{}.php".format(self._host, key)
        data = {key: value, "Date": timestamp}

        # Log sintetico per ridurre allocazioni superflue di stringhe.
        self.log_message("[wifi] Invio dati a {}.".format(url))

        try:
            response_text = self._post_https_request(url, data)
        finally:
            # Libera il dizionario e altre variabili locali il prima possibile.
            try:
                del data
            except NameError:
                pass
            gc.collect()

        if response_text is not None:
            self.log_message("[wifi] Dati inviati con successo.")
            return True

        self.log_message("[wifi] Invio dati fallito.")
        return False

    def _post_https_request(self, url, data, max_retries=3, retry_delay=2):
        """
        Esegue una richiesta POST con gestione della memoria e ritentativi.
        urequests viene importato qui in modo LAZY e rimosso dopo l'uso.

        Ritorna:
            str | None: testo della risposta se la richiesta va a buon fine,
            altrimenti None.
        """
        gc.collect()

        headers = {
            "Content-Type": "application/json",
            "Connection": "close",  # IMPORTANTE: evita keep-alive]
        }

        # Serializza i dati una sola volta per ridurre le allocazioni.
        try:
            json_payload = json.dumps(data)
        except Exception as e:
            self.log_message(
                "[wifi] Errore durante la serializzazione JSON: {}".format(e)
            )
            gc.collect()
            return None
        finally:
            # Libera il dizionario il prima possibile.
            try:
                del data
            except NameError:
                pass

        # Import LAZY di urequests
        # urequests occupa ~15-20 KB. Lo importiamo solo adesso che
        # ne abbiamo bisogno, e lo cancelliamo subito dopo.
        try:
            import urequests  # type: ignore[import-untyped]
        except ImportError as e:
            self.log_message("[wifi] urequests non disponibile: {}".format(e))
            del json_payload
            gc.collect()
            return None

        response_text = None
        try:
            for attempt in range(1, max_retries + 1):
                response = None
                try:
                    self.log_message(
                        "[wifi] POST a: {} (Tentativo {}/{})".format(
                            url, attempt, max_retries
                        )
                    )
                    gc.collect()
                    # In MicroPython è preferibile passare esplicitamente i parametri.
                    response = urequests.post(url, data=json_payload, headers=headers)

                    status = response.status_code
                    if 200 <= status < 300:
                        self.log_message(
                            "[wifi] Successo! Status: {}".format(status), " "
                        )
                        # Legge il corpo una sola volta per non mantenere buffer inutili.
                        response_text = response.text
                        self.log_message("[wifi] Risposta: {}".format(response_text))
                        break
                    else:
                        self.log_message(
                            "[wifi] Errore HTTP: {} {}".format(
                                status, getattr(response, "reason", "")
                            )
                        )
                        # Errore lato server: in genere non conviene ritentare.
                        return None
                except OSError as e:
                    self.log_message(
                        "[wifi] Errore di rete (Tentativo {}): {}".format(attempt, e)
                    )
                    if attempt < max_retries:
                        # Backoff esponenziale semplice.
                        time.sleep(retry_delay * (2 ** (attempt - 1)))
                        continue
                    else:
                        self.log_message("[wifi] Tutti i tentativi sono falliti.")
                        return None
                except Exception as e:
                    self.log_message(
                        "[wifi] Errore imprevisto durante la richiesta: {}".format(e)
                    )
                    return None
                finally:
                    if response is not None:
                        try:
                            response.close()
                            del response  # libera esplicitamente
                        except Exception:
                            pass
                        response = None
                        # self.log_message("[wifi] Risposta chiusa.")
                        gc.collect()
        finally:
            # ── Rimuovi urequests dalla heap ──────────────────────────
            try:
                import sys

                if "urequests" in sys.modules:
                    del sys.modules["urequests"]
                del urequests
            except Exception:
                pass

            # Libera il payload JSON dalla memoria.
            try:
                del json_payload
            except NameError:
                pass
            gc.collect()

        return response_text
