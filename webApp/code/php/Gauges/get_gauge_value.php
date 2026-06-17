<?php
// Rimosso il parametro $selector ($button POST) che veniva letto
// ma poi ignorato completamente nel resto del file.
// Se in futuro servirà per filtrare i dati, reintrodurlo qui con la logica relativa.

header('Content-Type: application/json');
include("../connection.php");
include("../queryAndFunction.php");

if ($con->connect_error) {
    http_response_code(500);
    exit(json_encode(['status' => 'error', 'message' => 'Connection failed']));
}

$dataNow = date('Y-m-d');

// PH
$resultPH = $con->query(getDailyPH($dataNow));
$ph_array = [];
while ($row = $resultPH->fetch_assoc()) {
    $ph_array[] = $row["ph"];
}

// EC
$resultEC = $con->query(getDailyEC($dataNow));
$ec_array = [];
while ($row = $resultEC->fetch_assoc()) {
    $ec_array[] = $row["ec"];
}

// Temperature
$resultT  = $con->query(getDailyTemperature($dataNow));
$t_array  = [];
while ($row = $resultT->fetch_assoc()) {
    $t_array[] = $row["temperature"];
}

$value = [
    "ValuePH" => !empty($ph_array) ? number_format(calculate_median($ph_array), 2)  : "0",
    "ValueEC" => !empty($ec_array) ? number_format(calculate_median($ec_array), 2)  : "0",
    "ValueT"  => !empty($t_array)  ? number_format(calculate_average($t_array), 2)  : "0",
];

$con->close();
echo json_encode($value);
