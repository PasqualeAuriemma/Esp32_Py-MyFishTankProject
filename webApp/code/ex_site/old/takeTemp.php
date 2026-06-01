<?php
  include("connection.php");

  if(!empty($_POST)){                  //($_GET)){
    $temperature = $_POST["Temp"];     // $_GET["Temp"];
    $dataSend = substr($_POST["Date"], 0, 10);
    $query = "INSERT INTO temp_tab (temperature, data_send)
            VALUES ('".$temperature."', '".$dataSend."')";
    if ($con->query($query) === TRUE) {
      echo "Ok ".$temperature  . " " . $dataSend;
    } else {
      echo "Error: " . $sql . "<br>" . $con->error;
    }
  }
   $con->close();

?>