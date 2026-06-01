<?php  
 //filter.php  
 if(isset($_POST["date"]))  
 {  
      include("connection.php");
      $output = '';  
      $query = "SELECT t.data_send as date, t.tds, e.ec FROM my_myfishtank.tds_tab t JOIN my_myfishtank.ec_tab e ON t.id = e.id WHERE FROM_UNIXTIME(t.data_send, '%Y-%m-%d') = '".$_POST["date"]."' ORDER BY t.data_send";
      $result_list = $con->query($query);  
      $con->close();
      $output .= "<ol>";
                  if ($result_list->num_rows > 0) {
                    while($row4 = $result_list->fetch_assoc()) {
      $output .= "<li class='style1'> At ".gmdate("H:i:s\ ", $row4['date'])."  TDS = ".round($row4['tds'])." ppm - EC = ".number_format($row4['ec'], 1)." µS/cm</li>";
                    }
                  }
      $output .= "</ol>";
      echo $output;  
    
 }  
 ?>

