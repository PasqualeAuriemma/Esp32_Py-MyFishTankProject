<?php
session_start();

if (empty($_SESSION['loggedIn']) || $_SESSION['loggedIn'] !== '1') {
    http_response_code(401);
    exit(json_encode(['status' => 'error', 'message' => 'Unauthorized']));
}

header('Content-Type: application/json');
include("../connection.php");
include("../queryAndFunction.php");

$temp = isset($_POST['temp']) ? trim($_POST['temp']) : '';

if ($temp === '' || !is_numeric($temp)) {
    exit(json_encode(['status' => 'false', 'message' => 'Invalid temperature value']));
}

$temp     = floatval($temp);
$dataSend = time();

$stmt = $con->prepare("INSERT INTO temp_tab (temperature, data_send) VALUES (?, ?)");
$stmt->bind_param("di", $temp, $dataSend);
$ok = $stmt->execute();
$stmt->close();

echo json_encode(['status' => $ok ? 'true' : 'false']);

$con->close();
