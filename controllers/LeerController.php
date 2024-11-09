<?php
// Incluir el archivo de conexión
include('../config/conexion.php');

// Establecer la codificación de la conexión a utf8mb4 (esto debe hacerse después de la conexión)
mysqli_set_charset($conexion, 'utf8mb4');

// Consulta SQL para obtener los contactos
$consulta = "SELECT * FROM contactos";

// Ejecutar la consulta
$resultado = mysqli_query($conexion, $consulta);

// Verificar si la consulta fue exitosa
if (!$resultado) {
    die(json_encode([
        'status' => false,
        'message' => 'Error en la consulta: ' . mysqli_error($conexion)
    ]));
}

// Obtener el número de registros
$registros = mysqli_num_rows($resultado);

// Arreglo para almacenar los resultados
$datos = [];

// Si hay registros, procesamos la información
if ($registros > 0) {
    while ($fila = mysqli_fetch_object($resultado)) {
        $datos[] = $fila;
    }

    // Respuesta exitosa con los datos obtenidos
    header('Content-Type: application/json; charset=UTF-8');
    echo json_encode([
        'status' => true,
        'data' => $datos
    ], JSON_UNESCAPED_UNICODE);
} else {
    // Si no hay registros
    header('Content-Type: application/json; charset=UTF-8');
    echo json_encode([
        'status' => false,
        'message' => 'No hay contactos registrados.'
    ], JSON_UNESCAPED_UNICODE);
}

// Cerrar la conexión
mysqli_close($conexion);
