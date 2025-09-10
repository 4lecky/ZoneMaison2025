<?php
// db.php - Conexión a BD con detección automática de entorno (local/hosting)

// Solo crear una conexión si no existe ya
if (!isset($GLOBALS['pdo_connection']) || $GLOBALS['pdo_connection'] === null) {
    
    // Detectar si estamos en local o en hosting
    $isLocal = in_array($_SERVER['SERVER_NAME'], ['localhost', '127.0.0.1']);

    if ($isLocal) {
        // Configuración LOCAL (XAMPP)
        $host = 'localhost';
        $dbname = 'zonemaisons';
        $username = 'root';
        $password = '';
    } else {
        // Configuración HOSTING
        $host = 'localhost';
        $dbname = 'u413625843_zonemaisons';
        $username = 'u413625843_LunaSalas';
        $password = 'ZoneMaison2025*';
    }

    try {
        // Crear conexión PDO
        $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
        
        // Configurar PDO para mostrar errores y fetch por defecto
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        
        // Refuerzo de codificación
        $pdo->exec("SET NAMES utf8mb4");
        $pdo->exec("SET CHARACTER SET utf8mb4");
        
        // Guardar en variable global
        $GLOBALS['pdo_connection'] = $pdo;
        
        // Log de conexión (solo en desarrollo)
        if ($isLocal) {
            error_log("DB: Conexión establecida correctamente a $dbname (local)");
        } else {
            error_log("DB: Conexión establecida correctamente a $dbname (hosting)");
        }

        // Definir constantes para correos (solo si no están definidas)
        if (!defined("HOST")) define("HOST", "smtp.gmail.com");
        if (!defined("USERNAME")) define("USERNAME", "zonemaizon2025@gmail.com");
        if (!defined("PASSWORD")) define("PASSWORD", "zzgovbvvgqrirzdh");
        if (!defined("SMTP_SECURE")) define("SMTP_SECURE", "TLS");
        if (!defined("TIEMPO_VIDA")) define("TIEMPO_VIDA", time() * 24);

// Importante los espacios entre la 'conexion' y el '='
// $conexion = new mysqli('localhost','root','','zonemaisons');
// $conexion->set_charset("utf8");

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

    } catch(PDOException $e) {
        // Log del error
        error_log("Error de conexión a BD: " . $e->getMessage());
        error_log("Host: $host, Database: $dbname, User: $username");
        
        // Mostrar error según entorno
        if ($isLocal) {
            die("Connection failed: " . $e->getMessage());
        } else {
            die("Error de conexión a la base de datos. Contacte al administrador.");
        }
    }
}

// Configurar logs de errores del sistema PQRS
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/../logs/pqrs_errors.log');

// Siempre retornar la conexión
return $GLOBALS['pdo_connection'];
?>
