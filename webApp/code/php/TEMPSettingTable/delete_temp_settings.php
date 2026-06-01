<?php 
session_start();
include('../connection.php');
include("../queryAndFunction.php");

$user_id = $_POST['id'];
$sql = getDeleteFromTableQuery($user_id, "temperature");
$delQuery = mysqli_query($con,$sql);
if($delQuery == true){
	 $data = array('status'=>'success',);
     echo json_encode($data);
}else{
     $data = array('status'=>'failed',);
     echo json_encode($data);
} 

?>