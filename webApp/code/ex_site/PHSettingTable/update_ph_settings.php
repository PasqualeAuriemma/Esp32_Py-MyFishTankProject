<?php 
include('connection.php');
$id = $_POST['id'];
$ph = $_POST['ph'];
$ph = !empty($ph) ? "'$ph'" : "NULL";
$send = $_POST['data_send'];
$send = !empty($send) ? "'$send'" : "NULL";


$sql = "UPDATE  `ph_tab` SET  `id`=$id , `ph`=$ph, `data_send`=$send  WHERE id='$id' ";
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
