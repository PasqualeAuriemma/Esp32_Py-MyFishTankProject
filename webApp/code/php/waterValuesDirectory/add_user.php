<?php
session_start();

if (empty($_SESSION['loggedIn']) || $_SESSION['loggedIn'] !== '1') {
    http_response_code(401);
    exit(json_encode(['status' => 'error', 'message' => 'Unauthorized']));
}

header('Content-Type: application/json');
include('../connection.php');
include('../queryAndFunction.php');

// ── Helper: valore numerico o null ────────────────────────────────────────────
function numOrNull($v) {
    $v = trim($v ?? '');
    return ($v !== '' && is_numeric($v)) ? floatval($v) : null;
}

$ecP = numOrNull($_POST['ecP'] ?? '');
$ecA = numOrNull($_POST['ecA'] ?? '');
$ph  = numOrNull($_POST['ph']  ?? '');
$no2 = numOrNull($_POST['no2'] ?? '');
$no3 = numOrNull($_POST['no3'] ?? '');
$gh  = numOrNull($_POST['gh']  ?? '');
$kh  = numOrNull($_POST['kh']  ?? '');
$po4 = numOrNull($_POST['po4'] ?? '');

$dataNow = date('Y-m-d');

// Se EC_PRE non fornito, usa mediana del giorno
if ($ecP === null) {
    $res = $con->query(getDailyEC($dataNow));
    $arr = [];
    while ($r = $res->fetch_assoc()) { $arr[] = $r['ec']; }
    $ecP = count($arr) ? round(calculate_median($arr), 2) : 0;
}

// Se PH non fornito, usa mediana del giorno
if ($ph === null) {
    $res = $con->query(getDailyPH($dataNow));
    $arr = [];
    while ($r = $res->fetch_assoc()) { $arr[] = $r['ph']; }
    $ph = count($arr) ? round(calculate_median($arr), 2) : 0;
}

// ── Prepared statement per INSERT ─────────────────────────────────────────────
$stmt = $con->prepare(
    "INSERT INTO watervalues_table (EC_PRE, EC_AFT, PH, no2, no3, gh, kh, po4)
     VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
);
$stmt->bind_param("dddddddd", $ecP, $ecA, $ph, $no2, $no3, $gh, $kh, $po4);

if ($stmt->execute()) {
    echo json_encode(['status' => 'true']);
} else {
    echo json_encode(['status' => 'false', 'message' => 'Insert failed']);
}

$stmt->close();
$con->close();
