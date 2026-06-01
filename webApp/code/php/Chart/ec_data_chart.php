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
}else if ($ec == "2"){
  $dataNow = date('Y-m-d', strtotime("-2 month"));
  $sqlEC_chart = getECChartQueryWithDate($dataNow);
}else if ($ec == "7"){
  $dataNow = date('Y-m-d', strtotime("-7 day"));
  $sqlEC_chart = getECChartQueryWithDate($dataNow); 
}else{
  $dataNow = date('Y-m-d', strtotime("-1 month"));
  $sqlEC_chart = getECChartQueryWithDate($dataNow);
}
$resultEC_chart = $con->query($sqlEC_chart);
$data = array();
foreach ($resultEC_chart as $row) {
	$data[] = $row;
}
$data_final = array();
$data_final['raw'] = $data;
$data_final['median'] = get_median($data);

echo json_encode($data_final);
?>