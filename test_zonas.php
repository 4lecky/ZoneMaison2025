<?php
$host = 'localhost';
$db   = 'zonemaisons';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
    echo "<h2>Conexión exitosa ✅</h2>";

    $stmt = $pdo->query("SELECT * FROM zonas_comunes");
    $zonas = $stmt->fetchAll();

    echo "<h3>Zonas encontradas:</h3><pre>";
    print_r($zonas);
    echo "</pre>";

} catch (PDOException $e) {
    echo "❌ Error de conexión: " . $e->getMessage();
}
