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

$output = '<div class="row">';

$sub_array = array();
$sub_array_quantities = array();
$sub_array_data = array();


while($row = mysqli_fetch_assoc($query)){
	$sub_array += [$row['name'] => number_format($row['qnt'], 2, '.', '')];
    $sub_array_data += [$row['name'] => $row['date']];
}

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
        //print $sql_quantities_npk;
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

while (list($var, $val) = each($sub_array)) {
  $pp = $sub_array_quantities[$var];
  $dat = $sub_array_data[$var];   
  //print "$var is $val and $pp oo $dat \n";
  if($var == "Co2"){
     // Creates DateTime objects
     $lastCharge = new DateTime($dat);
     $dataNow = new DateTime(); 
     if ($lastCharge > $dataNow){
       $remaining = $val;
     }else{
       // Calling the diff() function on above
       // two DateTime objects
       $difference = $lastCharge->diff($dataNow);  
       // Getting the difference between two
       // given DateTime objects
       $remaining = $val - $difference->format('%R%a days');
     }
     $whole_int = intval($val);
     $percentage = ($remaining * 100) / $val; 
     
     $output .=  "<div class=\"col-sm-4 grid-margin\">
                            <div class=\"card\">
                                <div class=\"card-body\">
                                    <h5 style=\"color:#f8f9fa;\">$var</h5>
                                    <p class=\"text-muted\">Remaining $remaining of $whole_int days</p>
                                    <div class=\"progress progress-md portfolio-progress\">
                                        <div class=\"progress-bar bg-success\" role=\"progressbar\" style=\"width: $percentage%\" aria-valuenow=\"$percentage\" aria-valuemin=\"0\" aria-valuemax=\"100\"></div>
                                    </div>
                                </div>
                            </div>
                        </div>";
  }else{
     $remaining = $val - $sub_array_quantities[$var];
     $percentage = ($remaining * 100) / $val; 
     //print " $remaining - $percentage%\n";
     $whole_int = intval($val);
     if($var == "Stick"){
        $whole = floor($remaining);      
        $fraction = ($remaining - $whole) * 100 ; 
        $fraction_string = "";
        if ($fraction == 15){
          $fraction_string .= "a quarter";
        }else if ($fraction == 50){
          $fraction_string .= "half";
        }else{
          $fraction_string .= "three quarters";
        }
        $output .=  "<div class=\"col-sm-4 grid-margin\">
                            <div class=\"card\">
                                <div class=\"card-body\">
                                    <h5 style=\"color:#f8f9fa;\">$var</h5>
                                    <p class=\"text-muted\">Remaining $whole sticks and $fraction_string on $whole_int</p>
                                    <div class=\"progress progress-md portfolio-progress\">
                                        <div class=\"progress-bar bg-success\" role=\"progressbar\" style=\"width: $percentage%\" aria-valuenow=\"$percentage\" aria-valuemin=\"0\" aria-valuemax=\"100\"></div>
                                    </div>
                                </div>
                            </div>
                        </div>";
     }else {
       $output .=  "<div class=\"col-sm-4 grid-margin\">
                            <div class=\"card\">
                                <div class=\"card-body\">
                                    <h5 style=\"color: #f8f9fa;\">$var</h5>
                                    <p class=\"text-muted\">Remaining $remaining ml on $whole_int ml</p>
                                    <div class=\"progress progress-md portfolio-progress\">
                                        <div class=\"progress-bar bg-success\" role=\"progressbar\" style=\"width: $percentage%\" aria-valuenow=\"$percentage\" aria-valuemin=\"0\" aria-valuemax=\"100\"></div>
                                    </div>
                                </div>
                            </div>
                        </div>";
     }
  }  
}

$output .= "</div>";

echo $output;
mysqli_close($con);
?>