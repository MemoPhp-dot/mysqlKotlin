<?php

// Cargar las variables de entorno
require_once 'vendor/autoload.php';

use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Conexión a la base de datos utilizando variables de entorno
$dsn = "mysql:host=" . $_ENV['DB_HOST'] . ";dbname=" . $_ENV['DB_DATABASE'];
$username = $_ENV['DB_USERNAME'];
$password = $_ENV['DB_PASSWORD'];

try {
    $pdo = new PDO($dsn, $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // Enviar un error de conexión si no se puede conectar
    echo json_encode([
        'status' => false,
        'message' => 'No se pudo conectar a la base de datos: ' . $e->getMessage()
    ]);
    exit;
}

// Verificar si se han enviado los datos del formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $jsonData = file_get_contents("php://input");
    $data = json_decode($jsonData, true);  // Decodifica el JSON en un array asociativo
    $correo = $data['correo'] ?? '';  // Accede al correo desde el JSON decodificado
    $contrasena = $data['contrasena'] ?? ''; // Asegúrate de que esta variable es la que se usa en la comparación

    // Buscar el usuario en la base de datos
    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE correo = :correo LIMIT 1");
    $stmt->execute(['correo' => $correo]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Verificar si el usuario existe y si la contraseña coincide
    if ($user && $contrasena === $user['contrasena']) {
        // Respuesta exitosa con datos del usuario
        echo json_encode([
            'status' => true,
            'data' => [
                'nombre' => $user['nombre'],
                'correo' => $user['correo'],
                // Puedes agregar más datos que desees retornar
            ]
        ]);
    } else {
        // Respuesta de error con mensaje
        echo json_encode([
            'status' => false,
            'message' => 'Correo o contrasena incorrectos.'
        ]);
    }
} else {
    // Respuesta de error si no es una solicitud POST
    echo json_encode([
        'status' => false,
        'message' => 'Metodo de solicitud no permitido.'
    ]);
}
