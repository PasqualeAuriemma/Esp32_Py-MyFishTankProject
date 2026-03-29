# main.py
import gc
gc.collect()

from machine import Pin, I2C, WDT, lightsleep  # type: ignore[import]
from Resource.Config        import Config
from Resource.board_pins    import BoardPins
from Modules.keyboard       import Keyboard
from Modules.ds3231         import DS3231_RTC
from Modules.relays         import Relays
from Manager.sdCardManager  import SDCardManager
from Manager.wifiConnection import WifiConnection
from secrets import WIFI_SSID, WIFI_PASSWORD

SERVER_HOST = "myfishtank.altervista.org"
gc.collect()


def main():
    wifi = WifiConnection(WLAN_INSTANCE, WIFI_SSID, WIFI_PASSWORD, SERVER_HOST)
    gc.collect()

    # Watchdog: resetta l'ESP32 se il loop non risponde entro 30 secondi.
    # Critico per la sicurezza: impedisce che riscaldatore/filtro rimangano
    # bloccati ON in caso di eccezione o hang dell'interprete.
    wdt        = WDT(timeout=30000)
    cfg        = Config()
    board      = Keyboard(BoardPins.KEYPAD_ANALOG)
    countdown  = 0
    gc_counter = 0

    print("[main] Initializing I2C...")
    i2c = I2C(0, scl=Pin(BoardPins.I2C_SCL),
                 sda=Pin(BoardPins.I2C_SDA), freq=400_000)

    print("[main] Initializing DS3231 RTC...")
    rtc = DS3231_RTC(i2c)

    if rtc.OSF():
        print("[main] RTC oscillator stopped - sync NTP...")
        from Manager.ntpManager import NTP
        NTP(wifi, rtc).sync()
        gc.collect()

    sdm = SDCardManager(
        sck_pin  = Pin(BoardPins.SCK_SD),
        mosi_pin = Pin(BoardPins.MOSI_SD),
        miso_pin = Pin(BoardPins.MISO_SD),
        sd_pin   = Pin(BoardPins.CS_SD, Pin.OUT),
    )

    relays = Relays()

    from Manager.viewer import Viewer
    viewer = Viewer(i2c=i2c, config=cfg, ds3231_rtc=rtc, conn=wifi, relays=relays)
    gc.collect()
    print("[main] Heap post-Viewer: {} bytes".format(gc.mem_free()))

    while True:
        try:
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
                        from Manager.menuBuilder import build_menu
                        build_menu(viewer, cfg)
                        gc.collect()
                        print("[main] Menu built - heap: {} bytes".format(
                            gc.mem_free()))
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

            viewer.run()

            if viewer.is_enabled_menu:
                countdown += 1
            if countdown >= 100:
                countdown = 0
                viewer.is_enabled_menu = False

            gc_counter += 1
            if gc_counter >= 500:
                gc.collect()
                gc_counter = 0

            wdt.feed()
            lightsleep(200)

        except Exception as e:
            print("[main] errore loop:", e)
            gc.collect()


if __name__ == "__main__":
    main()
