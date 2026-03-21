# main.py — usa direttamente le variabili da builtins (già globali)
import gc

gc.collect()

# ── STEP 2: import pesanti — solo ora la heap è già "prenotata" ────────
# MicroPython Libraries
from machine import Pin, I2C, WDT , lightsleep # type: ignore[import]

from Resource.Config import Config
from Resource.board_pins import BoardPins
from Modules.keyboard import Keyboard
from Modules.ds3231 import DS3231_RTC
from Modules.relays import Relays
from Manager.sdCardManager import SDCardManager
from Manager.wifiConnection import WifiConnection
from secrets import WIFI_SSID, WIFI_PASSWORD

from Menu.pymenu import (
    MenuList,
    ToggleItem,
    MenuEnum,
    MenuMonitoringSensor,
    BackItem,
    MenuConfirm,
    MenuWifiInfo,
    MenuSetDateTime,
    MenuSetTimer,
    MenuHeaterManage,
)

# WiFi credentials

SERVER_HOST = "myfishtank.altervista.org"

gc.collect()


def _create_menu(viewer: "Viewer", cfg: "Config"):
    """Build and install the entire menu tree into ``viewer.menu``."""
    viewer.menu.set_main_screen(
        MenuList(viewer.display, "MENU")
        .add(
            MenuEnum(
                viewer.display,
                "MODE",
                cfg.mode_list,
                (cfg.set_mode),
            )
        )
        .add(
            MenuList(viewer.display, "RELAYS")
            .add(
                ToggleItem(
                    "LIGHTS",
                    (cfg.get_on_off_light_auto),
                    (viewer.toggle_on_off_light_auto),
                    ("ON", "OFF"),
                )
            )
            .add(
                ToggleItem(
                    "FILTER",
                    (cfg.get_on_off_filter),
                    (viewer.toggle_on_off_filter),
                    ("ON", "OFF"),
                )
            )
            .add(
                ToggleItem(
                    "HEATER",
                    (cfg.get_on_off_heater),
                    (viewer.toggle_on_off_heater),
                    ("ON", "OFF"),
                )
            )
            .add(
                ToggleItem(
                    "FEEDER",
                    (cfg.get_on_off_feeder),
                    (viewer.toggle_on_off_feeder),
                    ("ON", "OFF"),
                )
            )
            .add(BackItem())
        )
        .add(
            MenuList(viewer.display, "SENSORS")
            .add(
                MenuList(viewer.display, "EC")
                .add(
                    ToggleItem(
                        "ACTIVATION",
                        (cfg.get_on_off_ec),
                        (viewer.toggle_on_off_ec),
                    )
                )
                .add(
                    MenuMonitoringSensor(
                        viewer.display,
                        "MONITORING",
                        visible=(cfg.get_on_off_ec),
                    )
                )
                .add(
                    ToggleItem(
                        "WEB SERVER",
                        (cfg.get_on_off_ec_sending),
                        (viewer.toggle_on_off_ec_sending),
                        visible=(cfg.get_on_off_ec),
                    )
                )
                .add(
                    MenuEnum(
                        viewer.display,
                        "WEB RATE",
                        cfg.freq,
                        cfg.set_freq_update_web_ec,
                        visible=(cfg.get_on_off_ec_sending),
                    )
                )
                .add(
                    MenuConfirm(
                        viewer.display,
                        "SEND TO WEB",
                        ("-> SEND", "<- BACK"),
                        viewer._send_ec,
                        visible=(cfg.get_on_off_ec_sending),
                    )
                )
                .add(BackItem())
            )
            .add(
                MenuList(viewer.display, "PH")
                .add(
                    ToggleItem(
                        "ACTIVATION",
                        (cfg.get_on_off_ph),
                        (viewer.toggle_on_off_ph),
                    )
                )
                .add(
                    MenuMonitoringSensor(
                        viewer.display,
                        "MONITORING",
                        visible=(cfg.get_on_off_ph),
                    )
                )
                .add(
                    ToggleItem(
                        "WEB SERVER",
                        (cfg.get_on_off_ph_sending),
                        (viewer.toggle_on_off_ph_sending),
                        visible=(cfg.get_on_off_ph),
                    )
                )
                .add(
                    MenuEnum(
                        viewer.display,
                        "WEB RATE",
                        cfg.freq,
                        cfg.set_freq_update_web_ph,
                        visible=(cfg.get_on_off_ph_sending),
                    )
                )
                .add(
                    MenuConfirm(
                        viewer.display,
                        "SEND TO WEB",
                        ("-> SEND", "<- BACK"),
                        viewer._send_ph,
                        visible=(cfg.get_on_off_ph_sending),
                    )
                )
                .add(BackItem())
            )
            .add(
                MenuList(viewer.display, "THERMOMETER")
                .add(
                    ToggleItem(
                        "ACTIVATION",
                        (cfg.get_on_off_temperature),
                        (viewer.toggle_on_off_temperature),
                    )
                )
                .add(
                    ToggleItem(
                        "WEB SERVER",
                        (cfg.get_on_off_temperature_sending),
                        (viewer.toggle_on_off_temperature_sending),
                        visible=(cfg.get_on_off_temperature),
                    )
                )
                .add(
                    MenuEnum(
                        viewer.display,
                        "WEB RATE",
                        cfg.freq,
                        cfg.set_freq_update_web_temperature,
                        visible=(cfg.get_on_off_temperature_sending),
                    )
                )
                .add(
                    MenuConfirm(
                        viewer.display,
                        "SEND TO WEB",
                        ("-> SEND", "<- BACK"),
                        viewer.send_temperature,
                        visible=(cfg.get_on_off_temperature_sending),
                    )
                )
                .add(BackItem())
            )
            .add(BackItem())
        )
        .add(
            MenuList(viewer.display, "SETTINGS")
            .add(
                MenuList(viewer.display, "WIFI")
                .add(MenuWifiInfo(viewer.display, "INFO"))
                .add(
                    MenuConfirm(
                        viewer.display,
                        "CONNECTING",
                        ("-> YES", "<- NO"),
                        cfg.set_connection_action,
                    )
                )
                .add(BackItem())
            )
            .add(MenuSetDateTime(viewer.display, "DATE/TIME", print))
            .add(
                MenuSetTimer(
                    viewer.display,
                    "LIGHT TIMER",
                    cfg.get_timer_time(),
                    cfg.set_timer_time,
                )
            )
            .add(
                MenuList(viewer.display, "HEATER AUTO")
                .add(
                    ToggleItem(
                        "ACTIVATION",
                        (cfg.get_on_off_heater_auto),
                        (viewer.toggle_on_off_heater_auto),
                    )
                )
                .add(
                    MenuHeaterManage(
                        viewer.display,
                        "SETTING",
                        cfg.get_timer_time(),
                        cfg.set_auto_heater,
                        visible=(cfg.get_on_off_heater_auto),
                    )
                )
                .add(BackItem())
            )
            .add(
                MenuList(viewer.display, "FILTER AUTO")
                .add(
                    ToggleItem(
                        "ACTIVATION",
                        (cfg.get_on_off_filter_auto),
                        (viewer.toggle_on_off_filter_auto),
                    )
                )
                .add(
                    MenuEnum(
                        viewer.display,
                        "RATE",
                        cfg.freq,
                        cfg.set_freq_filter,
                        visible=(cfg.get_on_off_filter_auto),
                    )
                )
                .add(BackItem())
            )
            .add(
                MenuConfirm(
                    viewer.display,
                    "RECOVERY",
                    ("-> YES", "<- NO"),
                    cfg.set_on_off_recovery,
                )
            )
            .add(BackItem())
        )
        .add(BackItem())
    )


def main():
    """Main function to manage the WiFi connection in a loop."""
    wifi = WifiConnection(WLAN_INSTANCE, WIFI_SSID, WIFI_PASSWORD, SERVER_HOST)
    gc.collect()

    countdown_menu = 0
    _gc_counter = 0

    # Watchdog: resetta l'ESP32 se il loop non risponde entro 30 secondi.
    # Critico per la sicurezza: impedisce che riscaldatore/filtro rimangano
    # bloccati ON in caso di eccezione o hang dell'interprete.
    wdt = WDT(timeout=30000)
    # Configuration
    cfg = Config()
    # Analog Inputs for Menu Control
    board = Keyboard(BoardPins.KEYPAD_ANALOG)

    # I2C Bus for Display and RTC
    print("Initializing I2C...")
    # TODO vedere dove usa # DS3231 on 0x68 self.I2C_ADDR = 0x68  # DEC 104, HEX 0x68
    i2c = I2C(
        0, scl=Pin(BoardPins.I2C_SCL), sda=Pin(BoardPins.I2C_SDA), freq=400_000
    )  # 400000

    # --- Real-Time Clock (RTC) ---
    print("Initializing DS3231 RTC...")
    rtc = DS3231_RTC(i2c)

    sdm = SDCardManager(
        sck_pin=Pin(BoardPins.SCK_SD),
        mosi_pin=Pin(BoardPins.MOSI_SD),
        miso_pin=Pin(BoardPins.MISO_SD),
        sd_pin=Pin(BoardPins.CS_SD, Pin.OUT),
    )

    relays = Relays()

    from Manager.viewer import Viewer

    viewer = Viewer(
        i2c=i2c, config=cfg, ds3231_rtc=rtc, conn=wifi, sd_manager=sdm, relays=relays
    )

    while True:
        try:
            key = board.get_digit_keyboard()

            if key == board.right_keypad_value:
                if viewer.is_enabled_menu:
                    countdown_menu = 0
                    viewer.menu.move(-1)
            elif key == board.left_keypad_value:
                if viewer.is_enabled_menu:
                    countdown_menu = 0
                    viewer.menu.move(1)
            elif key == board.ok_keypad_value:
                if not viewer.is_enabled_menu:
                    # Lazy load: build the menu on first use to save RAM at boot.
                    if viewer.menu.main_screen is None:
                        _create_menu(viewer, cfg)
                    viewer.is_enabled_menu = True
                else:
                    countdown_menu = 0
                    viewer.menu.click()
            elif key == board.down_keypad_value:
                if viewer.is_enabled_menu:
                    countdown_menu = 0
                    viewer.menu.shift(1)
            elif key == board.up_keypad_value:
                if viewer.is_enabled_menu:
                    countdown_menu = 0
                    viewer.menu.shift(-1)

            viewer.run()

            if viewer.is_enabled_menu:
                countdown_menu += 1

            if countdown_menu == 100:
                countdown_menu = 0
                viewer.is_enabled_menu = False

            _gc_counter += 1
            if _gc_counter >= 500:
                gc.collect()
                _gc_counter = 0

            wdt.feed()  # resetta il watchdog: conferma che il loop è vivo
            # time.sleep_ms(100)   # 20 Hz — reattivo per UI, riduce consumo CPU
            lightsleep(100)  # consumo ridotto ~80 mA a ~20 mA

        except Exception as e:
            # Logga l'errore senza uscire dal loop; il WDT farà reset
            # se la situazione è irrecuperabile (hang prolungato senza feed).
            print("[main] errore nel loop:", e)
            gc.collect()


if __name__ == "__main__":
    main()
