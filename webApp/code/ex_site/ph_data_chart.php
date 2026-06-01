<?php

if (!empty($_POST)){
  
  $ph = $_POST["button"];
  
  $ph = !empty($ph) ? "$ph" : "4";      
 
}   

header('Content-Type: application/json');

include("php/connection.php");
if ($con->connect_error) {
  die("Connection failed: " . $con->connect_error);
}


if($ph == "4"){ 
  $sqlPH_chart = "SELECT ph, FROM_UNIXTIME(data_send, '%Y-%m-%d') as send FROM my_myfishtank.ph_tab ORDER BY data_send";
}else if ($ph == "2"){
  $dataNow = date('Y-m-d', strtotime("-2 month"));
  $sqlPH_chart = "SELECT data_arrive as date, ph FROM my_myfishtank.ph_tab WHERE FROM_UNIXTIME(data_send, '%Y-%m-%d') >= '$dataNow' ORDER BY data_send";                       
}else if ($ph == "7"){
  $dataNow = date('Y-m-d', strtotime("-7 day"));
  $sqlPH_chart = "SELECT data_arrive as date, ph FROM my_myfishtank.ph_tab WHERE FROM_UNIXTIME(data_send, '%Y-%m-%d') >= '$dataNow' ORDER BY data_send";          
}else{
  $dataNow = date('Y-m-d', strtotime("-1 month"));
  $sqlPH_chart = "SELECT FROM_UNIXTIME(data_send, '%Y-%m-%d') as send, ph FROM my_myfishtank.ph_tab WHERE FROM_UNIXTIME(data_send, '%Y-%m-%d') >= '$dataNow' ORDER BY data_send";
}
  
$resultPH_chart = $con->query($sqlPH_chart);

$data = array();
foreach ($resultPH_chart as $row) {
	$data[] = $row;
}

echo json_encode($data);
?>