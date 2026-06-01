<?php 
include('connection.php');
$id = $_POST['id'];

$tds = $_POST['tds'];
$tds = !empty($tds) ? "'$tds'" : "NULL";
$send = $_POST['data_send'];
$send = !empty($send) ? "'$send'" : "NULL";


$sql = "UPDATE  `tds_tab` SET  `id`=$id, `tds`=$tds, `data_send`=$send  WHERE id='$id' ";
$query= mysqli_query($con,$sql);
$lastId = mysqli_insert_id($con);
if($query ==true)
{
   
    $data = array(
        'status'=>'true',
       
    );

    echo json_encode($data);
}else{
     $data = array('status'=>'false',);
    echo json_encode($data);
} 
   $con->close();
?>
