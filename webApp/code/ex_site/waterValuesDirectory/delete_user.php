<?php 
include('connection.php');

$user_id = $_POST['id'];
$sql = "DELETE FROM `my_myfishtank`.`watervalues_table` WHERE id='$user_id'";
$delQuery =mysqli_query($con,$sql);
if($delQuery==true){
	 $data = array(
        'status'=>'success',
    );
    echo json_encode($data);
}else{
     $data = array(
        'status'=>'failed', 
    );
    echo json_encode($data);
} 
$con->close();
?>