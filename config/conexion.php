<?php
// Cargar variables de entorno
require __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

// Obtener las variables de entorno para la conexión
$host = $_ENV['DB_HOST'];
$user = $_ENV['DB_USERNAME'];
$password = $_ENV['DB_PASSWORD'];
$db_name = $_ENV['DB_DATABASE'];

// Crear la conexión
$conexion = mysqli_connect($host, $user, $password, $db_name);

// Verificar la conexión
if (!$conexion) {
    die(json_encode([
        'status' => false,
        'message' => 'Conexión fallida: ' . mysqli_connect_error()
    ]));
}
