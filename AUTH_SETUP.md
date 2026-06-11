# Authentication System - Setup Guide

## File Creati

### 1. **Sign Up System** (`php/Login/sign_up_db.php`)
- Gestisce la registrazione di nuovi utenti
- Validazioni: email unica, password minimo 6 caratteri, conferma password
- Hash password con MD5
- Auto-login dopo registrazione

### 2. **Forgot Password System** (`php/Login/forgot_password_db.php`)
- Genera token di reset valido per 1 ora
- Salva token nel database
- Ritorna link di reset per testing
- **TODO**: Integrare invio email

### 3. **Reset Password** (`php/Login/reset_password_db.php`)
- Verifica token valido e non scaduto
- Aggiorna password con nuova hash
- Cancella token dopo uso

### 4. **Reset Password Page** (`reset_password.php`)
- Pagina standalone per il reset della password
- Riceve token via URL parameter: `reset_password.php?token=xxxxx`

### 5. **UI Updates** (index.php + loginManaging.js)
- Modal per Sign Up
- Modal per Forgot Password
- Pulsante "Forgot password" nel login
- Link "Sign Up" che apre il form di registrazione

---

## Setup Database

Esegui questo SQL nel tuo database:

```sql
ALTER TABLE `users`
ADD COLUMN `reset_token` VARCHAR(255) DEFAULT NULL,
ADD COLUMN `reset_token_expiry` DATETIME DEFAULT NULL;
```

Oppure usa il file: `database/update_users_table.sql`

---

## Flow di Utilizzo

### Sign Up
1. Clicca su "Sign Up" nel modal di login
2. Compila email, password, conferma password
3. Clicca "Register"
4. Accesso automatico

### Forgot Password
1. Clicca "Forgot password" nel modal di login
2. Inserisci email
3. Ricevi link di reset (in produzione via email)
4. Clicca il link per aprire `reset_password.php?token=xxxxx`
5. Inserisci nuova password e conferma
6. Reindirizzamento al login

---

## Prossimi Passi

### 1. Integrare Email (IMPORTANTE)
Nel file `php/Login/forgot_password_db.php`, decommentare e configurare:

```php
mail($email, "Password Reset", "Click here to reset: " . $reset_link);
```

Oppure usare una libreria come PHPMailer o SendGrid.

### 2. Sicurezza Password
Considerare di migrare da MD5 a bcrypt o password_hash():

```php
// Vecchio (non consigliato)
$password_hashed = md5($password);

// Nuovo (migliore)
$password_hashed = password_hash($password, PASSWORD_BCRYPT);
```

### 3. Aggiungere OAuth
Se vuoi Facebook/Google login, creare:
- `php/Login/facebook_login_db.php`
- `php/Login/google_login_db.php`

### 4. Rate Limiting
Aggiungere protezione contro brute force sul login

---

## Testing

1. Avvia il progetto con Docker
2. Vai su `index.php`
3. Clicca "Sign Up" e registrati
4. Logout
5. Clicca "Forgot password"
6. Usa il link mostrato in console (in sviluppo)
7. Resetta la password
8. Login con le nuove credenziali
