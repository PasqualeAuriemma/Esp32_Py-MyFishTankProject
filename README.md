# PyFishTank — ESP32 MicroPython

Aquarium controller running on ESP32 with MicroPython.

---

## Release on ESP32 with `.mpy` files

### What are `.mpy` files and why use them

MicroPython can run plain `.py` source files, but on an ESP32 with limited RAM
this comes at a cost: every `.py` file is **parsed and compiled at import time**,
consuming heap memory and slowing down boot.

A `.mpy` file is a **pre-compiled bytecode** file produced by `mpy-cross`.
It contains the same instructions but in a binary format the interpreter can
load directly, skipping the parser entirely.

| | `.py` source | `.mpy` bytecode |
|---|---|---|
| Parsed at import | yes — allocates heap | no — loaded directly |
| RAM saved per module | — | ~10–15 KB |
| Boot time | slower | faster |
| Readable source | yes | no |
| Traceback line numbers | full | with `-O0`, partial with `-O2` |

On this project the difference is significant: importing all modules from
`.py` sources consumed ~86 KB of heap before `main()` even started, triggering
firmware-level OOM crashes (`abort()` on core 1). Switching to `.mpy` brought
the figure down to ~70 KB, leaving enough room for the WiFi driver (~100 KB
required) and the application.

### Prerequisites

```bash
pip install mpy-cross
pip install mpremote      # only needed for automatic upload
```

Verify that the `mpy-cross` version matches the MicroPython version on the
device:

```bash
mpy-cross --version
# MicroPython v1.27.0 on 2025-12-13; mpy-cross emitting mpy v6.3
```

The device firmware version is printed at boot or via:

```python
import sys; print(sys.version)
```

### Project structure

```
PyFishTank/
├── Helper/
├── Icons/
├── Manager/
├── Menu/
├── Modules/
├── Resource/
├── scripts/
│   └── build_mpy.bat      ← build script (Windows)
├── build/                 ← compiled output (Pymakr sync folder)
│   ├── Helper/Singleton.mpy
│   ├── Manager/*.mpy
│   ├── ...
│   ├── boot.py            ← always .py
│   ├── main.py            ← always .py
│   └── secrets.py         ← always .py
├── boot.py
├── main.py
├── secrets.py
└── pymakr.conf
```

`boot.py`, `main.py` and `secrets.py` are **never compiled** — MicroPython
looks for them by name with the `.py` extension.

### Build step by step

#### 1. Compile all modules

Run the build script from the `scripts/` folder:

```bat
scripts\build_mpy.bat
```

This compiles every `.py` file inside `Helper/`, `Icons/`, `Manager/`,
`Menu/`, `Modules/` and `Resource/` into `.mpy` and places the result
directly inside `build/`, mirroring the original directory structure.
`boot.py`, `main.py` and `secrets.py` are copied as-is.

For a **debug build** (preserves line numbers in tracebacks):

```bat
scripts\build_mpy.bat 0
```

The optimization levels map to `mpy-cross -O<n>`:

| Level | Effect |
|---|---|
| `0` | debug — full line numbers in tracebacks |
| `1` | removes debug info |
| `2` | removes assert statements and docstrings (default, saves most RAM) |
| `3` | aggressive — also inlines small functions |

#### 2. Upload to the device

**Option A — Pymakr (VS Code)**

`pymakr.conf` sets `"sync_folder": "build/"` so Pymakr uploads the compiled
output directly. Click **Upload** in the Pymakr panel.

**Option B — mpremote (terminal)**

```bat
scripts\build_mpy.bat 2 COM3
```

Passing the COM port as the second argument triggers automatic upload via
`mpremote` after compilation.

**Option C — manual mpremote**

```bash
mpremote connect COM3 cp build/boot.py    :boot.py
mpremote connect COM3 cp build/main.py    :main.py
mpremote connect COM3 cp build/secrets.py :secrets.py
mpremote connect COM3 cp -r build/Helper  :Helper
mpremote connect COM3 cp -r build/Manager :Manager
mpremote connect COM3 cp -r build/Menu    :Menu
mpremote connect COM3 cp -r build/Modules :Modules
mpremote connect COM3 cp -r build/Resource :Resource
mpremote connect COM3 cp -r build/Icons   :Icons
mpremote connect COM3 reset
```

#### 3. Verify on the device

Connect to the REPL and check that only `.mpy` files are present (except the
three root `.py` files):

```python
import os

def ls_r(path='.'):
    for f in os.listdir(path):
        full = path + '/' + f
        try:
            os.listdir(full)
            ls_r(full)
        except:
            print(full)

ls_r()
```

Expected output — no `.py` files except `boot.py`, `main.py`, `secrets.py`:

```
./boot.py
./main.py
./secrets.py
./Helper/Singleton.mpy
./Manager/wifiConnection.mpy
./Manager/viewer.mpy
...
```

If `.py` and `.mpy` files with the **same name** coexist, MicroPython loads
the `.py` and silently ignores the `.mpy`. Always remove stale `.py` files
after uploading their compiled counterparts.

### Important rules

- Never upload `.py` and `.mpy` with the same module name to the device.
- After changing any source file, **rebuild and re-upload** — the `.mpy` on
  the device will not update automatically.
- `boot.py` and `main.py` must always be `.py` — MicroPython does not look
  for `.mpy` versions of these entry-point files.
- The `secrets.py` file contains WiFi credentials and is intentionally kept
  as `.py` (plain text) so it can be edited directly on the device via the
  REPL without a full rebuild.

### Development workflow

```
edit .py source
      │
      ▼
scripts\build_mpy.bat       ← compiles to build/
      │
      ▼
Pymakr Upload  (or build_mpy.bat 2 COM3)
      │
      ▼
mpremote REPL  ← test on device
```

For quick iteration during development, upload the `.py` source directly
(skipping compilation) and switch back to `.mpy` for the final release.

---

## Hardware — SD card SPI wiring

```
ESP32 GPIO18  →  SD SCK  (clock)
ESP32 GPIO23  →  SD MOSI (data in)
ESP32 GPIO19  →  SD MISO (data out)
ESP32 GPIO5   →  SD CS   (chip select)
ESP32 3.3V    →  SD VCC
ESP32 GND     →  SD GND
```

Use an SDHC card (4–32 GB) formatted as FAT32.

---

## Hardware — I2C wiring

```
ESP32 GPIO22  →  SCL  (SSD1306 display + DS3231 RTC)
ESP32 GPIO21  →  SDA  (SSD1306 display + DS3231 RTC)
```

Scan I2C devices from the REPL:

```python
from machine import Pin, I2C
i2c = I2C(0, scl=Pin(22), sda=Pin(21), freq=400_000)
print([hex(d) for d in i2c.scan()])
# expected: ['0x3c', '0x68']
#   0x3c = SSD1306 OLED display
#   0x68 = DS3231 RTC
```
