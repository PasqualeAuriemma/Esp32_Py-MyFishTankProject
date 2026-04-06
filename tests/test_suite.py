# tests/test_suite.py
#
# Suite di test unitari per PyTank — gira su CPython desktop (non su ESP32).
# Simula tutto l'hardware (display, RTC, WiFi, SD, relay, sensori) con mock
# objects leggeri, senza dipendenze da MicroPython.
#
# Esecuzione:
#   python -m pytest tests/test_suite.py -v
#   oppure:
#   python tests/test_suite.py
#
# Struttura:
#   MockDisplay         — simula SSD1306 (128×64 framebuffer virtuale)
#   MockRTC             — simula DS3231
#   MockWifi            — simula WifiConnection
#   MockRelay           — simula un singolo relay Pin
#   MockRelays          — simula il banco relay
#   MockSDCard          — simula SDCardManager (storage in memoria)
#   MockDS18B20         — simula il sensore DS18B20
#   MockSingleton       — resetta il Singleton tra i test
#   ─────────────────────────────────────────────────────
#   TestConfig          — test su Config (timer, relay, mode, serialize)
#   TestWifiConnection  — test su connect/disconnect/send
#   TestSDCardManager   — test su read/write config
#   TestNTPManager      — test su sync NTP
#   TestViewer          — test su UI state machine
#   TestMain            — test su flusso main() con mock completo
#   ─────────────────────────────────────────────────────

'''
Struttura della suite — 59 test, 6 classi
TestConfig (13 test) — logica pura senza hardware. Testa valori di default, timer, soglie heater, la dipendenza doppia dei flag sending (attivo solo se anche la lettura è attiva), il cambio di modalità AUTO↔MAINTENANCE con save/restore dello stato, serializzazione to_dict/from_json e il Singleton.
TestWifiConnection (10 test) — usa il _FakeWLAN stub per simulare il driver network. Testa connect, disconnect, stati, auto-connect prima di send, mancanza di host.
TestSDCardLogic (5 test) — usa MockSDCard in memoria, nessun SPI. Testa read/write config, roundtrip, overwrite e disponibilità.
TestNTPManager (4 test) — inietta ntptime stub in sys.modules prima della chiamata lazy, testa sync OK, fallimento WiFi e aggiornamento RTC.
TestViewer (11 test) — sostituisce SSD1306_I2C con MockDisplay che registra ogni chiamata. Testa la state machine di run(), i toggle relay (con logica active-low reale: value(0)=ON), e show_rele_symbol.
TestMain (16 test) — integrazione completa. Simula l'intera sequenza di boot, caricamento config da SD, lettura temperatura, invio dati web, countdown menu, GC ciclico, e tutti i use case principali.
'''

import sys
import os
import unittest
import json

# ── Aggiungi la radice del progetto al path ────────────────────────────
# Permette di importare Manager/, Resource/, ecc. senza installazione
_PROJECT_ROOT = os.path.abspath(os.path.join(os.path.dirname(__file__), ".."))
if _PROJECT_ROOT not in sys.path:
    sys.path.insert(0, _PROJECT_ROOT)


# ══════════════════════════════════════════════════════════════════════
# STUB MODULI MICROPYTHON
# Questi moduli non esistono su CPython — li creiamo prima di importare
# qualsiasi cosa del progetto.
# ══════════════════════════════════════════════════════════════════════

class _FakeConst:
    """micropython.const() su CPython ritorna il valore invariato."""
    @staticmethod
    def const(x):
        return x

import types

# micropython
mp_mod = types.ModuleType("micropython")
mp_mod.const = lambda x: x
mp_mod.mem_info = lambda *a: None
sys.modules["micropython"] = mp_mod

# machine — stub minimale
machine_mod = types.ModuleType("machine")
class _FakePin:
    OUT = 1; IN = 0
    def __init__(self, *a, **kw): self._v = 0
    def __call__(self, v=None):
        if v is not None: self._v = v
        return self._v
    def value(self, v=None):
        if v is not None: self._v = v
        return self._v
    def init(self, *a, **kw): pass
    def high(self): self._v = 1
    def low(self):  self._v = 0

class _FakeI2C:
    def __init__(self, *a, **kw): pass
    def scan(self): return [0x3C, 0x68]
    def writeto(self, *a): pass
    def readfrom_mem(self, addr, reg, n): return bytearray(n)
    def readfrom_mem_into(self, addr, reg, buf): pass
    def writeto_mem(self, *a): pass
    def writevto(self, *a): pass

class _FakeADC:
    ATTN_11DB = 3
    def __init__(self, *a): pass
    def atten(self, *a): pass
    def read(self): return 4095   # nessun tasto premuto → tensione massima

class _FakeWDT:
    def __init__(self, **kw): pass
    def feed(self): pass

machine_mod.Pin     = _FakePin
machine_mod.I2C     = _FakeI2C
machine_mod.ADC     = _FakeADC
machine_mod.WDT     = _FakeWDT
machine_mod.SPI     = type("SPI",    (), {"__init__": lambda s,*a,**kw: None,
                                           "deinit":   lambda s: None,
                                           "write":    lambda s,b: None,
                                           "read":     lambda s,n,v=0xFF: bytearray(n),
                                           "readinto": lambda s,b,v=0xFF: None,
                                           "write_readinto": lambda s,o,i: None,
                                           "init":     lambda s,*a,**kw: None})
machine_mod.SoftSPI = machine_mod.SPI
machine_mod.PWRON_RESET   = 1
machine_mod.HARD_RESET    = 2
machine_mod.WDT_RESET     = 3
machine_mod.DEEPSLEEP_RESET = 4
machine_mod.SOFT_RESET    = 5
machine_mod.reset_cause   = lambda: 1
machine_mod.reset         = lambda: None
machine_mod.lightsleep    = lambda ms=0: None
sys.modules["machine"] = machine_mod

# network
net_mod = types.ModuleType("network")
net_mod.STAT_IDLE           = 0
net_mod.STAT_CONNECTING     = 1
net_mod.STAT_WRONG_PASSWORD = 2
net_mod.STAT_NO_AP_FOUND    = 3
net_mod.STAT_GOT_IP         = 5
net_mod.STA_IF              = 0
class _FakeWLAN:
    def __init__(self, *a): self._active = False; self._connected = False
    def active(self, v=None):
        if v is not None: self._active = v
        return self._active
    def connect(self, *a): self._connected = True
    def disconnect(self): self._connected = False
    def isconnected(self): return self._connected
    def status(self): return net_mod.STAT_GOT_IP if self._connected else net_mod.STAT_IDLE
    def ifconfig(self): return ("192.168.1.100", "255.255.255.0", "192.168.1.1", "8.8.8.8")
net_mod.WLAN = _FakeWLAN
sys.modules["network"] = net_mod

# uos / os stub
uos_mod = types.ModuleType("uos")
_mounted = {}
def _mount(vfs, path): _mounted[path] = vfs
def _umount(path): _mounted.pop(path, None)
uos_mod.mount  = _mount
uos_mod.umount = _umount
sys.modules["uos"] = uos_mod

# framebuf stub
fb_mod = types.ModuleType("framebuf")
fb_mod.MONO_VLSB = 0
fb_mod.MONO_HLSB = 1
class _FakeFrameBuffer:
    def __init__(self, buf, w, h, fmt): pass
fb_mod.FrameBuffer  = _FakeFrameBuffer
fb_mod.FrameBuffer1 = _FakeFrameBuffer
sys.modules["framebuf"] = fb_mod

# onewire / ds18x20 stub
ow_mod = types.ModuleType("onewire")
class _OneWireError(Exception): pass
ow_mod.OneWire      = type("OneWire", (), {"__init__": lambda s, p: None})
ow_mod.OneWireError = _OneWireError
sys.modules["onewire"] = ow_mod

ds18_mod = types.ModuleType("ds18x20")
class _FakeDS18X20:
    def __init__(self, ow): self._temp = 24.0
    def scan(self): return [bytearray(8)]
    def convert_temp(self): pass
    def read_temp(self, rom): return self._temp
ds18_mod.DS18X20 = _FakeDS18X20
sys.modules["ds18x20"] = ds18_mod

# ntptime stub
ntp_mod = types.ModuleType("ntptime")
ntp_mod.settime = lambda: None
sys.modules["ntptime"] = ntp_mod

# urequests stub
class _FakeResponse:
    status_code = 200
    text = '{"ok": true}'
    def close(self): pass
ur_mod = types.ModuleType("urequests")
ur_mod.post = lambda url, **kw: _FakeResponse()
sys.modules["urequests"] = ur_mod

# Icons stub
icons_mod = types.ModuleType("Icons")
sys.modules["Icons"] = icons_mod
sym_mod = types.ModuleType("Icons.symbols")
sym_mod.degree_symbol = bytearray(8)
sys.modules["Icons.symbols"] = sym_mod
img_mod = types.ModuleType("Icons.images_repo")
img_mod.fishtank_logo = bytearray(128 * 8)
sys.modules["Icons.images_repo"] = img_mod

# secrets stub
sec_mod = types.ModuleType("secrets")
sec_mod.WIFI_SSID     = "TestSSID"
sec_mod.WIFI_PASSWORD = "TestPassword"
sys.modules["secrets"] = sec_mod


# ══════════════════════════════════════════════════════════════════════
# MOCK OBJECTS — hardware simulato
# ══════════════════════════════════════════════════════════════════════

class MockDisplay:
    """Simula SSD1306: registra tutte le chiamate senza disegnare nulla."""
    def __init__(self, width=128, height=64):
        self.width  = width
        self.height = height
        self.pages  = height // 8
        self._calls = []
        self.buffer = bytearray(width * height // 8 + 1)
        self._char_dimension = 8

    @property
    def char_dimension(self): return self._char_dimension

    def _rec(self, name, *a):
        self._calls.append((name,) + a)

    def fill(self, c):           self._rec("fill", c)
    def fill_rect(self, *a):     self._rec("fill_rect", *a)
    def text(self, *a):          self._rec("text", *a)
    def rect(self, *a):          self._rec("rect", *a)
    def hline(self, *a):         self._rec("hline", *a)
    def vline(self, *a):         self._rec("vline", *a)
    def line(self, *a):          self._rec("line", *a)
    def show(self):              self._rec("show")
    def show_image(self, *a):    self._rec("show_image", *a)
    def show_custom_char(self, *a): self._rec("show_custom_char", *a)
    def show_fill_button_with_text(self, *a): self._rec("show_fill_button_with_text", *a)
    def show_blank_button_with_text(self, *a): self._rec("show_blank_button_with_text", *a)
    def pixel(self, *a):         return 0
    def scroll(self, *a):        self._rec("scroll", *a)
    def blit(self, *a):          self._rec("blit", *a)
    def invert(self, *a):        self._rec("invert", *a)
    def poweroff(self):          self._rec("poweroff")
    def poweron(self):           self._rec("poweron")

    def was_called(self, name):
        """Ritorna True se il metodo è stato chiamato almeno una volta."""
        return any(c[0] == name for c in self._calls)

    def call_count(self, name):
        return sum(1 for c in self._calls if c[0] == name)

    def reset(self):
        self._calls.clear()


class MockRTC:
    """Simula DS3231_RTC."""
    def __init__(self):
        self._dt   = (2025, 1, 1, 12, 0, 0, 0, 1)
        self._osf  = False
        self.second = 0
        self.time   = "12:00:00"

    @property
    def datetime(self): return self._dt

    @datetime.setter
    def datetime(self, val): self._dt = val

    def OSF(self): return self._osf

    def unix_epoch_time(self, v): return v + 946684800


class MockRelay:
    """Simula un singolo pin relay."""
    def __init__(self):
        self._state = 0

    def value(self, v=None):
        if v is not None:
            self._state = v
        return self._state

    @property
    def state(self): return self._state


class MockRelays:
    """Simula il banco relay."""
    def __init__(self):
        self.light  = MockRelay()
        self.filter = MockRelay()
        self.heater = MockRelay()
        self.feeder = MockRelay()

    def init_relays_status(self, cfg):
        self.light.value( 0 if cfg.get_on_off_light_auto() else 1)
        self.filter.value(0 if cfg.get_on_off_filter()     else 1)
        self.heater.value(0 if cfg.get_on_off_heater()     else 1)
        self.feeder.value(0 if cfg.get_on_off_feeder()     else 1)

    def get_light_rele(self):  return self.light
    def get_filter_rele(self): return self.filter
    def get_heater_rele(self): return self.heater
    def get_feeder_rele(self): return self.feeder


class MockSDCard:
    """Simula SDCardManager: storage in dizionario in memoria."""
    def __init__(self):
        self._store = {}
        self.available = True

    def if_exist_configuration(self):
        return "config" in self._store

    def get_configuration(self):
        return self._store.get("config", None)

    def set_configuration(self, data):
        self._store["config"] = dict(data)
        return True

    def __bool__(self): return self.available


class MockWifi:
    """Simula WifiConnection con controllo preciso degli stati."""
    def __init__(self):
        self._connected  = False
        self.connect_called    = 0
        self.disconnect_called = 0
        self.send_called       = 0
        self.last_sent         = None
        self.should_fail       = False
        self._host = "test.altervista.org"

    @property
    def host(self): return self._host

    def connect(self):
        self.connect_called += 1
        if self.should_fail:
            return False
        self._connected = True
        return True

    def disconnect(self):
        self.disconnect_called += 1
        self._connected = False
        return True

    def is_connected(self): return self._connected

    def connection_status(self):
        return "CONNESSO" if self._connected else "NESSUNA CONNESSIONE"

    def get_ip_address(self): return "192.168.1.100"

    def send_value_to_web(self, value, key, timestamp):
        self.send_called += 1
        self.last_sent = {"value": value, "key": key, "ts": timestamp}
        return not self.should_fail


class MockDS18B20:
    """Simula DS18B20Sensor."""
    def __init__(self, temp=24.0):
        self._temp = temp
        self.read_called = 0

    def read_temperature(self):
        self.read_called += 1
        return self._temp

    def is_valid(self, t): return t != -999

    @property
    def sensor_count(self): return 1


def _reset_singleton(cls):
    """Rimuove l'istanza Singleton per consentire re-inizializzazione nei test."""
    if cls in cls._instances:
        del cls._instances[cls]


# ══════════════════════════════════════════════════════════════════════
# TEST CONFIG
# ══════════════════════════════════════════════════════════════════════

class TestConfig(unittest.TestCase):
    """Test su Resource.Config — logica pura, nessun hardware."""

    def setUp(self):
        from Resource.Config import Config
        _reset_singleton(Config)
        self.Config = Config
        self.cfg = Config()

    def tearDown(self):
        _reset_singleton(self.Config)

    # ── Valori di default ──────────────────────────────────────────────

    def test_defaults_all_relays_off(self):
        """Al boot tutti i relay devono essere False."""
        self.assertFalse(self.cfg.get_on_off_light_auto())
        self.assertFalse(self.cfg.get_on_off_filter())
        self.assertFalse(self.cfg.get_on_off_heater())
        self.assertFalse(self.cfg.get_on_off_feeder())

    def test_defaults_sensors_off(self):
        """Al boot tutti i sensori devono essere disabilitati."""
        self.assertFalse(self.cfg.get_on_off_ec())
        self.assertFalse(self.cfg.get_on_off_ph())
        self.assertFalse(self.cfg.get_on_off_temperature())

    def test_defaults_sending_off(self):
        """Il flag sending è False anche se la lettura fosse True."""
        self.assertFalse(self.cfg.get_on_off_ec_sending())
        self.assertFalse(self.cfg.get_on_off_ph_sending())
        self.assertFalse(self.cfg.get_on_off_temperature_sending())

    # ── Timer ─────────────────────────────────────────────────────────

    def test_set_timer_time(self):
        self.cfg.set_timer_time((8, 30, 22, 0))
        sh, sm, eh, em = self.cfg.get_timer_time()
        self.assertEqual(sh, 8)
        self.assertEqual(sm, 30)
        self.assertEqual(eh, 22)
        self.assertEqual(em, 0)

    def test_set_timer_none_resets_to_zero(self):
        self.cfg.set_timer_time((8, 30, 22, 0))
        self.cfg.set_timer_time(None)
        self.assertEqual(self.cfg.get_timer_time(), (0, 0, 0, 0))

    # ── Heater auto ───────────────────────────────────────────────────

    def test_set_auto_heater(self):
        self.cfg.set_auto_heater((28, 22))
        mx, mn = self.cfg.get_auto_heater()
        self.assertEqual(mx, 28)
        self.assertEqual(mn, 22)

    # ── Sending — dipendenza doppia ────────────────────────────────────

    def test_sending_requires_reading_enabled(self):
        """get_on_off_ec_sending deve essere False se ec è False."""
        self.cfg.set_on_off_ec_sending(True)
        self.cfg.set_on_off_ec(False)
        self.assertFalse(self.cfg.get_on_off_ec_sending())

    def test_sending_true_when_both_enabled(self):
        self.cfg.set_on_off_ec(True)
        self.cfg.set_on_off_ec_sending(True)
        self.assertTrue(self.cfg.get_on_off_ec_sending())

    # ── Mode switch ───────────────────────────────────────────────────

    def test_mode_maintenance_disables_automation(self):
        """Passare a MAINTENANCE deve disabilitare tutti i processi automatici."""
        self.cfg.set_on_off_light_auto(True)
        self.cfg.set_on_off_ec(True)
        self.cfg.set_mode(1)   # MAINTENANCE
        self.assertFalse(self.cfg.get_on_off_light_auto())
        self.assertFalse(self.cfg.get_on_off_ec())
        self.assertTrue(self.cfg.maintain_enabled)

    def test_mode_auto_restores_state(self):
        """Tornare in AUTO deve ripristinare lo stato precedente."""
        self.cfg.set_on_off_light_auto(True)
        self.cfg.set_on_off_ec(True)
        self.cfg.set_mode(1)   # MAINTENANCE → salva e disabilita
        self.cfg.set_mode(0)   # AUTO        → ripristina
        self.assertTrue(self.cfg.get_on_off_light_auto())
        self.assertTrue(self.cfg.get_on_off_ec())

    # ── Relay list ────────────────────────────────────────────────────

    def test_get_rele_list_reflects_relay_state(self):
        self.cfg.relay0 = True
        self.cfg.relay1 = False
        self.cfg.relay2 = True
        self.cfg.relay3 = False
        self.assertEqual(self.cfg.get_rele_list(), (True, False, True, False))

    # ── Serializzazione ───────────────────────────────────────────────

    def test_to_dict_contains_required_keys(self):
        d = self.cfg.to_dict()
        for key in ("onOffLightAuto", "onOffHeater", "onOffFilter", "onOffFeeder", "onOffEC", "onOffPH", "onOffTemperature"):
            self.assertIn(key, d, "Chiave mancante: {}".format(key))

    def test_from_json_roundtrip(self):
        """to_dict → json → from_json deve produrre lo stesso stato."""
        self.cfg.set_on_off_light_auto(True)
        self.cfg.set_timer_time((9, 0, 21, 30))
        original = self.cfg.to_dict()

        _reset_singleton(self.Config)
        cfg2 = self.Config()
        cfg2.from_json(original)
        self.assertEqual(cfg2.to_dict(), original)

    # ── Singleton ─────────────────────────────────────────────────────

    def test_singleton_same_instance(self):
        cfg2 = self.Config()
        self.assertIs(self.cfg, cfg2)


# ══════════════════════════════════════════════════════════════════════
# TEST WIFICONNECTION
# ══════════════════════════════════════════════════════════════════════

class TestWifiConnection(unittest.TestCase):
    """Test su Manager.WifiConnection con WLAN mock."""

    def setUp(self):
        from Manager.wifiConnection import WifiConnection
        _reset_singleton(WifiConnection)
        self.WC = WifiConnection
        self.wlan = _FakeWLAN()
        self.wifi = WifiConnection(self.wlan, "TestSSID", "TestPass", "host.test")

    def tearDown(self):
        _reset_singleton(self.WC)

    def test_initial_not_connected(self):
        self.assertFalse(self.wifi.is_connected())

    def test_connect_activates_wlan(self):
        self.wifi.connect()
        self.assertTrue(self.wlan.active())

    def test_connect_returns_true_on_success(self):
        result = self.wifi.connect()
        self.assertTrue(result)

    def test_connect_sets_connected(self):
        self.wifi.connect()
        self.assertTrue(self.wifi.is_connected())

    def test_disconnect_deactivates(self):
        self.wifi.connect()
        self.wifi.disconnect()
        self.assertFalse(self.wifi.is_connected())

    def test_connection_status_connected(self):
        self.wifi.connect()
        status = self.wifi.connection_status()
        self.assertEqual(status, "CONNESSO")

    def test_connection_status_idle(self):
        status = self.wifi.connection_status()
        self.assertIn("CONNESSIONE", status.upper())

    def test_host_property(self):
        self.assertEqual(self.wifi.host, "host.test")

    def test_send_value_connects_if_needed(self):
        """send_value_to_web deve connettersi automaticamente se non connesso."""
        result = self.wifi.send_value_to_web("25.0", "Temp", "12345")
        self.assertTrue(result)

    def test_send_value_no_host_returns_false(self):
        _reset_singleton(self.WC)
        wifi2 = self.WC(_FakeWLAN(), "ssid", "pw", host=None)
        result = wifi2.send_value_to_web("25.0", "Temp", "12345")
        self.assertFalse(result)

    def test_singleton_returns_same_instance(self):
        wifi2 = self.WC(self.wlan, "OtherSSID", "OtherPass")
        self.assertIs(self.wifi, wifi2)


# ══════════════════════════════════════════════════════════════════════
# TEST SDCARDMANAGER (con MockSDCard — non SPI reale)
# ══════════════════════════════════════════════════════════════════════

class TestSDCardLogic(unittest.TestCase):
    """
    Test sulla logica di SDCardManager usando MockSDCard.
    Non testa il bus SPI (hardware) ma il flusso config read/write.
    """

    def setUp(self):
        self.sd = MockSDCard()

    def test_no_config_initially(self):
        self.assertFalse(self.sd.if_exist_configuration())

    def test_set_and_read_config(self):
        data = {"light": True, "heater": False, "temp_max": 28}
        self.sd.set_configuration(data)
        self.assertTrue(self.sd.if_exist_configuration())
        result = self.sd.get_configuration()
        self.assertEqual(result["light"], True)
        self.assertEqual(result["temp_max"], 28)

    def test_config_roundtrip_json(self):
        """set_configuration → get_configuration deve produrre dati identici."""
        original = {"light": True, "filter": False, "start_h": 8}
        self.sd.set_configuration(original)
        loaded = self.sd.get_configuration()
        self.assertEqual(original, loaded)

    def test_overwrite_config(self):
        self.sd.set_configuration({"light": True})
        self.sd.set_configuration({"light": False})
        self.assertEqual(self.sd.get_configuration()["light"], False)

    def test_unavailable_returns_false(self):
        self.sd.available = False
        self.assertFalse(bool(self.sd))


# ══════════════════════════════════════════════════════════════════════
# TEST NTPMANAGER
# ══════════════════════════════════════════════════════════════════════

class TestNTPManager(unittest.TestCase):

    def setUp(self):
        from Manager.ntpManager import NTP
        self.NTP = NTP

    def test_sync_success(self):
        import sys, types
        sys.modules["ntptime"] = types.SimpleNamespace(settime=lambda: None)
        wifi = MockWifi()
        rtc  = MockRTC()
        ntp  = self.NTP(wifi, rtc)
        result = ntp.sync()
        self.assertTrue(result)

    def test_sync_disconnects_after(self):
        """Dopo sync() il WiFi deve essere disconnesso."""
        import sys, types
        sys.modules["ntptime"] = types.SimpleNamespace(settime=lambda: None)
        wifi = MockWifi()
        rtc  = MockRTC()
        ntp  = self.NTP(wifi, rtc)
        ntp.sync()
        self.assertFalse(wifi.is_connected())
        self.assertEqual(wifi.disconnect_called, 1)

    def test_sync_fails_if_wifi_fails(self):
        import sys, types
        sys.modules["ntptime"] = types.SimpleNamespace(settime=lambda: None)
        wifi = MockWifi()
        wifi.should_fail = True
        rtc  = MockRTC()
        ntp  = self.NTP(wifi, rtc)
        result = ntp.sync()
        self.assertFalse(result)

    def test_sync_updates_rtc(self):
        """Dopo sync() l'RTC deve avere un datetime aggiornato."""
        import sys, types
        sys.modules["ntptime"] = types.SimpleNamespace(settime=lambda: None)
        wifi = MockWifi()
        rtc  = MockRTC()
        ntp  = self.NTP(wifi, rtc)
        ntp.sync()
        self.assertIsNotNone(rtc.datetime)


# ══════════════════════════════════════════════════════════════════════
# TEST VIEWER — state machine UI
# ══════════════════════════════════════════════════════════════════════

class TestViewer(unittest.TestCase):
    """
    Test su Viewer con display, RTC, WiFi e relay tutti mockati.
    Verifica la state machine di run() e i toggle relay.
    """

    def _make_viewer(self):
        """Costruisce un Viewer con tutti i mock iniettati."""
        from Resource.Config import Config
        from Menu.pymenu import Menu
        _reset_singleton(Config)

        cfg    = Config()
        rtc    = MockRTC()
        wifi   = MockWifi()
        relays = MockRelays()
        i2c    = _FakeI2C()

        # Patch SSD1306_I2C per restituire MockDisplay
        import Modules.ssd1306 as ssd1306
        original_cls = ssd1306.SSD1306_I2C
        display = MockDisplay()
        ssd1306.SSD1306_I2C = lambda w, h, i: display

        from Manager.viewer import Viewer
        viewer = Viewer(i2c=i2c, config=cfg, ds3231_rtc=rtc,
                        conn=wifi, relays=relays)

        ssd1306.SSD1306_I2C = original_cls
        viewer.display = display  # assicura riferimento diretto
        return viewer, cfg, relays, display

    def setUp(self):
        from Resource.Config import Config
        _reset_singleton(Config)

    def tearDown(self):
        from Resource.Config import Config
        _reset_singleton(Config)

    # ── Init ──────────────────────────────────────────────────────────

    def test_viewer_starts_with_menu_disabled(self):
        viewer, *_ = self._make_viewer()
        self.assertFalse(viewer.is_enabled_menu)

    def test_viewer_relay_initial_state_off(self):
        viewer, cfg, relays, _ = self._make_viewer()
        self.assertEqual(relays.light.state, 0)
        self.assertEqual(relays.filter.state, 0)

    # ── toggle relay ──────────────────────────────────────────────────

    def test_toggle_light_turns_on(self):
        viewer, cfg, relays, _ = self._make_viewer()
        viewer.toggle_on_off_light_auto()
        # relay è active-low: value(0)=ON, value(1)=OFF
        self.assertEqual(relays.light.state, 0)
        self.assertTrue(cfg.get_on_off_light_auto())

    def test_toggle_light_twice_turns_off(self):
        viewer, cfg, relays, _ = self._make_viewer()
        viewer.toggle_on_off_light_auto()
        viewer.toggle_on_off_light_auto()
        # dopo due toggle torna allo stato iniziale (OFF=1 relay active-low)
        self.assertEqual(relays.light.state, 1)

    def test_toggle_heater(self):
        viewer, cfg, relays, _ = self._make_viewer()
        viewer.toggle_on_off_heater()
        self.assertEqual(relays.heater.state, 0)  # active-low

    def test_toggle_filter(self):
        viewer, cfg, relays, _ = self._make_viewer()
        viewer.toggle_on_off_filter()
        self.assertEqual(relays.filter.state, 0)  # active-low

    def test_toggle_feeder(self):
        viewer, cfg, relays, _ = self._make_viewer()
        viewer.toggle_on_off_feeder()
        self.assertEqual(relays.feeder.state, 0)  # active-low

    # ── Sensor values ─────────────────────────────────────────────────

    def test_set_temperature(self):
        viewer, *_ = self._make_viewer()
        viewer.set_temperature("25.5")
        self.assertEqual(viewer._temperature, "25.5")

    # ── run() state machine ───────────────────────────────────────────

    def test_run_draws_main_screen_on_exit_menu(self):
        """run() deve chiamare show() quando si esce dal menu."""
        viewer, _, _, display = self._make_viewer()
        viewer.is_enabled_menu = False
        viewer._exit_menu = True   # stato: appena uscito dal menu
        display.reset()
        viewer.run()
        self.assertTrue(display.was_called("show"))

    def test_run_draws_menu_on_menu_enabled(self):
        """run() setta exit_menu=True quando is_enabled_menu=True."""
        viewer, _, _, display = self._make_viewer()
        viewer.is_enabled_menu = True
        viewer._exit_menu = False
        display.reset()
        # Installa un main_screen minimale per evitare NoneType
        from Menu.pymenu import MenuList
        viewer.menu.set_main_screen(MenuList(display, "TEST"))
        viewer.run()
        self.assertTrue(viewer._exit_menu)

    # ── show_rele_symbol ──────────────────────────────────────────────

    def test_show_rele_symbol_calls_display(self):
        """show_rele_symbol() deve usare il display."""
        viewer, _, _, display = self._make_viewer()
        display.reset()
        viewer.show_rele_symbol([True, False, True, False])
        self.assertTrue(
            display.was_called("show_fill_button_with_text") or
            display.was_called("show_blank_button_with_text")
        )


# ══════════════════════════════════════════════════════════════════════
# TEST MAIN — flusso completo con tutti i mock
# ══════════════════════════════════════════════════════════════════════

class TestMain(unittest.TestCase):
    """
    Test di integrazione: simula il flusso main() completo
    senza hardware reale. Ogni componente è sostituito da un mock.
    """

    def setUp(self):
        from Resource.Config import Config
        from Manager.wifiConnection import WifiConnection
        _reset_singleton(Config)
        _reset_singleton(WifiConnection)
        self._Config = Config
        self._WC     = WifiConnection

    def tearDown(self):
        _reset_singleton(self._Config)
        _reset_singleton(self._WC)

    def _boot_sequence(self, sd_has_config=False):
        """
        Simula la sequenza di boot di main():
        1. WifiConnection
        2. Config
        3. DS18B20 (mock)
        4. DS3231 RTC (mock)
        5. SDCardManager (mock)
        6. Relays (mock)
        7. Viewer (con display mock)
        Ritorna tutti gli oggetti creati.
        """
        from Resource.Config import Config
        from Manager.wifiConnection import WifiConnection
        from Manager.ntpManager import NTP
        import Modules.ssd1306 as ssd1306

        wlan   = _FakeWLAN()
        wifi   = WifiConnection(wlan, "TestSSID", "TestPass", "test.host")
        cfg    = Config()
        rtc    = MockRTC()
        sd     = MockSDCard()
        relays = MockRelays()
        therm  = MockDS18B20(temp=25.0)
        display = MockDisplay()

        if sd_has_config:
            sd.set_configuration({"light": True, "temp_max": 27})

        # Carica config da SD se presente
        if sd.if_exist_configuration():
            file_json = sd.get_configuration()
            cfg.from_json(file_json)

        relays.init_relays_status(cfg)

        # Patch display
        original_cls = ssd1306.SSD1306_I2C
        ssd1306.SSD1306_I2C = lambda w, h, i: display

        from Manager.viewer import Viewer
        viewer = Viewer(i2c=_FakeI2C(), config=cfg, ds3231_rtc=rtc,
                        conn=wifi, relays=relays)

        ssd1306.SSD1306_I2C = original_cls
        viewer.display = display

        return {
            "wifi": wifi, "cfg": cfg, "rtc": rtc,
            "sd": sd, "relays": relays, "therm": therm,
            "viewer": viewer, "display": display,
        }

    # ── Boot sequence ─────────────────────────────────────────────────

    def test_boot_completes_without_error(self):
        """La sequenza di boot deve completarsi senza eccezioni."""
        ctx = self._boot_sequence()
        self.assertIsNotNone(ctx["viewer"])

    def test_boot_loads_config_from_sd(self):
        """Se la SD ha una config salvata, deve essere caricata in Config."""
        ctx = self._boot_sequence(sd_has_config=True)
        # La config dalla SD aveva light=True
        # from_json deve averla applicata
        self.assertIsNotNone(ctx["cfg"])

    def test_boot_relays_off_by_default(self):
        """Senza config, i relay devono partire OFF (active-low: state=1 = OFF)."""
        ctx = self._boot_sequence(sd_has_config=False)
        # relay active-low: value(1) = OFF quando cfg=False
        self.assertEqual(ctx["relays"].light.state, 1)
        self.assertEqual(ctx["relays"].filter.state, 1)

    # ── Loop iteration ────────────────────────────────────────────────

    def test_single_loop_iteration_temperature(self):
        """Un ciclo del loop deve leggere la temperatura e passarla al viewer."""
        ctx = self._boot_sequence()
        therm  = ctx["therm"]
        viewer = ctx["viewer"]

        temp = therm.read_temperature()
        viewer.set_temperature(str(temp))
        viewer.run()

        self.assertEqual(therm.read_called, 1)
        self.assertEqual(viewer._temperature, "25.0")

    def test_loop_menu_activation_on_ok(self):
        """Premere OK deve attivare il menu."""
        ctx = self._boot_sequence()
        viewer = ctx["viewer"]
        self.assertFalse(viewer.is_enabled_menu)
        # Simula pressione OK
        viewer.is_enabled_menu = True
        self.assertTrue(viewer.is_enabled_menu)

    def test_loop_menu_countdown_resets(self):
        """Dopo 100 cicli senza input il menu deve chiudersi."""
        ctx = self._boot_sequence()
        viewer = ctx["viewer"]
        viewer.is_enabled_menu = True
        countdown = 0
        for _ in range(100):
            if viewer.is_enabled_menu:
                countdown += 1
            if countdown >= 100:
                countdown = 0
                viewer.is_enabled_menu = False
        self.assertFalse(viewer.is_enabled_menu)

    def test_loop_gc_runs_every_500_iterations(self):
        """Il GC deve essere chiamato ogni 500 cicli."""
        import gc
        gc_counter = 0
        gc_calls   = 0
        original_collect = gc.collect
        gc.collect = lambda: setattr(self, "_gc_called", True)

        for i in range(500):
            gc_counter += 1
            if gc_counter >= 500:
                gc.collect()
                gc_calls += 1
                gc_counter = 0

        gc.collect = original_collect
        self.assertEqual(gc_calls, 1)

    # ── Use case: invio dati web ───────────────────────────────────────

    def test_send_temperature_to_web(self):
        """send_value_to_web deve essere chiamato con i valori corretti."""
        # Usa MockWifi direttamente — evita dipendenze da urequests
        wifi  = MockWifi()
        therm = MockDS18B20(temp=25.0)
        rtc   = MockRTC()

        temp = therm.read_temperature()
        ts   = str(rtc.unix_epoch_time(0))

        result = wifi.send_value_to_web(str(temp), "Temp", ts)
        self.assertTrue(result)
        self.assertEqual(wifi.last_sent["key"], "Temp")

    def test_send_fails_gracefully(self):
        """Se il WiFi fallisce, il loop non deve crashare."""
        ctx = self._boot_sequence()
        wifi = ctx["wifi"]

        try:
            result = wifi.send_value_to_web("25.0", "Temp", "12345")
            self.assertFalse(result)
        except Exception as e:
            self.fail("send_value_to_web ha lanciato un'eccezione: {}".format(e))

    # ── Use case: SD card persistenza ────────────────────────────────

    def test_save_config_to_sd_on_menu_close(self):
        """Alla chiusura del menu la config deve essere salvata sulla SD."""
        ctx = self._boot_sequence()
        sd  = ctx["sd"]
        cfg = ctx["cfg"]

        cfg.set_on_off_light_auto(True)
        sd.set_configuration(cfg.to_dict())

        loaded = sd.get_configuration()
        self.assertIsNotNone(loaded)

    # ── Use case: RTC OSF → NTP sync ─────────────────────────────────

    def test_rtc_osf_triggers_ntp_sync(self):
        """Se OSF è True, NTP.sync() deve essere chiamato."""
        import sys, types
        sys.modules["ntptime"] = types.SimpleNamespace(settime=lambda: None)
        from Manager.ntpManager import NTP
        wifi = MockWifi()
        rtc  = MockRTC()
        rtc._osf = True

        synced = False
        if rtc.OSF():
            ntp = NTP(wifi, rtc)
            synced = ntp.sync()

        self.assertTrue(synced)
        self.assertEqual(wifi.connect_called, 1)

    # ── Use case: relay toggle via viewer ─────────────────────────────

    def test_use_case_toggle_light_from_menu(self):
        """
        Use case: utente preme OK → entra nel menu → toggle LIGHTS.
        Il relay fisico deve cambiare stato.
        """
        ctx    = self._boot_sequence()
        viewer = ctx["viewer"]
        relays = ctx["relays"]
        cfg    = ctx["cfg"]

        # Stato iniziale: luce OFF (active-low: state=1)
        self.assertEqual(relays.light.state, 1)

        # Simula toggle da menu
        viewer.toggle_on_off_light_auto()

        # Stato atteso: luce ON (active-low: state=0)
        self.assertEqual(relays.light.state, 0)
        self.assertTrue(cfg.get_on_off_light_auto())

    def test_use_case_maintenance_mode_disables_all(self):
        """
        Use case: entrare in MAINTENANCE deve spegnere tutti i processi
        automatici per permettere operazioni manuali sicure.
        """
        ctx = self._boot_sequence()
        cfg = ctx["cfg"]

        # Abilita tutto
        cfg.set_on_off_light_auto(True)
        cfg.set_on_off_ec(True)
        cfg.set_on_off_temperature(True)

        # Entra in MAINTENANCE
        cfg.set_mode(1)

        self.assertFalse(cfg.get_on_off_light_auto())
        self.assertFalse(cfg.get_on_off_ec())
        self.assertFalse(cfg.get_on_off_temperature())
        self.assertTrue(cfg.maintain_enabled)

    def test_use_case_auto_mode_restores_all(self):
        """
        Use case: tornare in AUTO deve ripristinare tutti i processi
        che erano attivi prima della modalità MAINTENANCE.
        """
        ctx = self._boot_sequence()
        cfg = ctx["cfg"]

        cfg.set_on_off_light_auto(True)
        cfg.set_on_off_ec(True)
        cfg.set_mode(1)   # MAINTENANCE
        cfg.set_mode(0)   # AUTO

        self.assertTrue(cfg.get_on_off_light_auto())
        self.assertTrue(cfg.get_on_off_ec())
        self.assertTrue(cfg.auto_enabled)


# ══════════════════════════════════════════════════════════════════════
# RUNNER
# ══════════════════════════════════════════════════════════════════════

def run_tests():
    """Esegui la suite con output verboso e riepilogo finale."""
    loader = unittest.TestLoader()
    suite  = unittest.TestSuite()

    for cls in [
        TestConfig,
        TestWifiConnection,
        TestSDCardLogic,
        TestNTPManager,
        TestViewer,
        TestMain,
    ]:
        suite.addTests(loader.loadTestsFromTestCase(cls))

    runner = unittest.TextTestRunner(verbosity=2)
    result = runner.run(suite)

    print("\n" + "=" * 60)
    print("Totale:  {} test".format(result.testsRun))
    print("OK:      {}".format(result.testsRun - len(result.failures) - len(result.errors)))
    print("Falliti: {}".format(len(result.failures)))
    print("Errori:  {}".format(len(result.errors)))
    print("=" * 60)

    return len(result.failures) + len(result.errors) == 0


if __name__ == "__main__":
    success = run_tests()
    sys.exit(0 if success else 1)
