<?php

if (!empty($_POST)){
  $ec = $_POST["button"];
  $ec = !empty($ec) ? "$ec" : "4";      
}   
header('Content-Type: application/json');
include("../connection.php");
include("../queryAndFunction.php");

if ($con->connect_error) {
  die("Connection failed: " . $con->connect_error);
}

if($ec == "4"){ 
  $sqlEC_chart = getECChartQuery();
  $sqlPH_chart = getPhChartQuery();
  $sqlTemp_chart = getTemperatureChartQuery();
  
}else if ($ec == "2"){
  $dataNow = date('Y-m-d', strtotime("-2 month"));
  $sqlEC_chart = getECChartQueryWithDate($dataNow);
  $sqlPH_chart = getPhChartQueryWithDate($dataNow);
  $sqlTemp_chart = getTemperatureChartQueryWithDate($dataNow);   
}else if ($ec == "7"){
  $dataNow = date('Y-m-d', strtotime("-7 day"));
  $sqlEC_chart = getECChartQueryWithDate($dataNow); 
  $sqlPH_chart = getPhChartQueryWithDate($dataNow);  
  $sqlTemp_chart = getTemperatureChartQueryWithDate($dataNow);   
}else{
  $dataNow = date('Y-m-d', strtotime("-1 month"));
  $sqlEC_chart = getECChartQueryWithDate($dataNow);
  $sqlPH_chart = getPhChartQueryWithDate($dataNow);  
  $sqlTemp_chart = getTemperatureChartQueryWithDate($dataNow);
}

$resultEC_chart = $con->query($sqlEC_chart);

$resultTemp_chart = $con->query($sqlTemp_chart);
$resultPH_chart = $con->query($sqlPH_chart);

$data = array();
foreach ($resultEC_chart as $row) {
	$data[] = $row;
}

$dataTemp = array();
foreach ($resultTemp_chart as $rowT) {
	$dataTemp[] = $rowT;
}

$dataPH = array();
foreach ($resultPH_chart as $rowPH) {
	$dataPH[] = $rowPH;
}

$data_final = array();
$data_final['ec'] = get_median($data);
$data_final['ph'] = get_median_ph($dataPH);
$data_final['temp'] = get_median_temperature($dataTemp);


echo json_encode($data_final);
?>