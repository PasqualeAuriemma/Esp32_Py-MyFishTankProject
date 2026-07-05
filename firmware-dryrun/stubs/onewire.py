"""
Mock di `onewire` per dry-run.
Simula un bus 1-Wire con un singolo device finto, così ds18b20.py
può scansionare e leggere senza hardware reale collegato.
"""


class OneWireError(Exception):
    pass


class OneWire:
    def __init__(self, pin):
        self.pin = pin
        print("[onewire MOCK] OneWire bus su pin={}".format(pin))

    def reset(self, required=False):
        return True

    def readbyte(self):
        return 0xFF

    def writebyte(self, value):
        pass

    def write(self, buf):
        pass

    def select_rom(self, rom):
        pass

    def scan(self):
        # Un solo device 1-Wire finto, ROM plausibile a 8 byte (family code 0x28 = DS18B20)
        fake_rom = bytearray([0x28, 0xFF, 0x3A, 0x77, 0x91, 0x16, 0x04, 0xC1])
        print("[onewire MOCK] scan() -> 1 device trovato (simulato)")
        return [fake_rom]
