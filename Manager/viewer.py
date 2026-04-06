"""
Viewer — high-level UI controller for PyTank (ESP32 / MicroPython).

Responsibilities
----------------
- Drives the SSD1306 OLED display (128×64) over I2C.
- Reads the DS3231 hardware RTC and updates the time shown on screen.
- Manages the relays (lights, filter, heater, feeder) in response to
  menu actions and automatic scheduling logic.
- Persists the configuration to the SD card on first boot and reloads it.
- Builds and owns the entire menu tree (defined in ``set_menu()``).
- Provides the ``toggle_*`` callbacks wired to each ToggleItem in the menu.
- Exposes ``run()``, called on every iteration of the main loop, to
  update the display state machine.
"""

from machine import I2C  # type: ignore[import]

import Modules.ssd1306 as ssd1306
from time import sleep, localtime, time
from Resource.Config import Config
from Manager.wifiConnection import WifiConnection
from Modules.ds3231 import DS3231_RTC
from Modules.relays import Relays
from Menu.pymenu import Menu

# Pre-build relay label strings: evita str(index+1) ad ogni ridisegno.
_RELAY_LABELS = ("1", "2", "3", "4")


class Viewer:
    """High-level UI/controller for the OLED, RTC, WiFi and relays on ESP32."""

    __slots__ = (
        # Hardware / bus references
        "_con",
        "_i2c",
        "_ds_rtc",
        # Display
        "display",
        "oled_width",
        "oled_height",
        "_degree_symbol",
        # Menu state
        "menu",
        "_is_enabled_menu",
        "_exit_menu",
        # Sensor / time strings shown on main screen
        "_time",
        "_last_second",
        "_temperature",
        "_ec",
        "_ph",
        # Shared singletons
        "_config",
        # Relay pins
        "_light_rele",
        "_filter_rele",
        "_heater_rele",
        "_feeder_rele",
    )

    def __init__(
        self,
        i2c: I2C,
        config: Config,
        ds3231_rtc: DS3231_RTC,
        conn: WifiConnection,
        relays: Relays,
        width: int = 128,
        height: int = 64,
    ):
        """
        Args:
            i2c: Pre-configured I2C bus shared by the OLED display and the DS3231 RTC.
            config: Shared ``Config`` singleton that holds all runtime settings
                (relay states, sensor flags, timers, …).
            ds3231_rtc: Hardware RTC module used to read/write the current date-time.
            conn: ``WifiConnection`` singleton used for NTP sync and web data upload.
            sd_manager: ``SDCardManager`` used to persist and reload configuration
                on the SD card across power cycles.
            relays: ``Relays`` named-tuple / object that exposes the four relay pins
                (light, filter, heater, feeder).
            width: OLED display width in pixels (default 128).
            height: OLED display height in pixels (default 64).
        """

        # WiFi connection manager used for NTP and web posting.
        self._con = conn

        # ESP32 I2C pin assignment (OLED + DS3231).
        # if i2c:
        self._i2c = i2c
        # else:
        #   self._i2c = I2C(0, scl=Pin(22), sda=Pin(21), freq=800000)

        self._ds_rtc = ds3231_rtc  # RTC instance
        from Icons.symbols import degree_symbol

        self._degree_symbol = degree_symbol
        # Start the thread
        # self.set_ntp()
        self.oled_width = width
        self.oled_height = height
        self._is_enabled_menu = False
        self._exit_menu = True
        self._time = "00:00:00"
        self._last_second = -1  # int cache: evita alloc stringa ogni ciclo in run()
        self._temperature = "0"
        self._ec = "0"
        self._ph = "0"

        self._config = config

        # --- Display initialisation ------------------------------------------
        # Build the SSD1306 driver and paint the initial relay-status strip
        # before the menu tree is constructed, so the screen is never blank.
        self.display = ssd1306.SSD1306_I2C(self.oled_width, self.oled_height, self._i2c)
        self.show_rele_symbol(self._config.get_rele_list())

        # --- Menu tree -------------------------------------------------------
        self.menu = Menu(self)

        # --- Relay pin references (from the Relays object) -------------------
        self._light_rele = relays.get_light_rele()
        self._filter_rele = relays.get_filter_rele()
        self._heater_rele = relays.get_heater_rele()
        self._feeder_rele = relays.get_feeder_rele()
        # self.init_screen()
        # self.display.poweroff()

    def set_ntp(self):
        """Synchronize RTC with NTP server and send a test EC sample to the web endpoint."""
        self._con.connect()
        print(self._con.connection_status())
        import ntptime  # type: ignore[import]

        ntptime.settime()
        # unix_epoch_time1 = str(self._ds_rtc.unix_epoch_time(time()))
        # print("Unix epoch time:", unix_epoch_time1)
        # Send a single "test" EC value to verify connectivity / endpoint.
        # self._con.send_value_to_web("999", "Ec", unix_epoch_time1)
        self._con.disconnect()
        print(self._con.connection_status())
        print(list(localtime()))
        self._ds_rtc.datetime = localtime()

    # TODO check the last line for each toggle
    def toggle_on_off_light_auto(self):
        """Toggle the light relay (relay 0) and sync the flag in Config.

        Called by the LIGHTS ToggleItem in the RELAYS menu.
        Writes the new state to both the Config singleton and the physical pin.
        """
        new_val = not self._config.get_on_off_light_auto()
        self._config.set_on_off_light_auto(new_val)
        self._config.relay0 = new_val
        self._light_rele.value(0 if new_val else 1)
        return self._config.get_on_off_light_auto()

    def toggle_on_off_filter(self):
        """Toggle the water-filter relay (relay 1) and sync the flag in Config."""
        new_val = not self._config.get_on_off_filter()
        self._config.set_on_off_filter(new_val)
        self._config.relay1 = new_val
        self._filter_rele.value(0 if new_val else 1)
        return self._config.get_on_off_filter()

    def toggle_on_off_heater(self):
        """Toggle the heater relay (relay 2) and sync the flag in Config."""
        new_val = not self._config.get_on_off_heater()
        self._config.set_on_off_heater(new_val)
        self._config.relay2 = new_val
        self._heater_rele.value(0 if new_val else 1)
        return self._config.get_on_off_heater()

    def toggle_on_off_feeder(self):
        """Toggle the fish-feeder relay (relay 3) and sync the flag in Config."""
        new_val = not self._config.get_on_off_feeder()
        self._config.set_on_off_feeder(new_val)
        self._config.relay3 = new_val
        self._feeder_rele.value(0 if new_val else 1)
        return self._config.get_on_off_feeder()

    def toggle_on_off_heater_auto(self):
        """Toggle the automatic heater control mode in Config (no direct relay output)."""
        new_val = not self._config.get_on_off_heater_auto()
        self._config.set_on_off_heater_auto(new_val)
        return self._config.get_on_off_heater_auto()

    def toggle_on_off_filter_auto(self):
        """Toggle the automatic filter scheduling mode in Config (no direct relay output)."""
        new_val = not self._config.get_on_off_filter_auto()
        self._config.set_on_off_filter_auto(new_val)
        return self._config.get_on_off_filter_auto()

    def toggle_on_off_temperature(self):
        """Toggle the temperature sensor activation flag in Config."""
        new_val = not self._config.get_on_off_temperature()
        self._config.set_on_off_temperature(new_val)
        return self._config.get_on_off_temperature()

    def toggle_on_off_ph(self):
        """Toggle the pH sensor activation flag in Config."""
        new_val = not self._config.get_on_off_ph()
        self._config.set_on_off_ph(new_val)
        return self._config.get_on_off_ph()

    def toggle_on_off_ec(self):
        """Toggle the EC (electrical conductivity) sensor activation flag in Config."""
        new_val = not self._config.get_on_off_ec()
        self._config.set_on_off_ec(new_val)
        return self._config.get_on_off_ec()

    def toggle_on_off_ec_sending(self):
        """Toggle the transmission of EC data to the remote web server."""
        new_val = not self._config.get_on_off_ec_sending()
        self._config.set_on_off_ec_sending(new_val)
        return self._config.get_on_off_ec_sending()

    def toggle_on_off_ph_sending(self):
        """Toggle the transmission of pH data to the remote web server."""
        new_val = not self._config.get_on_off_ph_sending()
        self._config.set_on_off_ph_sending(new_val)
        return self._config.get_on_off_ph_sending()

    def toggle_on_off_temperature_sending(self):
        """Toggle the transmission of temperature data to the remote web server."""
        new_val = not self._config.get_on_off_temperature_sending()
        self._config.set_on_off_temperature_sending(new_val)
        return self._config.get_on_off_temperature_sending()

    def _send_ec(self, value):
        """Send the current EC reading to the remote web endpoint.

        Called by the ``MenuConfirm`` callback when the user selects
        "SEND TO WEB" inside the EC sensor sub-menu.

        Args:
            value: True if the user confirmed the action; False if they
                cancelled (e.g. selected "<- BACK").  The request is only
                issued when ``value`` is True.
        """
        unix_epoch_time1 = str(self._ds_rtc.unix_epoch_time(time()))
        print("Unix epoch time:", unix_epoch_time1)
        if value:
            self._con.send_value_to_web(self._ec, "Ec", unix_epoch_time1)

    def _send_ph(self, value):
        """Send the current pH reading to the remote web endpoint.

        Called by the ``MenuConfirm`` callback when the user selects
        "SEND TO WEB" inside the PH sensor sub-menu.

        Args:
            value: True if the user confirmed the action; False if cancelled.
        """
        unix_epoch_time1 = str(self._ds_rtc.unix_epoch_time(time()))
        print("Unix epoch time:", unix_epoch_time1)
        if value:
            self._con.send_value_to_web(self._ph, "PH", unix_epoch_time1)

    def send_temperature(self, value):
        """Send the current temperature reading to the remote web endpoint.

        Called by the ``MenuConfirm`` callback when the user selects
        "SEND TO WEB" inside the THERMOMETER sensor sub-menu.

        Args:
            value: True if the user confirmed the action; False if cancelled.
        """
        unix_epoch_time1 = str(self._ds_rtc.unix_epoch_time(time()))
        if value:
            self._con.send_value_to_web(self._temperature, "Temp", unix_epoch_time1)

    @property
    def exit_menu(self):
        """Transition flag: True immediately after the menu closes to force a screen reset."""
        return self._exit_menu

    @exit_menu.setter
    def exit_menu(self, value):
        self._exit_menu = value

    @property
    def time(self):
        """Current time string displayed on the main screen (format HH:MM:SS)."""
        return self._time

    def get_temperature(self):
        """Last temperature reading as a string (°C)."""
        return self._temperature

    def get_ec(self):
        """Last EC (electrical conductivity) reading as a string (µS/cm)."""
        return self._ec

    def get_ph(self):
        """Last pH reading as a string."""
        return self._ph

    @time.setter
    def time(self, value):
        """Set the displayed time string (HH:MM:SS)."""
        self._time = value

    def set_temperature(self, value):
        """Set the last temperature reading (string, °C)."""
        self._temperature = value

    def set_ec(self, value):
        """Set the last EC reading (string, µS/cm)."""
        self._ec = value

    def set_ph(self, value):
        """Set the last pH reading (string)."""
        self._ph = value

    def init_screen(self):
        """Show the splash / intro animation on first boot (currently disabled in __init__).

        Sequence:
        1. Display the fishtank logo (inverted) for 3 seconds.
        2. Scroll the text "PIA12 / AQUARIUM" across the screen.
        3. Animate a shrinking rectangle as a wipe-out effect.

        Note: ``fishtank_logo`` is imported lazily here to avoid loading the
        large bitmap into RAM at module import time.
        """
        # self.oled.invert(1)
        self.display.fill(0)
        self.display.invert(1)
        from Icons.images_repo import (
            fishtank_logo,
        )  # lazy import: keeps the large bitmap out of RAM until needed

        self.display.show_image(fishtank_logo, 128, 64)
        sleep(3)

        self.display.invert(0)
        # self.display.fill(0)   # fill entire screen with colour=0
        screen = [[39, 0, "PIA12"], [28, 57, "AQUARIUM"]]
        self.display.scroll_portion(screen, 128, 20)
        rect_start_x = 10
        rect_start_y = 10
        rect_width = 105
        rect_height = 45
        self.display.rect(
            rect_start_x, rect_start_y, rect_width, rect_height, 1
        )  # draw a rectangle outline 10,10 to width=107, height=53, colour=1
        self.display.show()
        sleep(2)
        # Cast to int for MicroPython/CPython compatibility (range needs int stop).
        for xx in range(rect_start_x, int(rect_height / 2) - 2, 4):
            self.display.fill(0)
            self.display.rect(
                xx + 10, xx + 10, int(rect_width - 2 * xx), int(rect_height - 2 * xx), 1
            )
            self.display.show()
            sleep(0.05)

    def show_main_screen(self):
        """Render the idle home screen onto the OLED framebuffer.

        Layout (128 × 51 px upper area, relay strip drawn separately below):
        - Row  2–16 : framed time box centred at the top (HH:MM:SS)
        - Row 23    : temperature label + value + degree symbol + "C"
        - Row 33    : EC label + value in µS/cm
        - Row 43    : pH label + value

        Only the upper 51 rows are cleared so the relay-status strip drawn
        by ``show_rele_symbol`` at rows 52–63 is preserved.

        Note: ``display.show()`` must be called by the caller to actually
        flush the framebuffer to the hardware.
        """
        # Clear only the content area (rows 0-50); leave relay strip intact.
        self.display.fill_rect(0, 0, 128, 51, 0)

        # Time box: thin rectangle border centred at the top.
        self.display.rect(25, 2, 75, 14, 1)
        self.display.text(self.time, 30, 5, 1)

        # Temperature row: label, zero-padded value, custom degree symbol, unit.
        self.display.text("TEMP:", 0, 23)
        self.display.text("{0:02d}".format(int(self._temperature)), 48, 23)
        self.display.show_custom_char(self._degree_symbol, 64, 23)  # °
        self.display.text("C", 72, 23)

        # EC row: label and zero-padded value with unit.
        self.display.text("EC:", 0, 33)
        self.display.text("{0:03}".format(self._ec) + " uS/cm", 32, 33)

        # pH row: label and value.
        self.display.text("PH:", 0, 43)
        self.display.text(self._ph, 32, 43)

    def show_rele_symbol(self, rele):
        """Render the relay-status strip in the bottom 12 rows of the OLED.

        Draws one small 12×12 button per relay starting at x=70, spaced 14 px
        apart.  A *filled* button indicates the relay is ON; an *outlined*
        (blank) button indicates it is OFF.  The button label is the 1-based
        relay number (1–4).

        Args:
            rele: Iterable of booleans, one per relay, in the order
                [light, filter, heater, feeder].  Typically obtained via
                ``Config.get_rele_list()``.
        """
        # Clear only the status-strip area so the content rows above are untouched.
        self.display.fill_rect(0, 52, 128, 12, 0)
        for index, rele_status in enumerate(rele):
            if rele_status:
                # Relay ON → filled (solid) button.
                self.display.show_fill_button_with_text(
                    _RELAY_LABELS[index], 70 + index * 14, 52, 12, 12
                )
            else:
                # Relay OFF → blank (outline only) button.
                self.display.show_blank_button_with_text(
                    _RELAY_LABELS[index], 70 + index * 14, 52, 12, 12
                )

    def draw(self):
        """Callback invoked by the menu framework when the user exits the menu.

        Sets ``is_enabled_menu`` to False so that ``run()`` knows it must
        redraw the main screen on the next iteration.
        """
        # Reset the menu-active flag so the state machine in run() switches
        # back to the idle main-screen rendering path.
        self.is_enabled_menu = False

    def run(self):
        """Display state machine — call once per iteration of the main loop.

        State transitions:
        1. Menu just opened (is_enabled_menu=True, exit_menu=False):
           draw the menu and set exit_menu=True to prevent double-drawing.
        2. Menu just closed (exit_menu=True, is_enabled_menu=False):
           reset navigation, refresh time and sensor readings, redraw
           the main screen and the relay status bar.
        3. Idle main screen: update only when the RTC second has changed,
           avoiding unnecessary full-screen redraws.
        """
        if self.is_enabled_menu and not (self.exit_menu):
            self.menu.draw()
            self.exit_menu = True
        elif self.exit_menu and not (self.is_enabled_menu):
            self.exit_menu = False
            self.menu.reset()
            self.time = self._ds_rtc.time
            self.show_main_screen()
            self.show_rele_symbol(self._config.get_rele_list())
            self.display.show()
        elif not (self.exit_menu) and not (self.is_enabled_menu):
            # Confronto su int: zero allocazioni stringa per i 59 cicli in cui nulla cambia.
            # La stringa HH:MM:SS viene costruita solo 1 volta al secondo.
            _s = self._ds_rtc.second  # 1 lettura I2C, restituisce int
            if _s != self._last_second:
                self._last_second = _s
                self.time = self._ds_rtc.time
                self.show_main_screen()
                self.display.show()

    @property
    def is_enabled_menu(self):
        """True when the menu overlay is active; False when the main screen is shown."""
        return self._is_enabled_menu

    @is_enabled_menu.setter
    def is_enabled_menu(self, value):
        """Set the menu-active flag."""
        self._is_enabled_menu = value
