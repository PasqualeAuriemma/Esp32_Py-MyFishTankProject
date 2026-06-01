<?php 
  // function used to add record with button inside temp_tab
    include("php/connection.php");

  if(!empty($_POST)){
    $temp = $_POST["temp"];
    $dataSend = time();
    
    $sql = "INSERT INTO temp_tab (temperature, data_send)
            VALUES ('".$temp."', '".$dataSend."')";
 
      
    $query= mysqli_query($con, $sql);
 

    if($query == true){
      $data = array(
        'status'=>'true', 
      );
      echo json_encode($data);
    }else{
      $data = array(
        'status'=>'false',
      );
      echo json_encode($data);
    }   
            
  }
  $con->close();
?>