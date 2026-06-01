<?php 
include('connection.php');
$k = $_POST['potassio']; if($k==""){5; }else{10;}
$mg = $_POST['magnesio'];
$fe = $_POST['ferro'];
$rinverdente = $_POST['rinverdente'];
$npk = $_POST['npk'];

$sql = "INSERT INTO `my_myfishtank`.`fertilization_tab` (`k`, `mg`, `fe`, `rinverdente`, `npk`) VALUES ('$k', '$mg', '$fe', '$rinverdente', '$npk')";
//$sql = "INSERT INTO fertilization_tab (k,mg, fe, rinverdente, npk) VALUES ($k, $mg, $fe, $rinverdente, $npk)";
$query= mysqli_query($con, $sql);
$lastId = mysqli_insert_id($con);
if($query == true){
    $data = array('status'=>'true', );
    echo json_encode($data);
}else{
     $data = array('status'=>'false',);
    echo json_encode($data);
} 
   $con->close();  
?>