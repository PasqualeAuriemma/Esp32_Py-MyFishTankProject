<?php

if (!empty($_POST)){
  
  $ec = $_POST["button"];
  
  $ec = !empty($ec) ? "$ec" : "4";      
 
}   

header('Content-Type: application/json');

include("php/connection.php");
if ($con->connect_error) {
  die("Connection failed: " . $con->connect_error);
}

if($ec == "4"){ 
  $sqlEC_chart = "SELECT e.ec as EC, t.tds as TDS, FROM_UNIXTIME(e.data_send, '%Y-%m-%d %H:%i:%s') as send FROM my_myfishtank.tds_tab t JOIN my_myfishtank.ec_tab e ON t.id = e.id ORDER BY e.data_send";
}else if ($ec == "2"){
  $dataNow = date('Y-m-d', strtotime("-2 month"));
  $sqlEC_chart = "SELECT e.ec as EC, t.tds as TDS, FROM_UNIXTIME(e.data_send, '%Y-%m-%d  %H:%i:%s') as send FROM my_myfishtank.tds_tab t JOIN my_myfishtank.ec_tab e ON t.id = e.id WHERE FROM_UNIXTIME(e.data_send, '%Y-%m-%d') >= '$dataNow' ORDER BY e.data_send";
}else if ($ec == "7"){
  $dataNow = date('Y-m-d', strtotime("-7 day"));
  $sqlEC_chart = "SELECT e.ec as EC, t.tds as TDS, FROM_UNIXTIME(e.data_send, '%Y-%m-%d  %H:%i:%s') as send FROM my_myfishtank.tds_tab t JOIN my_myfishtank.ec_tab e ON t.id = e.id WHERE FROM_UNIXTIME(e.data_send, '%Y-%m-%d') >= '$dataNow' ORDER BY e.data_send"; 
  //$sqlEC_chart = "SELECT data_arrive as date, ec FROM my_myfishtank.temp_tab WHERE FROM_UNIXTIME(data_send, '%Y-%m-%d') >= '$dataNow' ORDER BY data_send";          
}else{
  $dataNow = date('Y-m-d', strtotime("-1 month"));
  $sqlEC_chart = "SELECT e.ec as EC, t.tds as TDS, FROM_UNIXTIME(e.data_send, '%Y-%m-%d  %H:%i:%s') as send FROM my_myfishtank.tds_tab t JOIN my_myfishtank.ec_tab e ON t.id = e.id WHERE FROM_UNIXTIME(e.data_send, '%Y-%m-%d') >= '$dataNow' ORDER BY e.data_send";
}

$resultEC_chart = $con->query($sqlEC_chart);

//$data = array();
//$data['fruits'] = array('apple','banana','cherry');
//$data['animals'] = array('dog', 'elephant');
//echo json_encode($data);

$data = array();
foreach ($resultEC_chart as $row) {
	$data[] = $row;
}
// $list['column_name_1']
$data_final = array();
$data_final['raw'] = $data;

function calculate_median($arr) {
    $count = count($arr); //total numbers in array
    $middleval = floor(($count-1)/2); // find the middle value, or the lowest middle value
    if($count % 2) { // odd number, middle is the median
        $median = $arr[$middleval];
    } else { // even number, calculate avg of 2 medians
        $low = $arr[$middleval];
        $high = $arr[$middleval+1];
        $median = (($low+$high)/2);
    }
    return strval($median);
}

$data_median = array();

function get_median($arr) {
  $data_median = array();
  $tempvalue = "";
  $temparr = array();
  $finalData = "";
  foreach ($arr as $element) {
    $datasplit = explode(" ", $element['send'])[0];
    if ($tempvalue == ""){
      $tempvalue =  $datasplit;
      $temparr[] = $element['EC'];
    }else if ($tempvalue !=  $datasplit){
      $tempvalue =  $datasplit;
      $data_median[] = ["M" => calculate_median($temparr), "D" =>  $finalData];
      $temparr = array();
      $temparr[] = $element['EC'];
      $finalData = $element['send'];
    }else{
      $temparr[] = $element['EC'];
      $finalData = $element['send'];
    }
  }
  $data_median[] =  ["M" => calculate_median($temparr), "D" => $finalData];
  return $data_median;
}

$data_final['median'] = get_median($data);


echo json_encode($data_final);


?>