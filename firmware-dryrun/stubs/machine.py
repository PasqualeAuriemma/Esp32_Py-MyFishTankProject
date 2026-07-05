"""
Mock eseguibile del modulo `machine` per dry-run su Docker/Unix MicroPython.
Le firme rispettano machine.pyi fornito da Pasquale; qui aggiungiamo
comportamento simulato e logging, non solo segnature vuote.
"""

import time


class Pin:
    OUT = 1
    IN  = 0
    PULL_UP = 2
    PULL_DOWN = 3

    def __init__(self, pin_id, mode=None, pull=None):
        self.pin_id = pin_id
        self.mode = mode
        self.pull = pull
        self._value = 0
        print("[machine.Pin] init pin={} mode={} pull={}".format(pin_id, mode, pull))

    def init(self, mode=None, pull=None, value=None):
        if mode is not None:
            self.mode = mode
        if value is not None:
            self._value = value
        print("[machine.Pin] init pin={} mode={} value={}".format(self.pin_id, mode, value))

    def __call__(self, val=None):
        """Supporta pin(0) / pin(1) — idioma MicroPython per CS del bus SPI."""
        return self.value(val)

    _VERBOSE_PINS = {13, 14, 17, 25, 26, 27, 33}  # DS18B20, MOSFET PH/EC, Relays

    def value(self, val=None):
        if val is None:
            return self._value
        self._value = val
        if self.pin_id in self._VERBOSE_PINS:
            print("[machine.Pin] pin={} -> value={}".format(self.pin_id, val))

    def on(self):
        self.value(1)

    def off(self):
        self.value(0)


class I2C:
    def __init__(self, id=0, scl=None, sda=None, freq=400_000):
        self.id = id
        self.scl = scl
        self.sda = sda
        self.freq = freq
        print("[machine.I2C] init id={} freq={}".format(id, freq))

    def scan(self):
        # Nessun device reale: lista vuota per default.
        print("[machine.I2C] scan() -> []")
        return []

    def readfrom(self, addr, nbytes):
        print("[machine.I2C] readfrom addr={} nbytes={}".format(addr, nbytes))
        return bytes(nbytes)

    def writeto(self, addr, buf):
        # addr=0x3C (60) = OLED SSD1306 — silenzioso, genera troppo rumore
        if addr != 60:
            print("[machine.I2C] writeto addr={} buf={}".format(addr, buf))

    def readfrom_mem(self, addr, memaddr, nbytes):
        # Logga solo RTC (addr=104) — altri device silenziosissimi
        if addr == 104:
            print("[machine.I2C] readfrom_mem addr={} memaddr={} nbytes={}".format(addr, memaddr, nbytes))
        return bytes(nbytes)

    def readfrom_mem_into(self, addr, memaddr, buf):
        # Logga solo RTC (addr=104)
        if addr == 104:
            print("[machine.I2C] readfrom_mem_into addr={} memaddr={} len(buf)={}".format(
                addr, memaddr, len(buf)))
        if memaddr == 0 and len(buf) >= 7:
            import time as _t
            lt = _t.localtime()

            def _bcd(v):
                return ((v // 10) << 4) | (v % 10)

            buf[0] = _bcd(lt[5])           # secondi
            buf[1] = _bcd(lt[4])           # minuti
            buf[2] = _bcd(lt[3])           # ore (bit6=0 → 24h mode)
            buf[3] = _bcd(lt[6] + 1)       # giorno settimana (1-7)
            buf[4] = _bcd(lt[2])           # giorno mese
            buf[5] = _bcd(lt[1])           # mese (bit7=0 → no century)
            buf[6] = _bcd(lt[0] - 2000)    # anno (es. 2026 → 26)
        else:
            for i in range(len(buf)):
                buf[i] = 0

    def writeto_mem(self, addr, memaddr, buf):
        if addr != 60:
            print("[machine.I2C] writeto_mem addr={} memaddr={} buf={}".format(addr, memaddr, buf))

    def writevto(self, addr, vector):
        # Silenzioso: usato da SSD1306 per framebuffer (addr=60)
        pass


class WDT:
    """Watchdog mock: traccia i feed() e segnala se scade il timeout."""

    def __init__(self, timeout=5000):
        self.timeout_ms = timeout
        self._last_feed = time.time()
        print("[machine.WDT] init timeout={}ms".format(timeout))

    def feed(self):
        now = time.time()
        elapsed_ms = (now - self._last_feed) * 1000
        if elapsed_ms > self.timeout_ms:
            print("[machine.WDT] !!! TIMEOUT SUPERATO ({:.0f}ms > {}ms) — "
                  "su hardware reale qui ci sarebbe un RESET".format(elapsed_ms, self.timeout_ms))
        self._last_feed = now


class SPI:
    MSB = 0
    LSB = 1

    def __init__(self, id, baudrate=1_000_000, polarity=0, phase=0,
                 bits=8, firstbit=0, sck=None, mosi=None, miso=None):
        self.id = id
        self.baudrate = baudrate
        print("[machine.SPI] init id={} baudrate={}".format(id, baudrate))

    def init(self, baudrate=None, **kwargs):
        if baudrate is not None:
            self.baudrate = baudrate
        print("[machine.SPI] re-init baudrate={}".format(self.baudrate))

    def deinit(self):
        print("[machine.SPI] deinit")

    def read(self, nbytes, write=0x00):
        print("[machine.SPI] read nbytes={}".format(nbytes))
        return bytes(nbytes)

    def write(self, buf):
        print("[machine.SPI] write len={}".format(len(buf)))

    def write_readinto(self, write_buf, read_buf):
        print("[machine.SPI] write_readinto len={}".format(len(write_buf)))

    def readinto(self, buf, write=0x00):
        print("[machine.SPI] readinto len={}".format(len(buf)))
        for i in range(len(buf)):
            buf[i] = 0xFF


class ADC:
    ATTN_0DB   = 0
    ATTN_2_5DB = 1
    ATTN_6DB   = 2
    ATTN_11DB  = 3

    WIDTH_9BIT  = 0
    WIDTH_10BIT = 1
    WIDTH_11BIT = 2
    WIDTH_12BIT = 3

    def __init__(self, pin):
        self.pin = pin
        self._atten = self.ATTN_0DB
        print("[machine.ADC] init su pin={}".format(getattr(pin, 'pin_id', pin)))

    def atten(self, value):
        self._atten = value
        print("[machine.ADC] atten({})".format(value))

    def width(self, value):
        print("[machine.ADC] width({})".format(value))

    def read(self):
        # Nessun tasto premuto di default nel dry-run (valore alto = "null").
        # Per simulare una pressione, si può sovrascrivere questo metodo
        # da uno script di test specifico.
        return 4095


class RTC:
    def __init__(self):
        self._dt = (2026, 1, 1, 0, 0, 0, 0, 0)

    def datetime(self, datetimetuple=None):
        if datetimetuple is None:
            return self._dt
        self._dt = datetimetuple


def lightsleep(ms=None):
    """Sleep mock — usiamo time.sleep reale per rispettare i tempi del loop nel dry-run."""
    if ms is not None:
        time.sleep(ms / 1000.0)
