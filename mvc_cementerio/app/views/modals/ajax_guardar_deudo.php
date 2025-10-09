<?php

header('Content-Type: application/json');
require_once '/../../models/Database.php';

$response = ['success'=>false,'mensaje'=>'','deudo'=>null];

if ($_SERVER['REQUEST_METHOD']==='POST'){
    $dni = $_POST['dni'] ?? '';
    $nombre = $_POST['nombre'] ?? '';
    $apellido = $_POST['apellido'] ?? '';
    $telefono = $_POST['telefono'] ?? '';
    $email = $_POST['email'] ?? '';
    $domicilio = $_POST['domicilio'] ?? '';
    $localidad = $_POST['localidad'] ?? '';
    $codigo_postal = $_POST['codigo_postal'] ?? '';

    if (!$nombre || !$apellido || !$dni){
        $response['mensaje'] = 'Complete todos los campos obligatorios';
        echo json_encode($response); exit;
    }

    $stmt = $pdo->prepare("INSERT INTO deudos (dni,nombre,apellido,telefono,email,domicilio,localidad,codigo_postal)
                           VALUES (?,?,?,?,?,?,?,?)");
    $ok = $stmt->execute([$dni,$nombre,$apellido,$telefono,$email,$domicilio,$localidad,$codigo_postal]);

    if ($ok){
        $id = $pdo->lastInsertId();
        $response['success'] = true;
        $response['mensaje'] = 'Deudo creado correctamente';
        $response['deudo'] = ['id_deudo'=>$id,'nombre'=>$nombre,'apellido'=>$apellido,'dni'=>$dni];
    } else {
        $response['mensaje'] = 'Error al guardar en la base de datos';
    }
}
echo json_encode($response);
