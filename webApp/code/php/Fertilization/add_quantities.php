<?php
session_start();

if (empty($_SESSION['loggedIn']) || $_SESSION['loggedIn'] !== '1') {
    http_response_code(401);
    exit(json_encode(['status' => 'error', 'message' => 'Unauthorized']));
}

header('Content-Type: application/json');
include('../connection.php');
include('../queryAndFunction.php');

// ── Validazione: tutti i campi devono essere numerici o assenti ──────────────
$fields = ['potassio', 'magnesio', 'ferro', 'rinverdente', 'fosforo', 'azoto', 'npk'];
$values = [];

foreach ($fields as $f) {
    $v = isset($_POST[$f]) ? trim($_POST[$f]) : '';
    if ($v !== '' && !is_numeric($v)) {
        exit(json_encode(['status' => 'false', 'message' => "Invalid value for $f"]));
    }
    $values[$f] = ($v === '') ? null : floatval($v);
}

$stmt = $con->prepare(
    "INSERT INTO fertilization_tab (`k`, `mg`, `fe`, `rinverdente`, `p`, `n`, `npk`)
     VALUES (?, ?, ?, ?, ?, ?, ?)"
);
$stmt->bind_param(
    "ddddddd",
    $values['potassio'],
    $values['magnesio'],
    $values['ferro'],
    $values['rinverdente'],
    $values['fosforo'],
    $values['azoto'],
    $values['npk']
);

$ok = $stmt->execute();
$stmt->close();

echo json_encode(['status' => $ok ? 'true' : 'false']);

$con->close();
