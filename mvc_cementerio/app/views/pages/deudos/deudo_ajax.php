<?php
require_once __DIR__ . '/../../models/DeudoModel.php';

$model = new DeudoModel();

$draw   = $_POST['draw'] ?? 1;
$start  = intval($_POST['start'] ?? 0);
$length = intval($_POST['length'] ?? 10);
$search = $_POST['search']['value'] ?? '';
$orderColumnIndex = $_POST['order'][0]['column'] ?? 0;
$orderDir = $_POST['order'][0]['dir'] ?? 'asc';

$columns = ['dni', 'nombre', 'apellido', 'telefono', 'email', 'domicilio', 'localidad', 'codigo_postal'];
$orderCol = $columns[$orderColumnIndex] ?? 'id_deudo';

$totalRecords = $model->countAll();

if ($search) {
    $data = $model->getFiltered($search, $orderCol, $orderDir, $start, $length);
    $filteredRecords = $model->countFiltered($search);
} else {
    $data = $model->getPage($orderCol, $orderDir, $start, $length);
    $filteredRecords = $totalRecords;
}

echo json_encode([
    "draw" => intval($draw),
    "recordsTotal" => $totalRecords,
    "recordsFiltered" => $filteredRecords,
    "data" => $data
]);
exit;
