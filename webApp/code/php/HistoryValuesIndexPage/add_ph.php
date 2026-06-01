<?php 
  // function used to add record with button inside ph_tab
  include("../connection.php");
  include("../queryAndFunction.php");

  if(!empty($_POST)){
    $ph = $_POST["ph"];
    $ph = !empty($ph) ? "'$ph'" : "NULL";
    $dataSend = time();
    
    $sql = getPHInsertQuery($ph, $dataSend);
    $query= mysqli_query($con, $sql);
    if($query == true){
      $data = array('status'=>'true', );
      echo json_encode($data);
    }else{
      $data = array('status'=>'false',);
      echo json_encode($data);
    }   
  }
  $con->close();
?>