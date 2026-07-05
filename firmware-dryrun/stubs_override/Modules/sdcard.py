"""
Mock di Modules.sdcard per dry-run Docker.
NON lancia eccezioni: si costruisce silenziosamente come una SD card
presente ma vuota, così SDCardManager.__init__ completa senza errori.
os.VfsFat(sd) verrà chiamato con questo mock — anche uos.VfsFat è
già mockato e accetta qualsiasi block device.
"""


class SDCard:
    def __init__(self, spi, cs, baudrate=1_000_000):
        print("[Modules.sdcard MOCK] SDCard inizializzata (assenza hardware simulata silenziosamente)")
        self.spi = spi
        self.cs = cs
        self.sectors = 1024

    def readblocks(self, block_num, buf):
        for i in range(len(buf)):
            buf[i] = 0

    def writeblocks(self, block_num, buf):
        pass

    def ioctl(self, op, arg):
        if op == 4:   # numero blocchi
            return self.sectors
        if op == 5:   # dimensione blocco
            return 512
        return 0
