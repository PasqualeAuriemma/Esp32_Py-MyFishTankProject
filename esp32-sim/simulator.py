"""
ESP32 MicroPython Simulator
============================
Simula il firmware MicroPython dell'ESP32, replicando esattamente
la logica di wifiConnection.py ma in CPython standard.
 
Invia ciclicamente TempC, Ph, Tds alla web app MyFishTank.
"""
 
import time
import json
import random
import logging
import sys
import os
import requests
from datetime import datetime
from threading import Lock, Thread
import secret
 
# ─── Logging ────────────────────────────────────────────────────────────────
logging.basicConfig(
    level=logging.INFO,
    format="%(asctime)s [ESP32-SIM] %(message)s",
    datefmt="%H:%M:%S",
    stream=sys.stdout,
)
log = logging.getLogger("esp32-sim")
 
# ─── Configurazione (da env o default) ───────────────────────────────────────
HOST        = os.environ.get("WEBAPP_HOST", secret.WEBAPP_HOST)
PORT        = int(os.environ.get("WEBAPP_PORT", secret.WEBAPP_PORT))
PROTOCOL    = os.environ.get("WEBAPP_PROTOCOL", secret.WEBAPP_PROTOCOL)
IOT_TOKEN   = os.environ.get("IOT_TOKEN", secret.IOT_TOKEN)
SEND_INTERVAL = int(os.environ.get("SEND_INTERVAL_SEC", 120000)) # secondi tra un invio e l'altro
MAX_RETRIES = int(os.environ.get("MAX_RETRIES", 3))
 
 
# ─── Sensori simulati ─────────────────────────────────────────────────────────
class SensorSimulator:
    """
    Simula letture realistiche dei sensori con deriva lenta nel tempo,
    come farebbe un vero sensore in acquario.
    """
 
    def __init__(self):
        # Valori base (tipici acquario tropicale d'acqua dolce)
        self._temp   = 26.0   # °C
        self._ph     = 7.0    # pH
        self._ec    = 150.0  # ppm
 
    def read_all(self):
        """Ritorna un dict con tutti i valori simulati aggiornati."""
        # Deriva lenta ± piccolo rumore gaussiano
        self._temp  = max(22.0, min(30.0, self._temp  + random.gauss(0, 0.05)))
        self._ph    = max(6.0,  min(8.5,  self._ph    + random.gauss(0, 0.02)))
        self._ec   = max(50.0, min(400.0, self._ec  + random.gauss(0, 0.5)))
 
        return {
            "Temp": round(self._temp, 2),
            "PH":    round(self._ph,   2),
            "Ec":   round(self._ec,  1),
        }
 
 
# ─── Replica WifiConnection (adattata per CPython/Docker) ────────────────────
class WifiConnectionSim:
    """
    Replica fedele di wifiConnection.py adattata per CPython.
    Mantiene la stessa logica: coda asincrona, retry con backoff,
    header X-IoT-Token al posto di urequests.
    """
 
    def __init__(self, host, port, protocol, token):
        self._host     = host
        self._port     = port
        self._protocol = protocol
        self._token    = token
        self._queue    = []
        self._lock     = Lock()
 
        # Sessione requests riutilizzabile — Content-Type JSON allineato al firmware reale
        self._session  = requests.Session()
        self._session.headers.update({
            "Content-Type": "application/json",
            "Connection":  "close",
            "X-IoT-Token": self._token,
        })
 
    # ── API pubblica ──────────────────────────────────────────────────────────
 
    def send_value_to_web(self, value, key, timestamp):
        """Accoda il dato per l'invio asincrono (identico a MicroPython)."""
        log.info("[wifi] Accodato dato per l'invio: %s = %s", key, value)
        with self._lock:
            self._queue.append((value, key, timestamp))
        return True
 
    def has_queued_items(self):
        with self._lock:
            return len(self._queue) > 0
 
    def pop_queue(self):
        with self._lock:
            return self._queue.pop(0) if self._queue else None
 
    def flush_queue(self):
        """Processa tutti gli elementi in coda (chiamato dal background thread)."""
        while self.has_queued_items():
            item = self.pop_queue()
            if item:
                value, key, timestamp = item
                self._send_value_to_web_sync(value, key, timestamp)
 
    # ── Invio sincrono ────────────────────────────────────────────────────────
 
    def _send_value_to_web_sync(self, value, key, timestamp):
        url  = "{}://{}:{}/take{}.php".format(
            self._protocol, self._host, self._port, key
        )
        data = {key: value, "Date": timestamp}
 
        log.info("[wifi] Invio dati a %s", url)
 
        response_text = self._post_request(url, data)
        if response_text is not None:
            log.info("[wifi] Dati inviati con successo.")
            return True
 
        log.warning("[wifi] Invio dati fallito.")
        return False
 
    def _post_request(self, url, data, retry_delay=2):
        """
        POST con body JSON (json.dumps), allineato a wifiConnection.py.
        Il token viaggia nell'header X-IoT-Token (già nella sessione).
        """
        json_payload = json.dumps(data)

        for attempt in range(1, MAX_RETRIES + 1):
            try:
                log.info("[wifi] POST a: %s (Tentativo %d/%d)", url, attempt, MAX_RETRIES)
                response = self._session.post(
                    url,
                    data=json_payload,
                    timeout=10,
                )
 
                status = response.status_code
                if 200 <= status < 300:
                    log.info("[wifi] Successo! Status: %d | Risposta: %s",
                             status, response.text.strip())
                    return response.text
                else:
                    log.error("[wifi] Errore HTTP: %d %s", status, response.reason)
                    return None  # errore server → non ritentare
 
            except requests.exceptions.ConnectionError as e:
                log.warning("[wifi] Errore di rete (Tentativo %d): %s", attempt, e)
                if attempt < MAX_RETRIES:
                    sleep_time = retry_delay * (2 ** (attempt - 1))
                    log.info("[wifi] Attendo %ds prima del prossimo tentativo...", sleep_time)
                    time.sleep(sleep_time)
                else:
                    log.error("[wifi] Tutti i tentativi sono falliti.")
                    return None
 
            except Exception as e:
                log.error("[wifi] Errore imprevisto: %s", e)
                return None
 
        return None
 
 
# ─── Main loop ────────────────────────────────────────────────────────────────
 
def background_sender(wifi):
    """Thread di background che svuota la coda (come farebbe il secondo thread sull'ESP32)."""
    while True:
        wifi.flush_queue()
        time.sleep(1)
 
 
def main():
    log.info("=" * 55)
    log.info("  ESP32 MyFishTank Simulator — avvio")
    log.info("  Target: %s://%s:%d", PROTOCOL, HOST, PORT)
    log.info("  Token:  %s...%s", IOT_TOKEN[:6], IOT_TOKEN[-4:])
    log.info("  Intervallo invio: %ds", SEND_INTERVAL)
    log.info("=" * 55)
 
    # Attesa iniziale: la webapp potrebbe impiegare qualche secondo ad avviarsi
    startup_wait = int(os.environ.get("STARTUP_WAIT_SEC", 7005))
    log.info("Attendo %ds per il boot della webapp...", startup_wait)
    time.sleep(startup_wait)
 
    sensor = SensorSimulator()
    wifi   = WifiConnectionSim(HOST, PORT, PROTOCOL, IOT_TOKEN)
 
    # Avvia background sender thread
    sender_thread = Thread(target=background_sender, args=(wifi,), daemon=True)
    sender_thread.start()
    log.info("Background sender thread avviato.")
 
    cycle = 0
    while True:
        cycle += 1
        log.info("─── Ciclo #%d ───────────────────────────────────────", cycle)
        readings = sensor.read_all()
        #timestamp = datetime.now().strftime("%Y-%m-%d %H:%M:%S")
        # Timestamp Unix corrente (intero, secondi da epoch)
        dt = datetime.now()
        unix_ts = int(dt.timestamp())
        for key, value in readings.items():
            log.info("Sensore letto → %s = %s", key, value)
            wifi.send_value_to_web(value, key, unix_ts)
 
        log.info("Attendo %ds prima del prossimo ciclo...", SEND_INTERVAL)
        time.sleep(SEND_INTERVAL)
 
 
if __name__ == "__main__":
    main()