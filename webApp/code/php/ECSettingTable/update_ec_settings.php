<?php
session_start();

if (empty($_SESSION['loggedIn']) || $_SESSION['loggedIn'] !== '1') {
    http_response_code(401);
    exit(json_encode(['status' => 'error', 'message' => 'Unauthorized']));
}

header('Content-Type: application/json');
include('../connection.php');
include('../queryAndFunction.php');

$id   = isset($_POST['id'])        ? intval($_POST['id'])              : 0;
$ec   = isset($_POST['ec'])        ? trim($_POST['ec'])                : '';
$send = isset($_POST['data_send']) ? trim($_POST['data_send'])         : '';

if ($id <= 0) {
    exit(json_encode(['status' => 'error', 'message' => 'Invalid ID']));
}
if (empty($ec) || empty($send)) {
    exit(json_encode(['status' => 'error', 'message' => 'Missing fields']));
}
// Validazione data formato YYYY-MM-DD
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $send)) {
    exit(json_encode(['status' => 'error', 'message' => 'Invalid date format']));
}

$stmt = $con->prepare("UPDATE ec_tab SET ec = ?, data_send = ? WHERE id = ?");
$stmt->bind_param("ssi", $ec, $send, $id);

if ($stmt->execute()) {
    echo json_encode(['status' => 'true']);
} else {
    echo json_encode(['status' => 'false', 'message' => 'Update failed']);
}

$stmt->close();
$con->close();
