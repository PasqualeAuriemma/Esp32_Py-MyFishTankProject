# Dockerfile — MicroPython Unix port per dry-run del firmware MyFishTank
#
# Compila l'interprete MicroPython REALE (non un'emulazione) per Linux x86,
# così la semantica del linguaggio (bytecode, gc, eccezioni, moduli json/time)
# è identica a quella dell'ESP32. I moduli specifici hardware (machine,
# network, urequests, display) sono sostituiti con mock in stubs/.

FROM debian:bookworm-slim AS builder

RUN apt-get update && apt-get install -y --no-install-recommends \
    git build-essential libffi-dev pkg-config \
    ca-certificates python3 \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /build
RUN git clone --depth 1 --branch v1.23.0 https://github.com/micropython/micropython.git

WORKDIR /build/micropython
RUN make -C mpy-cross
RUN make -C ports/unix submodules
RUN make -C ports/unix

# ─── Stage finale: immagine leggera con solo il binario compilato ───────────
FROM debian:bookworm-slim

RUN apt-get update && apt-get install -y --no-install-recommends \
    libffi8 \
    && rm -rf /var/lib/apt/lists/*

COPY --from=builder /build/micropython/ports/unix/build-standard/micropython /usr/local/bin/micropython
RUN chmod +x /usr/local/bin/micropython

WORKDIR /firmware

# Gli stub hardware (machine.py, network.py, urequests.py, ecc.)
# vengono copiati nella working directory: MicroPython li trova per primi
# nel path di import, prima di cercare moduli built-in con lo stesso nome.
COPY stubs/ ./stubs/
COPY stubs_override/ ./stubs_override/
COPY entrypoint.py .

# Il firmware reale viene montato come volume in docker-compose.
# MICROPYPATH: l'ordine conta — stubs_override viene prima di stubs e di
# /firmware, così i mock specifici (es. Modules/sdcard) vincono sui moduli reali.
ENV MICROPYPATH=/firmware/stubs_override:/firmware/stubs:/firmware

# entrypoint.py esegue boot.py poi main.py in sequenza, nello stesso
# processo, replicando l'ordine reale di avvio dell'ESP32 (dove boot.py
# popola builtins.WLAN_INSTANCE prima che main.py giri).
CMD ["micropython", "entrypoint.py"]
