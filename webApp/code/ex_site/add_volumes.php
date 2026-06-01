<?php 
include('php/connection.php');

$sql = "UPDATE `fertilization_volumes` SET ";

$date = $_POST['data'];
$sql = !empty($date) ? $sql." `data_inizio` = '$date'" : $sql;

$volume = $_POST['vol'];
if(!empty($date) and !empty($volume)){
$sql = $sql.",";
}
$sql = !empty($volume) ? $sql." `qnt` = '$volume'" : $sql;

$select = $_POST['select'];
$sql = !empty($select) ? $sql." WHERE fertilizzante = '$select';" : $sql;

echo $sql;
//$sql = "INSERT INTO `my_myfishtank`.`fertilization_volumes` (`fertilizzante`, `qnt`, `data_inizio`) VALUES ($select,$volume,$date)";
$query= mysqli_query($con, $sql);

if($query == true){
    $data = array('status'=>'true', );
    echo json_encode($data);
}else{
     $data = array('status'=>'false',);
    echo json_encode($data);
} 
   $con->close();  
?>
