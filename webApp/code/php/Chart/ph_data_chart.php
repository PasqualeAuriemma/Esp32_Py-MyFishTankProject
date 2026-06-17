<?php
header('Content-Type: application/json');
include("../connection.php");
include("../queryAndFunction.php");

if ($con->connect_error) {
    http_response_code(500);
    exit(json_encode(['status' => 'error', 'message' => 'Connection failed']));
}

// ── Validazione parametro "button": whitelist esplicita ──────────────────────
$allowed = ['2', '4', '7']; // 2 mesi, all, 7 giorni — default 1 mese
$ph = isset($_POST['button']) ? (string) $_POST['button'] : '1';
if (!in_array($ph, $allowed, true)) {
    $ph = '1';
}

switch ($ph) {
    case '4':
        $sqlPH_chart = getPhChartQuery();
        break;
    case '2':
        $dataNow = date('Y-m-d', strtotime('-2 month'));
        $sqlPH_chart = getPhChartQueryWithDate($dataNow);
        break;
    case '7':
        $dataNow = date('Y-m-d', strtotime('-7 day'));
        $sqlPH_chart = getPhChartQueryWithDate($dataNow);
        break;
    default: // '1'
        $dataNow = date('Y-m-d', strtotime('-1 month'));
        $sqlPH_chart = getPhChartQueryWithDate($dataNow);
}

$resultPH_chart = $con->query($sqlPH_chart);

$data = [];
while ($row = $resultPH_chart->fetch_assoc()) {
    $data[] = $row;
}

$con->close();
echo json_encode($data);
