<?php
// Incluir el archivo de conexión
include('../config/conexion.php');

// Inicializar la respuesta
$response = ['status' => false, 'data' => null];

// Verificar si el método de solicitud es DELETE
if ($_SERVER['REQUEST_METHOD'] == 'DELETE') {
    // Obtener el ID desde la URL
    if (isset($_GET['id'])) {
        $id = $_GET['id'];

        // Consulta SQL para eliminar el contacto por ID
        $sql = "DELETE FROM contactos WHERE id = '$id'";

        // Ejecutar la consulta
        if (mysqli_query($conexion, $sql)) {
            // Verificar si se eliminó alguna fila
            if (mysqli_affected_rows($conexion) > 0) {
                $response['status'] = true;
                $response['data'] = 'Contacto eliminado exitosamente.';
            } else {
                $response['data'] = 'No se encontró el contacto para eliminar.';
            }
        } else {
            // Error en la consulta
            $response['data'] = 'Error al eliminar el contacto: ' . mysqli_error($conexion);
        }
    } else {
        // Si no se proporcionó el ID
        $response['data'] = 'ID de contacto no proporcionado.';
    }
} else {
    $response['data'] = 'Método de solicitud no permitido.';
}

// Enviar la respuesta como JSON
header('Content-Type: application/json; charset=UTF-8');
echo json_encode($response, JSON_UNESCAPED_UNICODE);

// Cerrar la conexión
mysqli_close($conexion);
