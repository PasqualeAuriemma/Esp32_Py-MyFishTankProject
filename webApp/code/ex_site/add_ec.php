<?php 
  // function used to add record with button inside the ec_tab and tds_tab
  include("php/connection.php");

  if(!empty($_POST)){
    $ec = $_POST["ec"];
    $dataSend = time();
    
    $tds = $ec * 0.64;
    $sqlEC = "INSERT INTO ec_tab (ec, data_send)
            VALUES ('".$ec."', '".$dataSend."')";
    $sqlTDS = "INSERT INTO tds_tab (tds, data_send)
            VALUES ('".$tds."', '".$dataSend."')";
      
    $queryEC= mysqli_query($con, $sqlEC);
    $queryTDS= mysqli_query($con, $sqlTDS);

    if($queryEC == true and $queryTDS == true){
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