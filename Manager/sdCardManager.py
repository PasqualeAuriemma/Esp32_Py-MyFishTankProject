"""
# Demonstrates ESP32 interface to MicroSD Card Adapter
# Create a text file and write running numbers.
# Open text file, read and print the content on debug port

* The ESP32 pin connections for MicroSD Card Adapter SPI

# MicroSD Card Adapter Power Pins
* MicroSD VCC pin to ESP32 +5V
* MicroSD GND pin to ESP32 GND

# MicroSD SPI Pins
* MicroSD MISO pin to ESP32 GPIO19
* MicroSD MOSI pin to ESP32 GPIO23
* MicroSD SCK pin to ESP32 GPIO18
* MicroSD CS pin to ESP32 GPIO05

Name:- M.Pugazhendi
Date:-  20thOct2021
Version:- V0.1
e-mail:- muthuswamy.pugazhendi@gmail.com
"""

from machine import SPI  # type: ignore[import]
import Modules.sdcard as sdcard
import os
import json
import uos  # type: ignore[import]

from Helper.Singleton import Singleton  # type: ignore[import]


class SDCardManager(Singleton):
    """Singleton manager for SD card access on ESP32."""

    __slots__ = ("_spi", "_sd", "_vfs", "_singleton_initialized")

    def __init__(
        self,
        sck_pin,
        mosi_pin,
        miso_pin,
        sd_pin,
    ) -> None:
        # Ensure one-time initialization when used as a Singleton.
        if getattr(self, "_singleton_initialized", True):
            return

        # Initialize the SD card SPI interface and wrap it with the sdcard driver.
        # baudrate=10 MHz: safe upper limit for most SD cards on hardware SPI bus 1.
        # Without an explicit baudrate MicroPython may default to a very low speed.
        self._spi = SPI(
            1, baudrate=10_000_000, sck=sck_pin, mosi=mosi_pin, miso=miso_pin
        )
        self._sd = sdcard.SDCard(self._spi, sd_pin)
        # Create a FAT VFS instance over the SD card (MicroPython-specific API).
        self._vfs = os.VfsFat(self._sd)  # type: ignore[attr-defined]

        self._singleton_initialized = True

    def set_configuration(self, data):
        """Persist a configuration dictionary as JSON on the SD card."""
        uos.mount(self._vfs, "/sd")
        try:
            # Convert dictionary to JSON string and write it.
            json_data = json.dumps(data)
            with open("/sd/data.json", "w") as file:
                file.write(json_data)
        finally:
            # Always unmount, even on error, to avoid leaving VFS mounted.
            uos.umount("/sd")

    def if_exist_configuration(self):
        """Return True if a valid configuration file exists on the SD card."""
        uos.mount(self._vfs, "/sd")
        try:
            os.stat("/sd/data.json")
            return True
        except OSError as e:
            if e.errno == 2:  # ENOENT: file not found → not an error
                return False
            print("Errore SD stat: {}".format(e))
            return False
        finally:
            # Always unmount, even on unexpected exceptions.
            uos.umount("/sd")

    def get_configuration(self):
        """Read configuration JSON from SD and return it as a dict.

        Returns:
            dict | None: config data if file is present and valid, else None.
        """
        file_path = "/sd/data.json"
        try:
            uos.mount(self._vfs, "/sd")
            try:
                with open(file_path, "r") as file:
                    print("Reading from SD card")
                    data = json.load(file)
                return data
            finally:
                uos.umount("/sd")
        except OSError as e:
            if e.errno == 2:  # ENOENT: No such file or directory
                print("Errore: Il file '{}' non è stato trovato.".format(file_path))
            else:
                print("Errore: {}".format(e))
