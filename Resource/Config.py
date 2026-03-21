"""
Configuration holder for PyTank (timers, relays, sensors, modes).
Singleton, __slots__ for memory, safe defaults, serialization to/from dict.
"""

import gc

from Helper.Singleton import Singleton


# Class-level constants (shared, not per-instance)
FREQ = ("1", "2", "3", "4", "6", "8", "12", "24")
MODE_LIST = ("AUTO", "MAINTENANCE", "STAND BY")
AUTOMATIC_PROCESS_STATE = (
    "light",
    "heater",
    "filter",
    "feeder",
    "ec",
    "ph",
    "temp",
    "temp_s",
    "ec_s",
    "ph_s",
)


class Config(Singleton):
    """Single source of config: timer, auto/heater, relays, sensors, modes. One instance per app."""

    __slots__ = (
        # Lighting timer: daily on/off window
        "_start_hour",
        "_start_minutes",
        "_end_hour",
        "_end_minutes",
        # Heater thresholds and operating-mode flags
        "_temp_max",
        "_temp_min",
        "_auto_enabled",
        "_maintain_enabled",
        "_stand_by",
        # Per-feature enable/disable switches
        "_on_off_light_auto",
        "_on_off_heater",
        "_on_off_ec",
        "_on_off_ph",
        "_on_off_temperature",
        "_on_off_filter",
        "_on_off_feeder",
        # Remote-sending enable flags (active only when reading is also enabled)
        "_on_off_temperature_sending",
        "_on_off_ec_sending",
        "_on_off_ph_sending",
        # Automatic sub-feature flags
        "_on_off_filter_auto",
        "_on_off_heater_auto",
        "_on_off_recovery",
        # Web update frequencies — strings from FREQ tuple
        "_freq_update_web_temperature",
        "_freq_update_web_ec",
        "_freq_update_web_ph",
        "_freq_filter",
        # Hour/minute of last NTP sync or config load
        "_hour_loading",
        "_min_loading",
        # Direct relay states
        "_relay0",
        "_relay1",
        "_relay2",
        "_relay3",
        # Latest sensor readings
        "_temperature",
        "_ec",
        "_ph",
        # Pending async action flags
        "_connection_action",
        "_send_action_ec",
        "_send_action_ph",
        # Singleton guard and saved-state snapshot
        "_singleton_initialized",
        "_saved_state",
    )

    def __init__(self):
        """Initialize all config fields to safe defaults. No-op if already initialized (Singleton)."""
        if getattr(self, "_singleton_initialized", True):
            return  # ← già inizializzato, esci subito
        self._start_hour = 0
        self._start_minutes = 0
        self._end_hour = 0
        self._end_minutes = 0
        self._temp_max = 0
        self._temp_min = 0
        self._auto_enabled = True
        self._maintain_enabled = False
        self._stand_by = False
        self._on_off_light_auto = False
        self._on_off_heater = False
        self._on_off_ec = False
        self._on_off_ph = False
        self._on_off_temperature = False
        self._on_off_filter = False
        self._on_off_feeder = False
        self._on_off_temperature_sending = False
        self._on_off_ec_sending = False
        self._on_off_ph_sending = False
        self._on_off_filter_auto = False
        self._on_off_heater_auto = False
        self._on_off_recovery = False
        self._freq_update_web_temperature = FREQ[0]
        self._freq_update_web_ec = FREQ[0]
        self._freq_update_web_ph = FREQ[0]
        self._freq_filter = FREQ[0]
        self._hour_loading = 0
        self._min_loading = 0
        self._relay0 = False
        self._relay1 = False
        self._relay2 = False
        self._relay3 = False
        self._temperature = 0.0
        self._ec = 0.0
        self._ph = 0.0
        self._connection_action = False
        self._send_action_ec = False
        self._send_action_ph = False
        self._singleton_initialized = True
        self._saved_state = None

    @property
    def freq(self):
        """Tuple of available update-frequency values in hours (e.g. '1', '2', … '24')."""
        return FREQ

    @property
    def mode_list(self):
        """Tuple of available operating mode names: AUTO, MAINTENANCE, STAND BY."""
        return MODE_LIST

    def set_timer_time(self, list_time=None):
        """Set start/end time. Avoid mutable default: pass [sh, sm, eh, em] or None for zeros."""
        if list_time is None:
            list_time = (0, 0, 0, 0)
        self._start_hour = list_time[0]
        self._start_minutes = list_time[1]
        self._end_hour = list_time[2]
        self._end_minutes = list_time[3]

    def get_timer_time(self):
        """Return (start_hour, start_minutes, end_hour, end_minutes) for the lighting timer."""
        return (
            self._start_hour,
            self._start_minutes,
            self._end_hour,
            self._end_minutes,
        )

    def set_auto_heater(self, list_temp=None):
        """Set temp max/min. Pass [max, min] or None to reset to zeros."""
        if list_temp is None:
            list_temp = (0, 0)
        self._temp_max = list_temp[0]
        self._temp_min = list_temp[1]

    def get_auto_heater(self):
        """Return (temp_max, temp_min) heater control thresholds (°C)."""
        return (self._temp_max, self._temp_min)

    def get_connection_action(self):
        """Return True if a WiFi connection/reconnection action is pending."""
        return self._connection_action

    def set_connection_action(self, value):
        """Set the pending WiFi connection action flag."""
        self._connection_action = value

    def get_send_action_ec(self):
        """Return True if a pending EC data send action is queued."""
        return self._send_action_ec

    def set_send_action_ec(self, value):
        """Set the pending EC data send action flag."""
        self._send_action_ec = value

    def get_send_action_ph(self):
        """Return True if a pending pH data send action is queued."""
        return self._send_action_ph

    def set_send_action_ph(self, value):
        """Set the pending pH data send action flag."""
        self._send_action_ph = value

    def set_on_off_recovery(self, value):
        """Enable or disable recovery mode (signals reconnect/restart on failure)."""
        self._on_off_recovery = value

    def get_on_off_recovery(self):
        """Return True if recovery mode is active."""
        return self._on_off_recovery

    def get_rele_list(self):
        """Return current states of all four relays as (relay0, relay1, relay2, relay3)."""
        return (self._relay0, self._relay1, self._relay2, self._relay3)

    def off_automatic_process(self):
        """Disable auto mode: save current on/off state to _temp and turn features off."""
        self._saved_state = {
            "light": self._on_off_light_auto,
            "heater": self._on_off_heater,
            "filter": self._on_off_filter,
            "feeder": self._on_off_feeder,
            "ec": self._on_off_ec,
            "ph": self._on_off_ph,
            "temp": self._on_off_temperature,
            "temp_s": self._on_off_temperature_sending,
            "ec_s": self._on_off_ec_sending,
            "ph_s": self._on_off_ph_sending,
        }
        self.set_on_off_light_auto(False)
        self.set_on_off_heater(False)
        self.set_on_off_filter(False)
        self.set_on_off_feeder(False)
        self.set_on_off_ec(False)
        self.set_on_off_ph(False)
        self.set_on_off_temperature(False)
        self.set_on_off_temperature_sending(False)
        self.set_on_off_ec_sending(False)
        self.set_on_off_ph_sending(False)
        self._auto_enabled = False
        self._maintain_enabled = True
        self._stand_by = False

    def on_automatic_process(self):
        """Re-enable auto: restore on/off state from _temp."""
        s = self._saved_state
        self.set_on_off_light_auto(s["light"])
        self.set_on_off_heater(s["heater"])
        self.set_on_off_filter(s["filter"])
        self.set_on_off_feeder(s["feeder"])
        self.set_on_off_ec(s["ec"])
        self.set_on_off_ph(s["ph"])
        self.set_on_off_temperature(s["temp"])
        self.set_on_off_temperature_sending(s["temp_s"])
        self.set_on_off_ec_sending(s["ec_s"])
        self.set_on_off_ph_sending(s["ph_s"])
        self._auto_enabled = True
        self._maintain_enabled = False
        self._stand_by = False
        self._saved_state = None  # libera il dict
        gc.collect()

    def stand_by_process(self):
        """Placeholder for stand-by mode logic (not yet implemented)."""
        pass

    def active_temperature_monitoring(self, value):
        """Enable or disable temperature reading and remote sending simultaneously."""
        self._on_off_temperature_sending = value
        self._on_off_temperature = value

    def active_ec_monitoring(self, value):
        """Enable or disable EC reading and remote sending simultaneously."""
        self._on_off_ec_sending = value
        self._on_off_ec = value

    def active_ph_monitoring(self, value):
        """Enable or disable pH reading and remote sending simultaneously."""
        self._on_off_ph_sending = value
        self._on_off_ph = value

    def to_dict(self):
        """Export config to a JSON-serializable dict."""
        return {
            "startHour": self.start_hour,
            "startMinutes": self.start_minutes,
            "endHour": self.end_hour,
            "endMinutes": self.end_minutes,
            "tempMax": self.temp_max,
            "tempMin": self.temp_min,
            "autoEnabled": self.auto_enabled,
            "maintainEnabled": self.maintain_enabled,
            "standBy": self.stand_by,
            "onOffLightAuto": self._on_off_light_auto,
            "onOffHeater": self._on_off_heater,
            "onOffEC": self._on_off_ec,
            "onOffPH": self._on_off_ph,
            "onOffTemperature": self._on_off_temperature,
            "onOffFilter": self._on_off_filter,
            "onOffFeeder": self._on_off_feeder,
            "onOffTemperatureSending": self._on_off_temperature_sending,
            "onOffECSending": self._on_off_ec_sending,
            "onOffPhSending": self._on_off_ph_sending,
            "onOffFilterAuto": self._on_off_filter_auto,
            "onOffHeaterAuto": self._on_off_heater_auto,
            "freqUpdateWebTemperature": self._freq_update_web_temperature,
            "freqUpdateWebEC": self._freq_update_web_ec,
            "freqUpdateWebPH": self._freq_update_web_ph,
            "freqFilter": self._freq_filter,
            "hourLoading": self.hour_loading,
            "minLoading": self.min_loading,
            "relay0": self.relay0,
            "relay1": self.relay1,
            "relay2": self.relay2,
            "relay3": self.relay3,
            "temperature": self.temperature,
            "ec": self.ec,
            "ph": self.ph,
            "onOffRecovery": self.on_off_recovery,
        }

    def _freq_index(self, value):
        """Resolve freq value from JSON: int index or string (e.g. '1') -> index."""
        if isinstance(value, int) and 0 <= value < len(FREQ):
            return value
        s = str(value)
        for i, f in enumerate(FREQ):
            if f == s:
                return i
        return 0

    def from_json(self, data):
        """Load config from a dict (e.g. parsed JSON). Uses setters; fixes key/typo bugs."""
        self.start_hour = data.get("startHour", 0)
        self.start_minutes = data.get("startMinutes", 0)
        self.end_hour = data.get("endHour", 0)
        self.end_minutes = data.get("endMinutes", 0)
        self.temp_max = data.get("tempMax", 0)
        self.temp_min = data.get("tempMin", 0)
        self.auto_enabled = data.get("autoEnabled", True)
        self.maintain_enabled = data.get("maintainEnabled", False)
        self.stand_by = data.get("standBy", False)
        self.set_on_off_light_auto(data.get("onOffLightAuto", False))
        self.set_on_off_heater(data.get("onOffHeater", False))
        self.set_on_off_ec(data.get("onOffEC", False))
        self.set_on_off_ph(data.get("onOffPH", False))
        self.set_on_off_temperature(data.get("onOffTemperature", False))
        self.set_on_off_filter(data.get("onOffFilter", False))
        self.set_on_off_feeder(data.get("onOffFeeder", False))
        self.set_on_off_temperature_sending(data.get("onOffTemperatureSending", False))
        self.set_on_off_ec_sending(data.get("onOffECSending", False))
        self.set_on_off_ph_sending(data.get("onOffPhSending", False))
        self.set_on_off_filter_auto(data.get("onOffFilterAuto", False))
        self.set_on_off_heater_auto(data.get("onOffHeaterAuto", False))
        self.set_freq_update_web_temperature(
            self._freq_index(data.get("freqUpdateWebTemperature", 0))
        )
        self.set_freq_update_web_ec(self._freq_index(data.get("freqUpdateWebEC", 0)))
        self.set_freq_update_web_ph(self._freq_index(data.get("freqUpdateWebPH", 0)))
        self.set_freq_filter(self._freq_index(data.get("freqFilter", 0)))
        self.hour_loading = data.get("hourLoading", 0)
        self.min_loading = data.get("minLoading", 0)
        self.relay0 = data.get("relay0", False)
        self.relay1 = data.get("relay1", False)
        self.relay2 = data.get("relay2", False)
        self.relay3 = data.get("relay3", False)
        self.temperature = float(data.get("temperature", 0))
        self.ec = float(data.get("ec", 0))
        self.ph = float(data.get("ph", 0))
        self.set_on_off_recovery(data.get("onOffRecovery", False))

    # --- Properties: time, temp, mode ---
    @property
    def start_hour(self):
        """Lighting timer start hour (0–23)."""
        return self._start_hour

    @start_hour.setter
    def start_hour(self, value):
        self._start_hour = value

    @property
    def start_minutes(self):
        """Lighting timer start minute (0–59)."""
        return self._start_minutes

    @start_minutes.setter
    def start_minutes(self, value):
        self._start_minutes = value

    @property
    def end_hour(self):
        """Lighting timer end hour (0–23)."""
        return self._end_hour

    @end_hour.setter
    def end_hour(self, value):
        self._end_hour = value

    @property
    def end_minutes(self):
        """Lighting timer end minute (0–59)."""
        return self._end_minutes

    @end_minutes.setter
    def end_minutes(self, value):
        self._end_minutes = value

    @property
    def temp_max(self):
        """Maximum water temperature threshold for heater auto control (°C)."""
        return self._temp_max

    @temp_max.setter
    def temp_max(self, value):
        self._temp_max = value

    @property
    def temp_min(self):
        """Minimum water temperature threshold for heater auto control (°C)."""
        return self._temp_min

    @temp_min.setter
    def temp_min(self, value):
        self._temp_min = value

    @property
    def auto_enabled(self):
        """True when the system is in automatic (normal) operating mode."""
        return self._auto_enabled

    @auto_enabled.setter
    def auto_enabled(self, value):
        self._auto_enabled = value

    @property
    def stand_by(self):
        """True when the system is in stand-by mode."""
        return self._stand_by

    @stand_by.setter
    def stand_by(self, value):
        self._stand_by = value

    @property
    def maintain_enabled(self):
        """True when the system is in maintenance mode (automation suspended)."""
        return self._maintain_enabled

    @maintain_enabled.setter
    def maintain_enabled(self, value):
        self._maintain_enabled = value

    def set_mode(self, value):
        """Switch operating mode: 0 = AUTO (restore saved state), 1 = MAINTENANCE (suspend automation)."""
        if value == 0:
            self.on_automatic_process()
        elif value == 1:
            self.off_automatic_process()

    # --- On/off getters/setters ---
    def get_on_off_light_auto(self):
        return getattr(self, "_on_off_light_auto", False)

    def set_on_off_light_auto(self, value):
        self._on_off_light_auto = value

    def get_on_off_heater(self):
        return self._on_off_heater

    def set_on_off_heater(self, value):
        self._on_off_heater = value

    def get_on_off_ec(self):
        return self._on_off_ec

    def set_on_off_ec(self, value):
        self._on_off_ec = value

    def get_on_off_ph(self):
        return self._on_off_ph

    def set_on_off_ph(self, value):
        self._on_off_ph = value

    def get_on_off_temperature(self):
        return self._on_off_temperature

    def set_on_off_temperature(self, value):
        self._on_off_temperature = value

    def get_on_off_filter(self):
        return self._on_off_filter

    def set_on_off_filter(self, value):
        self._on_off_filter = value

    def get_on_off_feeder(self):
        return self._on_off_feeder

    def set_on_off_feeder(self, value):
        self._on_off_feeder = value

    def get_on_off_temperature_sending(self):
        """Return True only if both temperature monitoring and remote sending are enabled."""
        return self._on_off_temperature_sending and self._on_off_temperature

    def set_on_off_temperature_sending(self, value):
        self._on_off_temperature_sending = value

    def get_on_off_ec_sending(self):
        """Return True only if both EC monitoring and remote sending are enabled."""
        return self._on_off_ec_sending and self._on_off_ec

    def set_on_off_ec_sending(self, value):
        self._on_off_ec_sending = value

    def get_on_off_ph_sending(self):
        """Return True only if both pH monitoring and remote sending are enabled."""
        return self._on_off_ph_sending and self._on_off_ph

    def set_on_off_ph_sending(self, value):
        self._on_off_ph_sending = value

    def get_on_off_heater_auto(self):
        return self._on_off_heater_auto

    def set_on_off_heater_auto(self, value):
        self._on_off_heater_auto = value

    def get_on_off_filter_auto(self):
        return self._on_off_filter_auto

    def set_on_off_filter_auto(self, value):
        self._on_off_filter_auto = value

    def get_freq_update_web_temperature(self):
        return self._freq_update_web_temperature

    def set_freq_update_web_temperature(self, value):
        """Set temperature web-update frequency. Accepts an int FREQ index or a FREQ string."""
        idx = (
            value
            if isinstance(value, int) and 0 <= value < len(FREQ)
            else self._freq_index(value)
        )
        self._freq_update_web_temperature = FREQ[idx]

    def get_freq_update_web_ec(self):
        return self._freq_update_web_ec

    def set_freq_update_web_ec(self, value):
        """Set EC web-update frequency. Accepts an int FREQ index or a FREQ string."""
        idx = (
            value
            if isinstance(value, int) and 0 <= value < len(FREQ)
            else self._freq_index(value)
        )
        self._freq_update_web_ec = FREQ[idx]

    def get_freq_update_web_ph(self):
        return self._freq_update_web_ph

    def set_freq_update_web_ph(self, value):
        """Set pH web-update frequency. Accepts an int FREQ index or a FREQ string."""
        idx = (
            value
            if isinstance(value, int) and 0 <= value < len(FREQ)
            else self._freq_index(value)
        )
        self._freq_update_web_ph = FREQ[idx]

    def get_freq_filter(self):
        return self._freq_filter

    def set_freq_filter(self, value):
        """Set filter-cycle frequency. Accepts an int FREQ index or a FREQ string."""
        idx = (
            value
            if isinstance(value, int) and 0 <= value < len(FREQ)
            else self._freq_index(value)
        )
        self._freq_filter = FREQ[idx]

    @property
    def hour_loading(self):
        """Hour component of the last NTP sync or config load time."""
        return self._hour_loading

    @hour_loading.setter
    def hour_loading(self, value):
        self._hour_loading = value

    @property
    def min_loading(self):
        """Minute component of the last NTP sync or config load time."""
        return self._min_loading

    @min_loading.setter
    def min_loading(self, value):
        self._min_loading = value

    @property
    def relay0(self):
        """Current on/off state of relay 0."""
        return self._relay0

    @relay0.setter
    def relay0(self, value):
        self._relay0 = value

    @property
    def relay1(self):
        """Current on/off state of relay 1."""
        return self._relay1

    @relay1.setter
    def relay1(self, value):
        self._relay1 = value

    @property
    def relay2(self):
        """Current on/off state of relay 2."""
        return self._relay2

    @relay2.setter
    def relay2(self, value):
        self._relay2 = value

    @property
    def relay3(self):
        """Current on/off state of relay 3."""
        return self._relay3

    @relay3.setter
    def relay3(self, value):
        self._relay3 = value

    @property
    def temperature(self):
        """Last measured water temperature (°C)."""
        return self._temperature

    @temperature.setter
    def temperature(self, value):
        self._temperature = value

    @property
    def ec(self):
        """Last measured electrical conductivity (EC) value."""
        return self._ec

    @ec.setter
    def ec(self, value):
        self._ec = value

    @property
    def ph(self):
        """Last measured pH value."""
        return self._ph

    @ph.setter
    def ph(self, value):
        self._ph = value
