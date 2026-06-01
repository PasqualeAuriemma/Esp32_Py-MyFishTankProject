<?php

if (!empty($_POST)){
  
  $temp = $_POST["button"];
  
  $temp = !empty($temp) ? "$temp" : "4";      
 
}   

header('Content-Type: application/json');

include("php/connection.php");
if ($con->connect_error) {
  die("Connection failed: " . $con->connect_error);
}


if($temp == "4"){ 
  $sqlTemp_chart = "SELECT temperature, FROM_UNIXTIME(data_send, '%Y-%m-%d') as send FROM my_myfishtank.temp_tab ORDER BY data_send";
}else if ($temp == "2"){
  $dataNow = date('Y-m-d', strtotime("-2 month"));
  $sqlTemp_chart = "SELECT data_arrive as date, temperature FROM my_myfishtank.temp_tab WHERE FROM_UNIXTIME(data_send, '%Y-%m-%d') >= '$dataNow' ORDER BY data_send";                       
}else if ($temp == "7"){
  $dataNow = date('Y-m-d', strtotime("-7 day"));
  $sqlTemp_chart = "SELECT data_arrive as date, temperature FROM my_myfishtank.temp_tab WHERE FROM_UNIXTIME(data_send, '%Y-%m-%d') >= '$dataNow' ORDER BY data_send";          
}else{
  $dataNow = date('Y-m-d', strtotime("-1 month"));
  $sqlTemp_chart = "SELECT FROM_UNIXTIME(data_send, '%Y-%m-%d') as send, temperature FROM my_myfishtank.temp_tab WHERE FROM_UNIXTIME(data_send, '%Y-%m-%d') >= '$dataNow' ORDER BY data_send";
}
  
$resultTemp_chart = $con->query($sqlTemp_chart);

$data = array();
foreach ($resultTemp_chart as $row) {
	$data[] = $row;
}

echo json_encode($data);
?>