<?php
session_start();

// Punto 1: verifica sessione attiva
if (empty($_SESSION['loggedIn']) || $_SESSION['loggedIn'] !== '1') {
    http_response_code(401);
    exit(json_encode(['status' => 'error', 'message' => 'Unauthorized']));
}

header('Content-Type: application/json');
include('../connection.php');
include('../queryAndFunction.php');

// Punto 2: validazione input
$id = isset($_POST['id']) ? intval($_POST['id']) : 0;
if ($id <= 0) {
    exit(json_encode(['status' => 'error', 'message' => 'Invalid ID']));
}

// Punto 3: prepared statement invece di SQL grezzo
$stmt = $con->prepare("DELETE FROM ec_tab WHERE id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Record not found']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Delete failed']);
}

$stmt->close();
$con->close();
