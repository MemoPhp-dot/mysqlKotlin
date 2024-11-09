<?php
include('../config/conexion.php');

// Asegurarte de que la conexión use UTF-8
mysqli_set_charset($conexion, 'utf8mb4');

// Obtener los datos en formato JSON de la solicitud
$rawData = file_get_contents("php://input");
$data = json_decode($rawData, true); // Decodificamos el JSON en un arreglo asociativo

$response = ['status' => false, 'data' => null];

// Si los datos se recibieron correctamente
if ($data && isset($data['nombre']) && isset($data['apellidos']) && isset($data['telefono']) && isset($data['email'])) {
    // Escapamos los datos para evitar inyecciones SQL
    $nombre = mysqli_real_escape_string($conexion, $data['nombre']);
    $apellidos = mysqli_real_escape_string($conexion, $data['apellidos']);
    $telefono = mysqli_real_escape_string($conexion, $data['telefono']);
    $email = mysqli_real_escape_string($conexion, $data['email']);

    // Insertar los datos en la base de datos
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
        // En caso de error en la consulta
        $response['data'] = mysqli_error($conexion);
    }
} else {
    // En caso de que falten datos
    $response['status'] = false;
    $response['data'] = 'Faltan datos en la solicitud.';
}

// Devolver la respuesta en formato JSON
echo json_encode($response);
