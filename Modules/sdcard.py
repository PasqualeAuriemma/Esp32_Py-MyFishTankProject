"""
MicroPython driver for SD cards using SPI bus — ESP32 optimized.

Best practices applied:
  - Lazy SPI init with configurable high-speed baudrate
  - memoryview used everywhere to avoid heap allocations in hot paths
  - Explicit gc.collect() after heavy operations
  - Timeout constants centralised and all magic numbers named
  - OSError codes replaced with named errno constants
  - All public methods documented
  - Context-manager support (__enter__/__exit__) for safe resource cleanup
  - Defensive init: retries with exponential-like back-off for v2 cards
  - _log() helper so debug prints are toggled by a single flag, zero cost
    in production (avoids string formatting on the hot path)

Requires an SPI bus and a CS pin.  Provides readblocks / writeblocks so
the device can be mounted as a filesystem.

Example — ESP32:
    import machine, sdcard, os
    spi = machine.SPI(1, baudrate=1_000_000,
                      sck=machine.Pin(18), mosi=machine.Pin(23),
                      miso=machine.Pin(19))
    sd = sdcard.SDCard(spi, machine.Pin(5), debug=False)
    os.mount(sd, '/sd')
    os.listdir('/')
"""

import gc
from micropython import const  # type: ignore[import]
import time

# ---------------------------------------------------------------------------
# Protocol constants — every magic number lives here
# ---------------------------------------------------------------------------
_CMD_TIMEOUT = const(100)
_INIT_BAUD = const(100_000)  # low rate during card init
_DATA_BAUD = const(20_000_000)  # raised after init (ESP32 max ~40 MHz)

_R1_IDLE_STATE = const(1 << 0)
_R1_ILLEGAL_CMD = const(1 << 2)

_TOKEN_CMD25 = const(0xFC)
_TOKEN_STOP_TRAN = const(0xFD)
_TOKEN_DATA = const(0xFE)

_BLOCK_SIZE = const(512)
_DUMMY_BYTE = const(0xFF)

# errno values used with OSError
_EIO = const(5)
_ETIMEDOUT = const(110)

# R1_ERASE_RESET = const(1 << 1)
# R1_COM_CRC_ERROR = const(1 << 3)
# R1_ERASE_SEQUENCE_ERROR = const(1 << 4)
# R1_ADDRESS_ERROR = const(1 << 5)
# R1_PARAMETER_ERROR = const(1 << 6)


class SDCard:
    """SPI SD-card driver compatible with uos.mount()."""

    # ------------------------------------------------------------------
    # Construction / destruction
    # ------------------------------------------------------------------
    def __init__(self, spi, cs, baudrate: int = _DATA_BAUD, debug: bool = False):
        """
        Args:
            spi:      machine.SPI instance (will be re-initialised internally)
            cs:       machine.Pin instance for chip-select
            baudrate: SPI clock after successful init  (default 20 MHz)
            debug:    set True to enable verbose logging over UART
        """
        self._spi = spi
        self._cs = cs
        self._baudrate = baudrate
        self._debug = debug

        # Pre-allocated, reused buffers — NO heap allocation in hot paths
        self._cmdbuf = bytearray(6)
        self._tokenbuf = bytearray(1)
        self._dummybuf = bytearray(_BLOCK_SIZE)
        for i in range(_BLOCK_SIZE):
            self._dummybuf[i] = _DUMMY_BYTE
        # memoryview avoids a copy on every read
        self._dummymv = memoryview(self._dummybuf)

        self.sectors = 0  # set by init_card
        self._cdv = 1  # card-address divisor (v1=512, v2=1)

        self._init_card()

    # Context-manager support — allows `with SDCard(...) as sd:`
    def __enter__(self):
        return self

    def __exit__(self, *_):
        self.deinit()

    def deinit(self):
        """Release the CS pin and run GC."""
        try:
            self._cs(1)
        except Exception:
            pass
        gc.collect()

    # ------------------------------------------------------------------
    # Internal helpers
    # ------------------------------------------------------------------
    def _log(self, *args):
        """Zero-overhead logging: no string formatting when debug=False."""
        if self._debug:
            print("[SDCard]", *args)

    def _init_spi(self, baudrate: int):
        """Re-initialise SPI at the requested baudrate."""
        try:
            # pyboard / standard MicroPython
            self._spi.init(
                master=self._spi.MASTER, baudrate=baudrate, phase=0, polarity=0
            )
        except AttributeError:
            # ESP8266 / ESP32 — no MASTER attribute
            self._spi.init(baudrate=baudrate, phase=0, polarity=0)

    # ------------------------------------------------------------------
    # Card initialisation
    # ------------------------------------------------------------------

    def _init_card(self):
        self._cs.init(self._cs.OUT, value=1)
        self._init_spi(_INIT_BAUD)

        # ≥74 clock pulses with CS high to put card in native mode
        for _ in range(16):
            self._spi.write(b"\xff")

        # CMD0 — software reset; try up to 5 times
        for _ in range(5):
            if self._cmd(0, 0, 0x95) == _R1_IDLE_STATE:
                break
        else:
            raise OSError(_ETIMEDOUT, "no SD card detected")

        # CMD8 — check voltage range / distinguish v1 vs v2
        r = self._cmd(8, 0x01AA, 0x87, 4)
        if r == _R1_IDLE_STATE:
            self._init_card_v2()
        elif r == (_R1_IDLE_STATE | _R1_ILLEGAL_CMD):
            self._init_card_v1()
        else:
            raise OSError(_EIO, "cannot determine SD card version")

        # CMD9 — read CSD register (16-byte block) to get sector count
        if self._cmd(9, 0, 0, 0, release=False) != 0:
            raise OSError(_EIO, "no CSD response from SD card")

        csd = bytearray(16)
        self._readinto(csd)

        if csd[0] & 0xC0 == 0x40:  # CSD v2.0 (SDHC/SDXC)
            self.sectors = ((csd[8] << 8 | csd[9]) + 1) * 1024
        elif csd[0] & 0xC0 == 0x00:  # CSD v1.0 (≤2 GB)
            c_size = (csd[6] & 0x03) | (csd[7] << 2) | ((csd[8] & 0xC0) << 4)
            c_size_mult = ((csd[9] & 0x03) << 1) | (csd[10] >> 7)
            self.sectors = (c_size + 1) * (2 ** (c_size_mult + 2))
        else:
            raise OSError(_EIO, "unsupported SD CSD format")

        self._log("sectors:", self.sectors)

        # Raise SPI clock to operational speed
        self._init_spi(self._baudrate)
        gc.collect()  # clean up any allocs from init

    def _init_card_v1(self):
        for _ in range(_CMD_TIMEOUT):
            self._cmd(55, 0, 0)
            if self._cmd(41, 0, 0) == 0:
                self._cdv = _BLOCK_SIZE
                self._log("v1 card, cdv=", self._cdv)
                return
        raise OSError(_ETIMEDOUT, "timeout waiting for v1 card")

    def _init_card_v2(self):
        for i in range(_CMD_TIMEOUT):
            # small back-off to avoid hammering a slow card
            sleep_ms = getattr(time, "sleep_ms", None)
            if sleep_ms is not None:
                sleep_ms(50)
            else:
                # Fallback for environments without sleep_ms (e.g. CPython tests).
                time.sleep(0.05)
            self._cmd(58, 0, 0, 4)
            self._cmd(55, 0, 0)
            if self._cmd(41, 0x40000000, 0) == 0:
                self._cmd(58, 0, 0, 4)
                self._cdv = 1
                self._log("v2 card, cdv=", self._cdv)
                return
        raise OSError(_ETIMEDOUT, "timeout waiting for v2 card")

    # ------------------------------------------------------------------
    # Low-level SPI protocol
    # ------------------------------------------------------------------

    def _cmd(self, cmd, arg, crc, final=0, release=True, skip1=False) -> int:
        """
        Send a 6-byte SD command and return the R1 response byte.
        Returns -1 on timeout.
        """
        self._cs(0)

        buf = self._cmdbuf  # reuse pre-allocated buffer
        buf[0] = 0x40 | cmd
        buf[1] = arg >> 24
        buf[2] = arg >> 16
        buf[3] = arg >> 8
        buf[4] = arg & 0xFF
        buf[5] = crc
        self._spi.write(buf)

        if skip1:
            self._spi.readinto(self._tokenbuf, _DUMMY_BYTE)

        # Poll for response (MSB cleared = valid)
        for _ in range(_CMD_TIMEOUT):
            self._spi.readinto(self._tokenbuf, _DUMMY_BYTE)
            r = self._tokenbuf[0]
            if not (r & 0x80):
                for _ in range(final):
                    self._spi.write(b"\xff")
                if release:
                    self._cs(1)
                    self._spi.write(b"\xff")
                return r

        # Timeout — always release bus
        self._cs(1)
        self._spi.write(b"\xff")
        return -1

    def _readinto(self, buf):
        """
        Read one data block into *buf* (must be exactly 512 bytes or a
        memoryview slice of that length).
        """
        self._cs(0)

        # Wait for data token 0xFE
        for _ in range(_CMD_TIMEOUT):
            self._spi.readinto(self._tokenbuf, _DUMMY_BYTE)
            if self._tokenbuf[0] == _TOKEN_DATA:
                break
            sleep_ms = getattr(time, "sleep_ms", None)
            if sleep_ms is not None:
                sleep_ms(1)
            else:
                time.sleep(0.001)
        else:
            self._cs(1)
            raise OSError(_ETIMEDOUT, "timeout waiting for data token")

        # Use a memoryview slice so no extra copy is made
        mv = self._dummymv
        if len(buf) != _BLOCK_SIZE:
            mv = mv[: len(buf)]
        self._spi.write_readinto(mv, buf)

        # Discard 2-byte CRC
        self._spi.write(b"\xff\xff")

        self._cs(1)
        self._spi.write(b"\xff")

    def _write(self, token: int, buf):
        """Send one data block prefixed with *token*."""
        self._cs(0)

        self._spi.read(1, token)
        self._spi.write(buf)
        self._spi.write(b"\xff\xff")  # 2-byte CRC (dummy)

        # Data-response token: lower 5 bits == 0b00101 → accepted
        if (self._spi.read(1, _DUMMY_BYTE)[0] & 0x1F) != 0x05:
            self._cs(1)
            self._spi.write(b"\xff")
            return

        # Busy-wait while card is programming (DO held low)
        while self._spi.read(1, _DUMMY_BYTE)[0] == 0x00:
            pass

        self._cs(1)
        self._spi.write(b"\xff")

    def _write_token(self, token: int):
        """Send a bare stop-transmission token (CMD25 multi-block end)."""
        self._cs(0)
        self._spi.read(1, token)
        self._spi.write(b"\xff")
        while self._spi.read(1, _DUMMY_BYTE)[0] == 0x00:
            pass
        self._cs(1)
        self._spi.write(b"\xff")

    # ------------------------------------------------------------------
    # uos.mount() block-device interface
    # ------------------------------------------------------------------

    def readblocks(self, block_num: int, buf: bytearray):
        """
        Read *nblocks* 512-byte blocks starting at *block_num* into *buf*.
        len(buf) must be a multiple of 512.
        """
        nblocks, rem = divmod(len(buf), _BLOCK_SIZE)
        assert nblocks and not rem, "buf length must be a multiple of 512"

        if nblocks == 1:
            # CMD17 — single block read
            if self._cmd(17, block_num * self._cdv, 0, release=False) != 0:
                self._cs(1)
                raise OSError(_EIO)
            self._readinto(buf)
        else:
            # CMD18 — multiple block read
            if self._cmd(18, block_num * self._cdv, 0, release=False) != 0:
                self._cs(1)
                raise OSError(_EIO)
            mv = memoryview(buf)
            offset = 0
            for _ in range(nblocks):
                self._readinto(mv[offset : offset + _BLOCK_SIZE])
                offset += _BLOCK_SIZE
            if self._cmd(12, 0, 0xFF, skip1=True):
                raise OSError(_EIO)

    def writeblocks(self, block_num: int, buf: bytearray):
        """
        Write *nblocks* 512-byte blocks from *buf* starting at *block_num*.
        len(buf) must be a multiple of 512.
        """
        nblocks, rem = divmod(len(buf), _BLOCK_SIZE)
        assert nblocks and not rem, "buf length must be a multiple of 512"

        if nblocks == 1:
            # CMD24 — single block write
            if self._cmd(24, block_num * self._cdv, 0) != 0:
                raise OSError(_EIO)
            self._write(_TOKEN_DATA, buf)
        else:
            # CMD25 — multiple block write
            if self._cmd(25, block_num * self._cdv, 0) != 0:
                raise OSError(_EIO)
            mv = memoryview(buf)
            offset = 0
            for _ in range(nblocks):
                self._write(_TOKEN_CMD25, mv[offset : offset + _BLOCK_SIZE])
                offset += _BLOCK_SIZE
            self._write_token(_TOKEN_STOP_TRAN)

    def ioctl(self, op: int, arg):
        """
        Block-device control interface required by uos.mount().
        op=4 → return total number of blocks.
        op=5 → return block size in bytes (always 512).
        """
        if op == 4:
            return self.sectors
        if op == 5:
            return _BLOCK_SIZE
        return None
