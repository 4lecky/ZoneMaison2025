<?php
// eliminarPqr.php - Para eliminar PQRS del usuario logueado

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Verificar que el usuario esté logueado
if (!isset($_SESSION['usuario']) || empty($_SESSION['usuario'])) {
    $_SESSION['error_mensaje'] = 'Debe iniciar sesión para eliminar PQRS';
    header("Location: ../views/login.php");
    exit;
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['error_mensaje'] = 'ID de PQRS no válido';
    header("Location: ../views/mis_pqrs.php");
    exit;
}

require_once '../models/pqrsModel.php';

try {
    $usuario = $_SESSION['usuario'];
    $pqrsModel = new PqrsModel();
    $id = (int)$_GET['id'];
    
    // Verificar que la PQRS existe y pertenece al usuario
    $pqr = $pqrsModel->obtenerPorId($id);
    
    if (!$pqr) {
        $_SESSION['error_mensaje'] = 'PQRS no encontrada';
        header("Location: ../views/mis_pqrs.php");
        exit;
    }

    if ($pqr['usuario_cc'] != $usuario['usuario_cc']) {
        $_SESSION['error_mensaje'] = 'No tiene permisos para eliminar esta PQRS';
        header("Location: ../views/mis_pqrs.php");
        exit;
    }

    if ($pqr['estado'] !== 'pendiente') {
        $_SESSION['error_mensaje'] = 'Solo se pueden eliminar PQRS que estén en estado pendiente';
        header("Location: ../views/mis_pqrs.php");
        exit;
    }

    // Intentar eliminar la PQRS
    if ($pqrsModel->eliminar($id, $usuario['usuario_cc'])) {
        $_SESSION['mensaje_pqrs'] = [
            'texto' => 'PQRS eliminada exitosamente',
            'tipo' => 'success'
        ];
        error_log("PQRS eliminada exitosamente - ID: $id - Usuario: " . $usuario['usuario_cc']);
    } else {
        $_SESSION['error_mensaje'] = 'Error al eliminar la PQRS. Intente nuevamente';
        error_log("Error eliminando PQRS - ID: $id - Usuario: " . $usuario['usuario_cc']);
    }

} catch (Exception $e) {
    error_log("Excepción eliminando PQRS: " . $e->getMessage());
    $_SESSION['error_mensaje'] = $e->getMessage();
}

// Redirigir de vuelta a mis PQRS
header("Location: ../views/mis_pqrs.php");
exit;
?>