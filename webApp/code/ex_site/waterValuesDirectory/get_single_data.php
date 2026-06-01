<?php include('connection.php');
  $id = $_POST['id'];
  $sql = "SELECT * FROM `my_myfishtank`.`watervalues_table` WHERE id='$id' LIMIT 1";
  $query = mysqli_query($con,$sql);
  $row = mysqli_fetch_assoc($query);
  echo json_encode($row);
  $con->close();
?>
