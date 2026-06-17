<?php
session_start();

if (empty($_SESSION['loggedIn']) || $_SESSION['loggedIn'] !== '1') {
    http_response_code(401);
    exit(json_encode(['status' => 'error', 'message' => 'Unauthorized']));
}

header('Content-Type: application/json');
include("../connection.php");
include("../queryAndFunction.php");

$ec = isset($_POST['ec']) ? trim($_POST['ec']) : '';

if ($ec === '' || !is_numeric($ec)) {
    exit(json_encode(['status' => 'false', 'message' => 'Invalid EC value']));
}

$ec       = floatval($ec);
$tds      = $ec * 0.64;
$dataSend = time();

$stmtEC  = $con->prepare("INSERT INTO ec_tab (ec, data_send) VALUES (?, ?)");
$stmtEC->bind_param("di", $ec, $dataSend);
$okEC = $stmtEC->execute();
$stmtEC->close();

$stmtTDS = $con->prepare("INSERT INTO tds_tab (tds, data_send) VALUES (?, ?)");
$stmtTDS->bind_param("di", $tds, $dataSend);
$okTDS = $stmtTDS->execute();
$stmtTDS->close();

echo json_encode(['status' => ($okEC && $okTDS) ? 'true' : 'false']);

$con->close();
