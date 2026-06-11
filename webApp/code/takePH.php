<?php
  include("php/connection.php");
  include("php/queryAndFunction.php");

  if(!empty($_POST)){
    $ph = isset($_POST["PH"]) ? $_POST["PH"] : null;
    $dataSend = isset($_POST["Date"]) ? substr($_POST["Date"], 0, 10) : null;

    if ($ph !== null && $dataSend !== null) {
      $ph = round(floatval($ph), 2);

      $stmt = $con->prepare("INSERT INTO ph_tab (ph, data_send) VALUES (?, ?)");
      if ($stmt) {
        $stmt->bind_param("ds", $ph, $dataSend);
        if ($stmt->execute()) {
          echo "Ok " . htmlspecialchars($ph) . " " . htmlspecialchars($dataSend);
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