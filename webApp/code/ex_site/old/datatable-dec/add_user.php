<?php 
include('connection.php');
$no2 = $_POST['no2'];
$no3 = $_POST['no3'];
$gh = $_POST['gh'];
$kh = $_POST['kh'];

$sql = "INSERT INTO `my_myfishtank`.`watervalues_table` (`no2`, `no3`, `gh`, `kh`) VALUES ('$no2', '$no3', '$gh', '$kh' )";
//$sql = "INSERT INTO `watervalues_table` (`no2`,`no3`,`gh`,`kh`) values ('$no2', '$no3', '$gh', '$kh' )";
$query= mysqli_query($con, $sql);
$lastId = mysqli_insert_id($con);
if($query == true)
{
   
    $data = array(
        'status'=>'true', 
    );
    echo json_encode($data);
}
else
{
     $data = array(
        'status'=>'false',
    );
    echo json_encode($data);
} 
   $con->close();
    
?>