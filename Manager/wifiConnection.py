import network  # type: ignore[import-untyped]
import time
import json
import gc
from secret import IOT_TOKEN  # type: ignore[import]

from Helper.Singleton import Singleton


class WifiConnection(Singleton):
    """Handles the WiFi connection for the device. Singleton: one instance per device."""

    __slots__ = (
        "_ssid",
        "_password",
        "_host",
        "_wlan",
        "_singleton_initialized",
        "_queue",
        "_queue_lock",
        "_token",
        "_wdt",
        "_debug_mode",
        "_has_connection_attempt",
    )

    # Mappa stati WiFi → stringa leggibile: allocata UNA VOLTA al caricamento del modulo.
    # Con moduli frozen, vive in flash invece che in RAM.
    _STATUS_MAP = {
        network.STAT_IDLE: "NESSUNA CONNESSIONE",
        network.STAT_CONNECTING: "CONNESSIONE IN CORSO",
        network.STAT_WRONG_PASSWORD: "PASSWORD ERRATA",
        network.STAT_NO_AP_FOUND: "NESSUN AP TROVATO",
        network.STAT_GOT_IP: "CONNESSO",
    }

    def __init__(self, wlan_driver, ssid, password, token, host=None, wdt=None, debug_mode=False):
        """Initializes the WifiConnection (only on first instantiation)."""
        if getattr(self, "_singleton_initialized", True):
            return
        self._ssid = ssid
        self._password = password
        self._token = token
        self._host = host
        # Initialize the interface, but don't activate it immediately
        self._wlan = wlan_driver
        self._queue = []
        import _thread
        self._queue_lock = _thread.allocate_lock()
        self._singleton_initialized = True
        self._wdt = wdt
        self._debug_mode = debug_mode  # Configurazione del logger (Punto 4)
        self._has_connection_attempt = False
        

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
        """
        Funzione di logging disaccoppiata.
        Se _debug_mode è False, non alloca stringhe sulla heap né impegna la CPU con la seriale.
        """
        if self._debug_mode:
            print(message, end=end)

    def _feed_wdt(self):
        """Nutre il watchdog se configurato. No-op altrimenti."""
        if self._wdt is not None:
            try:
                self._wdt.feed()
            except Exception:
                pass

    def connect(self):
        """
        Activates the WiFi interface and attempts to connect.
        Handles internal errors by resetting the interface.
        """
        try:
            if not self._wlan.active():
                self.log_message("Activating WiFi interface...")
                self._wlan.active(True)

            if self.is_connected():
                self._has_connection_attempt = True
                return True

            self.log_message("Connecting to network '{}'...".format(self._ssid))
            self._wlan.connect(self._ssid, self._password)

            max_wait = 15
            while max_wait > 0:
                if self._wlan.isconnected():
                    break
                self.log_message(".", end="")
                max_wait -= 1
                time.sleep(1)

            if self.is_connected():
                self.log_message("\nWiFi connected successfully!")
                self.log_message(
                    "Network configuration: {}".format(self._wlan.ifconfig())
                )
                self._has_connection_attempt = True
                return True
            else:
                self.log_message(
                    "Failed to connect. Status: {}".format(self._wlan.status())
                )
                self.log_message("Deactivating WiFi interface to reset state.")
                self._wlan.active(False)
                self._has_connection_attempt = True
                return False

        except OSError as e:
            self.log_message(
                "Caught an OSError: {}. Deactivating WiFi to reset state.".format(e)
            )
            self._wlan.active(False)
            self._has_connection_attempt = True
            return False

    def disconnect(self):
        """Disconnects from the WiFi network and deactivates the interface."""
        try:
            if not self._wlan.active():
                self.log_message(
                    "WiFi interface already inactive; nothing to disconnect."
                )
                self._has_connection_attempt = True
                return True

            if self.is_connected():
                self.log_message("Disconnecting from WiFi network...")
                self._wlan.disconnect()

                max_wait = 5
                while max_wait > 0 and self.is_connected():
                    time.sleep(1)
                    max_wait -= 1

            self.log_message("Deactivating WiFi interface to reset state.")
            self._wlan.active(False)
            self.log_message("WiFi disconnected successfully!")
            self._has_connection_attempt = True
            return True
        except OSError as e:
            self.log_message(
                "Caught an OSError: {}. Failed to disconnect from WiFi.".format(e)
            )
            self._has_connection_attempt = True
            return False

    def connection_status(self):
        """Restituisce lo stato leggibile della connessione."""
        return self._STATUS_MAP.get(self._wlan.status(), "STATO SCONOSCIUTO")

    def get_ip_address(self):
        """Restituisce l'indirizzo IP della connessione."""
        return self._wlan.ifconfig()[0]

    def send_value_to_web(self, value, key, timestamp):
        """
        Accoda un valore per l'invio asincrono in background.
        Restituisce False se il WiFi non ha mai stabilito una connessione valida,
        così il ciclo principale può gestire il fallimento senza considerarlo come successo.
        """
        if not self._has_connection_attempt:
            self.log_message("[wifi] Connessione WiFi non ancora verificata. Invio non eseguito.")
            return False

        self.log_message("[wifi] Accodato dato per l'invio: {} = {}".format(key, value))
        self._queue_lock.acquire()
        self._queue.append((value, key, timestamp))
        self._queue_lock.release()
        return True

    def has_queued_items(self):
        """Verifica se ci sono dati in coda da inviare."""
        self._queue_lock.acquire()
        res = len(self._queue) > 0
        self._queue_lock.release()
        return res

    def pop_queue(self):
        """Estrae il primo elemento dalla coda."""
        self._queue_lock.acquire()
        item = self._queue.pop(0) if self._queue else None
        self._queue_lock.release()
        return item

    def _send_value_to_web_sync(self, value, key, timestamp):
        """
        Invia un valore a un endpoint web in modo sincrono.
        Questa funzione deve essere eseguita dal thread di background.
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

        self.log_message("[wifi] Invio dati a {}.".format(url))

        try:
            response_text = self._post_https_request(url, data)
        finally:
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
            "X-IoT-Token": self._token,
            "Content-Type": "application/json",
            "Connection": "close",
        }

        try:
            json_payload = json.dumps(data)
        except Exception as e:
            self.log_message(
                "[wifi] Errore durante la serializzazione JSON: {}".format(e)
            )
            gc.collect()
            return None
        finally:
            try:
                del data
            except NameError:
                pass

        try:
            import urequests
        except ImportError as e:
            self.log_message("[wifi] urequests non disponibile: {}".format(e))
            del json_payload
            gc.collect()
            return None

        # ── Timeout esplicito sul socket ──────────────────────────────────────
        # Copre la fase di DNS lookup/connessione TCP, la causa più comune di
        # blocco prolungato in rete instabile. NON copre un eventuale hang
        # post-handshake TLS (limite noto di urequests+ssl) — per quello
        # serve il WDT (vedi sotto e commento in testa al file).
        try:
            import socket
            socket.setdefaulttimeout(15)
        except Exception:
            pass

        self._feed_wdt()  # checkpoint: stiamo per iniziare, siamo vivi

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
                    self._feed_wdt()  # checkpoint pre-richiesta
                    # In MicroPython è preferibile passare esplicitamente i parametri.
                    response = urequests.post(url, data=json_payload, headers=headers)
                    self._feed_wdt()  # checkpoint: la richiesta è tornata (non si è bloccata)

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
                        # Nutriamo il WDT a metà attesa: l'attesa è voluta,
                        # non un hang, quindi il WDT non deve scattare qui.
                        wait_time = retry_delay * (2 ** (attempt - 1))
                        half = wait_time / 2
                        time.sleep(half)
                        self._feed_wdt()
                        time.sleep(wait_time - half)
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