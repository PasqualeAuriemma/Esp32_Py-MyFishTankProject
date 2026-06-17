<?php
session_start();

header('Content-Type: application/json');
include('../connection.php');
include('../queryAndFunction.php');

// ── Whitelist colonne per ORDER BY ────────────────────────────────────────────
$allowed_columns = ['data', 'EC_PRE', 'EC_AFT', 'PH', 'no2', 'no3', 'gh', 'kh', 'po4'];

$order_dir = 'DESC';
if (isset($_POST['order'][0]['dir']) && strtoupper($_POST['order'][0]['dir']) === 'ASC') {
    $order_dir = 'ASC';
}

$order_col = 'id';
if (isset($_POST['order'][0]['column'])) {
    $col_index = intval($_POST['order'][0]['column']);
    if (isset($allowed_columns[$col_index])) {
        $order_col = $allowed_columns[$col_index];
    }
}

$search_value = '';
if (!empty($_POST['search']['value'])) {
    $search_value = trim($_POST['search']['value']);
}

$start  = isset($_POST['start'])  ? intval($_POST['start'])  : 0;
$length = isset($_POST['length']) ? intval($_POST['length']) : 10;
$draw   = isset($_POST['draw'])   ? intval($_POST['draw'])   : 1;

// ── Conteggio totale ───────────────────────────────────────────────────────────
$total_result = $con->query("SELECT COUNT(*) as cnt FROM watervalues_table");
$total_all    = intval($total_result->fetch_assoc()['cnt']);

// ── Query principale ──────────────────────────────────────────────────────────
$loggedIn = !empty($_SESSION['loggedIn']) && $_SESSION['loggedIn'] === '1';

if ($search_value !== '') {
    $like = '%' . $search_value . '%';
    $sql  = "SELECT id, data, EC_PRE, EC_AFT, PH, no2, no3, gh, kh, po4
             FROM watervalues_table
             WHERE CAST(data AS CHAR)   LIKE ?
                OR CAST(EC_PRE AS CHAR) LIKE ?
                OR CAST(EC_AFT AS CHAR) LIKE ?
                OR CAST(PH AS CHAR)     LIKE ?
                OR CAST(no2 AS CHAR)    LIKE ?
                OR CAST(no3 AS CHAR)    LIKE ?
                OR CAST(gh AS CHAR)     LIKE ?
                OR CAST(kh AS CHAR)     LIKE ?
                OR CAST(po4 AS CHAR)    LIKE ?
             ORDER BY `{$order_col}` {$order_dir}
             LIMIT ?, ?";
    $stmt = $con->prepare($sql);
    $stmt->bind_param(
        "sssssssssii",
        $like,$like,$like,$like,$like,$like,$like,$like,$like,
        $start, $length
    );
} else {
    $sql  = "SELECT id, data, EC_PRE, EC_AFT, PH, no2, no3, gh, kh, po4
             FROM watervalues_table
             ORDER BY `{$order_col}` {$order_dir}
             LIMIT ?, ?";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("ii", $start, $length);
}

$stmt->execute();
$result = $stmt->get_result();

$data = [];
while ($row = $result->fetch_assoc()) {
    $dt = date_create($row['data']);
    $sub = [
        $dt ? date_format($dt, 'd/m/Y') : $row['data'],
        $row['EC_PRE'],
        $row['EC_AFT'],
        $row['PH'],
        $row['no2'],
        $row['no3'],
        $row['gh'],
        $row['kh'],
        $row['po4'],
    ];

    if ($loggedIn) {
        $sub[] = '<a href="javascript:void(0);" data-id="' . intval($row['id']) . '"'
               . ' class="btn btn-info btn-sm editbtn"><i class="mdi mdi-table-edit"></i></a>'
               . '  '
               . '<a href="javascript:void(0);" data-id="' . intval($row['id']) . '"'
               . ' class="btn btn-danger btn-sm deleteBtn"><i class="mdi mdi-table-row-remove"></i></a>';
    } else {
        $sub[] = '';
    }

    $data[] = $sub;
}

$stmt->close();
$con->close();

echo json_encode([
    'draw'            => $draw,
    'recordsTotal'    => $total_all,
    'recordsFiltered' => $total_all,
    'data'            => $data,
]);
