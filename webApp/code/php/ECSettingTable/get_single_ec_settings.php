<?php 
session_start();
include('../connection.php');
include("../queryAndFunction.php");

$id = $_POST['id'];
$sql = getSigleElemFromTableQuery($id, "ec");
$query = mysqli_query($con,$sql);
$row = mysqli_fetch_assoc($query);
echo json_encode($row);
?>