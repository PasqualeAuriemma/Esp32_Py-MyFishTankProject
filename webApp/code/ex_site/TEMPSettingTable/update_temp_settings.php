temp<?php 
include('connection.php');
$id = $_POST['id'];

$temp = $_POST['temp'];
$temp = !empty($temp) ? "'$temp'" : "NULL";
$send = $_POST['data_send'];
$send = !empty($send) ? "'$send'" : "NULL";


$sql = "UPDATE  `temp_tab` SET  `id`=$id, `temp`=$temp, `data_send`=$send  WHERE id='$id' ";
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
