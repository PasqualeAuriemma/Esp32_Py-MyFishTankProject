<?php 

header('Content-Type: application/json');

include("php/connection.php");
include("php/queryAndFunction.php");

if ($con->connect_error) {
  die("Connection failed: " . $con->connect_error);
}

$q = intval($_GET['data_received']);
$sql_max = getFertilizationValues();
$sql_quantities = getAllFertilizationSum();
$sql_quantities_k = getFertilizationSum("k");
$sql_quantities_mg = getFertilizationSum("mg");
$sql_quantities_fe = getFertilizationSum("fe");
$sql_quantities_rin = getFertilizationSum("rinverdente");
$sql_quantities_p = getFertilizationSum("p");
$sql_quantities_n = getFertilizationSum("n");
$sql_quantities_npk = getFertilizationSum("npk");

$query = mysqli_query($con, $sql_max);
$output = '<ul class=/"nav/">';

$sub_array = array();
$sub_array_quantities = array();
$sub_array_data = array();

while($row = mysqli_fetch_assoc($query)){
	$sub_array += [$row['name'] => number_format($row['qnt'], 2, '.', '')];
    $sub_array_data += [$row['name'] => $row['date']];
}

while (list($var1, $data) = each($sub_array_data)) {
    if($var1 == "Potassio" && $data != null){
    	$sql_quantities_k = addDateToFertilizationQuery($sql_quantities_k, $data); 
    }
    if($var1 == "Magnesio" and $data != null){
    	$sql_quantities_mg = addDateToFertilizationQuery($sql_quantities_mg, $data); 
    }
    if($var1 == "Ferro" and $data != null){
    	$sql_quantities_fe = addDateToFertilizationQuery($sql_quantities_fe, $data); 
    }
    if($var1 == "Rinverdente" and $data != null){
    	$sql_quantities_rin = addDateToFertilizationQuery($sql_quantities_rin, $data); 
    }
    if($var1 == "Fosforo" and $data != null){
    	$sql_quantities_p = addDateToFertilizationQuery($sql_quantities_p, $data); 
    }
    if($var1 == "Azoto" and $data != null){
    	$sql_quantities_n = addDateToFertilizationQuery($sql_quantities_n, $data); 
    }
    if($var1 ==  "Stick" and $data != null){
    	$sql_quantities_npk = addDateToFertilizationQuery($sql_quantities_npk, $data); 
    }
}

$query_quantities_k = mysqli_query($con, $sql_quantities_k);
$query_quantities_mg = mysqli_query($con, $sql_quantities_mg);
$query_quantities_fe = mysqli_query($con, $sql_quantities_fe);
$query_quantities_rin = mysqli_query($con, $sql_quantities_rin);
$query_quantities_p = mysqli_query($con, $sql_quantities_p);
$query_quantities_n = mysqli_query($con, $sql_quantities_n);
$query_quantities_npk = mysqli_query($con, $sql_quantities_npk);

while($row = mysqli_fetch_assoc($query_quantities_k)){
	$sub_array_quantities += ["Potassio" => number_format($row['k'], 2, '.', '')];
}
while($row = mysqli_fetch_assoc($query_quantities_mg)){
	$sub_array_quantities += ["Magnesio" => number_format($row['mg'], 2, '.', '')];
}
while($row = mysqli_fetch_assoc($query_quantities_fe)){
	$sub_array_quantities += ["Ferro" => number_format($row['fe'], 2, '.', '')];
}
while($row = mysqli_fetch_assoc($query_quantities_rin)){
	$sub_array_quantities += ["Rinverdente" => number_format($row['rinverdente'], 2, '.', '')];
}
while($row = mysqli_fetch_assoc($query_quantities_p)){
	$sub_array_quantities += ["Fosforo" =>  number_format($row['p'], 2, '.', '')];
}
while($row = mysqli_fetch_assoc($query_quantities_n)){
	$sub_array_quantities += ["Azoto" => number_format($row['n'], 2, '.', '')];
}
while($row = mysqli_fetch_assoc($query_quantities_npk)){
	$sub_array_quantities += ["Stick" => number_format($row['npk'], 2, '.', '')];
}

while (list($var, $val) = each($sub_array)) {   
     $remaining = $val - $sub_array_quantities[$var];
     $pp = $sub_array_quantities[$var];
     $dat = $sub_array_data[$var];   
     $percentage = ($remaining * 100) / $val; 
     $whole_int = intval($val);
 
     $ppppp = "datepicker_$var";
     $output .= "<li class=\"nav-item menu-items\">
                 <input type=\"text\" name=\"$ppppp\" id=\"$ppppp\"  placeholder=\"$dat\" />
            </li>";
}
$output .= "</ul>";

echo $output;
mysqli_close($con);
?>