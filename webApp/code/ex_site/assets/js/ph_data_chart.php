<?php
header('Content-Type: application/json');
 include("php/connection.php");
          if ($con->connect_error) {
            die("Connection failed: " . $con->connect_error);
          }
          $sqlPH_chart = "SELECT ph, data_send as send FROM my_myfishtank.ph_tab ORDER BY data_send DESC";

          $resultPH_chart = $con->query($sqlPH_chart);



$data = array();
foreach ($resultPH_chart as $row) {
	$data[] = $row;
}



echo json_encode($data);
?>