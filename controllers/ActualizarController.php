<?php
// Incluir el archivo de conexión
include('../config/conexion.php');

// Establecer la codificación de la conexión a utf8mb4
mysqli_set_charset($conexion, 'utf8mb4');

// Inicializar la respuesta
$response = ['status' => false, 'data' => null];

// Verificar si el parámetro 'id' está presente en la URL para la petición GET
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Consulta SQL para obtener los datos del contacto por ID
    $consulta = "SELECT * FROM contactos WHERE id = '$id'";
    $resultado = mysqli_query($conexion, $consulta);

    // Verificar si se encontró el contacto
    if ($resultado && mysqli_num_rows($resultado) > 0) {
        $contacto = mysqli_fetch_assoc($resultado);

        // Respuesta exitosa con los datos del contacto
        $response['status'] = true;
        $response['data'] = $contacto;
    } else {
        // Si no se encontró el contacto
        $response['data'] = 'Contacto no encontrado.';
    }
}

// Verificar si se recibió una solicitud POST o PUT para la actualización
if ($_SERVER['REQUEST_METHOD'] == 'POST' || $_SERVER['REQUEST_METHOD'] == 'PUT') {
    // Obtener los datos del cuerpo de la solicitud (JSON)
    $input = json_decode(file_get_contents('php://input'), true);

    // Verificar si se recibieron los datos necesarios
    if (isset($input['id'], $input['nombre'], $input['apellidos'], $input['telefono'], $input['email'])) {
        $id = $input['id'];
        $nombre = mysqli_real_escape_string($conexion, $input['nombre']);
        $apellidos = mysqli_real_escape_string($conexion, $input['apellidos']);
        $telefono = mysqli_real_escape_string($conexion, $input['telefono']);
        $email = mysqli_real_escape_string($conexion, $input['email']);
        $fecha_actualizacion = date('Y-m-d H:i:s');

        // Consulta SQL para actualizar los datos del contacto
        $actualizar = "UPDATE contactos 
                       SET nombre = '$nombre', 
                           apellidos = '$apellidos', 
                           telefono = '$telefono', 
                           email = '$email', 
                           updated_at = '$fecha_actualizacion' 
                       WHERE id = '$id'";

        // Ejecutar la actualización
        if (mysqli_query($conexion, $actualizar)) {
            // Respuesta exitosa
            $response['status'] = true;
            $response['data'] = [
                'id' => $id,
                'nombre' => $nombre,
                'apellidos' => $apellidos,
                'telefono' => $telefono,
                'email' => $email,
                'updated_at' => $fecha_actualizacion
            ];
        } else {
            // Error en la actualización
            $response['data'] = 'Error al actualizar los datos: ' . mysqli_error($conexion);
        }
    } else {
        // Si falta algún dato en la solicitud
        $response['data'] = 'Faltan datos para la actualización.';
    }
}

// Enviar la respuesta como JSON
header('Content-Type: application/json; charset=UTF-8');
echo json_encode($response, JSON_UNESCAPED_UNICODE);

// Cerrar la conexión al final
mysqli_close($conexion);
