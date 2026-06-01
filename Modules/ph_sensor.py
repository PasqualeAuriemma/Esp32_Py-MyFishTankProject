# Modules/ph_sensor.py
#
# Driver for DFRobot Gravity Analog pH Meter V2 — SEN0169
# ESP32 / MicroPython — optimised for low RAM and industrial accuracy.
#
# ── Physics background ────────────────────────────────────────────────
# The SEN0169 outputs a voltage linearly proportional to the pH value.
# The probe uses a glass electrode that generates a Nernst potential:
#
#   V = V_offset + S * (pH_solution - pH_zero)
#
# where:
#   V_offset  — midpoint voltage at pH 7.0  (typically ~2.5 V at 3.3 V supply)
#   S         — slope (mV/pH unit, negative — Nernst: ~-59.16 mV/pH at 25°C)
#   pH_zero   — isopotential point (7.0 for most glass electrodes)
#
# Practical linear model used here (two-point calibration):
#   pH = (V - offset) / slope + 7.0
#
# Temperature compensation (Nernst equation):
#   S(T) = -0.05916 * (T + 273.15) / 298.15   (V/pH, at temperature T °C)
#
# ── Hardware wiring ───────────────────────────────────────────────────
#   SEN0169 VCC    → 3.3 V (or 5 V — adjust vref accordingly)
#   SEN0169 GND    → GND
#   SEN0169 Signal → ESP32 ADC GPIO (GPIO34, 35, 36 or 39 — input-only)
#   BNC connector  → pH probe
#
# ── Two-point calibration procedure ──────────────────────────────────
#   1. Rinse probe, immerse in pH 7.0 buffer → call calibrate_midpoint()
#   2. Rinse probe, immerse in pH 4.0 or 10.0 buffer → call calibrate_slope()
#   3. Store offset + slope in NVS or Config and pass them at construction.
#
# ── Usage ─────────────────────────────────────────────────────────────
#   from Modules.ph_sensor import PHSensor
#
#   sensor = PHSensor(pin=35)                        # minimal
#   sensor = PHSensor(                               # fully parametric
#       pin=35,
#       vref=3.3,
#       adc_resolution=12,
#       samples=10,
#       offset=0.0,        # volts — from calibration
#       slope=-0.05916,    # V/pH  — from calibration
#       temperature=25.0,
#       debug=True,
#   )
#
#   ph = sensor.read_ph()
#   v  = sensor.read_voltage()
#   sensor.set_temperature(28.0)
#
#   # In-place calibration:
#   sensor.calibrate_midpoint(7.0)   # immersed in pH 7 buffer
#   sensor.calibrate_slope(4.0)      # immersed in pH 4 buffer

import gc
import utime as time               # type: ignore[import]
from machine import Pin, ADC         # type: ignore[import]
from micropython import const         # type: ignore[import]

# ── Module-level constants ─────────────────────────────────────────────
_ADC_MAX_12BIT = const(4095)
_ADC_MAX_11BIT = const(2047)
_ADC_MAX_10BIT = const(1023)
_ADC_MAX_9BIT  = const(511)

_DEFAULT_VREF        = 3.3
_DEFAULT_SAMPLES     = const(10)
_DEFAULT_RESOLUTION  = const(12)
_DEFAULT_TEMPERATURE = 25.0

# Nernst slope at 25 °C:  R*T/F * ln(10) = 8.314*298.15/96485 * 2.303
# = 0.05916 V/pH  (negative because pH increases as voltage decreases)
_NERNST_SLOPE_25 = -0.05916          # V / pH unit at 25 °C
_NERNST_FACTOR   =  0.05916 / 298.15 # used for temperature scaling

# Typical midpoint voltage at pH 7 for a 3.3 V supply
# (varies ±200 mV between probes — always calibrate)
_DEFAULT_OFFSET = 0.0                # V — deviation from ideal at pH 7

# pH physical range guard
_PH_MIN =  0.0
_PH_MAX = 14.0

# Warm-up time after enabling the MOSFET switch (ms)
_WARMUP_MS = const(150)

# Sentinel for invalid readings
_INVALID = -999.0

# Minimum voltage delta required between pH 7 and slope buffer
# to accept a slope calibration (guards against probe not in buffer)
_MIN_SLOPE_DELTA_V = 0.050           # 50 mV


class PHSensor:
    """
    Driver for DFRobot Gravity Analog pH Meter V2 (SEN0169).

    Fully parametric: all physical constants, ADC settings and calibration
    coefficients are injectable at construction time.  No subclassing
    required for different board revisions or calibration workflows.

    Calibration model (two-point linear):
        pH = (V - V_mid) / S(T) + 7.0
    where:
        V_mid = midpoint voltage at pH 7 (offset parameter, calibrated)
        S(T)  = Nernst slope at temperature T, scaled from 25 °C reference

    Args:
        pin            (int | Pin): ADC-capable GPIO.
        vref           (float):     ADC reference voltage (V). Default 3.3.
        adc_resolution (int):       ADC bit depth: 9–12. Default 12.
        samples        (int):       Readings averaged per measurement. Default 10.
        offset         (float):     Midpoint calibration offset (V). Default 0.0.
        slope          (float):     Calibrated slope (V/pH). Default Nernst 25°C.
        temperature    (float):     Water temperature for compensation (°C).
        switch_pin     (int | Pin | None): Optional MOSFET switch GPIO.
        debug          (bool):      Verbose logging. Default False.
    """

    __slots__ = (
        "_adc",
        "_vref",
        "_adc_max",
        "_samples",
        "_offset",          # V — midpoint calibration (pH 7 buffer)
        "_slope",           # V/pH — calibrated Nernst slope
        "_temperature",     # °C — for temperature compensation
        "_switch_pin",      # optional MOSFET Pin
        "_debug",
        "_raw_buf",         # pre-allocated sample buffer — zero GC on hot path
        "_cal_v_mid",       # stored voltage during midpoint calibration
    )

    _RES_MAP = {
        9:  _ADC_MAX_9BIT,
        10: _ADC_MAX_10BIT,
        11: _ADC_MAX_11BIT,
        12: _ADC_MAX_12BIT,
    }

    def __init__(
        self,
        pin,
        vref: float          = _DEFAULT_VREF,
        adc_resolution: int  = _DEFAULT_RESOLUTION,
        samples: int         = _DEFAULT_SAMPLES,
        offset: float        = _DEFAULT_OFFSET,
        slope: float         = _NERNST_SLOPE_25,
        temperature: float   = _DEFAULT_TEMPERATURE,
        switch_pin           = None,
        debug: bool          = False,
    ):
        if adc_resolution not in self._RES_MAP:
            raise ValueError(
                "Invalid adc_resolution: {}. Use 9, 10, 11 or 12.".format(
                    adc_resolution))
        if samples < 1:
            raise ValueError("samples must be >= 1, got {}.".format(samples))
        if slope == 0.0:
            raise ValueError("slope cannot be 0 — probe would be unusable.")

        self._debug      = debug
        self._vref       = vref
        self._adc_max    = self._RES_MAP[adc_resolution]
        self._samples    = samples
        self._offset     = float(offset)
        self._slope      = float(slope)
        self._temperature = float(temperature)
        self._cal_v_mid  = None   # populated during calibrate_midpoint()

        # Configure ADC
        _pin = pin if isinstance(pin, Pin) else Pin(pin)
        self._adc = ADC(_pin)
        self._adc.atten(ADC.ATTN_11DB)   # extends range to ~3.6 V on ESP32

        # Optional MOSFET switch
        if switch_pin is not None:
            sw = switch_pin if isinstance(switch_pin, Pin) \
                 else Pin(switch_pin, Pin.OUT)
            sw.value(0)
            self._switch_pin = sw
        else:
            self._switch_pin = None

        # Pre-allocate sample buffer — reused on every read, zero allocation
        self._raw_buf = [0] * samples

        gc.collect()
        self._log(
            "Init OK — vref={}V, {}bit, {} samples, "
            "offset={:.4f}V, slope={:.5f}V/pH, T={}°C".format(
                vref, adc_resolution, samples,
                offset, slope, temperature))

    # ── Logging ────────────────────────────────────────────────────────

    def _log(self, *args):
        """Zero-cost when debug=False — string never formatted."""
        if self._debug:
            print("[pH]", *args)

    # ── Hardware switch ────────────────────────────────────────────────

    def _switch_on(self):
        if self._switch_pin is not None:
            self._switch_pin.value(1)
            time.sleep_ms(_WARMUP_MS)

    def _switch_off(self):
        if self._switch_pin is not None:
            self._switch_pin.value(0)

    # ── ADC sampling ───────────────────────────────────────────────────

    def _read_raw_average(self) -> int:
        """
        Fill _raw_buf with ADC readings, sort in-place, trim the outer 20 %,
        and return the integer mean of the remaining samples.
        No heap allocation — _raw_buf is pre-allocated in __init__.
        """
        buf = self._raw_buf
        n   = self._samples
        adc = self._adc

        for i in range(n):
            buf[i] = adc.read()

        # In-place insertion sort (O(n²), n ≤ 32 — no secondary allocation)
        for i in range(1, n):
            key = buf[i]
            j   = i - 1
            while j >= 0 and buf[j] > key:
                buf[j + 1] = buf[j]
                j -= 1
            buf[j + 1] = key

        # Trimmed mean: discard bottom and top 20 %
        trim  = n // 5
        start = trim
        end   = n - trim
        total = 0
        for i in range(start, end):
            total += buf[i]
        count = end - start
        return total // count if count > 0 else buf[n // 2]

    def _raw_to_voltage(self, raw: int) -> float:
        return raw * self._vref / self._adc_max

    # ── Temperature-compensated slope ─────────────────────────────────

    def _slope_at_temperature(self) -> float:
        """
        Scale the calibrated slope from the calibration temperature (25 °C)
        to the current water temperature using the Nernst equation.

        S(T) = S_cal * (T_K / 298.15)
        where T_K = temperature + 273.15
        """
        t_k = self._temperature + 273.15
        return self._slope * (t_k / 298.15)

    # ── Conversion ────────────────────────────────────────────────────

    def _voltage_to_ph(self, voltage: float) -> float:
        """
        Convert compensated voltage to pH using the two-point calibration model:
            pH = (V - V_mid) / S(T) + 7.0
        where V_mid is the voltage at pH 7 (stored in _offset).
        """
        s = self._slope_at_temperature()
        return (voltage - self._offset) / s + 7.0

    # ── Public API ─────────────────────────────────────────────────────

    def set_temperature(self, temperature: float) -> None:
        """
        Update the water temperature for Nernst slope compensation.
        Call this after each DS18B20 reading to maintain accuracy.

        Args:
            temperature: Water temperature in °C.
        """
        self._temperature = float(temperature)
        self._log("Temperature updated to {}°C".format(temperature))

    def read_voltage(self) -> float:
        """
        Read the raw ADC voltage (V) from the pH probe without conversion.
        Useful for diagnostics and manual calibration.

        Returns:
            Voltage in Volts, or _INVALID on error.
        """
        try:
            self._switch_on()
            raw     = self._read_raw_average()
            voltage = self._raw_to_voltage(raw)
            self._log("raw={} voltage={:.4f} V".format(raw, voltage))
            return voltage
        except Exception as e:
            self._log("read_voltage error: {}".format(e))
            return _INVALID
        finally:
            self._switch_off()

    def read_ph(self) -> float:
        """
        Read the pH value, temperature-compensated using the Nernst slope.

        Returns:
            pH value (0.0 – 14.0), or _INVALID (-999.0) on error.
        """
        try:
            self._switch_on()
            raw     = self._read_raw_average()
            voltage = self._raw_to_voltage(raw)
            ph      = self._voltage_to_ph(voltage)

            # Clamp to physical range
            if ph < _PH_MIN:
                ph = _PH_MIN
            elif ph > _PH_MAX:
                ph = _PH_MAX

            self._log(
                "raw={} V={:.4f} pH={:.2f} (T={}°C slope={:.5f})".format(
                    raw, voltage, ph,
                    self._temperature,
                    self._slope_at_temperature()))
            return ph

        except Exception as e:
            self._log("read_ph error: {}".format(e))
            return _INVALID
        finally:
            self._switch_off()

    def read_all(self) -> tuple:
        """
        Single ADC cycle returning (voltage, ph).
        More efficient than calling read_voltage() and read_ph() separately.

        Returns:
            (voltage: float, ph: float) — any field is _INVALID on error.
        """
        try:
            self._switch_on()
            raw     = self._read_raw_average()
            voltage = self._raw_to_voltage(raw)
            ph      = self._voltage_to_ph(voltage)

            if ph < _PH_MIN:  ph = _PH_MIN
            elif ph > _PH_MAX: ph = _PH_MAX

            self._log("V={:.4f} V  pH={:.2f}".format(voltage, ph))
            return (voltage, ph)

        except Exception as e:
            self._log("read_all error: {}".format(e))
            return (_INVALID, _INVALID)
        finally:
            self._switch_off()

    # ── Two-point calibration ──────────────────────────────────────────

    def calibrate_midpoint(self, buffer_ph: float = 7.0) -> float:
        """
        Step 1 of two-point calibration.
        Immerse the probe in the pH 7.0 (or any midpoint) buffer solution,
        wait for stabilisation, then call this method.

        Stores the measured voltage as the new midpoint offset and
        returns it so the caller can persist it to NVS / SD card.

        Args:
            buffer_ph: Known pH of the buffer solution (default 7.0).

        Returns:
            Measured voltage (V) at the midpoint buffer, or _INVALID on error.
        """
        voltage = self.read_voltage()
        if not self.is_valid(voltage):
            self._log("calibrate_midpoint: read failed")
            return _INVALID

        # Compute what the offset must be to make pH = buffer_ph at this V:
        # pH = (V - offset) / slope + 7  →  offset = V - slope*(pH-7)
        s = self._slope_at_temperature()
        self._offset     = voltage - s * (buffer_ph - 7.0)
        self._cal_v_mid  = voltage

        self._log(
            "Midpoint calibrated: buffer_pH={} V={:.4f} → offset={:.4f}".format(
                buffer_ph, voltage, self._offset))
        return voltage

    def calibrate_slope(self, buffer_ph: float) -> float:
        """
        Step 2 of two-point calibration.
        Immerse the probe in a second buffer (pH 4.0 or pH 10.0),
        wait for stabilisation, then call this method.

        Requires calibrate_midpoint() to have been called first.

        Args:
            buffer_ph: Known pH of the second buffer (e.g. 4.0 or 10.0).

        Returns:
            Measured slope (V/pH), or _INVALID on error.
        """
        if self._cal_v_mid is None:
            self._log("calibrate_slope: call calibrate_midpoint() first")
            return _INVALID

        voltage = self.read_voltage()
        if not self.is_valid(voltage):
            self._log("calibrate_slope: read failed")
            return _INVALID

        delta_v  = voltage - self._cal_v_mid
        delta_ph = buffer_ph - 7.0

        if delta_ph == 0.0:
            self._log("calibrate_slope: buffer_ph must differ from 7.0")
            return _INVALID

        if abs(delta_v) < _MIN_SLOPE_DELTA_V:
            self._log(
                "calibrate_slope: voltage delta {:.4f} V too small — "
                "probe may not be in buffer".format(delta_v))
            return _INVALID

        self._slope = delta_v / delta_ph

        self._log(
            "Slope calibrated: buffer_pH={} V={:.4f} "
            "delta_V={:.4f} → slope={:.5f} V/pH".format(
                buffer_ph, voltage, delta_v, self._slope))
        return self._slope

    def get_calibration(self) -> tuple:
        """
        Return the current calibration parameters for persistence.

        Returns:
            (offset: float, slope: float)
            Store these in Config / SD card and pass them back at next boot.
        """
        return (self._offset, self._slope)

    # ── Utilities ──────────────────────────────────────────────────────

    @staticmethod
    def is_valid(value: float) -> bool:
        """Return True if *value* is a real reading (not the error sentinel)."""
        return value != _INVALID

    @property
    def offset(self) -> float:
        """Current midpoint calibration offset (V)."""
        return self._offset

    @property
    def slope(self) -> float:
        """Current calibrated slope (V/pH)."""
        return self._slope

    @property
    def temperature(self) -> float:
        """Current compensation temperature (°C)."""
        return self._temperature

    def __repr__(self) -> str:
        return (
            "PHSensor(vref={}V, {} samples, "
            "offset={:.4f}V, slope={:.5f}V/pH, T={}°C)".format(
                self._vref, self._samples,
                self._offset, self._slope, self._temperature)
        )
