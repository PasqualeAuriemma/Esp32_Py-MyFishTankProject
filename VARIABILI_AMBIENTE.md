# Configurazione Variabili d'Ambiente per PHP

## 📋 Panoramica

Tutti i file `connection.php` del progetto sono stati aggiornati per leggere le credenziali del database dalle **variabili d'ambiente**. Questo permette di avere una **unica configurazione** che funziona sia in **localhost** che in **produzione**.

---

## ⚙️ Come Funziona

### In Docker (Localhost)

Le variabili d'ambiente vengono specificate nel file `docker-compose.yml`:

```yaml
web:
  build: .
  environment:
    DB_HOST: db
    DB_USER: root
    DB_PASS: ""
    DB_NAME: my_myfishtank
```

Quando il container Apache avvia PHP, queste variabili sono **automaticamente disponibili** a tutti i file PHP.

### Su Altervista (Produzione)

Le variabili d'ambiente devono essere configurate nel **panel di controllo di Altervista** oppure tramite un file `.htaccess`:

```apache
SetEnv DB_HOST your_host
SetEnv DB_USER your_username
SetEnv DB_PASS your_password
SetEnv DB_NAME your_database_name
```

---

## 🔧 Valori di Fallback

Se le variabili d'ambiente **non sono definite**, PHP usa i valori di fallback:

```php
$db_host = getenv('DB_HOST') ?: 'localhost';
$db_user = getenv('DB_USER') ?: 'root';
$db_pass = getenv('DB_PASS') !== false ? getenv('DB_PASS') : '';
$db_name = getenv('DB_NAME') ?: 'my_myfishtank';
```

**Questo significa che:**
- Se esegui PHP localmente **senza Docker**, usa automaticamente `localhost:root:""` (perfetto per lo sviluppo)
- Se esegui in Docker, usa le variabili specificate in `docker-compose.yml`
- Se esegui su Altervista, usa le variabili configurate nel panel

---

## 🐳 Avviare Localhost con Docker

### 1️⃣ Assicurati che Docker Desktop sia installato

### 2️⃣ Vai alla cartella del progetto

```bash
cd c:\Users\rmmpq\Desktop\Esp32_Py-MyFishTankProject
```

### 3️⃣ Avvia i container

```bash
docker-compose up -d
```

Questo avvia:
- **Database**: `mysql:8.0` su porta `3306`
- **Web Server**: Apache + PHP su porta `8080`

### 4️⃣ Accedi al sito

```
http://localhost:8080
```

### 5️⃣ Arresta i container

```bash
docker-compose down
```

---

## 📝 File Aggiornati

I seguenti file sono stati aggiornati per usare le variabili d'ambiente:

✅ `webApp/code/assets/js/php/connection.php`  
✅ `webApp/code/php/connection.php`  
✅ `webApp/code/ex_site/assets/js/php/connection.php`  
✅ `webApp/code/ex_site/php/connection.php`  
✅ `webApp/code/ex_site/ECSettingTable/connection.php`  
✅ `webApp/code/ex_site/Fertilization/connection.php`  
✅ `webApp/code/ex_site/PHSettingTable/connection.php`  
✅ `webApp/code/ex_site/TDSSettingTable/connection.php`  
✅ `webApp/code/ex_site/TEMPSettingTable/connection.php`  
✅ `webApp/code/ex_site/waterValuesDirectory/connection.php`

---

## 🚀 Uso su Altervista

Se vuoi deployare su Altervista, aggiungi al file `.htaccess` nella radice di `public_html/`:

```apache
SetEnv DB_HOST [tuo_host_altervista]
SetEnv DB_USER [tuo_username]
SetEnv DB_PASS [tua_password]
SetEnv DB_NAME my_myfishtank
```

Se non hai accesso a `.htaccess`, puoi configurare le variabili tramite il **panel di controllo di Altervista**.

---

## ✨ Vantaggi

| Vantaggio | Descrizione |
|-----------|-------------|
| 🔒 **Sicurezza** | Le credenziali non sono hardcoded nel codice |
| 🔄 **Portabilità** | Stesso codice funziona su localhost, Docker e produzione |
| 👁️ **Visibilità** | `docker-compose.yml` documenta chiaramente la configurazione |
| 🛠️ **Facilità** | Cambiar database = modificar una sola variabile |

---

## 🐛 Fix Critici Applicati

### 1️⃣ takePH.php - Bug di Casing

**Prima:**
```php
$ph = $_POST["Ph"];  // ❌ Ricerca "Ph" ma il firmware invia "PH"
```

**Dopo:**
```php
$ph = isset($_POST["PH"]) ? $_POST["PH"] : null;  // ✅ Corretto e con validazione
```

### 2️⃣ takePH.php - SQL Injection

**Prima:**
```php
$query = getPHInsertQuery($ph, $dataSend);  // ❌ Usa concatenazione di stringhe
if ($con->query($query) === TRUE) { ... }
```

**Dopo:**
```php
$stmt = $con->prepare("INSERT INTO ph_tab (ph, data_send) VALUES (?, ?)");  // ✅ Prepared Statement
$stmt->bind_param("ds", $ph, $dataSend);
if ($stmt->execute()) { ... }
```

---

## ✅ Checklist Finale

- [x] Tutte le variabili d'ambiente configurate in `docker-compose.yml`
- [x] Tutti i `connection.php` aggiornati
- [x] Fallback values per lo sviluppo locale
- [x] `takePH.php` corretto (casing e Prepared Statements)
- [x] `takeTemp.php` e `takeEc.php` già avevano Prepared Statements
