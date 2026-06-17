<?php
header('Content-Type: application/json');
include("../connection.php");
include("../queryAndFunction.php");

if ($con->connect_error) {
    http_response_code(500);
    exit(json_encode(['status' => 'error', 'message' => 'Connection failed']));
}

// Parametro $q letto ma non usato — rimosso fino a implementazione reale
// $q = intval($_GET['data_received']);

$sql_max        = getFertilizationValues();
$sql_quantities_k   = getFertilizationSum("k");
$sql_quantities_mg  = getFertilizationSum("mg");
$sql_quantities_fe  = getFertilizationSum("fe");
$sql_quantities_rin = getFertilizationSum("rinverdente");
$sql_quantities_p   = getFertilizationSum("p");
$sql_quantities_n   = getFertilizationSum("n");
$sql_quantities_npk = getFertilizationSum("npk");

$query = mysqli_query($con, $sql_max);

$output             = '<div class="row">';
$sub_array          = [];
$sub_array_quantities = [];
$sub_array_data     = [];

while ($row = mysqli_fetch_assoc($query)) {
    $sub_array[$row['name']]      = number_format($row['qnt'], 2, '.', '');
    $sub_array_data[$row['name']] = $row['date'];
}

foreach ($sub_array_data as $var1 => $data) {
    if ($data === null) continue;

    switch ($var1) {
        case 'Potassio':
            $sql_quantities_k   = addDateToFertilizationQuery($sql_quantities_k,   $data); break;
        case 'Magnesio':
            $sql_quantities_mg  = addDateToFertilizationQuery($sql_quantities_mg,  $data); break;
        case 'Ferro':
            $sql_quantities_fe  = addDateToFertilizationQuery($sql_quantities_fe,  $data); break;
        case 'Rinverdente':
            $sql_quantities_rin = addDateToFertilizationQuery($sql_quantities_rin, $data); break;
        case 'Fosforo':
            $sql_quantities_p   = addDateToFertilizationQuery($sql_quantities_p,   $data); break;
        case 'Azoto':
            $sql_quantities_n   = addDateToFertilizationQuery($sql_quantities_n,   $data); break;
        case 'Stick':
            $sql_quantities_npk = addDateToFertilizationQuery($sql_quantities_npk, $data); break;
    }
}

$query_quantities_k   = mysqli_query($con, $sql_quantities_k);
$query_quantities_mg  = mysqli_query($con, $sql_quantities_mg);
$query_quantities_fe  = mysqli_query($con, $sql_quantities_fe);
$query_quantities_rin = mysqli_query($con, $sql_quantities_rin);
$query_quantities_p   = mysqli_query($con, $sql_quantities_p);
$query_quantities_n   = mysqli_query($con, $sql_quantities_n);
$query_quantities_npk = mysqli_query($con, $sql_quantities_npk);

while ($row = mysqli_fetch_assoc($query_quantities_k))   { $sub_array_quantities['Potassio']   = number_format($row['k'],           2, '.', ''); }
while ($row = mysqli_fetch_assoc($query_quantities_mg))  { $sub_array_quantities['Magnesio']   = number_format($row['mg'],          2, '.', ''); }
while ($row = mysqli_fetch_assoc($query_quantities_fe))  { $sub_array_quantities['Ferro']      = number_format($row['fe'],          2, '.', ''); }
while ($row = mysqli_fetch_assoc($query_quantities_rin)) { $sub_array_quantities['Rinverdente']= number_format($row['rinverdente'], 2, '.', ''); }
while ($row = mysqli_fetch_assoc($query_quantities_p))   { $sub_array_quantities['Fosforo']    = number_format($row['p'],           2, '.', ''); }
while ($row = mysqli_fetch_assoc($query_quantities_n))   { $sub_array_quantities['Azoto']      = number_format($row['n'],           2, '.', ''); }
while ($row = mysqli_fetch_assoc($query_quantities_npk)) { $sub_array_quantities['Stick']      = number_format($row['npk'],         2, '.', ''); }

// ── Punto 7: secondo each() rimosso ──────────────────────────────────────────
foreach ($sub_array as $var => $val) {
    $pp  = $sub_array_quantities[$var] ?? 0;
    $dat = $sub_array_data[$var]       ?? null;

    if ($var === "Co2") {
        $lastCharge = new DateTime($dat);
        $dataNow    = new DateTime();

        if ($lastCharge > $dataNow) {
            $remaining = $val;
        } else {
            $difference = $lastCharge->diff($dataNow);
            $remaining  = $val - $difference->format('%R%a');
        }

        $whole_int  = intval($val);
        $percentage = $whole_int > 0 ? ($remaining * 100) / $whole_int : 0;

        $output .= '<div class="col-sm-4 grid-margin">
                        <div class="card"><div class="card-body">
                            <h5 style="color:#f8f9fa;">' . htmlspecialchars($var) . '</h5>
                            <p class="text-muted">Remaining ' . $remaining . ' of ' . $whole_int . ' days</p>
                            <div class="progress progress-md portfolio-progress">
                                <div class="progress-bar bg-success" role="progressbar"
                                     style="width:' . $percentage . '%"
                                     aria-valuenow="' . $percentage . '" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div></div>
                    </div>';
    } else {
        $remaining  = $val - $pp;
        $whole_int  = intval($val);
        $percentage = $whole_int > 0 ? ($remaining * 100) / $whole_int : 0;

        if ($var === "Stick") {
            $whole  = floor($remaining);
            $fraction = ($remaining - $whole) * 100;
            if ($fraction == 25)      { $fraction_string = "a quarter"; }
            elseif ($fraction == 50)  { $fraction_string = "half"; }
            elseif ($fraction == 75)  { $fraction_string = "three quarters"; }
            else                      { $fraction_string = ""; }

            $label = $whole . ($fraction_string ? " and $fraction_string" : "") . " sticks on $whole_int";
        } else {
            $label = "$remaining ml on $whole_int ml";
        }

        $output .= '<div class="col-sm-4 grid-margin">
                        <div class="card"><div class="card-body">
                            <h5 style="color:#f8f9fa;">' . htmlspecialchars($var) . '</h5>
                            <p class="text-muted">Remaining ' . $label . '</p>
                            <div class="progress progress-md portfolio-progress">
                                <div class="progress-bar bg-success" role="progressbar"
                                     style="width:' . $percentage . '%"
                                     aria-valuenow="' . $percentage . '" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div></div>
                    </div>';
    }
}

$output .= '</div>';
echo $output;
mysqli_close($con);
