<?php
// obtenerPqr.php - Controlador para obtener datos de una PQRS específica para edición

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json');

// Verificar que el usuario esté logueado
if (!isset($_SESSION['usuario']) || empty($_SESSION['usuario'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Usuario no autenticado'
    ]);
    exit;
}

require_once '../models/pqrsModel.php';

try {
    $id = (int)($_GET['id'] ?? 0);
    
    if ($id <= 0) {
        throw new Exception('ID de PQRS no válido');
    }
    
    // CORRECCIÓN: Obtener datos completos del usuario desde la BD
    $usuario = $_SESSION['usuario'];
    
    // Obtener conexión a BD para verificar usuario
    require_once '../config/db.php';
    
    if (!$pdo) {
        throw new Exception("No se pudo obtener la conexión a la base de datos");
    }
    
    $stmt = $pdo->prepare("SELECT usuario_cc FROM tbl_usuario WHERE usuario_cc = ? AND usu_estado = 'Activo'");
    $usuarioId = $usuario['id'] ?? $usuario['usuario_cc'] ?? null;
    
    if (!$usuarioId) {
        throw new Exception('Datos de sesión incompletos');
    }
    
    $stmt->execute([$usuarioId]);
    $usuario_completo = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$usuario_completo) {
        throw new Exception('Usuario no encontrado en la base de datos');
    }
    
    $usuarioCc = $usuario_completo['usuario_cc'];
    
    // Obtener PQRS
    $pqrsModel = new PqrsModel();
    $pqrs = $pqrsModel->obtenerPorId($id);
    
    if (!$pqrs) {
        throw new Exception('PQRS no encontrada');
    }
    
    // Verificar permisos: solo el usuario dueño puede ver
    if ($usuarioCc != $pqrs['usuario_cc']) {
        throw new Exception('No tiene permisos para ver esta PQRS');
    }
    
    // Solo se pueden editar PQRS pendientes
    if ($pqrs['estado'] !== 'pendiente') {
        throw new Exception('Solo se pueden editar PQRS pendientes');
    }
    
    echo json_encode([
        'success' => true,
        'data' => $pqrs
    ]);
    
} catch (Exception $e) {
    error_log("Error en obtenerPqr.php: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>