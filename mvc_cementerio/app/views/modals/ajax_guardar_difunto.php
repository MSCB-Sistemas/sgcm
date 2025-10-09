<?php

header('Content-Type: application/json');
require_once '/../../models/Database.php';

$response = ['success' => false, 'mensaje' => '', 'difunto' => null];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $deudo = $_POST['deudo'] ?? null;
    $nombre = $_POST['nombre'] ?? '';
    $apellido = $_POST['apellido'] ?? '';
    $dni = $_POST['dni'] ?? '';
    $edad = $_POST['edad'] ?? null;
    $fecha_fallecimiento = $_POST['fecha_fallecimiento'] ?? null;
    $sexo = $_POST['sexo'] ?? null;
    $nacionalidad = $_POST['nacionalidad'] ?? null;
    $estado_civil = $_POST['estado_civil'] ?? null;
    $domicilio = $_POST['domicilio'] ?? '';
    $localidad = $_POST['localidad'] ?? '';
    $codigo_postal = $_POST['codigo_postal'] ?? '';

    if (!$deudo || !$nombre || !$apellido || !$dni) {
        $response['mensaje'] = 'Complete todos los campos obligatorios';
        echo json_encode($response);
        exit;
    }

    $stmt = $pdo->prepare("
        INSERT INTO difuntos 
        (id_deudo, nombre, apellido, dni, edad, fecha_fallecimiento, id_sexo, id_nacionalidad, id_estado_civil, domicilio, localidad, codigo_postal)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    $ok = $stmt->execute([$deudo, $nombre, $apellido, $dni, $edad, $fecha_fallecimiento, $sexo, $nacionalidad, $estado_civil, $domicilio, $localidad, $codigo_postal]);

    if ($ok) {
        $id = $pdo->lastInsertId();
        $response['success'] = true;
        $response['mensaje'] = 'Difunto creado correctamente';
        $response['difunto'] = [
            'id_difunto' => $id,
            'nombre' => $nombre,
            'apellido' => $apellido,
            'dni' => $dni
        ];
    } else {
        $response['mensaje'] = 'Error al guardar en la base de datos';
    }
}

echo json_encode($response);
