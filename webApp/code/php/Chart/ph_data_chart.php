<?php

if (!empty($_POST)){
  
  $ph = $_POST["button"];
  
  $ph = !empty($ph) ? "$ph" : "4";      
 
}   

header('Content-Type: application/json');

include("../connection.php");
include("../queryAndFunction.php");

if ($con->connect_error) {
  die("Connection failed: " . $con->connect_error);
}

if($ph == "4"){ 
  $sqlPH_chart = getPhChartQuery();
}else if ($ph == "2"){
  $dataNow = date('Y-m-d', strtotime("-2 month"));
  $sqlPH_chart = getPhChartQueryWithDate($dataNow);                       
}else if ($ph == "7"){
  $dataNow = date('Y-m-d', strtotime("-7 day"));
  $sqlPH_chart = getPhChartQueryWithDate($dataNow);          
}else{
  $dataNow = date('Y-m-d', strtotime("-1 month"));
  $sqlPH_chart = getPhChartQueryWithDate($dataNow);
}
  
$resultPH_chart = $con->query($sqlPH_chart);

$data = array();
foreach ($resultPH_chart as $row) {
	$data[] = $row;
}

echo json_encode($data);
?>