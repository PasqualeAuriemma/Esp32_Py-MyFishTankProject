<?php
header('Content-Type: application/json');
 include("connection.php");
          if ($con->connect_error) {
            die("Connection failed: " . $con->connect_error);
          }
          $sqlT = "SELECT  data_send as send, ph FROM my_myfishtank.ph_tab ORDER BY data_send DESC limit 4";
          $sqlEC = "SELECT ec, data_send as send FROM my_myfishtank.ec_tab ORDER BY data_send DESC limit 1";
          //$sqlTDS = "SELECT tds, data_send as send FROM my_myfishtank.tds_tab ORDER BY data_send DESC limit 1";
          $sqlPH = "SELECT ph, data_send as send FROM my_myfishtank.ph_tab ORDER BY data_send DESC limit 1";

          $result = $con->query($sqlT);



$data = array();
foreach ($result as $row) {
	$data[] = $row;
}



echo json_encode($data);
?>