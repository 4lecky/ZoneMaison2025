<?php

// Importante los espacios entre la 'conexion' y el '='
// $conexion = new mysqli('localhost','root','','zonemaisons');
// $conexion->set_charset("utf8");

// conexion local
$host = 'localhost';
$db   = 'zonemaisons';
$user = 'root';
$pass = '';
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

return $pdo;
// !IMPORTANTE¡ No olvidar el 'return'

?>