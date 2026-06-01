<?php

session_start();

include('connection.php');

$output= array();
$sql = "SELECT * FROM watervalues_table";

$totalQuery = mysqli_query($con,$sql);
$total_all_rows = mysqli_num_rows($totalQuery);

if(isset($_POST['search']['value']))
{
	$search_value = $_POST['search']['value'];
	$sql .= " WHERE data like '%".$search_value."%'";
    $sql .= " OR EC_PRE like '%".$search_value."%'";
	$sql .= " OR EC_AFT like '%".$search_value."%'";
	$sql .= " OR PH like '%".$search_value."%'";
	$sql .= " OR no2 like '%".$search_value."%'";
	$sql .= " OR no3 like '%".$search_value."%'";
	$sql .= " OR gh like '%".$search_value."%'";
    $sql .= " OR kh like '%".$search_value."%'";
    $sql .= " OR po4 like '%".$search_value."%'";
}

if(isset($_POST['order']))
{
	$column_name = $_POST['order'][0]['column'];
	$order = $_POST['order'][0]['dir'];
	$sql .= " ORDER BY ".$column_name." ".$order."";
}
else
{
	$sql .= " ORDER BY id desc";
}

if($_POST['length'] != -1)
{
	$start = $_POST['start'];
	$length = $_POST['length'];
	$sql .= " LIMIT  ".$start.", ".$length;
}	

$query = mysqli_query($con,$sql);
$count_rows = mysqli_num_rows($query);
$data = array();
while($row = mysqli_fetch_assoc($query))
{
	$sub_array = array();
	//$sub_array[] = $row['id'];
	$sub_array[] = date_format(date_create($row['data']),"d/m/Y");
    $sub_array[] = $row['EC_PRE'];
	$sub_array[] = $row['EC_AFT'];
	$sub_array[] = $row['PH'];
	$sub_array[] = $row['no2'];
	$sub_array[] = $row['no3'];
	$sub_array[] = $row['gh'];
    $sub_array[] = $row['kh'];
    $sub_array[] = $row['po4'];
    if (!isset($_SESSION["email"]) || !isset($_SESSION["loggedIn"])){
    	$sub_array[] = '';      
    }else{
        $sub_array[] = '<a href="javascript:void();" data-id="'.$row['id'].'"  class="btn btn-info btn-sm editbtn" ><i class="mdi mdi-table-edit"></i></a>  <a href="javascript:void();" data-id="'.$row['id'].'"  class="btn btn-danger btn-sm deleteBtn" ><i class="mdi mdi-table-row-remove"></i></a>';      
    }
    $data[] = $sub_array;
}



$output = array(
	'draw'=> intval($_POST['draw']),
	'recordsTotal' =>$count_rows ,
	'recordsFiltered'=>   $total_all_rows,
	'data'=>$data,
);
echo  json_encode($output);
