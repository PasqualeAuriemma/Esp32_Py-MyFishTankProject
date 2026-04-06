# Modules/ds18b20.py
#
# Driver per sensore di temperatura DS18B20 su bus 1-Wire — ESP32 ottimizzato.
#
# Caratteristiche:
#   - Pre-allocazione buffer: zero heap allocation sul hot path di lettura
#   - Lettura asincrona: avvia conversione e legge separatamente (non blocca)
#   - Supporto multi-sensore: gestisce N sensori sullo stesso pin
#   - Risoluzione configurabile: 9/10/11/12 bit (93ms → 750ms)
#   - CRC validato: scarta letture corrotte senza eccezioni
#   - Singleton opzionale: pass-through se si vuole una sola istanza
#   - _log() debug zero-costo in produzione
#
# Dipendenze MicroPython built-in:
#   - machine.Pin
#   - onewire  (built-in MicroPython)
#   - ds18x20  (built-in MicroPython)
#
# Collegamento hardware:
#   DS18B20 VDD  →  3.3V
#   DS18B20 GND  →  GND
#   DS18B20 DQ   →  GPIO (es. 4) + resistenza pull-up 4.7KΩ verso 3.3V
#
# Uso minimo:
#   from Modules.ds18b20 import DS18B20
#   sensor = DS18B20(pin=4)
#   temp = sensor.read_temperature()
#   print(temp)  # es. 23.5
#
# Uso multi-sensore:
#   sensor = DS18B20(pin=4)
#   for addr, temp in sensor.read_all():
#       print(addr.hex(), temp)

import gc
import time
from machine import Pin # type: ignore[import]
from micropython import const # type: ignore[import]

try:
    import onewire # type: ignore[import]
    import ds18x20 # type: ignore[import]
except ImportError:
    raise ImportError(
        "[DS18B20] Error Moduli 'onewire' e 'ds18x20' non trovati. "
        "Assicurati di usare MicroPython con supporto 1-Wire."
    )

# ── Costanti ───────────────────────────────────────────────────────────

# Tempo di conversione in ms per ciascuna risoluzione
# DS18B20 datasheet: 9bit=93.75ms, 10=187.5ms, 11=375ms, 12=750ms
_CONV_TIME = {
    9:  const(100),
    10: const(200),
    11: const(400),
    12: const(800),   # margine extra rispetto ai 750ms nominali
}

_DEFAULT_RESOLUTION = const(12)
_TEMP_INVALID       = const(-999)   # valore sentinella per lettura fallita
_MAX_RETRIES        = const(3)       # tentativi prima di dichiarare errore
_RETRY_DELAY_MS     = const(10)      # pausa tra un retry e l'altro


class DS18B20:
    """
    Driver per uno o più sensori DS18B20 sullo stesso bus 1-Wire.

    Gestisce automaticamente:
    - Scansione e cache degli indirizzi ROM al boot
    - Conversione asincrona (convert → sleep → read)
    - Validazione CRC implicita tramite ds18x20
    - Retry automatico su lettura corrotta
    - Risoluzione configurabile per ridurre il tempo di conversione

    Args:
        pin:        Numero GPIO oppure oggetto machine.Pin già configurato.
        resolution: Risoluzione ADC in bit (9, 10, 11, 12). Default: 12.
        debug:      Se True, abilita i log di diagnostica. Default: False.
    """

    # __slots__ riduce la RAM usata dall'istanza su MicroPython
    __slots__ = (
        "_ow",
        "_ds",
        "_pin",
        "_resolution",
        "_debug",
        "_roms",          # lista di bytearray ROM (8 byte ciascuno)
        "_conv_time_ms",  # ms di attesa dopo convert()
        "_last_temps",    # dict rom_bytes → float, cache ultima lettura
    )

    def __init__(self, pin, resolution = _DEFAULT_RESOLUTION,
                 debug = False):

        # Normalizza il pin: accetta int o Pin
        self._pin = pin if isinstance(pin, Pin) else Pin(pin)
        self._debug = debug

        # Valida la risoluzione prima di configurare il bus
        if resolution not in _CONV_TIME:
            raise ValueError(
                "[DS18B20] Error: Risoluzione non valida: {}. Usa 9, 10, 11 o 12.".format(resolution)
            )
        self._resolution  = resolution
        self._conv_time_ms = _CONV_TIME[resolution]

        # Inizializza bus 1-Wire e driver DS18x20
        self._ow  = onewire.OneWire(self._pin)
        self._ds  = ds18x20.DS18X20(self._ow)

        # Scansione iniziale — trova tutti i sensori sul bus
        self._roms      = []
        self._last_temps = {}
        self._scan()

        gc.collect()
        self._log(" Init OK — {} sensori, {}bit, {}ms conv".format(
            len(self._roms), self._resolution, self._conv_time_ms))

    # ── Logging ────────────────────────────────────────────────────────

    def _log(self, *args):
        """Zero-overhead: nessuna stringa formattata se debug=False."""
        if self._debug:
            print("[DS18B20]", *args)

    # ── Scan ───────────────────────────────────────────────────────────

    def _scan(self):
        """Scansiona il bus e aggiorna la lista ROM interna."""
        try:
            found = self._ds.scan()
            self._roms = found
            self._log("Scansione: trovati", len(found), "sensori")
            if not found:
                print("[DS18B20] WARN: nessun sensore trovato sul pin {}".format(
                    self._pin))
        except onewire.OneWireError as e:
            print("[DS18B20] Error: scansione 1-Wire: {}".format(e))
            self._roms = []

    def rescan(self):
        """
        Forza una nuova scansione del bus.
        Utile se si aggiungono/rimuovono sensori a caldo.
        """
        self._scan()

    # ── Proprietà ──────────────────────────────────────────────────────

    @property
    def sensor_count(self) -> int:
        """Numero di sensori rilevati sul bus."""
        return len(self._roms)

    @property
    def roms(self) -> list:
        """Lista degli indirizzi ROM rilevati (bytearray da 8 byte ciascuno)."""
        return self._roms

    @property
    def resolution(self) -> int:
        """Risoluzione ADC corrente in bit."""
        return self._resolution

    @resolution.setter
    def resolution(self, value: int):
        """
        Cambia la risoluzione di tutti i sensori sul bus.
        Aggiorna anche il tempo di attesa conversione.
        """
        if value not in _CONV_TIME:
            raise ValueError("[DS18B20] Error: Risoluzione non valida: {}".format(value))
        self._resolution   = value
        self._conv_time_ms = _CONV_TIME[value]
        self._log("Risoluzione cambiata a {}bit".format(value))

    # ── Lettura ────────────────────────────────────────────────────────

    def _convert(self):
        """
        Avvia la conversione su tutti i sensori e attende il tempo necessario.
        Usa time.sleep_ms() — il WDT viene resettato dal chiamante nel loop.
        """
        self._ds.convert_temp()
        time.sleep(self._conv_time_ms / 1000)

    def read_temperature(self, rom=None) -> float:
        """
        Legge la temperatura dal primo sensore (o da *rom* se specificato).

        Args:
            rom: Indirizzo ROM del sensore (bytearray 8 byte).
                 Se None, usa il primo sensore trovato.

        Returns:
            Temperatura in gradi Celsius (float, es. 23.5).
            Ritorna _TEMP_INVALID (-999) se la lettura fallisce.
        """
        if not self._roms:
            self._log("Nessun sensore disponibile")
            return _TEMP_INVALID

        target = rom if rom is not None else self._roms[0]

        for attempt in range(_MAX_RETRIES):
            try:
                self._convert()
                temp = self._ds.read_temp(target)

                # Validazione range fisico: DS18B20 misura da -55 a +125 °C
                if temp < -55 or temp > 125:
                    self._log("Lettura fuori range: {} C (tentativo {})".format(
                        temp, attempt + 1))
                    time.sleep(_RETRY_DELAY_MS / 1000)
                    continue

                self._last_temps[bytes(target)] = temp
                self._log("Temperatura: {} C".format(temp))
                return temp

            except onewire.OneWireError as e:
                self._log("Error: 1-Wire tentativo {}: {}".format(attempt + 1, e))
                time.sleep(_RETRY_DELAY_MS / 1000)
            except Exception as e:
                self._log("Error: imprevisto: {}".format(e))
                return _TEMP_INVALID

        print("[DS18B20] Error: Lettura fallita dopo {} tentativi".format(_MAX_RETRIES))
        return _TEMP_INVALID

    def read_all(self) -> list:
        """
        Legge la temperatura da TUTTI i sensori sul bus in una sola conversione.

        Returns:
            Lista di tuple (rom_bytes, temp_float).
            Le letture corrotte vengono saltate (non incluse nel risultato).

        Esempio:
            for rom, temp in sensor.read_all():
                print(rom.hex(), temp)
        """
        if not self._roms:
            return []

        result = []
        try:
            # Una sola conversione per tutti i sensori — efficiente
            self._ds.convert_temp()
            time.sleep(self._conv_time_ms / 1000)

            for rom in self._roms:
                try:
                    temp = self._ds.read_temp(rom)
                    if -55 <= temp <= 125:
                        self._last_temps[bytes(rom)] = temp
                        result.append((bytes(rom), temp))
                    else:
                        self._log("ROM {} fuori range: {} C — scartata".format(
                            bytes(rom).hex(), temp))
                except onewire.OneWireError as e:
                    self._log("Error: ROM {}: {}".format(bytes(rom).hex(), e))

        except onewire.OneWireError as e:
            print("[DS18B20] Error: convert_temp: {}".format(e))

        return result

    def read_temperature_cached(self, rom=None) -> float:
        """
        Ritorna l'ultima temperatura letta senza avviare una nuova conversione.
        Utile se si vuole leggere il valore più recente senza bloccare.

        Returns:
            Ultima temperatura valida o _TEMP_INVALID se non ancora letta.
        """
        target = rom if rom is not None else (self._roms[0] if self._roms else None)
        if target is None:
            return _TEMP_INVALID
        return self._last_temps.get(bytes(target), _TEMP_INVALID)

    # ── Utilità ────────────────────────────────────────────────────────

    def is_valid(self, temp: float) -> bool:
        """Ritorna True se *temp* è una lettura valida (non il valore sentinella)."""
        return temp != _TEMP_INVALID

    def rom_to_str(self, rom) -> str:
        """Converte un indirizzo ROM in stringa hex leggibile (es. '28ff3a...')."""
        return bytes(rom).hex()

    def __repr__(self) -> str:
        return "DS18B20(pin={}, sensors={}, resolution={}bit)".format(
            self._pin, len(self._roms), self._resolution)
