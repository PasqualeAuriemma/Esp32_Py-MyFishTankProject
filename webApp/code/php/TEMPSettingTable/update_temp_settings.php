<?php
session_start();

if (empty($_SESSION['loggedIn']) || $_SESSION['loggedIn'] !== '1') {
    http_response_code(401);
    exit(json_encode(['status' => 'error', 'message' => 'Unauthorized']));
}

header('Content-Type: application/json');
include('../connection.php');
include('../queryAndFunction.php');

$id   = isset($_POST['id'])        ? intval($_POST['id'])        : 0;
$temp = isset($_POST['temp'])      ? trim($_POST['temp'])        : '';
$send = isset($_POST['data_send']) ? trim($_POST['data_send'])   : '';

if ($id <= 0) {
    exit(json_encode(['status' => 'error', 'message' => 'Invalid ID']));
}
if (empty($temp) || empty($send)) {
    exit(json_encode(['status' => 'error', 'message' => 'Missing fields']));
}
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $send)) {
    exit(json_encode(['status' => 'error', 'message' => 'Invalid date format']));
}

$stmt = $con->prepare("UPDATE temp_tab SET temperature = ?, data_send = ? WHERE id = ?");
$stmt->bind_param("ssi", $temp, $send, $id);

if ($stmt->execute()) {
    echo json_encode(['status' => 'true']);
} else {
    echo json_encode(['status' => 'false', 'message' => 'Update failed']);
}

$stmt->close();
$con->close();
