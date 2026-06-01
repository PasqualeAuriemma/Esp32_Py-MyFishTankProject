# Modules/tds_sensor.py
#
# Driver for DFRobot Gravity Analog TDS Sensor — SEN0244
# ESP32 / MicroPython — optimised for low RAM and high accuracy.
#
# ── Physics background ────────────────────────────────────────────────
# The SEN0244 outputs an analog voltage proportional to the TDS value
# of the solution.  The onboard op-amp gives a 0-2.3 V range mapped to
# 0-1000 ppm (parts-per-million) TDS.
#
# Conversion chain:
#   ADC raw (0-4095 on 12-bit ESP32)
#     → voltage  (V)          = raw * VREF / ADC_MAX
#     → EC       (µS/cm)      = compensation(voltage, temperature)
#     → TDS      (ppm)        = EC_value * TDS_FACTOR
#
# Temperature compensation (empirical, per DFRobot datasheet):
#   EC_25 = EC_T / (1 + 0.02 * (T - 25))
#   where EC_25 is EC at reference temperature 25 °C
#         EC_T  is the raw EC reading at temperature T
#
# ── Hardware wiring ───────────────────────────────────────────────────
#   SEN0244 VCC  → 3.3 V or 5 V (adjust VREF accordingly)
#   SEN0244 GND  → GND
#   SEN0244 A    → ESP32 ADC GPIO (e.g. GPIO34, 35, 36, 39 — input-only)
#
# ── Usage ─────────────────────────────────────────────────────────────
#   from Modules.tds_sensor import TDSSensor
#
#   sensor = TDSSensor(pin=34)                 # minimal
#   sensor = TDSSensor(                        # fully parametric
#       pin=34,
#       vref=3.3,
#       adc_resolution=12,
#       samples=10,
#       temperature=25.0,
#       debug=True,
#   )
#
#   ec  = sensor.read_ec()         # µS/cm, temperature-compensated
#   tds = sensor.read_tds()        # ppm
#   sensor.set_temperature(28.5)   # update compensation temperature
#
# ── Notes ─────────────────────────────────────────────────────────────
#   * Use input-only ADC pins (GPIO34-39) for best accuracy.
#   * Run a gc.collect() before reading if RAM is tight.
#   * The MOSFET switch (GPIO17 in the original schematic) should be
#     driven HIGH before reading and LOW after to avoid electrode
#     polarisation.  Pass switch_pin= to handle this automatically.

import gc
import utime # pyright: ignore[reportMissingImports]
from machine import Pin, ADC         # type: ignore[import]
from micropython import const         # type: ignore[import]

# ── Module-level constants ─────────────────────────────────────────────
_ADC_MAX_12BIT  = const(4095)         # 2^12 - 1
_ADC_MAX_11BIT  = const(2047)         # 2^11 - 1
_ADC_MAX_10BIT  = const(1023)         # 2^10 - 1
_ADC_MAX_9BIT   = const(511)          # 2^9  - 1

_DEFAULT_VREF        = 3.3            # Volts — change to 5.0 if powered at 5 V
_DEFAULT_SAMPLES     = const(10)      # ADC readings averaged per measurement
_DEFAULT_RESOLUTION  = const(12)      # bits
_DEFAULT_TEMPERATURE = 25.0           # °C reference temperature

# DFRobot empirical calibration coefficients (SEN0244 datasheet):
#   EC (µS/cm) = (133.42 * V³ - 255.86 * V² + 857.39 * V) * K
# where K is a user calibration factor (default 1.0).
_COEFF_A = 133.42
_COEFF_B = 255.86
_COEFF_C = 857.39

# TDS ≈ EC × 0.5 (NaCl standard; adjust for other solutions)
_TDS_FACTOR = 0.5

# Temperature compensation coefficient (2 % per °C from 25 °C)
_TEMP_COEFF = 0.02

# Sentinel for invalid readings
_INVALID = -999.0

# Warm-up delay for the electrode after switching the MOSFET on (ms)
_WARMUP_MS = const(100)


class TDSSensor:
    """
    Driver for the DFRobot Gravity Analog TDS Sensor (SEN0244).

    All key parameters are injectable at construction time, making the
    class fully reusable across different board revisions and calibration
    requirements without subclassing.

    Args:
        pin         (int | Pin): ADC-capable GPIO number or Pin object.
        vref        (float):     ADC reference voltage in Volts (default 3.3).
        adc_resolution (int):    ADC bit depth: 9, 10, 11, or 12 (default 12).
        samples     (int):       Number of raw ADC readings to average (default 10).
        temperature (float):     Water temperature in °C for compensation (default 25.0).
        k_value     (float):     User calibration multiplier (default 1.0).
        tds_factor  (float):     EC-to-TDS conversion factor (default 0.5 for NaCl).
        switch_pin  (int | Pin | None): Optional MOSFET switch GPIO.
            If provided, it is driven HIGH before reading and LOW after to
            prevent electrode polarisation.
        debug       (bool):      Enable verbose logging (default False).
    """

    __slots__ = (
        "_adc",
        "_vref",
        "_adc_max",
        "_samples",
        "_temperature",
        "_k_value",
        "_tds_factor",
        "_switch_pin",
        "_debug",
        "_raw_buf",       # pre-allocated list for ADC samples — zero GC on hot path
    )

    # ── Resolution → ADC max value lookup ─────────────────────────────
    _RES_MAP = {
        9:  _ADC_MAX_9BIT,
        10: _ADC_MAX_10BIT,
        11: _ADC_MAX_11BIT,
        12: _ADC_MAX_12BIT,
    }

    def __init__(
        self,
        pin,
        vref: float        = _DEFAULT_VREF,
        adc_resolution: int = _DEFAULT_RESOLUTION,
        samples: int        = _DEFAULT_SAMPLES,
        temperature: float  = _DEFAULT_TEMPERATURE,
        k_value: float      = 1.0,
        tds_factor: float   = _TDS_FACTOR,
        switch_pin          = None,
        debug: bool         = False,
    ):
        if adc_resolution not in self._RES_MAP:
            raise ValueError(
                "Invalid adc_resolution: {}. Use 9, 10, 11 or 12.".format(
                    adc_resolution))

        if samples < 1:
            raise ValueError("samples must be >= 1, got {}.".format(samples))

        self._debug      = debug
        self._vref       = vref
        self._adc_max    = self._RES_MAP[adc_resolution]
        self._samples    = samples
        self._temperature = float(temperature)
        self._k_value    = float(k_value)
        self._tds_factor = float(tds_factor)

        # Configure ADC
        _pin = pin if isinstance(pin, Pin) else Pin(pin)
        self._adc = ADC(_pin)
        self._adc.atten(ADC.ATTN_11DB)   # full-scale ~3.6 V on ESP32

        # Optional MOSFET switch to protect electrodes
        if switch_pin is not None:
            sw = switch_pin if isinstance(switch_pin, Pin) \
                 else Pin(switch_pin, Pin.OUT)
            sw.value(0)   # start OFF
            self._switch_pin = sw
        else:
            self._switch_pin = None

        # Pre-allocate sample buffer — avoids repeated list allocation on hot path
        self._raw_buf = [0] * samples

        gc.collect()
        self._log("Init OK — vref={} V, {}bit, {} samples, T={}°C".format(
            vref, adc_resolution, samples, temperature))

    # ── Logging ────────────────────────────────────────────────────────

    def _log(self, *args):
        """Zero-cost debug logging: string is never formatted when debug=False."""
        if self._debug:
            print("[TDS]", *args)

    # ── Hardware access ────────────────────────────────────────────────

    def _switch_on(self):
        if self._switch_pin is not None:
            self._switch_pin.value(1)
            utime.sleep_ms(_WARMUP_MS)

    def _switch_off(self):
        if self._switch_pin is not None:
            self._switch_pin.value(0)

    # ── ADC sampling ───────────────────────────────────────────────────

    def _read_raw_average(self) -> int:
        """
        Read `_samples` ADC values into the pre-allocated buffer and
        return their integer average.  No heap allocation on this path.
        """
        buf  = self._raw_buf
        n    = self._samples
        adc  = self._adc

        for i in range(n):
            buf[i] = adc.read()

        # Insertion-sort in-place — O(n²) but n≤32, no extra allocation
        for i in range(1, n):
            key = buf[i]
            j   = i - 1
            while j >= 0 and buf[j] > key:
                buf[j + 1] = buf[j]
                j -= 1
            buf[j + 1] = key

        # Discard bottom and top 20 % (2 values each side for n=10)
        trim  = n // 5
        start = trim
        end   = n - trim
        total = 0
        for i in range(start, end):
            total += buf[i]
        count = end - start
        return total // count if count > 0 else buf[n // 2]

    def _raw_to_voltage(self, raw: int) -> float:
        """Convert ADC raw value to voltage using VREF and ADC resolution."""
        return raw * self._vref / self._adc_max

    # ── Conversion chain ───────────────────────────────────────────────

    def _voltage_to_ec_raw(self, voltage: float) -> float:
        """
        Apply the DFRobot polynomial to get raw EC in µS/cm.
        Formula: EC = (133.42*V³ - 255.86*V² + 857.39*V) * K
        """
        v2 = voltage * voltage
        v3 = v2 * voltage
        return (_COEFF_A * v3 - _COEFF_B * v2 + _COEFF_C * voltage) * self._k_value

    def _compensate_temperature(self, ec_raw: float) -> float:
        """
        Apply temperature compensation to normalise EC to 25 °C reference.
        EC_25 = EC_T / (1 + TEMP_COEFF * (T - 25))
        """
        compensation = 1.0 + _TEMP_COEFF * (self._temperature - 25.0)
        # Guard against division by zero (temperature compensation = 0)
        if compensation == 0.0:
            return ec_raw
        return ec_raw / compensation

    # ── Public API ─────────────────────────────────────────────────────

    def set_temperature(self, temperature: float) -> None:
        """
        Update the water temperature used for EC compensation.
        Call this whenever a fresh DS18B20 (or similar) reading is available.

        Args:
            temperature: Water temperature in °C.
        """
        self._temperature = float(temperature)
        self._log("Temperature updated to {}°C".format(temperature))

    def set_k_value(self, k: float) -> None:
        """
        Update the calibration K factor.
        Obtain K by measuring a known-EC standard solution:
            K = EC_standard / EC_measured_at_k1

        Args:
            k: Calibration multiplier (dimensionless, default 1.0).
        """
        if k <= 0:
            raise ValueError("k_value must be > 0, got {}.".format(k))
        self._k_value = float(k)
        self._log("K value updated to {}".format(k))

    def read_voltage(self) -> float:
        """
        Read the raw ADC voltage (V) without any EC conversion.
        Useful for manual calibration or diagnostics.

        Returns:
            Voltage in Volts, or _INVALID on error.
        """
        try:
            #self._switch_on()
            raw     = self._read_raw_average()
            voltage = self._raw_to_voltage(raw)
            self._log("raw={} voltage={:.4f} V".format(raw, voltage))
            return voltage
        except Exception as e:
            self._log("read_voltage error: {}".format(e))
            return _INVALID
        finally:
            #self._switch_off()
            pass

    def read_ec(self) -> float:
        """
        Read EC (Electrical Conductivity) in µS/cm, temperature-compensated.

        Returns:
            EC value in µS/cm, or _INVALID (-999.0) on error.
        """
        try:
            #self._switch_on()
            raw     = self._read_raw_average()
            voltage = self._raw_to_voltage(raw)
            ec_raw  = self._voltage_to_ec_raw(voltage)
            ec_comp = self._compensate_temperature(ec_raw)

            # Physical sanity check: EC range for aquariums/hydroponics 0-5000
            if ec_comp < 0.0:
                ec_comp = 0.0

            self._log(
                "raw={} V={:.3f} EC_raw={:.1f} EC_comp={:.1f} µS/cm".format(
                    raw, voltage, ec_raw, ec_comp))
            return ec_comp

        except Exception as e:
            self._log("read_ec error: {}".format(e))
            return _INVALID
        finally:
            #self._switch_off()
            pass

    def read_tds(self) -> float:
        """
        Read TDS (Total Dissolved Solids) in ppm, temperature-compensated.

        TDS = EC × tds_factor   (default factor 0.5 for NaCl calibration)

        Returns:
            TDS in ppm, or _INVALID on error.
        """
        ec = self.read_ec()
        if ec == _INVALID:
            return _INVALID
        tds = ec * self._tds_factor
        self._log("TDS={:.1f} ppm".format(tds))
        return tds

    def read_all(self) -> tuple:
        """
        Perform a single ADC read cycle and return all derived values.
        More efficient than calling read_ec() and read_tds() separately
        because the ADC is sampled only once.

        Returns:
            (voltage: float, ec: float, tds: float)
            Any field is _INVALID on error.
        """
        try:
            self._switch_on()
            raw     = self._read_raw_average()
            voltage = self._raw_to_voltage(raw)
            ec_raw  = self._voltage_to_ec_raw(voltage)
            ec_comp = self._compensate_temperature(ec_raw)
            if ec_comp < 0.0:
                ec_comp = 0.0
            tds = ec_comp * self._tds_factor
            self._log(
                "V={:.3f} V  EC={:.1f} µS/cm  TDS={:.1f} ppm".format(
                    voltage, ec_comp, tds))
            return (voltage, ec_comp, tds)

        except Exception as e:
            self._log("read_all error: {}".format(e))
            return (_INVALID, _INVALID, _INVALID)
        finally:
            self._switch_off()

    # ── Utilities ──────────────────────────────────────────────────────

    @staticmethod
    def is_valid(value: float) -> bool:
        """Return True if *value* is a valid reading (not the sentinel)."""
        return value != _INVALID

    @property
    def temperature(self) -> float:
        """Current compensation temperature (°C)."""
        return self._temperature

    @property
    def k_value(self) -> float:
        """Current calibration K factor."""
        return self._k_value

    def __repr__(self) -> str:
        return (
            "TDSSensor(vref={} V, {} samples, T={}°C, K={})".format(
                self._vref, self._samples, self._temperature, self._k_value)
        )
