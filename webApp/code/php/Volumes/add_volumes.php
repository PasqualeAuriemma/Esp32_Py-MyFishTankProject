<?php 
include('../connection.php');
include("../queryAndFunction.php");

$date = $_POST['data'];
$volume = $_POST['vol'];
$select = $_POST['select'];

$sql = getUpdateFertilizationVolumeQuery($date, $volume, $select);
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
