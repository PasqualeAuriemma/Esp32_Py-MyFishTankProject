# board_pins.py
"""
PIN MAPPING ESP32
-------------------I/O DIGITALI--------------------------------
   G27 RELE' in 1
   G26 RELE' in 2
   G25 RELE' in 3
   G33 RELE' in 4
   G19 miso SD
   G23 mosi SD
   G18 sck SD
   G5  cs SD
   G17 MOSFET SWITCH EC
   G14 MOSFET SWITCH PH
---------------------------------------------------------------
   5v alimentazione board
   gnd alimentazione board
-------------------I/O ANALOGICI-------------------------------
   G4  Tasti keypad
   G34 PH
   G13 DS18B20
------------------ADS1115--------------------------------------
   A0-ADS TDS-EC Meter v 1.0 KS0429
------------------I2C------------------------------------------
   G21 SDA
   G22 SCL
-----------------------------------------------------------------------------------------------------------
"""

from micropython import const # type: ignore[import]

class BoardPins:
    """Hardware pin mapping for the board (ESP32)."""

    # --- I2C Devices (OLED Display and RTC) ---
    # Pins for I2C bus might need to be configured depending on the ESP32 board.
    # Default for many boards are: SDA=21, SCL=22
    I2C_SDA = const(21)
    I2C_SCL = const(22)

    SCK_SD = const(18)
    MOSI_SD = const(23)
    MISO_SD = const(19)
    CS_SD = const(5)

    # Relays
    RELAY_LIGHT = const(27)  # int C inline, zero heap overhead
    RELAY_FILTER = const(26)
    RELAY_HEATER = const(25)
    RELAY_FEEDER = const(33)

    # Analog Inputs (Potentiometers for menu control)
    # A value > 2000 is considered a "press".
    # pin G4 (Tasti keypad) T0
    # Keypad analog
    KEYPAD_ANALOG = const(4)

    # DS18B20, OneWire Temperature Sensor
    DS18B20 = const(13)
