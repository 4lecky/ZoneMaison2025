<?php
// obtenerPqr.php - Para obtener datos de una PQRS específica para edición

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Verificar que el usuario esté logueado
if (!isset($_SESSION['usuario']) || empty($_SESSION['usuario'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Debe iniciar sesión'
    ]);
    exit;
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'ID de PQRS no válido'
    ]);
    exit;
}

require_once '../models/pqrsModel.php';

try {
    $pqrsModel = new PqrsModel();
    $pqr = $pqrsModel->obtenerPorId($_GET['id']);
    
    if (!$pqr) {
        echo json_encode([
            'success' => false,
            'message' => 'PQRS no encontrada'
        ]);
        exit;
    }

    // Verificar que la PQRS pertenezca al usuario logueado
    $usuario = $_SESSION['usuario'];
    if ($pqr['usuario_cc'] != $usuario['usuario_cc']) {
        echo json_encode([
            'success' => false,
            'message' => 'No tiene permisos para acceder a esta PQRS'
        ]);
        exit;
    }

    // Verificar que la PQRS esté en estado pendiente (solo se pueden editar pendientes)
    if ($pqr['estado'] !== 'pendiente') {
        echo json_encode([
            'success' => false,
            'message' => 'Solo se pueden editar PQRS que estén en estado pendiente'
        ]);
        exit;
    }

    echo json_encode([
        'success' => true,
        'data' => [
            'id' => $pqr['id'],
            'tipo_pqr' => $pqr['tipo_pqr'],
            'asunto' => $pqr['asunto'],
            'mensaje' => $pqr['mensaje'],
            'medio_respuesta' => $pqr['medio_respuesta'],
            'archivos' => $pqr['archivos'],
            'estado' => $pqr['estado'],
            'fecha_creacion' => $pqr['fecha_creacion']
        ]
    ]);

} catch (Exception $e) {
    error_log("Error en obtenerPqr: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Error interno del servidor'
    ]);
}
?>