# main.py
import gc
gc.collect()

import _thread
import time

from machine import Pin, I2C, WDT, lightsleep  # type: ignore[import]
from Resource.Config        import Config
from Resource.board_pins    import BoardPins
from Modules.keyboard       import Keyboard
from Modules.ds3231         import DS3231_RTC
from Modules.relays         import Relays
from Manager.sdCardManager  import SDCardManager
from Manager.wifiConnection import WifiConnection
from secret import WIFI_SSID, WIFI_PASSWORD, IOT_TOKEN # type: ignore[import]
from Modules.ds18b20 import DS18B20
gc.collect()
from Modules.tds_sensor import TDSSensor
from Modules.ph_sensor import PHSensor

'''
Step 1 — midpoint (pH 7 buffer):
    offset = V_measured - slope * (buffer_pH - 7.0)

Step 2 — slope (pH 4 or pH 10 buffer):
    slope = (V_step2 - V_mid) / (buffer_pH - 7.0)

Conversion:
    pH = (V - offset) / S(T) + 7.0
'''

SERVER_HOST = "myfishtank.altervista.org"
gc.collect()


def sensor_loop(termometer, ec_sensor, ph_sensor, sensor_values, lock):
    while True:
        temperature = termometer.read_temperature()
        ec_sensor.set_temperature(temperature)
        ec = ec_sensor.read_ec()
        ph_sensor.set_temperature(temperature)
        ph = ph_sensor.read_ph()

        lock.acquire()
        sensor_values["temperature"] = temperature
        sensor_values["ec"] = ec
        sensor_values["ph"] = ph
        lock.release()

        time.sleep(0.1)


def wifi_sending_loop(wifi, cfg, rtc, sensor_values, lock):
    last_sent = {"Temp": 0, "Ec": 0, "PH": 0}
    
    # Aspetta un po' all'avvio affinché i sensori abbiano valori validi
    time.sleep(10)
    
    while True:
        try:
            # 1. Invio automatico periodico basato sulle frequenze configurate
            current_time = time.time()
            unix_ts = str(rtc.unix_epoch_time(current_time))
            
            sensors_config = [
                ("Temp", cfg.get_on_off_temperature_sending, cfg.get_freq_update_web_temperature, "temperature"),
                ("Ec", cfg.get_on_off_ec_sending, cfg.get_freq_update_web_ec, "ec"),
                ("PH", cfg.get_on_off_ph_sending, cfg.get_freq_update_web_ph, "ph")
            ]
            
            for key, get_sending_enabled, get_freq, value_key in sensors_config:
                if get_sending_enabled():
                    try:
                        freq_hours = int(get_freq())
                    except ValueError:
                        freq_hours = 8  # fallback di sicurezza
                    
                    freq_seconds = freq_hours * 3600
                    
                    if current_time - last_sent[key] >= freq_seconds:
                        lock.acquire()
                        val = sensor_values.get(value_key)
                        lock.release()
                        
                        if val is not None:
                            if key == "PH":
                                str_val = "{:.2f}".format(val)
                            elif key == "Ec":
                                str_val = str(int(val))
                            else:
                                str_val = "{:.1f}".format(val)
                                
                            # Accoda per l'invio asincrono
                            wifi.send_value_to_web(str_val, key, unix_ts)
                            last_sent[key] = current_time
            
            # 2. Gestione della coda WiFi (invio vero e proprio in background)
            while wifi.has_queued_items():
                item = wifi.pop_queue()
                if item:
                    val, key, ts = item
                    success = wifi._send_value_to_web_sync(val, key, ts)
                    if not success:
                        print("[wifi_sending_loop] Invio fallito, riaccodamento...")
                        wifi.send_value_to_web(val, key, ts)
                        time.sleep(10)  # Aspetta prima di ritentare
                        break
                    time.sleep(1)
            
            time.sleep(5)
            gc.collect()
            
        except Exception as e:
            print("[wifi_sending_loop] Errore:", e)
            time.sleep(10)


def main():
    wifi = WifiConnection(WLAN_INSTANCE, WIFI_SSID, WIFI_PASSWORD, IOT_TOKEN, SERVER_HOST)
    gc.collect()

    # Watchdog: resetta l'ESP32 se il loop non risponde entro 30 secondi.
    # Critico per la sicurezza: impedisce che riscaldatore/filtro rimangano
    # bloccati ON in caso di eccezione o hang dell'interprete.
    wdt        = WDT(timeout=30000)
    _cfg       = Config()
    board      = Keyboard(BoardPins.KEYPAD_PIN)
    countdown  = 0
    gc_counter = 0
    
    termometer = DS18B20(pin=BoardPins.DS18B20_PIN, resolution=12)

    print("[main] Initializing I2C...")
    i2c = I2C(0, scl=Pin(BoardPins.I2C_SCL_PIN),
                 sda=Pin(BoardPins.I2C_SDA_PIN), freq=400_000)

    print("[main] Initializing DS3231 RTC...")
    rtc = DS3231_RTC(i2c)

    if rtc.OSF():
        print("[main] RTC oscillator stopped - sync NTP...")
        from Manager.ntpManager import NTP
        NTP(wifi, rtc).sync()
        gc.collect()
    
    sdm = SDCardManager(
        sck_pin  = Pin(BoardPins.SCK_SD_PIN),
        mosi_pin = Pin(BoardPins.MOSI_SD_PIN),
        miso_pin = Pin(BoardPins.MISO_SD_PIN),
        sd_pin   = Pin(BoardPins.CS_SD_PIN, Pin.OUT),
    )
    
    if sdm:
        if sdm.if_exist_configuration():
            file_json = sdm.get_configuration()
            _cfg.from_json(file_json)

    gc.collect()
    '''
    # After calibration, save to SD / Config:
    offset, slope = ph_sensor.get_calibration()
    config.ph_offset = offset
    config.ph_slope  = slope
    '''
    ph_sensor = PHSensor(
        pin         = BoardPins.PH_ADC_PIN,
        switch_pin  = BoardPins.MOSFET_PH_PIN,
        vref        = 3.3,
        samples     = 10,
        offset      = _cfg.ph_offset,
        slope       = _cfg.ph_slope,
        temperature = 25.0,
    )
    gc.collect()
    
    ec_sensor = TDSSensor(
        pin        = BoardPins.EC_ADC_PIN,      # e.g. GPIO0
        switch_pin = BoardPins.MOSFET_EC_PIN,   # e.g. GPIO17
        vref       = 5.0,
        samples    = 30,
        temperature= 25.0,
        debug      = False,
    )
    
    relays = Relays()
    relays.init_relays_status(_cfg)
    gc.collect()

    sensor_values = {"temperature": None, "ec": None, "ph": None}
    sensor_lock = _thread.allocate_lock()
    _thread.start_new_thread(sensor_loop, (termometer, ec_sensor, ph_sensor, sensor_values, sensor_lock))
    _thread.start_new_thread(wifi_sending_loop, (wifi, _cfg, rtc, sensor_values, sensor_lock))

    from Manager.viewer import Viewer
    viewer = Viewer(i2c=i2c, config=_cfg, ds3231_rtc=rtc, conn=wifi, relays=relays)
    gc.collect()
    print("[main] Heap 2: {} bytes".format(gc.mem_free()))

    while True:
        try:
            wdt.feed()
            key = board.get_digit_keyboard()
     
            if key == board.up_keypad_value and viewer.is_enabled_menu:
                countdown = 0
                viewer.menu.move(-1)

            elif key == board.down_keypad_value and viewer.is_enabled_menu:
                countdown = 0
                viewer.menu.move(1)

            elif key == board.ok_keypad_value:
                if not viewer.is_enabled_menu:
                    # Lazy menu build: il menu viene costruito SOLO al primo
                    # click OK. menuBuilder importato lazy — non occupa RAM
                    # durante il boot normale.
                    if viewer.menu.main_screen is None:
                        from Menu.menuBuilder import build_menu
                        build_menu(viewer, _cfg)
                        gc.collect()
                    viewer.is_enabled_menu = True
                else:
                    countdown = 0
                    viewer.menu.click()

            elif key == board.right_keypad_value and viewer.is_enabled_menu:
                countdown = 0
                viewer.menu.shift(1)

            elif key == board.left_keypad_value and viewer.is_enabled_menu:
                countdown = 0
                viewer.menu.shift(-1)

            sensor_lock.acquire()
            temperature = sensor_values.get("temperature")
            ec = sensor_values.get("ec")
            ph = sensor_values.get("ph")
            sensor_lock.release()

            if ph is not None and ph_sensor.is_valid(ph):
                viewer.set_ph("{:.1f}".format(ph))
            
            if ec is not None and ec_sensor.is_valid(ec):
                viewer.set_ec(str(int(ec)))
            
            if temperature is not None and termometer.is_valid(temperature):
                viewer.set_temperature(str(temperature))

            viewer.run()
            
            wdt.feed()

            if viewer.is_enabled_menu:
                countdown += 1
            if countdown >= 100:
                countdown = 0
                viewer.is_enabled_menu = False
                sdm.set_configuration(_cfg.to_dict())
                gc.collect()
                
            gc_counter += 1
            if gc_counter >= 500:
                gc.collect()
                gc_counter = 0

            wdt.feed()
            lightsleep(250)

        except Exception as e:
            print("[main] Error: loop", e)
            gc.collect()


if __name__ == "__main__":
    main()
