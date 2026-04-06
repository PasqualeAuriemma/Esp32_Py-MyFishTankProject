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
import os, json, uos, gc  # type: ignore[import]
from Helper.Singleton import Singleton  # type: ignore[import]

_INIT_BAUD   = 1_000_000
_MOUNT_POINT = "/sd"
_CONFIG_FILE = "/SETTINGS.JSON"

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
        if getattr(self, "_singleton_initialized", False):
            return
        
        # Initialize the SD card SPI interface and wrap it with the sdcard driver.
        # baudrate=10 MHz: safe upper limit for most SD cards on hardware SPI bus 1.
        self._spi, self._sd = self._init_spi(sck_pin, mosi_pin, miso_pin, sd_pin)
        # Create a FAT VFS instance over the SD card (MicroPython-specific API).
        self._vfs = os.VfsFat(self._sd)  # type: ignore[attr-defined]

        self._singleton_initialized = True

    @staticmethod
    def _init_spi(sck_pin, mosi_pin, miso_pin, sd_pin):
        # Initialize the SD card SPI interface and wrap it with the sdcard driver.
        # baudrate=10 MHz: safe upper limit for most SD cards on hardware SPI bus 1.
        try:
            spi = SPI(2, baudrate=_INIT_BAUD, polarity=0, phase=0, sck=sck_pin, mosi=mosi_pin, miso=miso_pin)
            gc.collect()
            sd = sdcard.SDCard(spi, sd_pin)
            return spi, sd
        except Exception as e:
            print("[SDCardManager] SPI fallito: {} ...".format(e))
            raise OSError(19, "[SDCardManager] SD card non rilevata - controlla pin e collegamenti")

    def set_configuration(self, data):
        """Persist a configuration dictionary as JSON on the SD card."""
        import gc
        gc.collect()
        uos.mount(self._vfs, _MOUNT_POINT)
        try:
            # Convert dictionary to JSON string and write it.
            json_data = json.dumps(data)
            with open(_MOUNT_POINT + _CONFIG_FILE, "w") as file:
                file.write(json_data)
        finally:
            # Always unmount, even on error, to avoid leaving VFS mounted.
            uos.umount(_MOUNT_POINT)

    def _mount_do(self, fn):
        """Monta /sd, esegue fn(), smonta sempre. Ritorna il valore di fn()."""
        uos.mount(self._vfs, _MOUNT_POINT)
        try:
            return fn()
        finally:
            uos.umount(_MOUNT_POINT)        

    def if_exist_configuration(self):
        """Return True if a valid configuration file exists on the SD card."""
        try:
            uos.mount(self._vfs, _MOUNT_POINT)
            try:
                os.stat(_MOUNT_POINT + _CONFIG_FILE)
                return True
            except OSError as e:
                if e.errno == 2:  # ENOENT: file not found → not an error
                    return False
                print("[SDCardManager] Errore SD stat:", e)
                return False
            finally:
                uos.umount(_MOUNT_POINT)
        except OSError as e:
            if e.errno == 19:  # ENODEV: No device
                print("[SDCardManager] Errore: SD card non trovata o non inizializzata (ENODEV). Controlla collegamenti e SD.")
                return False
            print("[SDCardManager] Errore mount SD:", e)
            return False

    def get_configuration(self):
        """Read configuration JSON from SD and return it as a dict.

        Returns:
            dict | None: config data if file is present and valid, else None.
        """
        import gc
        gc.collect()
        file_path = _MOUNT_POINT + _CONFIG_FILE
        try:
            uos.mount(self._vfs, _MOUNT_POINT)
            try:
                with open(file_path, "r") as file:
                    #print("[SDCardManager] Reading from SD card")
                    data = json.load(file)
                return data
            finally:
                uos.umount(_MOUNT_POINT)
        except OSError as e:
            if e.errno == 2:  # ENOENT: No such file or directory
                print("[SDCardManager] Errore: Il file '{}' non è stato trovato.".format(file_path))
            else:
                print("[SDCardManager] Errore: {}".format(e))
