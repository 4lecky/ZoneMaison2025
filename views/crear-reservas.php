<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Configuración de base de datos
$servername = "localhost";
$username = "tu_usuario_db";
$password = "tu_password_db";
$dbname = "tu_base_de_datos";

try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Obtener datos JSON del POST
    $input = json_decode(file_get_contents('php://input'), true);
    
    // Validar datos requeridos
    $required_fields = ['nombre', 'apartamento', 'documento', 'fecha', 'hora_inicio', 'hora_fin', 'area_id'];
    foreach ($required_fields as $field) {
        if (empty($input[$field])) {
            throw new Exception("Campo requerido faltante: $field");
        }
    }
    
    // Validar que la fecha no sea en el pasado
    $reservationDate = new DateTime($input['fecha']);
    $today = new DateTime();
    $today->setTime(0, 0, 0);
    
    if ($reservationDate < $today) {
        throw new Exception("No se pueden hacer reservas para fechas pasadas");
    }
    
    // Validar que sea con al menos 48 horas de anticipación
    $minDate = new DateTime('+2 days');
    $minDate->setTime(0, 0, 0);
    
    if ($reservationDate < $minDate) {
        throw new Exception("Las reservas deben hacerse con mínimo 48 horas de anticipación");
    }
    
    // Verificar disponibilidad del horario
    $stmt = $pdo->prepare("
        SELECT COUNT(*) as count 
        FROM tbl_reserva 
        WHERE rese_fecha_inicio = ? 
        AND rese_area_id = ?
        AND (
            (rese_hora_inicio < ? AND rese_hora_fin > ?) 
            OR (rese_hora_inicio < ? AND rese_hora_fin > ?)
            OR (rese_hora_inicio >= ? AND rese_hora_fin <= ?)
        )
    ");
    
    $stmt->execute([
        $input['fecha'],
        $input['area_id'],
        $input['hora_fin'], $input['hora_inicio'],    // Conflicto tipo 1
        $input['hora_fin'], $input['hora_fin'],       // Conflicto tipo 2  
        $input['hora_inicio'], $input['hora_inicio']  // Conflicto tipo 3
    ]);
    
    $conflict = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($conflict['count'] > 0) {
        throw new Exception("El horario seleccionado ya está ocupado");
    }
    
    // Generar nuevo ID de reserva
    $stmt = $pdo->prepare("SELECT MAX(rese_id) as max_id FROM tbl_reserva");
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $newId = ($result['max_id'] ?? 0) + 1;
    
    // Insertar nueva reserva
    $stmt = $pdo->prepare("
        INSERT INTO tbl_reserva (
            rese_id,
            rese_usuario_nombre,
            rese_numero_apartamento,
            rese_fecha_inicio,
            rese_fecha_fin,
            rese_hora_inicio,
            rese_hora_fin,
            rese_area_id,
            rese_usuario_cc
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    
    $stmt->execute([
        $newId,
        $input['nombre'],
        $input['apartamento'],
        $input['fecha'],
        $input['fecha'], // Mismo día para inicio y fin
        $input['hora_inicio'],
        $input['hora_fin'],
        $input['area_id'],
        $input['documento']
    ]);
    
    echo json_encode([
        'success' => true,
        'message' => 'Reserva creada exitosamente',
        'reservation_id' => $newId
    ]);
    
} catch(Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
    
} catch(PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error de base de datos: ' . $e->getMessage()
    ]);
}
?>