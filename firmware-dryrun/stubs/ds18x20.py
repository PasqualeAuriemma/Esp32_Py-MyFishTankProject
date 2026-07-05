"""
Mock di `ds18x20` per dry-run.
Simula letture di temperatura realistiche (deriva lenta intorno a 25°C),
così ds18b20.py può fare convert_temp()/read_temp() senza sensore reale.
"""

import random


class DS18X20:
    def __init__(self, onewire_bus):
        self.ow = onewire_bus
        self._temp = 25.0  # punto di partenza plausibile

    def scan(self):
        return self.ow.scan()

    def convert_temp(self):
        # Simula una piccola deriva ad ogni conversione, come un sensore reale
        self._temp += random.gauss(0, 0.1)
        self._temp = max(15.0, min(35.0, self._temp))
        print("[ds18x20 MOCK] convert_temp() avviata")

    def read_temp(self, rom):
        print("[ds18x20 MOCK] read_temp(rom={}) -> {:.2f}".format(
            bytes(rom).hex(), self._temp))
        return round(self._temp, 2)
