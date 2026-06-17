<?php
session_start();

if (empty($_SESSION['loggedIn']) || $_SESSION['loggedIn'] !== '1') {
    http_response_code(401);
    exit(json_encode(['status' => 'error', 'message' => 'Unauthorized']));
}

header('Content-Type: application/json');
include("../connection.php");
include("../queryAndFunction.php");

$ph = isset($_POST['ph']) ? trim($_POST['ph']) : '';

if ($ph === '' || !is_numeric($ph)) {
    exit(json_encode(['status' => 'false', 'message' => 'Invalid PH value']));
}

$ph       = round(floatval($ph), 2);
$dataSend = time();

$stmt = $con->prepare("INSERT INTO ph_tab (ph, data_send) VALUES (?, ?)");
$stmt->bind_param("di", $ph, $dataSend);
$ok = $stmt->execute();
$stmt->close();

echo json_encode(['status' => $ok ? 'true' : 'false']);

$con->close();
