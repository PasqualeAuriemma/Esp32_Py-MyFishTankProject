<?php
session_start();

if (empty($_SESSION['loggedIn']) || $_SESSION['loggedIn'] !== '1') {
    http_response_code(401);
    exit(json_encode(['status' => 'error', 'message' => 'Unauthorized']));
}

header('Content-Type: application/json');
include('../connection.php');
include('../queryAndFunction.php');

// ── Parametri DataTables ───────────────────────────────────────────────────────

// Punto: colonne consentite per ORDER BY — whitelist esplicita
// (impedisce SQL injection sul parametro order[0][column])
$TABLE      = 'ph_tab';       // es. ec_tab
$COL_VALUE  = 'ph';   // es. ec
$COL_DATE   = 'data_send';
$COL_ARRIVE = 'data_arrive';

$allowed_columns = ['id', $COL_VALUE, $COL_DATE, $COL_ARRIVE];

// Direzione ORDER: solo ASC o DESC
$order_dir = 'DESC';
if (isset($_POST['order'][0]['dir']) && strtoupper($_POST['order'][0]['dir']) === 'ASC') {
    $order_dir = 'ASC';
}

// Colonna ORDER: indice numerico → nome colonna tramite whitelist
$order_col = 'id';
if (isset($_POST['order'][0]['column'])) {
    $col_index = intval($_POST['order'][0]['column']);
    if (isset($allowed_columns[$col_index])) {
        $order_col = $allowed_columns[$col_index];
    }
}

// Ricerca
$search_value = '';
if (!empty($_POST['search']['value'])) {
    $search_value = trim($_POST['search']['value']);
}

// Paginazione
$start  = isset($_POST['start'])  ? intval($_POST['start'])  : 0;
$length = isset($_POST['length']) ? intval($_POST['length']) : 10;
$draw   = isset($_POST['draw'])   ? intval($_POST['draw'])   : 1;

// ── Conteggio totale ───────────────────────────────────────────────────────────
$total_result = $con->query("SELECT COUNT(*) as cnt FROM `{$TABLE}`");
$total_row    = $total_result->fetch_assoc();
$total_all    = intval($total_row['cnt']);

// ── Query principale con ricerca ───────────────────────────────────────────────
if ($search_value !== '') {
    // Ricerca sul valore numerico e sulla data — prepared statement
    $like = '%' . $search_value . '%';
    $sql  = "SELECT id, `{$COL_VALUE}`, data_send, data_arrive
             FROM `{$TABLE}`
             WHERE CAST(`{$COL_VALUE}` AS CHAR) LIKE ?
                OR data_send LIKE ?
             ORDER BY `{$order_col}` {$order_dir}
             LIMIT ?, ?";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("ssii", $like, $like, $start, $length);
} else {
    $sql  = "SELECT id, `{$COL_VALUE}`, data_send, data_arrive
             FROM `{$TABLE}`
             ORDER BY `{$order_col}` {$order_dir}
             LIMIT ?, ?";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("ii", $start, $length);
}

$stmt->execute();
$result = $stmt->get_result();

$data = [];
while ($row = $result->fetch_assoc()) {
    $arrive_fmt = '';
    if (!empty($row[$COL_ARRIVE])) {
        $dt = date_create($row[$COL_ARRIVE]);
        $arrive_fmt = $dt ? date_format($dt, 'd/m/Y H:i:s') : '';
    }

    $actions = '<a href="javascript:void(0);" data-id="' . intval($row['id']) . '"'
             . ' class="btn btn-info btn-sm editbtnPhS"><i class="mdi mdi-table-edit"></i></a>'
             . '  '
             . '<a href="javascript:void(0);" data-id="' . intval($row['id']) . '"'
             . ' class="btn btn-danger btn-sm deleteBtnPhS"><i class="mdi mdi-table-row-remove"></i></a>';

    $data[] = [
        intval($row['id']),
        htmlspecialchars($row[$COL_VALUE]),
        htmlspecialchars($row['data_send']),
        $arrive_fmt,
        $actions,
    ];
}

$stmt->close();
$con->close();

echo json_encode([
    'draw'            => $draw,
    'recordsTotal'    => $total_all,
    'recordsFiltered' => $total_all,
    'data'            => $data,
]);
