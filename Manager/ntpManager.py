import time
import gc


class NTP:
    """
    Sincronizza il DS3231 con NTP.

    Dipendenze iniettate nel costruttore — NtpSync non sa nulla
    di WiFi o RTC internamente, li riceve già pronti.
    """

    def __init__(self, wifi, rtc):
        """
        Args:
            wifi: istanza WifiConnection
            rtc:  istanza DS3231_RTC
        """
        self._wifi = wifi
        self._rtc = rtc

    def sync(self):
        """
        Connette il WiFi, sincronizza l'RTC interno dell'ESP32 via NTP,
        copia l'ora sul DS3231 fisico, disconnette.

        Ritorna True se la sync è riuscita, False altrimenti.
        """
        import ntptime  # type: ignore[import]

        try:
            # 1. Connetti WiFi
            if not self._wifi.connect():
                print("[ntp] Connessione WiFi fallita.")
                return False

            # 2. Sync NTP → aggiorna machine.RTC() interno dell'ESP32
            ntptime.settime()
            print("[ntp] NTP sync OK — ora UTC: {}".format(time.localtime()))

            # 3. Copia l'ora sul chip DS3231 fisico (sopravvive ai reboot)
            self._rtc.datetime = time.localtime()
            print("[ntp] DS3231 aggiornato.")

            return True

        except OSError as e:
            print("[ntp] Errore di rete: {}".format(e))
            return False

        except Exception as e:
            print("[ntp] Errore imprevisto: {}".format(e))
            return False

        finally:
            # Disconnetti e libera ntptime sempre, anche in caso di errore
            self._wifi.disconnect()
            try:
                import sys

                sys.modules.pop("ntptime", None)
                del ntptime
            except Exception:
                pass
            gc.collect()
