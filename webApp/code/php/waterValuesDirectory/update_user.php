<?php
session_start();

if (empty($_SESSION['loggedIn']) || $_SESSION['loggedIn'] !== '1') {
    http_response_code(401);
    exit(json_encode(['status' => 'error', 'message' => 'Unauthorized']));
}

header('Content-Type: application/json');
include('../connection.php');
include('../queryAndFunction.php');

function numOrNull($v) {
    $v = trim($v ?? '');
    return ($v !== '' && is_numeric($v)) ? floatval($v) : null;
}

$id  = isset($_POST['id']) ? intval($_POST['id']) : 0;
if ($id <= 0) {
    exit(json_encode(['status' => 'false', 'message' => 'Invalid ID']));
}

$ecP = numOrNull($_POST['ecP'] ?? '');
$ecA = numOrNull($_POST['ecA'] ?? '');
$ph  = numOrNull($_POST['ph']  ?? '');
$no2 = numOrNull($_POST['no2'] ?? '');
$no3 = numOrNull($_POST['no3'] ?? '');
$gh  = numOrNull($_POST['gh']  ?? '');
$kh  = numOrNull($_POST['kh']  ?? '');
$po4 = numOrNull($_POST['po4'] ?? '');

// ── Prepared statement per UPDATE ─────────────────────────────────────────────
$stmt = $con->prepare(
    "UPDATE watervalues_table
     SET EC_PRE = ?, EC_AFT = ?, PH = ?, no2 = ?, no3 = ?, gh = ?, kh = ?, po4 = ?
     WHERE id = ?"
);
$stmt->bind_param("ddddddddi", $ecP, $ecA, $ph, $no2, $no3, $gh, $kh, $po4, $id);

if ($stmt->execute()) {
    echo json_encode(['status' => 'true']);
} else {
    echo json_encode(['status' => 'false', 'message' => 'Update failed']);
}

$stmt->close();
$con->close();
