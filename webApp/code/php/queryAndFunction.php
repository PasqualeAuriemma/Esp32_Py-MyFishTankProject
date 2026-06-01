<?php   

function getFertilizationInsertQuery($k, $mg, $fe, $rinverdente, $p, $n, $npk){
  return "INSERT INTO fertilization_tab (`k`, `mg`, `fe`, `rinverdente`, `p`, `n`, `npk`) VALUES (".$k.", ".$mg.", ".$fe.", ".$rinverdente.", ".$p.", ".$n.", ".$npk.")";
}

function getWaterValuesInsertQuery($ecP, $ecA, $ph, $no2, $no3, $gh, $kh, $po4){
  return "INSERT INTO watervalues_table (`EC_PRE`, `EC_AFT`, `PH`, `no2`, `no3`, `gh`, `kh`, `po4`) VALUES (".$ecP.", ".$ecA.", ".$ph.", ".$no2.", ".$no3.", ".$gh.", ".$kh.", ".$po4.")";
}

function getDeleteFromTableQuery($user_id, $table){
	$select_table = "";
	if ($table == "temperature"){
		$select_table = "temp_tab";
	}else if($table == "ph"){
		$select_table = "ph_tab";
	}else if($table == "ec"){
		$select_table = "ec_tab";
	}else if($table == "watervalues"){
	    $select_table = "watervalues_table";
    }else if($table == "fertilization"){
	    $select_table = "fertilization_tab";
    }else{
		$select_table = "tds_tab";
	}
    return "DELETE FROM ".$select_table." WHERE id='".$user_id."'";
}


function getFromTableQuery($table){
	if ($table == "temperature"){
	    return "SELECT * FROM temp_tab";
	}else if($table == "ph"){
		return "SELECT * FROM ph_tab";
	}else if($table == "ec"){
		return "SELECT * FROM ec_tab";
	}else if($table == "watervalues"){
	    return "SELECT * FROM watervalues_table";
    }else if($table == "fertilization"){
	    return "SELECT * FROM fertilization_tab";
    }else{
		return "SELECT * FROM tds_tab";
	}	
}

function getSeachFromTableQuery($sql, $search_value, $table){
	$sql .= " WHERE FORMAT(data_arrive, 'dd/MM/yyyy H:m:s', 'en-US' ) like '%".$search_value."%'";
    $sql .= " OR id like '%".$search_value."%'";
	$sql .= " OR ".$table." like '%".$search_value."%'";
	$sql .= " OR data_send like '%".$search_value."%'";
	return $sql;
}

function getSeachFromFertilizationTableQuery($sql, $search_value){	
	$sql .= " WHERE FORMAT(data, 'dd/MM/yyyy', 'en-US' ) like '%".$search_value."%'";
	$sql .= " OR k like '%".$search_value."%'";
	$sql .= " OR mg like '%".$search_value."%'";
	$sql .= " OR fe like '%".$search_value."%'";
    $sql .= " OR rinverdente like '%".$search_value."%'";
    $sql .= " OR p like '%".$search_value."%'";
    $sql .= " OR n like '%".$search_value."%'";
    $sql .= " OR npk like '%".$search_value."%'";
	return $sql;
	
}

function getOrderFromTableQuery($sql, $column_name, $order){
	$sql .= " ORDER BY ".$column_name." ".$order."";
	return $sql;
}

function getLimitFromTableQuery($sql, $start, $length){
	$sql .= " LIMIT  ".$start.", ".$length;
	return $sql;
}

function getSigleElemFromTableQuery($id, $table){
	$select_table = "";
	if ($table == "temperature"){
		$select_table = "temp_tab";
	}else if($table == "ph"){
		$select_table = "ph_tab";
	}else if($table == "ec"){
		$select_table = "ec_tab";
	}else if($table == "watervalues"){
	    $select_table = "watervalues_table";
    }else if($table == "fertilization"){
	    $select_table = "fertilization_tab";
    }else{
		$select_table = "tds_tab";
	}
	return "SELECT * FROM ".$select_table." WHERE id='".$id."' LIMIT 1";
}

function getUpdateTableQuery($id, $temp, $send, $table){
	$select_table = "";
	$element = "";
	if ($table == "temperature"){
		$select_table = "temp_tab";
		$element = "temperature";
	}else if($table == "ph"){
		$select_table = "ph_tab";
		$element = "ph";
	}else if($table == "ec"){
		$select_table = "ec_tab";
        $element = "ec";
	}else{
		$select_table = "tds_tab";
		$element = "tds";
	}	
    return "UPDATE ".$select_table." SET `id`=".$id.", `".$element."`=".$temp.", `data_send`=".$send." WHERE id='".$id."'";
}

function getUpdateFertilizationTableQuery($k, $mg, $fe, $rinverdente, $p, $n, $npk, $id){
	return "UPDATE `fertilization_tab` SET `k` = ".$k.", `mg` = ".$mg.", `fe` = ".$fe.", `rinverdente`= ".$rinverdente.", `p`= ".$p.", `n` = ".$n.", `npk` = ".$npk." WHERE id = '".$id."'";
}

function getUpdateWaterValuesTableQuery($ecP, $ecA, $ph, $no2, $no3, $gh, $kh, $po4, $id){
    return "UPDATE `watervalues_table` SET `EC_PRE` = ".$ecP.", `EC_AFT` = ".$ecA.", `PH`= ".$ph.", `no2`= ".$no2.", `no3`= ".$no3.", `gh` = ".$gh.", `kh`= ".$kh.", `po4` = ".$po4." WHERE id= '".$id."'";
}
function getECChartQuery(){
  return "SELECT e.ec as EC, t.tds as TDS, FROM_UNIXTIME(e.data_send, '%Y-%m-%d %H:%i:%s') as send FROM tds_tab t JOIN ec_tab e ON t.id = e.id ORDER BY e.data_send";
}        

function getPhChartQuery(){
  return "SELECT ph, FROM_UNIXTIME(data_send, '%Y-%m-%d') as send FROM ph_tab ORDER BY data_send";
}

function getTemperatureChartQuery(){
  return "SELECT temperature, FROM_UNIXTIME(data_send, '%Y-%m-%d') as send FROM temp_tab ORDER BY data_send";
}

function getECChartQueryWithDate($dataNow){
  return "SELECT e.ec as EC, t.tds as TDS, FROM_UNIXTIME(e.data_send, '%Y-%m-%d  %H:%i:%s') as send FROM tds_tab t JOIN ec_tab e ON t.id = e.id WHERE FROM_UNIXTIME(e.data_send, '%Y-%m-%d') >= '$dataNow' ORDER BY e.data_send";
}        

function getPhChartQueryWithDate($dataNow){
  return "SELECT FROM_UNIXTIME(data_send, '%Y-%m-%d') as send, ph FROM ph_tab WHERE FROM_UNIXTIME(data_send, '%Y-%m-%d') >= '$dataNow' ORDER BY data_send";                       
}

function getTemperatureChartQueryWithDate($dataNow){
  return "SELECT temperature, FROM_UNIXTIME(data_send, '%Y-%m-%d') as send FROM temp_tab WHERE FROM_UNIXTIME(data_send, '%Y-%m-%d') >= '$dataNow' ORDER BY data_send";      
}

function getECInsertQuery($ec, $dataSend){
  return "INSERT INTO ec_tab (ec, data_send) VALUES ('".$ec."', '".$dataSend."')";
}

function getTDSInsertQuery($tds, $dataSend){
  return "INSERT INTO tds_tab (tds, data_send) VALUES ('".$tds."', '".$dataSend."')";
}

function getPHInsertQuery($ph, $dataSend){
  return "INSERT INTO ph_tab (ph, data_send) VALUES (".$ph.", '".$dataSend."')";
}

function getTEMPInsertQuery($temp, $dataSend){
  return "INSERT INTO temp_tab (temperature, data_send) VALUES ('".$temp."', '".$dataSend."')";
}

function getDailyTemperature($data){
  return "SELECT temperature, FROM_UNIXTIME(data_send, '%Y-%m-%d') as send_t FROM temp_tab WHERE FROM_UNIXTIME(data_send, '%Y-%m-%d') = '$data' ORDER BY data_send";
}

function getDailyEC($data){
	return "SELECT ec, FROM_UNIXTIME(data_send, '%Y-%m-%d') as send_e FROM ec_tab WHERE FROM_UNIXTIME(data_send, '%Y-%m-%d') = '$data' ORDER BY data_send";
}

function getDailyPH($data){
	return "SELECT ph, FROM_UNIXTIME(data_send, '%Y-%m-%d') as send_p FROM ph_tab WHERE FROM_UNIXTIME(data_send, '%Y-%m-%d') = '$data' ORDER BY data_send";
}

function getDailyDescTDS_EC($data){
	return "SELECT e.id as id, e.data_send as data, e.ec as ec FROM tds_tab t JOIN ec_tab e ON t.id = e.id WHERE FROM_UNIXTIME(e.data_send, '%Y-%m-%d') = '$data' ORDER BY e.data_send DESC";
}          

function getDailyDescPh($data){
	return "SELECT p.id as id, p.data_send as data, p.ph as ph FROM ph_tab p WHERE FROM_UNIXTIME(p.data_send, '%Y-%m-%d') = '$data' ORDER BY p.data_send DESC";
}

function getDailyDescTemperature($data){
	return "SELECT t.id as id, t.data_send as data, t.temperature as temp FROM temp_tab t WHERE FROM_UNIXTIME(t.data_send, '%Y-%m-%d') = '$data' ORDER BY t.data_send DESC";
}

function getFertilizationSum($element){
	return "SELECT SUM($element) AS $element FROM fertilization_tab";
}

function getAllFertilizationSum(){
	return "SELECT SUM(k) AS k, SUM(mg) AS mg, SUM(fe) AS fe, SUM(rinverdente) as rinverdente, SUM(p) AS p, SUM(n) AS n, SUM(npk) AS npk FROM fertilization_tab";
}

function getFertilizationValues(){
	return "SELECT fertilizzante AS name, qnt, data_inizio AS date FROM fertilization_volumes";
}

function getUpdateFertilizationVolumeQuery($date, $volume, $select){
	$sql = "UPDATE `fertilization_volumes` SET ";
    $sql = !empty($date) ? $sql." `data_inizio` = '$date'" : $sql;
    if(!empty($date) and !empty($volume)){
       $sql = $sql.", ";
    }
    $sql = !empty($volume) ? $sql." `qnt` = '$volume'" : $sql;
    $sql = !empty($select) ? $sql." WHERE fertilizzante = '$select';" : $sql;
    //$sql = "INSERT INTO `my_myfishtank`.`fertilization_volumes` (`fertilizzante`, `qnt`, `data_inizio`) VALUES ($select,$volume,$date)";

	return $sql; 
}

function getFertilizationVolumes(){
   return "SELECT fertilizzante AS name, qnt, data_inizio AS date FROM fertilization_volumes";
}

function addDateToFertilizationQuery($sql_query, $data){
   return $sql_query." WHERE data >= DATE(\"".$data."\")";
}


function calculate_median($arr) {
  $count = count($arr); //total numbers in array
  $middleval = floor(($count-1)/2); // find the middle value, or the lowest middle value
  if($count % 2) { // odd number, middle is the median
    $median = $arr[$middleval];
  } else { // even number, calculate avg of 2 medians
    $low = $arr[$middleval];
    $high = $arr[$middleval+1];
    $median = (($low+$high)/2);
  }
  return $median;
}
          
function calculate_average($arr) {
  $count = count($arr); //total numbers in array
  foreach ($arr as $value) {
    $total = $total + $value; // total value of array numbers
  }
  $average = ($total/$count); // get average value
  return $average;
}

function get_median($arr) {
  $data_median = array();
  $tempvalue = "";
  $temparr = array();
  $finalData = "";
  foreach ($arr as $element) {
    $datasplit = explode(" ", $element['send'])[0];
    if ($tempvalue == ""){
      $tempvalue =  $datasplit;
      $temparr[] = $element['EC'];
    }else if ($tempvalue !=  $datasplit){
      $tempvalue =  $datasplit;
      $data_median[] = ["M" => calculate_median($temparr), "D" =>  $finalData];
      $temparr = array();
      $temparr[] = $element['EC'];
      $finalData = $element['send'];
    }else{
      $temparr[] = $element['EC'];
      $finalData = $element['send'];
    }
  }
  $data_median[] =  ["M" => calculate_median($temparr), "D" => $finalData];
  return $data_median;
}

function get_median_ph($arr) {
  $data_median = array();
  $tempvalue = "";
  $temparr = array();
  $finalData = "";
  foreach ($arr as $element) {
    $datasplit = explode(" ", $element['send'])[0];
    if ($tempvalue == ""){
      $tempvalue =  $datasplit;
      $temparr[] = $element['ph'];
    }else if ($tempvalue !=  $datasplit){
      $tempvalue =  $datasplit;
      $data_median[] = ["M_PH" => calculate_median($temparr), "D_PH" =>  $finalData];
      $temparr = array();
      $temparr[] = $element['ph'];
      $finalData = $element['send'];
    }else{
      $temparr[] = $element['ph'];
      $finalData = $element['send'];
    }
  }
  $data_median[] =  ["M_PH" => calculate_median($temparr), "D_PH" => $finalData];
  return $data_median;
}

function get_median_temperature($arr) {
  $data_median = array();
  $tempvalue = "";
  $temparr = array();
  $finalData = "";
  foreach ($arr as $element) {
    $datasplit = explode(" ", $element['send'])[0];
    if ($tempvalue == ""){
      $tempvalue =  $datasplit;
      $temparr[] = $element['temperature'];
    }else if ($tempvalue !=  $datasplit){
      $tempvalue =  $datasplit;
      $data_median[] = ["M_T" => calculate_median($temparr), "D_T" =>  $finalData];
      $temparr = array();
      $temparr[] = $element['temperature'];
      $finalData = $element['send'];
    }else{
      $temparr[] = $element['temperature'];
      $finalData = $element['send'];
    }
  }
  $data_median[] =  ["M_T" => calculate_median($temparr), "D_T" => $finalData];
  return $data_median;
}

function normalize($value, $min, $max) {
	$normalized = ($value - $min) / ($max - $min);
	return $normalized;
}

function denormalize($normalized, $min, $max) {
	$denormalized = ($normalized * ($max - $min) + $min);
	return $denormalized;
}
          


?>   