<?php

if (!empty($_POST)){
  
  $selector = $_POST["button"];
  
  $selector = !empty($selector) ? "$selector" : "1";      
  
} else{
  $selector = "1"; 
}

header('Content-Type: application/json');

include("../connection.php");
include("../queryAndFunction.php");

if ($con->connect_error) {
  die("Connection failed: " . $con->connect_error);
}

   $dataNow1 = date('Y-m-d');

   $sqlPH = getDailyPH($dataNow1);
   
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
  
   $sqlEC = getDailyEC($dataNow1);
   
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

  $sqlT = getDailyTemperature($dataNow1);
  
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