<?php
// Leggi le credenziali dalle variabili d'ambiente
// Se non sono definite, usa i valori di fallback per lo sviluppo locale
$db_host = getenv('DB_HOST') ?: 'localhost';
$db_user = getenv('DB_USER') ?: 'root';
$db_pass = getenv('DB_PASS') ?: '';
$db_name = getenv('DB_NAME') ?: 'my_myfishtank';

// Crea la connessione
$con = new mysqli($db_host, $db_user, $db_pass, $db_name);

// Check connection
if ($con -> connect_errno) {
  echo "Failed to connect to MySQL: " . $con -> connect_error;
  exit();
}

?>