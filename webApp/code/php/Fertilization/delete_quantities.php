<?php
session_start();

if (empty($_SESSION['loggedIn']) || $_SESSION['loggedIn'] !== '1') {
    http_response_code(401);
    exit(json_encode(['status' => 'error', 'message' => 'Unauthorized']));
}

header('Content-Type: application/json');
include('../connection.php');
include('../queryAndFunction.php');

$id = isset($_POST['id']) ? intval($_POST['id']) : 0;
if ($id <= 0) {
    exit(json_encode(['status' => 'failed', 'message' => 'Invalid ID']));
}

$stmt = $con->prepare("DELETE FROM fertilization_tab WHERE id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    echo json_encode(['status' => ($stmt->affected_rows > 0) ? 'success' : 'failed']);
} else {
    echo json_encode(['status' => 'failed']);
}

$stmt->close();
$con->close();
