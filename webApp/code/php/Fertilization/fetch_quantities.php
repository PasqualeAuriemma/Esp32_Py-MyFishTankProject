<?php
session_start();

header('Content-Type: application/json');
include('../connection.php');
include('../queryAndFunction.php');

if ($con->connect_error) {
    http_response_code(500);
    exit(json_encode(['status' => 'error', 'message' => 'Connection failed']));
}

// ── Whitelist colonne per ORDER BY ────────────────────────────────────────────
$allowed_columns = ['data', 'k', 'mg', 'fe', 'rinverdente', 'p', 'n', 'npk'];

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
$total_result = $con->query("SELECT COUNT(*) as cnt FROM fertilization_tab");
$total_row    = $total_result->fetch_assoc();
$total_all    = intval($total_row['cnt']);

// ── Query principale ─────────────────────────────────────────────────────────
if ($search_value !== '') {
    $like = '%' . $search_value . '%';
    $sql  = "SELECT id, data, k, mg, fe, rinverdente, p, n, npk
             FROM fertilization_tab
             WHERE CAST(k AS CHAR)            LIKE ?
                OR CAST(mg AS CHAR)           LIKE ?
                OR CAST(fe AS CHAR)           LIKE ?
                OR CAST(rinverdente AS CHAR)  LIKE ?
                OR CAST(p AS CHAR)            LIKE ?
                OR CAST(n AS CHAR)            LIKE ?
                OR CAST(npk AS CHAR)          LIKE ?
                OR data                       LIKE ?
             ORDER BY `{$order_col}` {$order_dir}
             LIMIT ?, ?";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("ssssssssii",
        $like, $like, $like, $like, $like, $like, $like, $like, $start, $length);
} elseif ($length != -1) {
    $sql  = "SELECT id, data, k, mg, fe, rinverdente, p, n, npk
             FROM fertilization_tab
             ORDER BY `{$order_col}` {$order_dir}
             LIMIT ?, ?";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("ii", $start, $length);
} else {
    $sql  = "SELECT id, data, k, mg, fe, rinverdente, p, n, npk
             FROM fertilization_tab
             ORDER BY `{$order_col}` {$order_dir}";
    $stmt = $con->prepare($sql);
}

$stmt->execute();
$result = $stmt->get_result();

$loggedIn = !empty($_SESSION['loggedIn']) && $_SESSION['loggedIn'] === '1';

$data = [];
while ($row = $result->fetch_assoc()) {
    $sub_array = [
        date_format(date_create($row['data']), 'd/m/Y'),
        $row['k'],
        $row['mg'],
        $row['fe'],
        $row['rinverdente'],
        $row['p'],
        $row['n'],
        $row['npk'],
    ];

    if ($loggedIn) {
        $sub_array[] = '<a href="javascript:void(0);" data-id="' . intval($row['id']) . '"'
                     . ' class="btn btn-info btn-sm editbtnF"><i class="mdi mdi-table-edit"></i></a>'
                     . '  '
                     . '<a href="javascript:void(0);" data-id="' . intval($row['id']) . '"'
                     . ' class="btn btn-danger btn-sm deleteBtnF"><i class="mdi mdi-table-row-remove"></i></a>';
    } else {
        $sub_array[] = '';
    }

    $data[] = $sub_array;
}

$stmt->close();
$con->close();

echo json_encode([
    'draw'            => $draw,
    'recordsTotal'    => $total_all,
    'recordsFiltered' => $total_all,
    'data'            => $data,
]);
