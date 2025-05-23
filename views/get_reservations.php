<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

// Configuración de base de datos
$servername = "localhost";
$username = "tu_usuario_db";
$password = "tu_password_db";
$dbname = "tu_base_de_datos";

try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Obtener reservas del mes actual y próximo mes
    $currentDate = date('Y-m-01');
    $nextMonth = date('Y-m-01', strtotime('+2 months'));
    
    $stmt = $pdo->prepare("
        SELECT 
            rese_fecha_inicio,
            rese_fecha_fin,
            rese_hora_inicio,
            rese_hora_fin,
            rese_area_id
        FROM tbl_reserva 
        WHERE rese_fecha_inicio >= ? 
        AND rese_fecha_inicio < ?
        AND rese_area_id = 1
        ORDER BY rese_fecha_inicio, rese_hora_inicio
    ");
    
    $stmt->execute([$currentDate, $nextMonth]);
    $reservations = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode($reservations);
    
} catch(PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'error' => 'Error de base de datos',
        'message' => $e->getMessage()
    ]);
}
?>