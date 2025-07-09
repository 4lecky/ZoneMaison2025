<?php
<<<<<<< HEAD
=======

// Importante los espacios entre la 'conexion' y el '='
// $conexion = new mysqli('localhost','root','','zonemaisons');
// $conexion->set_charset("utf8");

// conexion local
>>>>>>> b749a1aeeb17e11874b55fe17b55fa3d883dc79d
$host = 'localhost';
$db   = 'zonemaisons';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

<<<<<<< HEAD
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
=======

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
>>>>>>> b749a1aeeb17e11874b55fe17b55fa3d883dc79d
];

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=$charset", $user, $pass, $options);
<<<<<<< HEAD
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}
=======
} catch (\PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}

return $pdo;
// !IMPORTANTE¡ No olvidar el 'return'

?>
>>>>>>> b749a1aeeb17e11874b55fe17b55fa3d883dc79d
