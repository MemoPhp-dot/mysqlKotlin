<?php
include('../config/conexion.php');


// Obtener los datos en formato JSON de la solicitud
$rawData = file_get_contents("php://input");
$data = json_decode($rawData, true); // Decodificamos el JSON en un arreglo asociativo

$response = ['status' => false, 'data' => null];

// Si los datos se recibieron correctamente
if ($data && isset($data['nombre']) && isset($data['apellidos']) && isset($data['telefono']) && isset($data['email'])) {
    $nombre = mysqli_real_escape_string($conexion, $data['nombre']);
    $apellidos = mysqli_real_escape_string($conexion, $data['apellidos']);
    $telefono = mysqli_real_escape_string($conexion, $data['telefono']);
    $email = mysqli_real_escape_string($conexion, $data['email']);

    $sql = "INSERT INTO contactos (nombre, apellidos, telefono, email) VALUES ('$nombre', '$apellidos', '$telefono', '$email')";

    if (mysqli_query($conexion, $sql)) {
        $response['status'] = true;
        $response['data'] = [
            'id' => mysqli_insert_id($conexion),
            'nombre' => $nombre,
            'apellidos' => $apellidos,
            'telefono' => $telefono,
            'email' => $email
        ];
    } else {
        $response['data'] = mysqli_error($conexion);
    }
} else {
    $response['status'] = false;
    $response['data'] = 'Faltan datos en la solicitud.';
}

echo json_encode($response);
$response['data'] = 'Faltan datos en la solicitud.';
