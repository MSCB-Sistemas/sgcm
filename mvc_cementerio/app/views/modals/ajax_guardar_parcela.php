<?php

header('Content-Type: application/json');
require_once '/../../models/Database.php';

$response = ['success'=>false,'mensaje'=>'','parcela'=>null];

if ($_SERVER['REQUEST_METHOD']==='POST'){
    $tipo_parcela = $_POST['tipo_parcela'] ?? null;
    $deudo = $_POST['deudo'] ?? null;
    $numero_ubicacion = $_POST['numero_ubicacion'] ?? '';
    $hilera = $_POST['hilera'] ?? '';
    $seccion = $_POST['seccion'] ?? '';
    $fraccion = $_POST['fraccion'] ?? '';
    $nivel = $_POST['nivel'] ?? '';
    $orientacion = $_POST['orientacion'] ?? null;

    if (!$tipo_parcela || !$deudo){
        $response['mensaje'] = 'Tipo de parcela y deudo son obligatorios';
        echo json_encode($response); exit;
    }

    $stmt = $pdo->prepare("INSERT INTO parcelas 
        (id_tipo_parcela,id_deudo,numero_ubicacion,hilera,seccion,fraccion,nivel,id_orientacion)
        VALUES (?,?,?,?,?,?,?,?)");
    $ok = $stmt->execute([$tipo_parcela,$deudo,$numero_ubicacion,$hilera,$seccion,$fraccion,$nivel,$orientacion]);

    if ($ok){
        $id = $pdo->lastInsertId();
        $response['success'] = true;
        $response['mensaje'] = 'Parcela creada correctamente';
        $response['parcela'] = [
            'id_parcela'=>$id,
            'numero_ubicacion'=>$numero_ubicacion,
            'hilera'=>$hilera,
            'seccion'=>$seccion
        ];
    } else {
        $response['mensaje'] = 'Error al guardar en la base de datos';
    }
}
echo json_encode($response);
