<?php
header('Content-Type: application/json');
include("../connection.php");
include("../queryAndFunction.php");

if ($con->connect_error) {
    http_response_code(500);
    exit(json_encode(['status' => 'error', 'message' => 'Connection failed']));
}

$sql_max            = getFertilizationValues();
$sql_quantities_k   = getFertilizationSum("k");
$sql_quantities_mg  = getFertilizationSum("mg");
$sql_quantities_fe  = getFertilizationSum("fe");
$sql_quantities_rin = getFertilizationSum("rinverdente");
$sql_quantities_p   = getFertilizationSum("p");
$sql_quantities_n   = getFertilizationSum("n");
$sql_quantities_npk = getFertilizationSum("npk");

$query = mysqli_query($con, $sql_max);

$sub_array          = [];
$sub_array_quantities = [];
$sub_array_data     = [];

while ($row = mysqli_fetch_assoc($query)) {
    $sub_array[$row['name']]      = floatval($row['qnt']);
    $sub_array_data[$row['name']] = $row['date'];
}

foreach ($sub_array_data as $var1 => $data) {
    if ($data === null) continue;
    switch ($var1) {
        case 'Potassio':   $sql_quantities_k   = addDateToFertilizationQuery($sql_quantities_k,   $data); break;
        case 'Magnesio':   $sql_quantities_mg  = addDateToFertilizationQuery($sql_quantities_mg,  $data); break;
        case 'Ferro':      $sql_quantities_fe  = addDateToFertilizationQuery($sql_quantities_fe,  $data); break;
        case 'Rinverdente':$sql_quantities_rin = addDateToFertilizationQuery($sql_quantities_rin, $data); break;
        case 'Fosforo':    $sql_quantities_p   = addDateToFertilizationQuery($sql_quantities_p,   $data); break;
        case 'Azoto':      $sql_quantities_n   = addDateToFertilizationQuery($sql_quantities_n,   $data); break;
        case 'Stick':      $sql_quantities_npk = addDateToFertilizationQuery($sql_quantities_npk, $data); break;
    }
}

$query_quantities_k   = mysqli_query($con, $sql_quantities_k);
$query_quantities_mg  = mysqli_query($con, $sql_quantities_mg);
$query_quantities_fe  = mysqli_query($con, $sql_quantities_fe);
$query_quantities_rin = mysqli_query($con, $sql_quantities_rin);
$query_quantities_p   = mysqli_query($con, $sql_quantities_p);
$query_quantities_n   = mysqli_query($con, $sql_quantities_n);
$query_quantities_npk = mysqli_query($con, $sql_quantities_npk);

while ($row = mysqli_fetch_assoc($query_quantities_k))   { $sub_array_quantities['Potassio']    = floatval($row['k']); }
while ($row = mysqli_fetch_assoc($query_quantities_mg))  { $sub_array_quantities['Magnesio']    = floatval($row['mg']); }
while ($row = mysqli_fetch_assoc($query_quantities_fe))  { $sub_array_quantities['Ferro']       = floatval($row['fe']); }
while ($row = mysqli_fetch_assoc($query_quantities_rin)) { $sub_array_quantities['Rinverdente'] = floatval($row['rinverdente']); }
while ($row = mysqli_fetch_assoc($query_quantities_p))   { $sub_array_quantities['Fosforo']     = floatval($row['p']); }
while ($row = mysqli_fetch_assoc($query_quantities_n))   { $sub_array_quantities['Azoto']       = floatval($row['n']); }
while ($row = mysqli_fetch_assoc($query_quantities_npk)) { $sub_array_quantities['Stick']       = floatval($row['npk']); }

$result = [];

foreach ($sub_array as $var => $max) {
    $used = $sub_array_quantities[$var] ?? 0;
    $dat  = $sub_array_data[$var] ?? null;

    if ($var === 'Co2') {
        if ($dat === null) {
            $remaining = $max;
        } else {
            $lastCharge = new DateTime($dat);
            $dataNow    = new DateTime();
            if ($lastCharge > $dataNow) {
                $remaining = $max;
            } else {
                $diff      = $lastCharge->diff($dataNow);
                $remaining = $max - intval($diff->format('%a'));
            }
        }
        $result[] = [
            'name'      => $var,
            'max'       => intval($max),
            'remaining' => round($remaining, 2),
            'unit'      => 'days',
            'label'     => 'Remaining ' . round($remaining, 0) . ' of ' . intval($max) . ' days'
        ];
    } elseif ($var === 'Stick') {
        $remaining = $max - $used;
        $whole     = floor($remaining);
        $fraction  = ($remaining - $whole) * 100;
        if ($fraction == 25)     { $fs = 'a quarter'; }
        elseif ($fraction == 50) { $fs = 'half'; }
        elseif ($fraction == 75) { $fs = 'three quarters'; }
        else                     { $fs = ''; }
        $label = 'Remaining ' . $whole . ($fs ? ' and ' . $fs : '') . ' sticks on ' . intval($max);
        $result[] = [
            'name'      => $var,
            'max'       => floatval($max),
            'remaining' => round($remaining, 2),
            'unit'      => 'sticks',
            'label'     => $label
        ];
    } else {
        $remaining = $max - $used;
        $result[] = [
            'name'      => $var,
            'max'       => floatval($max),
            'remaining' => round($remaining, 2),
            'unit'      => 'ml',
            'label'     => 'Remaining ' . round($remaining, 0) . ' ml on ' . intval($max) . ' ml'
        ];
    }
}

mysqli_close($con);
echo json_encode($result);
