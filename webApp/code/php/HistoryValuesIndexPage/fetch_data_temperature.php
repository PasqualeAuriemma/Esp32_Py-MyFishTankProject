<?php
header('Content-Type: application/json');
include("../connection.php");
include("../queryAndFunction.php");

if ($con->connect_error) {
    http_response_code(500);
    exit(json_encode(['status' => 'error', 'message' => 'Connection failed']));
}

$d = $_GET['d'] ?? 'noData';

if ($d === 'noData') {
    $dataNow1 = date('Y-m-d');
} elseif (preg_match('/^\d{4}-\d{2}-\d{2}$/', $d)) {
    $dataNow1 = $d;
} else {
    $dataNow1 = date('Y-m-d');
}

$sql = getDailyDescTemperature($dataNow1);

$query = $con->query($sql);
$count_rows = $query->num_rows;

$data = [];
while ($row = $query->fetch_assoc()) {
    // Punto: il file originale leggeva $row['temp'] ma la query restituisce
    // la colonna come "temp" tramite alias (t.temperature as temp) — verificato in queryAndFunction.php
    $data[] = [
        'id'   => $row['id'],
        'data' => gmdate('H:i:s', $row['data']),
        't'    => $row['temp'],
    ];
}

$output = [
    'draw'            => isset($_GET['draw']) ? intval($_GET['draw']) : 1,
    'recordsTotal'    => $count_rows,
    'recordsFiltered' => $count_rows,
    'data'            => $data,
];

$con->close();
echo json_encode($output);
