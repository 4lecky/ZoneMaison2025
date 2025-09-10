<?php

// Importane los espacios entre la 'conexion' y el '='

// conexion local
// $host = 'localhost';
// $db   = 'zonemaisons';
// $user = 'root';
// $pass = '';
// $charset = 'utf8mb4';


// Conexión hosting
$host = "localhost";
$db = "u413625843_zonemaisons";
$user = "u413625843_LunaSalas";
$pass = "ZoneMaison2025*";
$charset = 'utf8mb4';

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=$charset", $user, $pass, $options);
} catch (\PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}


// Para pruebas de correo electronico (mailtrap)
// define("HOST","sandbox.smtp.mailtrap.io");
// define("PORT","587");
// define("USERNAME","7b919ecea72a1d");
// define("PASSWORD","fe72640e7e8274");

// Direccion REAL (De donde van a salir los correos)
define("HOST", "smtp.gmail.com");
define("USERNAME", "zonemaizon2025@gmail.com");
define("PASSWORD", "zzgovbvvgqrirzdh");
// zzgo vbvv gqri rzdh


define("SMTP_SECURE", "TLS");
define("TIEMPO_VIDA", time() * 24);


// Configurar logs de errores del sistema PQRS
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/../logs/pqrs_errors.log');

// Siempre retornar la conexión
return $pdo;
?>
