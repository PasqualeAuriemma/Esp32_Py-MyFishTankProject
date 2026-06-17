<?php
header('Content-Type: application/json');
include("../connection.php");
include("../queryAndFunction.php");

if ($con->connect_error) {
    http_response_code(500);
    exit(json_encode(['status' => 'error', 'message' => 'Connection failed']));
}

$allowed = ['2', '4', '7'];
$temp = isset($_POST['button']) ? (string) $_POST['button'] : '1';
if (!in_array($temp, $allowed, true)) {
    $temp = '1';
}

switch ($temp) {
    case '4':
        $sqlTemp_chart = getTemperatureChartQuery();
        break;
    case '2':
        $dataNow = date('Y-m-d', strtotime('-2 month'));
        $sqlTemp_chart = getTemperatureChartQueryWithDate($dataNow);
        break;
    case '7':
        $dataNow = date('Y-m-d', strtotime('-7 day'));
        $sqlTemp_chart = getTemperatureChartQueryWithDate($dataNow);
        break;
    default: // '1'
        $dataNow = date('Y-m-d', strtotime('-1 month'));
        $sqlTemp_chart = getTemperatureChartQueryWithDate($dataNow);
}

$resultTemp_chart = $con->query($sqlTemp_chart);

$data = [];
while ($row = $resultTemp_chart->fetch_assoc()) {
    $data[] = $row;
}

$con->close();
echo json_encode($data);
