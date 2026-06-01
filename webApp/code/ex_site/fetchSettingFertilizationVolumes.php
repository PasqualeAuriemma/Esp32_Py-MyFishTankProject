<?php 

header('Content-Type: application/json');

include("php/connection.php");
if ($con->connect_error) {
  die("Connection failed: " . $con->connect_error);
}

$q = intval($_GET['data_received']);
 
$sql_max = "SELECT fertilizzante AS name, qnt, data_inizio AS date FROM my_myfishtank.fertilization_volumes";
$sql_max1 = "SELECT nitratoPotassio AS nk, solfatoMagnesio AS sm, anidrideFosforica AS af, azoto AS a, ferroPotenziato AS fp, rinverderte AS r, stick AS s FROM my_myfishtank.fertilization_volume";
$sql_quantities = "SELECT  SUM(k) AS k, SUM(mg) AS mg, SUM(fe) AS fe, SUM(rinverdente) as rinverdente, SUM(p) AS p, SUM(n) AS n, SUM(npk) AS npk FROM my_myfishtank.fertilization_tab";

$sql_quantities_k = "SELECT SUM(k) AS k FROM my_myfishtank.fertilization_tab";
$sql_quantities_mg = "SELECT SUM(mg) AS mg FROM my_myfishtank.fertilization_tab";
$sql_quantities_fe = "SELECT SUM(fe) AS fe FROM my_myfishtank.fertilization_tab";
$sql_quantities_rin = "SELECT SUM(rinverdente) as rinverdente FROM my_myfishtank.fertilization_tab";
$sql_quantities_p = "SELECT SUM(p) AS p FROM my_myfishtank.fertilization_tab";
$sql_quantities_n = "SELECT SUM(n) AS n FROM my_myfishtank.fertilization_tab";
$sql_quantities_npk = "SELECT SUM(npk) AS npk FROM my_myfishtank.fertilization_tab";

$query = mysqli_query($con, $sql_max);

$output = '<ul class=/"nav/">';

//                        <div class="col-sm-4 grid-margin">
//                            <div class="card">
//                                <div class="card-body">
//                                    <h5>Revenue</h5>
//                                    <p class="text-muted">Well, it seems to be working now. </p>
//                                    <div class="progress progress-md portfolio-progress">
//                                        <div class="progress-bar bg-success" role="progressbar" style="width: 50%" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
//                                    </div>
//                                </div>
//                            </div>
//                        </div>';

$sub_array = array();
$sub_array_quantities = array();
$sub_array_data = array();

while($row = mysqli_fetch_assoc($query)){
	$sub_array += [$row['name'] => number_format($row['qnt'], 2, '.', '')];
    $sub_array_data += [$row['name'] => $row['date']];
}

/*
while($row = mysqli_fetch_assoc($query)){
	$sub_array = ["Potassio" => $row['nk'], 
                  "Magnesio" => $row['sm'], 
                  "Fosforo" => $row['af'], 
                  "Azoto" => $row['a'], 
                  "Ferro" => $row['fp'], 
                  "Rinverdente" => $row['r'], 
                  "Stick" => $row['s']];
    //echo $row['nk']." ".$row['sm']." ".$row['af']." ".$row['a']." ".$row['fp']." ".$row['r']." ".$row['s'] ;
}
*/

while (list($var1, $data) = each($sub_array_data)) {
    //print "$var1 is $data \n"; 
    if($var1 == "Potassio" && $data != null){
    	$sql_quantities_k = $sql_quantities_k." WHERE data >= DATE(\"".$data."\")"; 
        //print $sql_quantities_k;
    }
    if($var1 == "Magnesio" and $data != null){
    	$sql_quantities_mg = $sql_quantities_mg." WHERE data >= DATE(\"".$data."\")"; 
        //print $sql_quantities_mg;
    }
    if($var1 == "Ferro" and $data != null){
    	$sql_quantities_fe = $sql_quantities_fe." WHERE data >= DATE(\"".$data."\")"; 
        //print $sql_quantities_ef;
    }
    if($var1 == "Rinverdente" and $data != null){
    	$sql_quantities_rin = $sql_quantities_rin." WHERE data >= DATE(\"".$data."\")"; 
        //print $sql_quantities_rin;
    }
    if($var1 == "Fosforo" and $data != null){
    	$sql_quantities_p = $sql_quantities_p." WHERE data >= DATE(\"".$data."\")"; 
        //print $sql_quantities_p;
    }
    if($var1 == "Azoto" and $data != null){
    	$sql_quantities_n= $sql_quantities_n." WHERE data >= DATE(\"".$data."\")"; 
        //print $sql_quantities_n;
    }
    if($var1 ==  "Stick" and $data != null){
    	$sql_quantities_npk = $sql_quantities_npk." WHERE data >= DATE(\"".$data."\")"; 
        print $sql_quantities_npk;
    }
}

$query_quantities_k = mysqli_query($con, $sql_quantities_k);
$query_quantities_mg = mysqli_query($con, $sql_quantities_mg);
$query_quantities_fe = mysqli_query($con, $sql_quantities_fe);
$query_quantities_rin = mysqli_query($con, $sql_quantities_rin);
$query_quantities_p = mysqli_query($con, $sql_quantities_p);
$query_quantities_n = mysqli_query($con, $sql_quantities_n);
$query_quantities_npk = mysqli_query($con, $sql_quantities_npk);
//$query_quantities = mysqli_query($con, $sql_quantities);

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



/*
while($row = mysqli_fetch_assoc($query_quantities)){
	$sub_array_quantities = ["Potassio" => number_format($row['k'], 2, '.', ''), 
                             "Magnesio" => number_format($row['mg'], 2, '.', ''), 
                             "Ferro" => number_format($row['fe'], 2, '.', ''), 
                             "Rinverdente" => number_format($row['rinverdente'], 2, '.', ''), 
                             "Fosforo" =>  number_format($row['p'], 2, '.', ''), 
                             "Azoto" => number_format($row['n'], 2, '.', ''), 
                             "Stick" => number_format($row['npk'], 2, '.', '') ];
}
*/


   while (list($var, $val) = each($sub_array)) {
     //$pp = $sub_array_quantities[$var];
     //print "$var is $val and $pp \n";
   
     $remaining = $val - $sub_array_quantities[$var];
     
     $pp = $sub_array_quantities[$var];
     $dat = $sub_array_data[$var];   
     //print "$var is $val and $pp oo $dat \n";
   
  
     $percentage = ($remaining * 100) / $val; 
     //print " $remaining - $percentage%\n";
     $whole_int = intval($val);
/*       
     $output .=    "<li class=\"nav-item menu-items\">
                       <form id=\"add$var\" name=\"add$var\" action=\"\">
                          <h5 style=\"color: #f8f9fa;\">$var</h5>
                          <p>
                            <input type=\"text\" name=\"datepicker_$var\" id=\"datepicker_$var\"  placeholder=\"$dat\" />
                            <input type=\"button\" name=\"filter_$var\" id=\"filter_$var\" value=\"Select\" class=\"btn btn-info\" />
                            <input type=\"number\" step=\"any\" class=\"form-control\" value=\"$val\" id=\"addField$var\" name=\"$var\">
                          </p>

                          <div class=\"text-center\">
                             <button type=\"submit\" class=\"btn btn-primary\">Submit</button>
                          </div>
                       </form>                               
                    </li>";
 */
 
 $ppppp = "datepicker_$var";
 
 $output .= "<li class=\"nav-item menu-items\">
                 <input type=\"text\" name=\"$ppppp\" id=\"$ppppp\"  placeholder=\"$dat\" />
             </li>";
 
   }

$output .= "</ul>";

echo $output;
mysqli_close($con);
?>