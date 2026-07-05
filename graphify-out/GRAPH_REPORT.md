# Graph Report - Esp32_Py-MyFishTankProject  (2026-07-05)

## Corpus Check
- 105 files · ~54,116 words
- Verdict: corpus is large enough that graph structure adds value.

## Summary
- 1142 nodes · 1762 edges · 165 communities (113 shown, 52 thin omitted)
- Extraction: 92% EXTRACTED · 8% INFERRED · 0% AMBIGUOUS · INFERRED: 149 edges (avg confidence: 0.5)
- Token cost: 0 input · 0 output

## Graph Freshness
- Built from commit: `1f5ff0ce`
- Run `git rev-parse HEAD` and compare to check if the graph is stale.
- Run `graphify update .` after code changes (no API cost).

## Community Hubs (Navigation)
- [[_COMMUNITY_Community 0|Community 0]]
- [[_COMMUNITY_Community 1|Community 1]]
- [[_COMMUNITY_Community 2|Community 2]]
- [[_COMMUNITY_Community 3|Community 3]]
- [[_COMMUNITY_Community 4|Community 4]]
- [[_COMMUNITY_Community 5|Community 5]]
- [[_COMMUNITY_Community 6|Community 6]]
- [[_COMMUNITY_Community 7|Community 7]]
- [[_COMMUNITY_Community 8|Community 8]]
- [[_COMMUNITY_Community 9|Community 9]]
- [[_COMMUNITY_Community 10|Community 10]]
- [[_COMMUNITY_Community 11|Community 11]]
- [[_COMMUNITY_Community 12|Community 12]]
- [[_COMMUNITY_Community 13|Community 13]]
- [[_COMMUNITY_Community 14|Community 14]]
- [[_COMMUNITY_Community 15|Community 15]]
- [[_COMMUNITY_Community 16|Community 16]]
- [[_COMMUNITY_Community 17|Community 17]]
- [[_COMMUNITY_Community 18|Community 18]]
- [[_COMMUNITY_Community 19|Community 19]]
- [[_COMMUNITY_Community 20|Community 20]]
- [[_COMMUNITY_Community 21|Community 21]]
- [[_COMMUNITY_Community 22|Community 22]]
- [[_COMMUNITY_Community 23|Community 23]]
- [[_COMMUNITY_Community 24|Community 24]]
- [[_COMMUNITY_Community 25|Community 25]]
- [[_COMMUNITY_Community 26|Community 26]]
- [[_COMMUNITY_Community 27|Community 27]]
- [[_COMMUNITY_Community 28|Community 28]]
- [[_COMMUNITY_Community 29|Community 29]]
- [[_COMMUNITY_Community 30|Community 30]]
- [[_COMMUNITY_Community 31|Community 31]]
- [[_COMMUNITY_Community 32|Community 32]]
- [[_COMMUNITY_Community 33|Community 33]]
- [[_COMMUNITY_Community 34|Community 34]]
- [[_COMMUNITY_Community 35|Community 35]]
- [[_COMMUNITY_Community 36|Community 36]]
- [[_COMMUNITY_Community 37|Community 37]]
- [[_COMMUNITY_Community 38|Community 38]]
- [[_COMMUNITY_Community 39|Community 39]]
- [[_COMMUNITY_Community 40|Community 40]]
- [[_COMMUNITY_Community 41|Community 41]]
- [[_COMMUNITY_Community 42|Community 42]]
- [[_COMMUNITY_Community 43|Community 43]]
- [[_COMMUNITY_Community 44|Community 44]]
- [[_COMMUNITY_Community 45|Community 45]]
- [[_COMMUNITY_Community 46|Community 46]]
- [[_COMMUNITY_Community 47|Community 47]]
- [[_COMMUNITY_Community 48|Community 48]]
- [[_COMMUNITY_Community 49|Community 49]]
- [[_COMMUNITY_Community 50|Community 50]]
- [[_COMMUNITY_Community 51|Community 51]]
- [[_COMMUNITY_Community 52|Community 52]]
- [[_COMMUNITY_Community 53|Community 53]]
- [[_COMMUNITY_Community 54|Community 54]]
- [[_COMMUNITY_Community 55|Community 55]]
- [[_COMMUNITY_Community 56|Community 56]]
- [[_COMMUNITY_Community 57|Community 57]]
- [[_COMMUNITY_Community 63|Community 63]]
- [[_COMMUNITY_Community 64|Community 64]]
- [[_COMMUNITY_Community 65|Community 65]]
- [[_COMMUNITY_Community 66|Community 66]]
- [[_COMMUNITY_Community 67|Community 67]]
- [[_COMMUNITY_Community 68|Community 68]]
- [[_COMMUNITY_Community 69|Community 69]]
- [[_COMMUNITY_Community 70|Community 70]]
- [[_COMMUNITY_Community 71|Community 71]]
- [[_COMMUNITY_Community 72|Community 72]]
- [[_COMMUNITY_Community 73|Community 73]]
- [[_COMMUNITY_Community 74|Community 74]]
- [[_COMMUNITY_Community 75|Community 75]]
- [[_COMMUNITY_Community 76|Community 76]]
- [[_COMMUNITY_Community 77|Community 77]]
- [[_COMMUNITY_Community 78|Community 78]]
- [[_COMMUNITY_Community 79|Community 79]]
- [[_COMMUNITY_Community 80|Community 80]]
- [[_COMMUNITY_Community 89|Community 89]]
- [[_COMMUNITY_Community 90|Community 90]]
- [[_COMMUNITY_Community 92|Community 92]]
- [[_COMMUNITY_Community 93|Community 93]]
- [[_COMMUNITY_Community 94|Community 94]]
- [[_COMMUNITY_Community 95|Community 95]]
- [[_COMMUNITY_Community 96|Community 96]]
- [[_COMMUNITY_Community 97|Community 97]]
- [[_COMMUNITY_Community 98|Community 98]]
- [[_COMMUNITY_Community 100|Community 100]]
- [[_COMMUNITY_Community 101|Community 101]]
- [[_COMMUNITY_Community 103|Community 103]]
- [[_COMMUNITY_Community 104|Community 104]]
- [[_COMMUNITY_Community 105|Community 105]]
- [[_COMMUNITY_Community 107|Community 107]]
- [[_COMMUNITY_Community 108|Community 108]]
- [[_COMMUNITY_Community 111|Community 111]]

## God Nodes (most connected - your core abstractions)
1. `Config` - 113 edges
2. `Viewer` - 66 edges
3. `WifiConnection` - 50 edges
4. `MenuList` - 49 edges
5. `Menu` - 36 edges
6. `DS3231_RTC` - 34 edges
7. `MockDisplay` - 34 edges
8. `NTP` - 31 edges
9. `SSD1306` - 25 edges
10. `TestMain` - 25 edges

## Surprising Connections (you probably didn't know these)
- `SDCardManager` --uses--> `Singleton`  [INFERRED]
  Manager/sdCardManager.py → Helper/Singleton.py
- `WifiConnection` --uses--> `Singleton`  [INFERRED]
  Manager/wifiConnection.py → Helper/Singleton.py
- `Config` --uses--> `Singleton`  [INFERRED]
  Resource/Config.py → Helper/Singleton.py
- `_FakeADC` --uses--> `NTP`  [INFERRED]
  tests/test_suite.py → Manager/ntpManager.py
- `_FakeDS18X20` --uses--> `NTP`  [INFERRED]
  tests/test_suite.py → Manager/ntpManager.py

## Import Cycles
- None detected.

## Communities (165 total, 52 thin omitted)

### Community 0 - "Community 0"
Cohesion: 0.11
Nodes (9): I2C, main(), Viewer — high-level UI controller for PyTank (ESP32 / MicroPython).  Responsib, Args:             i2c: Pre-configured I2C bus shared by the OLED display and th, Initialize relay states based on Config., Wrapper for all relay pins (outputs)., Relays, BoardPins (+1 more)

### Community 1 - "Community 1"
Cohesion: 0.06
Nodes (14): _FakeWLAN, MockRTC, MockWifi, Se OSF è True, NTP.sync() deve essere chiamato., Simula WifiConnection con controllo preciso degli stati., Test su Manager.WifiConnection con WLAN mock., _send_value_to_web_sync deve connettersi automaticamente se non connesso., send_value_to_web deve accodare il dato senza connettersi immediatamente. (+6 more)

### Community 2 - "Community 2"
Cohesion: 0.08
Nodes (9): MockDisplay, Simula SSD1306: registra tutte le chiamate senza disegnare nulla., Ritorna True se il metodo è stato chiamato almeno una volta., Test su Viewer con display, RTC, WiFi e relay tutti mockati.     Verifica la sta, Costruisce un Viewer con tutti i mock iniettati., run() deve chiamare show() quando si esce dal menu., run() setta exit_menu=True quando is_enabled_menu=True., show_rele_symbol() deve usare il display. (+1 more)

### Community 3 - "Community 3"
Cohesion: 0.07
Nodes (19): MockSDCard, Alla chiusura del menu la config deve essere salvata sulla SD., Use case: utente preme OK → entra nel menu → toggle LIGHTS.         Il relay fis, Use case: entrare in MAINTENANCE deve spegnere tutti i processi         automati, Use case: tornare in AUTO deve ripristinare tutti i processi         che erano a, Simula SDCardManager: storage in dizionario in memoria., Test sulla logica di SDCardManager usando MockSDCard.     Non testa il bus SPI (, set_configuration → get_configuration deve produrre dati identici. (+11 more)

### Community 4 - "Community 4"
Cohesion: 0.07
Nodes (11): - image array byte in hex, Method to write Text on OLED/LCD Displays with a variable font size          A, Clear the same portion, Scroll out horizontally         It accepts as argument a number that controls t, Continuous horizontal scroll         If you want to scroll the screen in and ou, Scroll in vertically         The scroll_in_screen_v(screen) scrolls in the cont, Scroll out vertically         You can use the scroll_out_screen_v(speed) functi, Continuous vertical scroll         If you want to scroll the screen in and out (+3 more)

### Community 5 - "Community 5"
Cohesion: 0.05
Nodes (4): calculate_median(), get_median(), get_median_ph(), get_median_temperature()

### Community 6 - "Community 6"
Cohesion: 0.09
Nodes (17): PHSensor, Zero-cost when debug=False — string never formatted., Fill _raw_buf with ADC readings, sort in-place, trim the outer 20 %,         and, Scale the calibrated slope from the calibration temperature (25 °C)         to t, Convert compensated voltage to pH using the two-point calibration model:, Update the water temperature for Nernst slope compensation.         Call this af, Read the raw ADC voltage (V) from the pH probe without conversion.         Usefu, Read the pH value, temperature-compensated using the Nernst slope.          Retu (+9 more)

### Community 7 - "Community 7"
Cohesion: 0.09
Nodes (16): Zero-cost debug logging: string is never formatted when debug=False., Read `_samples` ADC values into the pre-allocated buffer and         return thei, Convert ADC raw value to voltage using VREF and ADC resolution., Apply the DFRobot polynomial to get raw EC in µS/cm.         Formula: EC = (133., Apply temperature compensation to normalise EC to 25 °C reference.         EC_25, Update the water temperature used for EC compensation.         Call this wheneve, Update the calibration K factor.         Obtain K by measuring a known-EC standa, Read the raw ADC voltage (V) without any EC conversion.         Useful for manua (+8 more)

### Community 8 - "Community 8"
Cohesion: 0.06
Nodes (12): Config, Tuple of available operating mode names: AUTO, MAINTENANCE, STAND BY., Set start/end time. Avoid mutable default: pass [sh, sm, eh, em] or None for zer, Return (start_hour, start_minutes, end_hour, end_minutes) for the lighting timer, Enable or disable temperature reading and remote sending simultaneously., Single source of config: timer, auto/heater, relays, sensors, modes. One instanc, Export config to a JSON-serializable dict., Lighting timer start hour (0–23). (+4 more)

### Community 9 - "Community 9"
Cohesion: 0.09
Nodes (15): Handles the WiFi connection for the device. Singleton: one instance per device., Disconnects from the WiFi network and deactivates the interface., Restituisce lo stato leggibile della connessione., Restituisce l'indirizzo IP della connessione., Accoda un valore per l'invio asincrono in background.         Ritorna sempre Tr, Verifica se ci sono dati in coda da inviare., Estrae il primo elemento dalla coda., Invia un valore a un endpoint web in modo sincrono.         Questa funzione dev (+7 more)

### Community 10 - "Community 10"
Cohesion: 0.07
Nodes (11): Rimuove l'istanza Singleton per consentire re-inizializzazione nei test., Test su Resource.Config — logica pura, nessun hardware., Al boot tutti i relay devono essere False., Al boot tutti i sensori devono essere disabilitati., Il flag sending è False anche se la lettura fosse True., get_on_off_ec_sending deve essere False se ec è False., Passare a MAINTENANCE deve disabilitare tutti i processi automatici., Tornare in AUTO deve ripristinare lo stato precedente. (+3 more)

### Community 11 - "Community 11"
Cohesion: 0.09
Nodes (15): DS18B20, Zero-overhead: nessuna stringa formattata se debug=False., Scansiona il bus e aggiorna la lista ROM interna., Forza una nuova scansione del bus.         Utile se si aggiungono/rimuovono sens, Numero di sensori rilevati sul bus., Lista degli indirizzi ROM rilevati (bytearray da 8 byte ciascuno)., Risoluzione ADC corrente in bit., Cambia la risoluzione di tutti i sensori sul bus.         Aggiorna anche il temp (+7 more)

### Community 12 - "Community 12"
Cohesion: 0.15
Nodes (4): ConfirmItem, EnumItem, MenuRow, Aggiorna il label decorator prima del disegno. Sovrascrivere nelle sottoclassi.

### Community 13 - "Community 13"
Cohesion: 0.11
Nodes (13): MenuList, Azzera l'indice di selezione alla prima voce., Restituisce la lista memorizzata nella cache delle voci attualmente visibili., Restituisce il numero di voci attualmente visibili., Sposta la selezione di un passo su, con wrap-around all'ultima voce., Sposta la selezione di un passo giù, con wrap-around alla prima voce., Restituisce la voce visibile a *position* aggiornando il suo stato attivo., Chiama _on_enter la prima volta che la lista viene mostrata (lazy build). (+5 more)

### Community 14 - "Community 14"
Cohesion: 0.09
Nodes (9): MenuHeaterManage, Disegna questa schermata info e rimane su di essa., Editor soglie min/max temperatura per il controllo automatico del riscaldatore., Sposta selezione su (direction < 0) o giù (direction > 0) e ridisegna., Cambia campo attivo: right (direction < 0) o left, poi ridisegna., Torna alla schermata radice e azzera l'indice di selezione., Azzera la lista corrente e risale di un livello., Applica questa opzione enum, aggiorna il marcatore di selezione del padre e torn (+1 more)

### Community 15 - "Community 15"
Cohesion: 0.17
Nodes (5): MenuCallback, Valida che *param* sia callable o una tupla ``(callable, arg)``.          Args, Invoca *func* con eventuali argomenti legati più eventuali *args* extra., Chiama ``state_callback`` e restituisce lo stato booleano corrente., Aggiorna il label a destra per riflettere lo stato corrente del toggle.

### Community 16 - "Community 16"
Cohesion: 0.08
Nodes (14): MenuError, Renderizza lo stato WiFi all'interno di un rettangolo bordo., Restituisce il booleano corrente dello stato di connessione., Inverte il flag status (chiamato esternamente per riflettere i cambiamenti di co, Disegna *text* centrato orizzontalmente alla posizione verticale *y*., Schermata di errore full-screen con testo word-wrapped.      La stringa ``mess, Torna alla vista padre (es. dopo aver letto il messaggio di errore)., Disegna la schermata di errore e rimane su di essa. (+6 more)

### Community 17 - "Community 17"
Cohesion: 0.13
Nodes (12): background_sender(), main(), ESP32 MicroPython Simulator ============================ Simula il firmware Micr, Processa tutti gli elementi in coda (chiamato dal background thread)., POST con body JSON (json.dumps), allineato a wifiConnection.py.         Il token, Thread di background che svuota la coda (come farebbe il secondo thread sull'ESP, Simula letture realistiche dei sensori con deriva lenta nel tempo,     come fare, Ritorna un dict con tutti i valori simulati aggiornati. (+4 more)

### Community 18 - "Community 18"
Cohesion: 0.20
Nodes (4): Disable auto mode: save current on/off state to _temp and turn features off., Re-enable auto: restore on/off state from _temp., Load config from a dict (e.g. parsed JSON). Uses setters; fixes key/typo bugs., Switch operating mode: 0 = AUTO (restore saved state), 1 = MAINTENANCE (suspend

### Community 19 - "Community 19"
Cohesion: 0.32
Nodes (16): _build_ec(), _build_filter_auto(), _build_heater_auto(), build_menu(), _build_ph(), _build_relays(), _build_root(), _build_sensors() (+8 more)

### Community 20 - "Community 20"
Cohesion: 0.06
Nodes (11): ButtonItem, MenuSetDateTime, MenuSetTimer, Limita *value* all'intervallo [0, max_value) con wrap-around a entrambi i lati., Conferma le impostazioni del timer e restituisce un ButtonItem che le salva., Valida min < max; restituisce ButtonItem o MenuError., Esegue il callback e ridisegna la lista padre., Editor interattivo di data e ora su display OLED completo.      L'utente navig (+3 more)

### Community 21 - "Community 21"
Cohesion: 0.10
Nodes (6): MenuItem, MenuView, MenuWifiInfo, Menu framework per display OLED SSD1306 su ESP32 / MicroPython (progetto PyTank), Schermata informativa in sola lettura sullo stato della connessione WiFi., Torna al menu padre senza eseguire alcuna azione.

### Community 22 - "Community 22"
Cohesion: 0.29
Nodes (3): _crc7(), MicroPython driver for SD cards using SPI bus.  Requires an SPI bus and a CS p, SDCard

### Community 23 - "Community 23"
Cohesion: 0.13
Nodes (10): Remove the singleton instance so it can be garbage-collected., Return the current instance or None if reset. Does not create one., Singleton, # Demonstrates ESP32 interface to MicroSD Card Adapter # Create a text file and, Keyboard, Initialize the ADC channel used by the keyboard.          Args:             p, Read the analog keyboard and translate it into a numeric key code.          Re, # NOTE: at the moment all branches use the same threshold. In a typical (+2 more)

### Community 24 - "Community 24"
Cohesion: 0.13
Nodes (6): _FakeResponse, _FakeWDT, MockDS18B20, Esegui la suite con output verboso e riepilogo finale., Simula DS18B20Sensor., run_tests()

### Community 25 - "Community 25"
Cohesion: 0.06
Nodes (18): DS3231_RTC, Get the DS1307 I2C bus address          :returns:   DS1307 I2C bus address, Get the current time.         Reads datetime once — avoids 3 separate I2C reads, Get the year from the RTC          :returns:   Year of RTC         :rtype:, Get the month from the RTC          :returns:   Month of RTC         :rtype:, Get the day from the RTC          :returns:   Day of RTC         :rtype:, Get the hour from the RTC          :returns:   Hour of RTC         :rtype:, Get the minute from the RTC          :returns:   Minute of RTC         :rtype (+10 more)

### Community 26 - "Community 26"
Cohesion: 0.18
Nodes (6): Read configuration JSON from SD and return it as a dict.          Returns:, Singleton manager for SD card access on ESP32., Persist a configuration dictionary as JSON on the SD card., Monta /sd, esegue fn(), smonta sempre. Ritorna il valore di fn()., Return True if a valid configuration file exists on the SD card., SDCardManager

### Community 27 - "Community 27"
Cohesion: 0.17
Nodes (4): MockRelay, MockRelays, Simula un singolo pin relay., Simula il banco relay.

### Community 28 - "Community 28"
Cohesion: 0.17
Nodes (7): NTP, Args:             wifi: istanza WifiConnection             rtc:  istanza DS323, Connette il WiFi, sincronizza l'RTC interno dell'ESP32 via NTP,         copia l, Sincronizza il DS3231 con NTP.      Dipendenze iniettate nel costruttore — Ntp, _FakeConst, _FakeFrameBuffer, micropython.const() su CPython ritorna il valore invariato.

### Community 29 - "Community 29"
Cohesion: 0.14
Nodes (7): ListItem, Renderizza questa riga nello slot verticale *pos* della lista.          Il dis, Avvolge *item* in un proxy ``ListItem`` e lo appende a questa lista., Setter esplicito per lo stato di cache visibile, usato nei test., Esegue ``change_callback``, aggiorna il decorator e ridisegna la lista padre., Restituisce il decorator dell'oggetto avvolto, oppure '>' come freccia di defaul, Sincronizza il decorator di questo proxy dall'oggetto avvolto prima del disegno.

### Community 30 - "Community 30"
Cohesion: 0.18
Nodes (6): Exception, Menu, Controller di alto livello che gestisce quale schermata è attiva.      Funge d, Installa screen come radice e la imposta come schermata corrente., _FakeDS18X20, _OneWireError

### Community 31 - "Community 31"
Cohesion: 0.14
Nodes (8): Synchronize RTC with NTP server and send a test EC sample to the web endpoint., Toggle the light relay (relay 0) and sync the flag in Config.          Called, Toggle the fish-feeder relay (relay 3) and sync the flag in Config., Toggle the automatic filter scheduling mode in Config (no direct relay output)., Toggle the EC (electrical conductivity) sensor activation flag in Config., High-level UI/controller for the OLED, RTC, WiFi and relays on ESP32., Show the splash / intro animation on first boot (currently disabled in __init__), Viewer

### Community 32 - "Community 32"
Cohesion: 0.24
Nodes (5): Set alarm1, can match mday, wday, hour, minute, second          time    : tupl, Get/set alarm 2 (can match minute, hour, day)          time    : tuple, (minut, Enable/disable interrupt for alarm1, alarm2 or both.          Enabling the int, Check if the alarm flag is set and clear the alarm flag, Convert decimal to binary coded decimal (BCD) format          :param      valu

### Community 33 - "Community 33"
Cohesion: 0.20
Nodes (5): Resolve freq value from JSON: int index or string (e.g. '1') -> index., Set temperature web-update frequency. Accepts an int FREQ index or a FREQ string, Set EC web-update frequency. Accepts an int FREQ index or a FREQ string., Set pH web-update frequency. Accepts an int FREQ index or a FREQ string., Set filter-cycle frequency. Accepts an int FREQ index or a FREQ string.

### Community 34 - "Community 34"
Cohesion: 0.15
Nodes (7): Get the current datetime          (2023, 4, 18, 0, 10, 34, 4, 108), Set datetime          (2023, 4, 18, 20, 23, 38, 1, 108) by time.gmtime(time.ti, Get the day of the year          :param      year:   The year         :type, Determines whether the specified year is a leap year.          :param      yea, Returns the oscillator stop flag (OSF).          1 indicates that the oscillat, Clear the oscillator stop flag (OSF), Convert binary coded decimal (BCD) format to decimal          :param      valu

### Community 35 - "Community 35"
Cohesion: 0.25
Nodes (5): Send the current EC reading to the remote web endpoint.          Called by the, Send the current pH reading to the remote web endpoint.          Called by the, Send the current temperature reading to the remote web endpoint.          Call, Current time string displayed on the main screen (format HH:MM:SS)., Set the displayed time string (HH:MM:SS).

### Community 36 - "Community 36"
Cohesion: 0.25
Nodes (4): Render the idle home screen onto the OLED framebuffer.          Layout (128 ×, Render the relay-status strip in the bottom 12 rows of the OLED.          Draw, Callback invoked by the menu framework when the user exits the menu., Display state machine — call once per iteration of the main loop.          Sta

### Community 40 - "Community 40"
Cohesion: 0.83
Nodes (3): bandColor(), renderVolumeGauge(), showVolumes()

### Community 41 - "Community 41"
Cohesion: 0.22
Nodes (4): MenuMonitoringSensor, Aggiorna le letture del sensore e ridisegna se il monitoraggio live è attivo., Disattiva il monitoraggio live e naviga al menu padre., Attiva/disattiva il monitoraggio live e ridisegna; rimane su questa schermata.

### Community 48 - "Community 48"
Cohesion: 0.47
Nodes (3): getModal(), hideModal(), showModal()

### Community 50 - "Community 50"
Cohesion: 0.33
Nodes (3): Toggle the transmission of EC data to the remote web server., Toggle the transmission of pH data to the remote web server., Toggle the transmission of temperature data to the remote web server.

## Knowledge Gaps
- **52 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `Config` connect `Community 8` to `Community 0`, `Community 1`, `Community 2`, `Community 3`, `Community 10`, `Community 18`, `Community 23`, `Community 24`, `Community 27`, `Community 28`, `Community 30`, `Community 31`, `Community 33`, `Community 37`, `Community 39`, `Community 42`, `Community 51`, `Community 52`, `Community 53`, `Community 54`, `Community 55`, `Community 56`, `Community 63`, `Community 67`, `Community 71`, `Community 78`, `Community 79`, `Community 80`, `Community 89`, `Community 90`, `Community 92`, `Community 93`, `Community 94`, `Community 95`, `Community 96`, `Community 97`, `Community 98`, `Community 100`, `Community 101`, `Community 103`, `Community 104`, `Community 105`, `Community 107`, `Community 108`, `Community 111`?**
  _High betweenness centrality (0.188) - this node is a cross-community bridge._
- **Why does `Viewer` connect `Community 31` to `Community 0`, `Community 1`, `Community 2`, `Community 3`, `Community 8`, `Community 9`, `Community 10`, `Community 24`, `Community 25`, `Community 27`, `Community 28`, `Community 30`, `Community 35`, `Community 36`, `Community 37`, `Community 39`, `Community 42`, `Community 49`, `Community 50`, `Community 64`, `Community 65`, `Community 66`, `Community 68`, `Community 69`, `Community 70`, `Community 72`, `Community 73`, `Community 74`, `Community 75`, `Community 76`, `Community 77`?**
  _High betweenness centrality (0.157) - this node is a cross-community bridge._
- **Why does `Menu` connect `Community 30` to `Community 0`, `Community 1`, `Community 2`, `Community 3`, `Community 37`, `Community 39`, `Community 42`, `Community 10`, `Community 12`, `Community 14`, `Community 16`, `Community 21`, `Community 24`, `Community 27`, `Community 28`, `Community 31`?**
  _High betweenness centrality (0.106) - this node is a cross-community bridge._
- **Are the 25 inferred relationships involving `Config` (e.g. with `Viewer` and `Singleton`) actually correct?**
  _`Config` has 25 INFERRED edges - model-reasoned connections that need verification._
- **Are the 28 inferred relationships involving `Viewer` (e.g. with `WifiConnection` and `Menu`) actually correct?**
  _`Viewer` has 28 INFERRED edges - model-reasoned connections that need verification._
- **Are the 25 inferred relationships involving `WifiConnection` (e.g. with `Viewer` and `Singleton`) actually correct?**
  _`WifiConnection` has 25 INFERRED edges - model-reasoned connections that need verification._
- **Are the 23 inferred relationships involving `MenuList` (e.g. with `_FakeADC` and `_FakeConst`) actually correct?**
  _`MenuList` has 23 INFERRED edges - model-reasoned connections that need verification._