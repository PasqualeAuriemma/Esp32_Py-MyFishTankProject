"""
Mock di `framebuf` per dry-run.
Simula FrameBuffer come contenitore passivo: i metodi di disegno (fill,
text, pixel, ecc.) aggiornano solo un buffer interno senza renderizzare
nulla visivamente — l'obiettivo è verificare che ssd1306.py e il codice
applicativo che lo usa (Viewer, menu) girino senza eccezioni, non
vedere il display reale.
"""

MONO_VLSB = 0
MONO_HLSB = 1
MONO_HMSB = 2
RGB565 = 3
GS2_HMSB = 4
GS4_HMSB = 5
GS8 = 6


class FrameBuffer:
    def __init__(self, buffer, width, height, format, stride=None):
        self.buffer = buffer
        self.width = width
        self.height = height
        self.format = format

    def fill(self, c):
        pass

    def pixel(self, x, y, c=None):
        return 0 if c is None else None

    def hline(self, x, y, w, c):
        pass

    def vline(self, x, y, h, c):
        pass

    def line(self, x1, y1, x2, y2, c):
        pass

    def rect(self, x, y, w, h, c):
        pass

    def fill_rect(self, x, y, w, h, c):
        pass

    def text(self, string, x, y, c=1):
        pass

    def scroll(self, xstep, ystep):
        pass

    def blit(self, fbuf, x, y, key=-1, palette=None):
        pass
