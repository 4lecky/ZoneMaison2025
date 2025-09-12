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
    
    $pqrsModel = new PqrsModel();
    $id = (int)$_GET['id'];
    
    // Verificar que la PQRS existe y pertenece al usuario
    $pqr = $pqrsModel->obtenerPorId($id);
    
    if (!$pqr) {
        $_SESSION['error_mensaje'] = 'PQRS no encontrada';
        header("Location: ../views/mis_pqrs.php");
        exit;
    }

    if ($pqr['usuario_cc'] != $usuarioCc) {
        $_SESSION['error_mensaje'] = 'No tiene permisos para eliminar esta PQRS';
        header("Location: ../views/mis_pqrs.php");
        exit;
    }

    if ($pqr['estado'] !== 'pendiente') {
        $_SESSION['error_mensaje'] = 'Solo se pueden eliminar PQRS que estén en estado pendiente';
        header("Location: ../views/mis_pqrs.php");
        exit;
    }

    // Eliminar archivos adjuntos si existen
    if (!empty($pqr['archivos'])) {
        $archivosData = json_decode($pqr['archivos'], true);
        if (is_array($archivosData)) {
            foreach ($archivosData as $archivo) {
                if (!empty($archivo['ruta'])) {
                    $rutaCompleta = '../' . $archivo['ruta'];
                    if (file_exists($rutaCompleta)) {
                        unlink($rutaCompleta);
                    }
                }
            }
        }
    }

    // Intentar eliminar la PQRS
    if ($pqrsModel->eliminar($id, $usuarioCc)) {
        $_SESSION['mensaje_pqrs'] = [
            'texto' => 'PQRS eliminada exitosamente',
            'tipo' => 'success'
        ];
        error_log("PQRS eliminada exitosamente - ID: $id - Usuario: " . $usuarioCc);
    } else {
        $_SESSION['error_mensaje'] = 'Error al eliminar la PQRS. Intente nuevamente';
        error_log("Error eliminando PQRS - ID: $id - Usuario: " . $usuarioCc);
    }

} catch (Exception $e) {
    error_log("Excepción eliminando PQRS: " . $e->getMessage());
    $_SESSION['error_mensaje'] = $e->getMessage();
}

// Redirigir de vuelta a mis PQRS
header("Location: ../views/mis_pqrs.php");
exit;