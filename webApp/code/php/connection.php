<?php
$db_host = getenv('DB_HOST') ?: 'localhost';
$db_user = getenv('DB_USER') ?: 'root';
$db_pass = getenv('DB_PASS') !== false ? getenv('DB_PASS') : '';
$db_name = getenv('DB_NAME') ?: 'my_myfishtank';

$con = new mysqli($db_host, $db_user, $db_pass, $db_name);

// Check connection
if ($con->connect_errno) {
  echo "Failed to connect to MySQL: " . $con->connect_error;
  exit();
}
// ── Punto 10: token condiviso con i device IoT (Arduino/ESP) ──────────────────
// Genera un valore casuale sicuro, es: php -r "echo bin2hex(random_bytes(32));"
// Inserisci lo stesso valore nel firmware del device.
define('IOT_SECRET', 'ffa1443a93134abbce231e714f5b07ff01aa77b0ffefefecefa8d2b173189089');
 
// ── Punto 11: codice invito per la registrazione ───────────────────────────────
// Chiunque voglia registrarsi dovrà conoscere questo codice.
define('INVITE_CODE', 'myfishtankRegistration');
?>