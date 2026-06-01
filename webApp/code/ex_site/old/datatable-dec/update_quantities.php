<?php 
include('connection.php');
$id = $_POST['id'];
$k = $_POST['potassio'];
$mg = $_POST['magnesio'];
$fe = $_POST['ferro'];
$rinverdente = $_POST['rinverdente'];
$npk = $_POST['npk'];

$sql = "UPDATE `fertilization_tab` SET  `k`='$k' , `mg`= '$mg', `fe`='$fe',  `rinverdente`='$rinverdente',  `npk`='$npk'  WHERE id='$id' ";
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
