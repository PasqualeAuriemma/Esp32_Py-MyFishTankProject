<?php

if (!empty($_POST)){
  
  $temp = $_POST["button"];
  
  $temp = !empty($temp) ? "$temp" : "4";      
 
}   

header('Content-Type: application/json');

include("../connection.php");
include("../queryAndFunction.php");

if ($con->connect_error) {
  die("Connection failed: " . $con->connect_error);
}


if($temp == "4"){ 
  $sqlTemp_chart = getTemperatureChartQuery();
}else if ($temp == "2"){
  $dataNow = date('Y-m-d', strtotime("-2 month"));
  $sqlTemp_chart = getTemperatureChartQueryWithDate($dataNow);                       
}else if ($temp == "7"){
  $dataNow = date('Y-m-d', strtotime("-7 day"));
  $sqlTemp_chart = getTemperatureChartQueryWithDate($dataNow);          
}else{
  $dataNow = date('Y-m-d', strtotime("-1 month"));
  $sqlTemp_chart = getTemperatureChartQueryWithDate($dataNow);
}
  
$resultTemp_chart = $con->query($sqlTemp_chart);

$data = array();
foreach ($resultTemp_chart as $row) {
	$data[] = $row;
}

echo json_encode($data);
?>