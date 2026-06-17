<?php
session_start();

if (empty($_SESSION['loggedIn']) || $_SESSION['loggedIn'] !== '1') {
    http_response_code(401);
    exit(json_encode(['status' => 'error', 'message' => 'Unauthorized']));
}

header('Content-Type: application/json');
include('../connection.php');
include('../queryAndFunction.php');

$date   = trim($_POST['data']   ?? '');
$volume = trim($_POST['vol']    ?? '');
$select = trim($_POST['select'] ?? '');

if (empty($select)) {
    exit(json_encode(['status' => 'false', 'message' => 'Missing fertilizer name']));
}

// Whitelist dei fertilizzanti consentiti — impedisce injection sul WHERE
$allowed = ['Potassio','Magnesio','Ferro','Rinverdente','Fosforo','Azoto','Stick','Co2'];
if (!in_array($select, $allowed, true)) {
    exit(json_encode(['status' => 'false', 'message' => 'Invalid fertilizer name']));
}

// Validazione data (formato YYYY-MM-DD o vuota)
if (!empty($date) && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
    exit(json_encode(['status' => 'false', 'message' => 'Invalid date format']));
}

// Validazione volume numerico
if (!empty($volume) && !is_numeric($volume)) {
    exit(json_encode(['status' => 'false', 'message' => 'Invalid volume value']));
}

// ── Prepared statement per UPDATE ─────────────────────────────────────────────
// Costruiamo la query in base a cosa è stato fornito (data, volume, o entrambi)
if (!empty($date) && !empty($volume)) {
    $stmt = $con->prepare(
        "UPDATE fertilization_volumes SET data_inizio = ?, qnt = ? WHERE fertilizzante = ?"
    );
    $vol = floatval($volume);
    $stmt->bind_param("sds", $date, $vol, $select);
} elseif (!empty($date)) {
    $stmt = $con->prepare(
        "UPDATE fertilization_volumes SET data_inizio = ? WHERE fertilizzante = ?"
    );
    $stmt->bind_param("ss", $date, $select);
} elseif (!empty($volume)) {
    $stmt = $con->prepare(
        "UPDATE fertilization_volumes SET qnt = ? WHERE fertilizzante = ?"
    );
    $vol = floatval($volume);
    $stmt->bind_param("ds", $vol, $select);
} else {
    exit(json_encode(['status' => 'false', 'message' => 'Nothing to update']));
}

if ($stmt->execute()) {
    echo json_encode(['status' => 'true']);
} else {
    echo json_encode(['status' => 'false', 'message' => 'Update failed']);
}

$stmt->close();
$con->close();
