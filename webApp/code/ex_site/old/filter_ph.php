<?php  
 //filter.php  
 if(isset($_POST["date"]))  
 {  
      include("connection.php");
      $output = '';  
      $query = "SELECT data_send as date, ph FROM my_myfishtank.ph_tab WHERE FROM_UNIXTIME(data_send, '%Y-%m-%d') = '".$_POST["date"]."' ORDER BY data_send";
      $result_list = $con->query($query);  
      $con->close();
      $output .= "<ol>";
                  if ($result_list->num_rows > 0) {
                    while($row4 = $result_list->fetch_assoc()) {
      $output .= "<li> At ".gmdate("H:i:s\ ", $row4['date'])."  PH = ".$row4['ph']."</li>";
                    }
                  }
      $output .= "</ol>";
      echo $output;  
    
 }  
 ?>

