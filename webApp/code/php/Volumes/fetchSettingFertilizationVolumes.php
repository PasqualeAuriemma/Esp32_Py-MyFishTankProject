<?php
header('Content-Type: application/json');

include("php/connection.php");
include("php/queryAndFunction.php");

if ($con->connect_error) {
    http_response_code(500);
    exit(json_encode(['status' => 'error', 'message' => 'Connection failed']));
}

// Parametro non usato rimosso: $q = intval($_GET['data_received']);

$sql_max = getFertilizationValues();
$query   = mysqli_query($con, $sql_max);

$sub_array      = [];
$sub_array_data = [];

while ($row = mysqli_fetch_assoc($query)) {
    $sub_array[$row['name']]      = number_format($row['qnt'], 2, '.', '');
    $sub_array_data[$row['name']] = $row['date'];
}

// ── each() rimosso (PHP 8 fatal) → foreach ───────────────────────────────────
$output = '<ul class="nav">';

foreach ($sub_array as $var => $val) {
    $dat    = $sub_array_data[$var] ?? '';
    $pickId = 'datepicker_' . htmlspecialchars($var);

    $output .= '<li class="nav-item menu-items">'
             . '<input type="text" name="' . $pickId . '" id="' . $pickId . '"'
             . ' placeholder="' . htmlspecialchars($dat ?? '') . '" />'
             . '</li>';
}

$output .= '</ul>';

mysqli_close($con);
echo $output;
