<?php 
include('connection.php');
$no2 = $_POST['no2'];
$no3 = $_POST['no3'];
$gh = $_POST['gh'];
$kh = $_POST['kh'];
$id = $_POST['id'];

$sql = "UPDATE `my_myfishtank`.`watervalues_table` SET  `no2`='$no2' , `no3`= '$no3', `gh`='$gh',  `kh`='$kh' WHERE id='$id' ";
$query= mysqli_query($con,$sql);
$lastId = mysqli_insert_id($con);
if($query ==true){
    $data = array(
        'status'=>'true',
    );
    echo json_encode($data);
}else{
     $data = array(
        'status'=>'false', 
    );
    echo json_encode($data);
} 
   $con->close();
?>