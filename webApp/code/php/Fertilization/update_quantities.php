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
    exit(json_encode(['status' => 'false', 'message' => 'Invalid ID']));
}

$fields = ['potassio', 'magnesio', 'ferro', 'rinverdente', 'fosforo', 'azoto', 'npk'];
$values = [];

foreach ($fields as $f) {
    $v = isset($_POST[$f]) ? trim($_POST[$f]) : '';
    if ($v !== '' && !is_numeric($v)) {
        exit(json_encode(['status' => 'false', 'message' => "Invalid value for $f"]));
    }
    $values[$f] = ($v === '') ? null : floatval($v);
}

$stmt = $con->prepare(
    "UPDATE fertilization_tab
     SET `k` = ?, `mg` = ?, `fe` = ?, `rinverdente` = ?, `p` = ?, `n` = ?, `npk` = ?
     WHERE id = ?"
);
$stmt->bind_param(
    "dddddddi",
    $values['potassio'],
    $values['magnesio'],
    $values['ferro'],
    $values['rinverdente'],
    $values['fosforo'],
    $values['azoto'],
    $values['npk'],
    $id
);

$ok = $stmt->execute();
$stmt->close();

echo json_encode(['status' => $ok ? 'true' : 'false']);

$con->close();
