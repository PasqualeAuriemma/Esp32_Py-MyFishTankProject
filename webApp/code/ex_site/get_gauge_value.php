<?php

if (!empty($_POST)){
  
  $selector = $_POST["button"];
  
  $selector = !empty($selector) ? "$selector" : "1";      
  
} else{
  $selector = "1"; 
}

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
  return $median;
}

function calculate_average($arr) {
  $count = count($arr); //total numbers in array
  foreach ($arr as $value) {
    $total = $total + $value; // total value of array numbers
  }
  $average = ($total/$count); // get average value
  return $average;
}


header('Content-Type: application/json');

include("php/connection.php");
if ($con->connect_error) {
  die("Connection failed: " . $con->connect_error);
}

   $dataNow1 = date('Y-m-d');

   $sqlPH = "SELECT ph, FROM_UNIXTIME(data_send, '%Y-%m-%d') as send_p FROM my_myfishtank.ph_tab WHERE FROM_UNIXTIME(data_send, '%Y-%m-%d') = '$dataNow1' ORDER BY data_send";
   $resultPH = $con->query($sqlPH);  
   $ph_array = array();
   if ($resultPH->num_rows > 0) {
     // output data of each row
     while($rowPH = $resultPH->fetch_assoc()) {
       $ph_array[] = $rowPH["ph"];
       $sendPH = $rowPH["send_p"];
     }
   } else {
     $sendPH = "no data";
   }
  
   $sqlEC = "SELECT ec, FROM_UNIXTIME(data_send, '%Y-%m-%d') as send_e FROM my_myfishtank.ec_tab WHERE FROM_UNIXTIME(data_send, '%Y-%m-%d') = '$dataNow1' ORDER BY data_send";
   $resultEC = $con->query($sqlEC); 
   $ec_array = array();
   if ($resultEC->num_rows > 0) {
      // output data of each row
      while($rowEC = $resultEC->fetch_assoc()) {
        $ec_array[] = $rowEC["ec"];
        $sendEC = $rowEC["send_e"];
      }
    } else {   
      $sendEC = "no data";
    }


  $sqlT = "SELECT temperature, FROM_UNIXTIME(data_send, '%Y-%m-%d') as send_t FROM my_myfishtank.temp_tab WHERE FROM_UNIXTIME(data_send, '%Y-%m-%d') = '$dataNow1' ORDER BY data_send";
  $resultT = $con->query($sqlT);
  $t_array = array();
  if ($resultT->num_rows > 0) {
    // output data of each row
    while($rowT = $resultT->fetch_assoc()) {
      $t_array[] = $rowT["temperature"];
      $sendT = $rowT["send_t"];
    }
  } else {
    $sendT = "no data";
  }
  $value = array("ValuePH" => number_format(calculate_median($ph_array), 2),
                 "ValueEC" => number_format(calculate_median($ec_array), 2),
                 "ValueT" => number_format(calculate_average($t_array), 2)
                 );
  $con->close();
    
  echo json_encode($value);
?>