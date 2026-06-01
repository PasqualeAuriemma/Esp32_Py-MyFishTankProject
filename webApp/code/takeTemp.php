<?php
  include("php/connection.php");
  include("php/queryAndFunction.php");
  
  if(!empty($_POST)){
    $temperature = isset($_POST["Temp"]) ? $_POST["Temp"] : null;
    $dataSend = isset($_POST["Date"]) ? substr($_POST["Date"], 0, 10) : null;
    
    if ($temperature !== null && $dataSend !== null) {
      $stmt = $con->prepare("INSERT INTO temp_tab (temperature, data_send) VALUES (?, ?)");
      if ($stmt) {
        $stmt->bind_param("ds", $temperature, $dataSend);
        if ($stmt->execute()) {
          echo "Ok " . htmlspecialchars($temperature) . " " . htmlspecialchars($dataSend);
        } else {
          echo "Error: " . $stmt->error;
        }
        $stmt->close();
      } else {
        echo "Error: " . $con->error;
      }
    } else {
      echo "Error: Missing parameters";
    }
  }
  $con->close();
?>