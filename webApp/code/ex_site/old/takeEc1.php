<?php
include("connection.php");
if(!empty($_POST)){
  $sqlTDS = "SELECT tds, data_send as send, data_arrive as arr FROM my_myfishtank.tds_tab ORDER BY id";  
  
  $i = 0;
  //INSERT INTO `my_myfishtank`.`tds_tab` (`id`, `tds`, `data_send`, `data_arrive`) VALUES (NULL, NULL, NULL, CURRENT_TIMESTAMP);
  //$sqlT = "SELECT temperature, data_send as send FROM my_myfishtank.temp_tab ORDER BY data_send DESC limit 1";
  $resultT = $con->query($sqlTDS);
  if ($resultT->num_rows > 0) {
    $i = $i + 1;
            // output data of each row
            while($rowT = $resultT->fetch_assoc()) {
              $tds = $rowT["tds"];
              $sendT = $rowT["send"];
              $sendA = $rowT["arr"];
           
    $ec = $tds / 0.64;
    $queryEC = "INSERT INTO ec_tab (ec, data_send, data_arrive)
            VALUES ('".$ec."', '".$sendT."', '".$sendA."')";
    if ($con->query($queryEC) === TRUE) {
       echo $i."Ok ".$ec." ".$dataSend."\n";
    } else {
      echo "Error: ".$sql."<br>".$con->error;
    }
   }

          }
  }
   $con->close();
?>