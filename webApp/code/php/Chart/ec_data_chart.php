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
        $sqlEC_chart = getECChartQuery();
        break;
    case '2':
        $dataNow = date('Y-m-d', strtotime('-2 month'));
        $sqlEC_chart = getECChartQueryWithDate($dataNow);
        break;
    case '7':
        $dataNow = date('Y-m-d', strtotime('-7 day'));
        $sqlEC_chart = getECChartQueryWithDate($dataNow);
        break;
    default: // '1'
        $dataNow = date('Y-m-d', strtotime('-1 month'));
        $sqlEC_chart = getECChartQueryWithDate($dataNow);
}

$resultEC_chart = $con->query($sqlEC_chart);

$data = [];
while ($row = $resultEC_chart->fetch_assoc()) {
    $data[] = $row;
}

$data_final = [
    'raw'    => $data,
    'median' => get_median($data),
];

$con->close();
echo json_encode($data_final);
