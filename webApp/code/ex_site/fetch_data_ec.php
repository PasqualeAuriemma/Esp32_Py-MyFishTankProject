<?php 
if (!empty($_GET)){
  $data = $_GET["d"];
}else{
   $data = date('Y-m-d');
} 

header('Content-Type: application/json');

include("php/connection.php");
if ($con->connect_error) {
  die("Connection failed: " . $con->connect_error);
}

if ($data == "noData"){
	$dataNow1 = date('Y-m-d');
}else{
    $dataNow1 = $data;
}
$output= array();
  
$sql = "SELECT  e.id as id, e.data_send as data, e.ec as ec FROM my_myfishtank.tds_tab t JOIN my_myfishtank.ec_tab e ON t.id = e.id WHERE FROM_UNIXTIME(e.data_send, '%Y-%m-%d') = '$dataNow1' ORDER BY e.data_send DESC";

$totalQuery = mysqli_query($con,$sql);
$total_all_rows = mysqli_num_rows($totalQuery);

$query = mysqli_query($con,$sql);
$count_rows = mysqli_num_rows($query);

$data = array();
$result = $con->query($sql);

//foreach ($result as $row) {
//	$data[] = $row;
//}



while($row = mysqli_fetch_assoc($query))
{
	$sub_array = array();
	$sub_array = [ "id" => $row['id'], "data" => gmdate("H:i:s", $row['data']), "ec" => $row['ec']];
	$data[] = $sub_array;
}


$output = array(
	'draw'=> intval($_POST['draw']),
	'recordsTotal' => $count_rows ,
	'recordsFiltered' => $total_all_rows,
	'data' => $data,
);
echo  json_encode($output);
