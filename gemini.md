# Piano di Sviluppo Aggiornato: ESP32 Fish Tank & Web App

Questo documento contiene il piano d'azione dettagliato per ottimizzare il firmware MicroPython su ESP32, implementare l'architettura dual-core asincrona per il WiFi, correggere e modernizzare la Web App PHP/HTML/JS, ed impostare un ambiente di test locale (localhost) tramite Docker Compose.

---

## 1. Analisi dello Stato Attuale & Considerazioni Tecniche

### A. Firmware ESP32 (MicroPython)
* **Thread e Core**: Attualmente `main.py` esegue la lettura dei sensori su un thread secondario (`sensor_loop` su Core 1) e l'interfaccia utente (OLED + tastiera) sul thread principale (Core 0).
* **Problema WiFi Sincrono**: L'invio dei dati al web tramite `send_value_to_web` e la sincronizzazione NTP vengono eseguiti in modo sincrono sul thread principale. Se il server risponde lentamente o la rete ha problemi, l'interfaccia si blocca e c'è il rischio di far scattare il Watchdog Timer (WDT) impostato a 30s.

### B. Web App (PHP, HTML, CSS, JS)
Esaminando la cartella `webApp/`, ho riscontrato diversi aspetti critici da correggere e migliorare:
1. **Mancanza di Sanitarizzazione (SQL Injection)**: I file `takeTemp.php`, `takePh.php` e `takeEc.php` inseriscono i parametri POST direttamente nelle query SQL senza prepared statements. Chiunque potrebbe inviare richieste POST manipolate per compromettere il database.
2. **Disallineamento dei Nomi dei File e dei Parametri (Bug Critico)**:
   * Nel firmware dell'ESP32, la chiave del pH viene passata come `"PH"`. Questo genera una richiesta a `takePH.php` con parametro POST `"PH"`.
   * Sul server, il file si chiama `takePh.php` (con la 'h' minuscola) e cerca la chiave `$_POST["Ph"]`. 
   * Su sistemi case-sensitive (come i server Linux di Altervista), la richiesta a `takePH.php` restituirà un **errore 404** e, anche se trovasse il file, `$_POST["Ph"]` risulterebbe vuoto poiché l'ESP32 invia `"PH"`.
3. **Disallineamento dei Database**:
   * Il file `connection.php` tenta di connettersi al database `my_myfishtank`.
   * Il file `localhost.sql` crea e inizializza un database chiamato `my_forestaaquatica`. Questo causerà errori di connessione sul server locale.
4. **Hardcoding delle Credenziali**:
   * Le credenziali del database in `connection.php` sono codificate direttamente nel codice. È preferibile utilizzare variabili d'ambiente per consentire una facile configurazione tra localhost e produzione.
5. **Modernizzazione della UI**:
   * Il sito utilizza il template Corona Admin. Contiene molte risorse pesanti inutilizzate (owl-carousel, mappe, fogli di stile esterni). Consigliamo di modernizzare l'interfaccia applicando un design premium Glassmorphism (sfondi semitrasparenti sfocati, modalità scura nativa curata, transizioni fluide) per renderla ultra-leggera e responsive.

---

## 2. Soluzione Proposta ed Obiettivi

### A. Firmware ESP32: Coda di Invio Asincrona (Dual Core)
1. **Core 0 (UI & Controlli)**: Gestirà lo schermo OLED, la tastiera, il watchdog e la logica di accensione dei relè.
2. **Core 1 (Sensori)**: Continuerà a leggere a intervalli regolari i valori di pH, EC e Temperatura.
3. **Core 1 (Coda WiFi in Background)**:
   * Creeremo una coda di messaggi/azioni thread-safe (ad esempio, una lista condivisa protetta da un lock).
   * Un thread dedicato in background (`wifi_sending_loop`) monitorerà questa coda.
   * Quando ci sono letture da inviare o azioni manuali (NTP sync o invio forzato), il thread si connetterà al WiFi, effettuerà la richiesta HTTP in modo asincrono senza bloccare l'interfaccia utente, e poi si scollegherà per risparmiare energia (se configurato così) o rimarrà connesso.

### B. Sicurezza e Correzione Web App (PHP)
1. **Risoluzione Bug di Naming**: Standardizzeremo tutte le chiamate e i file sulla chiave `"PH"` (rinomineremo `takePh.php` in `takePH.php` o allineeremo i parametri per essere coerenti).
2. **Prepared Statements**: Riscriveremo le query SQL in PHP utilizzando i Prepared Statements di MySQLi per eliminare ogni vulnerabilità da SQL Injection.
3. **Configurazione Dinamica**: Modificheremo `connection.php` per leggere le credenziali del database dalle variabili d'ambiente (`DB_HOST`, `DB_USER`, `DB_PASS`, `DB_NAME`), con valori di fallback per facilitare l'esecuzione locale.

### C. Creazione dell'Ambiente Localhost (Docker Compose)
Per permetterti di testare l'intera web app (inclusi i database e la ricezione dei dati dall'ESP32) sul tuo computer locale, creeremo un ambiente Docker Compose contenente:
1. **Un container Database (`db`)**: Basato su MySQL 8.0, che caricherà automaticamente il file `localhost.sql` all'avvio.
2. **Un container Web Server (`web`)**: Con Apache e PHP 8.0/8.1, configurato con l'estensione `mysqli` abilitata. Questo container ospiterà il codice della cartella `webApp/code`.
3. **Istruzioni di Avvio**: Un singolo comando (`docker-compose up -d`) permetterà di far girare tutto su `http://localhost:8080`.

---

## 3. Domande Aperte e Decisioni

> [!IMPORTANT]
> 1. **Invio dei Dati**: L'invio automatico dei dati al server deve avvenire a intervalli fissi (es. ogni X ore definiti in `freqUpdateWeb...`) o preferisci inviare i dati solo manualmente dalle opzioni del menu?
> 2. **Nome del Database**: Preferisci standardizzare il nome del database su `my_myfishtank` sia in `connection.php` che in `localhost.sql`?
> 3. **Approvazione Docker**: Ti va bene utilizzare Docker Compose per impostare l'ambiente localhost? (Richiede l'installazione di Docker Desktop sul tuo PC Windows).

---

## 4. Fasi del Piano di Esecuzione

* [ ] **Fase 1: Configurazione Ambiente Localhost (Docker)**
  * Creazione di `docker-compose.yml` nella cartella principale del progetto.
  * Allineamento del nome del database in `localhost.sql` e configurazione delle credenziali dinamiche in `connection.php`.
  * Avvio e test dell'ambiente locale per verificare che il sito web risponda correttamente su localhost.
* [ ] **Fase 2: Aggiornamento e Messa in Sicurezza di PHP**
  * Integrazione dei Prepared Statements nei file `takeTemp.php`, `takePh.php`, `takeEc.php` per prevenire SQL Injection.
  * Risoluzione dei bug relativi al caso di lettere ("PH" vs "Ph") e allineamento con la logica dell'ESP32.
* [ ] **Fase 3: Implementazione Dual Core su ESP32**
  * Creazione della coda asincrona e del thread di background `wifi_sending_loop` in `main.py`.
  * Modifica di `WifiConnection` per inserire i messaggi in coda anziché bloccare il thread chiamante.
  * Assicurazione del corretto funzionamento del Watchdog (WDT) durante l'invio asincrono.
* [ ] **Fase 4: Suite di Test Unitari**
  * Estensione di `tests/test_suite.py` per testare la coda di invio asincrona e i thread simulati su CPython.
* [ ] **Fase 5: Modernizzazione UI e Nuove Feature del Sito**
  * Refactoring dello stile della Dashboard con Glassmorphism moderno e dark mode curata.
  * Integrazione di nuove feature grafiche ed elementi di visualizzazione.
