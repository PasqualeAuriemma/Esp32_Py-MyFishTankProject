<?php
header('Content-Type: application/json');
include("../connection.php");
include("../queryAndFunction.php");

if ($con->connect_error) {
    http_response_code(500);
    exit(json_encode(['status' => 'error', 'message' => 'Connection failed']));
}

$allowed = ['2', '4', '7'];
$ec = isset($_POST['button']) ? (string) $_POST['button'] : '1';
if (!in_array($ec, $allowed, true)) {
    $ec = '1';
}

switch ($ec) {
    case '4':
        $sqlEC_chart   = getECChartQuery();
        $sqlPH_chart   = getPhChartQuery();
        $sqlTemp_chart = getTemperatureChartQuery();
        break;
    case '2':
        $dataNow       = date('Y-m-d', strtotime('-2 month'));
        $sqlEC_chart   = getECChartQueryWithDate($dataNow);
        $sqlPH_chart   = getPhChartQueryWithDate($dataNow);
        $sqlTemp_chart = getTemperatureChartQueryWithDate($dataNow);
        break;
    case '7':
        $dataNow       = date('Y-m-d', strtotime('-7 day'));
        $sqlEC_chart   = getECChartQueryWithDate($dataNow);
        $sqlPH_chart   = getPhChartQueryWithDate($dataNow);
        $sqlTemp_chart = getTemperatureChartQueryWithDate($dataNow);
        break;
    default: // '1'
        $dataNow       = date('Y-m-d', strtotime('-1 month'));
        $sqlEC_chart   = getECChartQueryWithDate($dataNow);
        $sqlPH_chart   = getPhChartQueryWithDate($dataNow);
        $sqlTemp_chart = getTemperatureChartQueryWithDate($dataNow);
}

$resultEC_chart   = $con->query($sqlEC_chart);
$resultPH_chart   = $con->query($sqlPH_chart);
$resultTemp_chart = $con->query($sqlTemp_chart);

$data = [];
while ($row = $resultEC_chart->fetch_assoc()) {
    $data[] = $row;
}

$dataPH = [];
while ($rowPH = $resultPH_chart->fetch_assoc()) {
    $dataPH[] = $rowPH;
}

$dataTemp = [];
while ($rowT = $resultTemp_chart->fetch_assoc()) {
    $dataTemp[] = $rowT;
}

$data_final = [
    'ec'   => get_median($data),
    'ph'   => get_median_ph($dataPH),
    'temp' => get_median_temperature($dataTemp),
];

$con->close();
echo json_encode($data_final);
