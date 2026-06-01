<?php 
  // function used to add record with button inside ph_tab
  include("connection.php");

  if(!empty($_POST)){
    $ph = $_POST["ph"];
    $ph = !empty($ph) ? "'$ph'" : "NULL";
    $dataSend = time();
    
    $sql = "INSERT INTO ph_tab (ph, data_send)
            VALUES (".$ph.", '".$dataSend."')";
 
      
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