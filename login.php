<?php

// Incluir el archivo de conexión
include('./config/conexion.php');

// Verificar si se han enviado los datos del formulario
$response = ['status' => false, 'message' => null, 'data' => null];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtener el cuerpo de la solicitud (JSON)
    $jsonData = file_get_contents("php://input");
    $data = json_decode($jsonData, true);  // Decodificar el JSON en un array asociativo

    // Obtener los datos del JSON
    $correo = $data['correo'] ?? '';
    $contrasena = $data['contrasena'] ?? '';

    // Buscar el usuario en la base de datos
    $stmt = $conexion->prepare("SELECT * FROM usuarios WHERE correo = ? LIMIT 1");
    $stmt->bind_param('s', $correo);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    // Verificar si el usuario existe y si la contraseña coincide
    if ($user && $contrasena === $user['contrasena']) {
        // Respuesta exitosa con datos del usuario
        $response['status'] = true;
        $response['data'] = [
            'nombre' => $user['nombre'],
            'correo' => $user['correo'],
        ];
    } else {
        // Respuesta de error con mensaje
        $response['message'] = 'Correo o contraseña incorrectos.';
    }
} else {
    // Respuesta de error si no es una solicitud POST
    $response['message'] = 'Método de solicitud no permitido.';
}

// Enviar la respuesta como JSON
header('Content-Type: application/json; charset=UTF-8');
echo json_encode($response, JSON_UNESCAPED_UNICODE);

// Cerrar la conexión
$conexion->close();
