<?php
// editarPqr.php - Controlador para editar PQRS existentes
header('Content-Type: application/json; charset=utf-8');

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

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'success' => false,
        'message' => 'Método no permitido'
    ]);
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
    
    // Validar datos recibidos
    if (!isset($_POST['id']) || !is_numeric($_POST['id'])) {
        echo json_encode([
            'success' => false,
            'message' => 'ID de PQRS no válido'
        ]);
        exit;
    }

    $id = (int)$_POST['id'];
    
    // Verificar que la PQRS existe y pertenece al usuario
    $pqrExistente = $pqrsModel->obtenerPorId($id);
    
    if (!$pqrExistente) {
        echo json_encode([
            'success' => false,
            'message' => 'PQRS no encontrada'
        ]);
        exit;
    }

    if ($pqrExistente['usuario_cc'] != $usuarioCc) {
        echo json_encode([
            'success' => false,
            'message' => 'No tiene permisos para editar esta PQRS'
        ]);
        exit;
    }

    if ($pqrExistente['estado'] !== 'pendiente') {
        echo json_encode([
            'success' => false,
            'message' => 'Solo se pueden editar PQRS pendientes'
        ]);
        exit;
    }

    // Validar campos obligatorios
    $errores = [];

    if (empty($_POST['tipo_pqr']) || !in_array($_POST['tipo_pqr'], ['peticion', 'queja', 'reclamo', 'sugerencia'])) {
        $errores[] = "Tipo de PQRS inválido";
    }

    if (empty(trim($_POST['asunto'])) || strlen(trim($_POST['asunto'])) < 5) {
        $errores[] = "El asunto debe tener al menos 5 caracteres";
    }

    if (empty(trim($_POST['mensaje'])) || strlen(trim($_POST['mensaje'])) < 10) {
        $errores[] = "El mensaje debe tener al menos 10 caracteres";
    }

    if (empty($_POST['medio_respuesta'])) {
        $errores[] = "Debe seleccionar al menos un medio de respuesta";
    }

    if (!empty($errores)) {
        echo json_encode([
            'success' => false,
            'message' => implode('. ', $errores)
        ]);
        exit;
    }

    // Procesar archivos si se subieron nuevos
    $archivosJson = $pqrExistente['archivos']; // Mantener archivos existentes por defecto
    
    if (isset($_FILES['archivos']) && !empty($_FILES['archivos']['name'][0])) {
        // Eliminar anteriores si existen
        if (!empty($pqrExistente['archivos'])) {
            $archivosAnteriores = json_decode($pqrExistente['archivos'], true);
            if (is_array($archivosAnteriores)) {
                foreach ($archivosAnteriores as $archivo) {
                    if (!empty($archivo['ruta'])) {
                        $rutaCompleta = '../' . $archivo['ruta'];
                        if (file_exists($rutaCompleta)) {
                            unlink($rutaCompleta);
                        }
                    }
                }
            }
        }
        
        // Procesar nuevos
        $archivosProcesados = procesarArchivosSimple($_FILES['archivos']);
        if (!$archivosProcesados['success']) {
            echo json_encode([
                'success' => false,
                'message' => 'Error procesando archivos: ' . implode('. ', $archivosProcesados['errores'])
            ]);
            exit;
        }
        
        $archivosJson = $archivosProcesados['json'];
    }

    // Datos para actualizar
    $datosActualizacion = [
        'id' => $id,
        'tipo_pqr' => $_POST['tipo_pqr'],
        'asunto' => trim($_POST['asunto']),
        'mensaje' => trim($_POST['mensaje']),
        'medio_respuesta' => $_POST['medio_respuesta'],
        'archivos' => $archivosJson
    ];

    // Actualizar
    if ($pqrsModel->actualizar($datosActualizacion)) {
        echo json_encode([
            'success' => true,
            'message' => 'PQRS actualizada exitosamente'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Error al actualizar la PQRS'
        ]);
    }

} catch (Exception $e) {
    error_log("Error en editarPqr: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Error interno del servidor: ' . $e->getMessage()
    ]);
}

/**
 * Función para procesar archivos
 */
function procesarArchivosSimple($archivos) {
    $directorioDestino = '../uploads/pqrs/';
    
    if (!file_exists($directorioDestino)) {
        if (!mkdir($directorioDestino, 0755, true)) {
            return [
                'success' => false,
                'errores' => ['No se pudo crear el directorio para archivos'],
                'json' => ''
            ];
        }
    }

    $archivosGuardados = [];
    $errores = [];
    $extensionesPermitidas = ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png'];
    $tamañoMaximo = 5 * 1024 * 1024; // 5MB

    if (is_array($archivos['name'])) {
        $totalArchivos = count($archivos['name']);
        
        for ($i = 0; $i < $totalArchivos; $i++) {
            if ($archivos['error'][$i] !== UPLOAD_ERR_OK || empty($archivos['name'][$i])) {
                continue;
            }

            $nombreOriginal = $archivos['name'][$i];
            $tamañoArchivo = $archivos['size'][$i];
            $archivoTemporal = $archivos['tmp_name'][$i];
            $extension = strtolower(pathinfo($nombreOriginal, PATHINFO_EXTENSION));

            if (!in_array($extension, $extensionesPermitidas)) {
                $errores[] = "Archivo '$nombreOriginal': tipo no permitido";
                continue;
            }

            if ($tamañoArchivo > $tamañoMaximo) {
                $errores[] = "Archivo '$nombreOriginal': muy grande (máximo 5MB)";
                continue;
            }

            $nombreUnico = uniqid() . '_' . time() . '.' . $extension;
            $rutaDestino = $directorioDestino . $nombreUnico;

            if (move_uploaded_file($archivoTemporal, $rutaDestino)) {
                $archivosGuardados[] = [
                    'nombre_original' => $nombreOriginal,
                    'nombre_archivo' => $nombreUnico,
                    'ruta' => 'uploads/pqrs/' . $nombreUnico,
                    'tipo' => $archivos['type'][$i],
                    'tamaño' => $tamañoArchivo,
                    'fecha_subida' => date('Y-m-d H:i:s')
                ];
            } else {
                $errores[] = "No se pudo guardar '$nombreOriginal'";
            }
        }
    }

    return [
        'success' => empty($errores),
        'errores' => $errores,
        'json' => !empty($archivosGuardados) ? json_encode($archivosGuardados) : ''
    ];
}
?>
