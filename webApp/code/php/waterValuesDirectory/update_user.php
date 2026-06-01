<?php 
session_start();
include('../connection.php');
include("../queryAndFunction.php");

$ecP = $_POST['ecP'];
$ecP = !empty($ecP) || $ecP=="0" ? "'$ecP'" : "NULL";
$ecA = $_POST['ecA'];
$ecA = !empty($ecA) || $ecA=="0" ? "'$ecA'" : "NULL";
$ph = $_POST['ph'];
$ph = !empty($ph) || $ph=="0" ? "'$ph'" : "NULL";
$no2 = $_POST['no2'];
$no2 = !empty($no2) || $no2=="0" ? "'$no2'" : "NULL";
$no3 = $_POST['no3'];
$no3 = !empty($no3) || $no3=="0" ? "'$no3'" : "NULL";
$gh = $_POST['gh'];
$gh = !empty($gh) || $gh=="0" ? "'$gh'" : "NULL";
$kh = $_POST['kh'];
$kh = !empty($kh) || $kh=="0" ? "'$kh'" : "NULL";
$po4 = $_POST['po4'];
$po4 = !empty($po4) || $po4=="0" ? "'$po4'" : "NULL";
$id = $_POST['id'];

$sql = getUpdateWaterValuesTableQuery($ecP, $ecA, $ph, $no2, $no3, $gh, $kh, $po4, $id);

$query= mysqli_query($con,$sql);
$lastId = mysqli_insert_id($con);
if($query ==true){
    $data = array('status'=>'true',);
    echo json_encode($data);
}else{
    $data = array('status'=>'false',);
    echo json_encode($data);
} 
   $con->close();
?>