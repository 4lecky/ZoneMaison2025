<?php
// AdministradorController.php - Versión COMPLETA CORREGIDA CON DEBUG

// Control de errores y output
error_reporting(E_ALL);
ini_set('display_errors', 0);
ob_start();

// Headers para AJAX
header('Content-Type: application/json; charset=utf-8');

// Iniciar sesión
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

try {
    // Verificar que sea una petición POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Método no permitido');
    }

    // Verificar que tenga permisos de administrador
    if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
        throw new Exception('Acceso denegado');
    }

    // Obtener la acción
    $accion = $_POST['accion'] ?? '';

    if (empty($accion)) {
        throw new Exception('Acción no especificada');
    }

    // Cargar dependencias
    require_once '../models/pqrsModel.php';
    require_once '../config/MailConfig.php';

    $pqrsModel = new PqrsModel();

    // Enrutador de acciones
    switch ($accion) {
        case 'responder_pqrs':
            responderPqrs($pqrsModel, $_POST, $_FILES);
            break;
            
        case 'responder_pqrs_debug':  // Opción temporal de debug
            responderPqrsDebug($pqrsModel, $_POST, $_FILES);
            break;
            
        case 'cambiar_estado':
            cambiarEstado($pqrsModel, $_POST);
            break;
            
        case 'marcar_proceso':
            marcarComoProceso($pqrsModel, $_POST);
            break;
            
        default:
            throw new Exception('Acción no válida: ' . $accion);
    }

} catch (Exception $e) {
    // Limpiar cualquier output previo
    while (ob_get_level()) {
        ob_end_clean();
    }
    
    // Log del error
    error_log("AdministradorController Error: " . $e->getMessage());
    
    // Enviar respuesta de error en JSON
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
    exit;
}

/**
 * Cambiar estado de PQRS
 */
function cambiarEstado($pqrsModel, $datos) {
    try {
        $id = (int)($datos['id'] ?? 0);
        $estado = trim($datos['estado'] ?? '');
        
        if ($id <= 0) {
            throw new Exception('ID de PQRS no válido');
        }
        
        $estadosValidos = ['pendiente', 'en_proceso', 'resuelto'];
        if (!in_array($estado, $estadosValidos)) {
            throw new Exception('Estado no válido');
        }
        
        $pqrs = $pqrsModel->obtenerPorId($id);
        if (!$pqrs) {
            throw new Exception('PQRS no encontrada');
        }
        
        if ($pqrs['estado'] === $estado) {
            throw new Exception('La PQRS ya tiene ese estado');
        }
        
        $resultado = $pqrsModel->actualizarEstado($id, $estado);
        
        if (!$resultado) {
            throw new Exception('No se pudo actualizar el estado');
        }
        
        while (ob_get_level()) {
            ob_end_clean();
        }
        
        echo json_encode([
            'success' => true,
            'message' => 'Estado actualizado correctamente',
            'data' => [
                'id' => $id,
                'estado_anterior' => $pqrs['estado'],
                'estado_nuevo' => $estado
            ]
        ]);
        exit;
        
    } catch (Exception $e) {
        error_log("Error en cambiarEstado: " . $e->getMessage());
        throw new Exception('Error al cambiar estado: ' . $e->getMessage());
    }
}

/**
 * Marcar PQRS como en proceso
 */
function marcarComoProceso($pqrsModel, $datos) {
    try {
        $id = (int)($datos['id'] ?? 0);
        
        if ($id <= 0) {
            throw new Exception('ID de PQRS no válido');
        }
        
        $pqrs = $pqrsModel->obtenerPorId($id);
        if (!$pqrs) {
            throw new Exception('PQRS no encontrada');
        }
        
        if ($pqrs['estado'] !== 'pendiente') {
            throw new Exception('Solo se pueden marcar como "En Proceso" las PQRS pendientes');
        }
        
        $resultado = $pqrsModel->actualizarEstado($id, 'en_proceso');
        
        if (!$resultado) {
            throw new Exception('No se pudo actualizar el estado');
        }
        
        while (ob_get_level()) {
            ob_end_clean();
        }
        
        echo json_encode([
            'success' => true,
            'message' => 'PQRS marcada como "En Proceso" correctamente',
            'data' => [
                'id' => $id,
                'estado_anterior' => 'pendiente',
                'estado_nuevo' => 'en_proceso'
            ]
        ]);
        exit;
        
    } catch (Exception $e) {
        error_log("Error en marcarComoProceso: " . $e->getMessage());
        throw new Exception('Error al marcar como proceso: ' . $e->getMessage());
    }
}

/**
 * FUNCIÓN PRINCIPAL: Responder PQRS (versión normal funcionando)
 */
function responderPqrs($pqrsModel, $datos, $archivos) {
    try {
        error_log("=== RESPONDER PQRS - INICIO ===");
        
        // Validar datos requeridos
        $id = (int)($datos['id'] ?? 0);
        $respuesta = trim($datos['respuesta'] ?? '');
        
        if ($id <= 0) {
            throw new Exception('ID de PQRS no válido');
        }
        
        if (empty($respuesta)) {
            throw new Exception('La respuesta no puede estar vacía');
        }
        
        if (strlen($respuesta) < 10) {
            throw new Exception('La respuesta debe tener al menos 10 caracteres');
        }

        // Obtener la PQRS completa
        $pqrs = method_exists($pqrsModel, 'obtenerPqrsCompleta') 
                ? $pqrsModel->obtenerPqrsCompleta($id) 
                : $pqrsModel->obtenerPorId($id);
        
        if (!$pqrs) {
            throw new Exception('PQRS no encontrada');
        }
        
        if ($pqrs['estado'] === 'resuelto') {
            throw new Exception('Esta PQRS ya fue resuelta');
        }

        // Procesar archivos adjuntos
        $adjuntosProcesados = [];
        if (!empty($archivos['adjuntos'])) {
            error_log("Procesando adjuntos para PQRS ID: $id");
            error_log("Archivos recibidos: " . print_r($archivos['adjuntos'], true));
            
            $adjuntosProcesados = procesarAdjuntos($archivos['adjuntos'], $id);
            
            if ($adjuntosProcesados === false) {
                throw new Exception('Error procesando los archivos adjuntos');
            }
            
            error_log("Adjuntos procesados: " . count($adjuntosProcesados));
            
            // DEBUG: Verificar que los archivos existen después del procesamiento
            foreach ($adjuntosProcesados as $i => $adj) {
                $existe = file_exists($adj['ruta']);
                error_log("Adjunto $i después del procesamiento: {$adj['nombre_original']} -> Existe: " . ($existe ? 'SÍ' : 'NO'));
            }
        }

        // Determinar si marcar como resuelto
        $marcarResuelto = isset($datos['marcar_resuelto']) && $datos['marcar_resuelto'] == '1';
        
        // Obtener ID del administrador
        $adminId = $_SESSION['usuario_cc'] ?? $_SESSION['usuario_id'] ?? 1;

        // Guardar la respuesta CON adjuntos
        $resultado = $pqrsModel->guardarRespuesta($id, $respuesta, $adminId, $marcarResuelto, $adjuntosProcesados);
        
        if (!$resultado) {
            throw new Exception('No se pudo guardar la respuesta en la base de datos');
        }

        // Preparar datos para notificación CON adjuntos
        $datosCorreo = [
            'id' => $id,
            'nombres' => $pqrs['nombres'] ?? '',
            'apellidos' => $pqrs['apellidos'] ?? '',
            'email' => $pqrs['email'] ?? '',
            'tipo_pqr' => $pqrs['tipo_pqr'] ?? '',
            'asunto' => $pqrs['asunto'] ?? '',
            'mensaje' => $pqrs['mensaje'] ?? '',
            'respuesta' => $respuesta,
            'fecha_creacion' => $pqrs['fecha_creacion'] ?? date('Y-m-d H:i:s'),
            'fecha_respuesta' => date('Y-m-d H:i:s'),
            'adjuntos' => $adjuntosProcesados
        ];

        // DEBUG: Verificar datos antes del envío de correo
        error_log("=== DATOS PARA CORREO ===");
        error_log("Email destino: " . $datosCorreo['email']);
        error_log("Adjuntos en datos correo: " . count($datosCorreo['adjuntos']));

        // Enviar notificación por correo CON adjuntos
        $resultadoNotificacion = enviarNotificacionCorreoDebug($datosCorreo);

        // Limpiar output buffer y enviar respuesta exitosa
        while (ob_get_level()) {
            ob_end_clean();
        }
        
        $totalAdjuntos = count($adjuntosProcesados);
        $mensaje = 'Respuesta enviada correctamente';
        if ($totalAdjuntos > 0) {
            $mensaje .= " con $totalAdjuntos archivo(s) adjunto(s)";
        }
        
        echo json_encode([
            'success' => true,
            'message' => $mensaje,
            'data' => [
                'id' => $id,
                'estado_nuevo' => $marcarResuelto ? 'resuelto' : 'en_proceso',
                'adjuntos_procesados' => $totalAdjuntos,
                'notificacion_enviada' => $resultadoNotificacion['success'],
                'detalle_correo' => $resultadoNotificacion['detalle'] ?? 'Sin detalles'
            ]
        ]);
        exit;
        
    } catch (Exception $e) {
        error_log("Error en responderPqrs: " . $e->getMessage());
        throw new Exception('Error al responder PQRS: ' . $e->getMessage());
    }
}

/**
 * FUNCIÓN DEBUG: Responder PQRS con logging detallado
 */
function responderPqrsDebug($pqrsModel, $datos, $archivos) {
    try {
        error_log("=== INICIO DEBUG RESPONDER PQRS ===");
        
        // Validaciones básicas
        $id = (int)($datos['id'] ?? 0);
        $respuesta = trim($datos['respuesta'] ?? '');
        
        if ($id <= 0) {
            throw new Exception('ID de PQRS no válido');
        }
        
        if (empty($respuesta)) {
            throw new Exception('La respuesta no puede estar vacía');
        }

        // DEBUG: Mostrar estructura completa de $_FILES
        error_log("1. ESTRUCTURA COMPLETA DE \$_FILES:");
        error_log(print_r($archivos, true));
        
        // DEBUG: Verificar específicamente 'adjuntos'
        error_log("2. VERIFICANDO CAMPO 'adjuntos':");
        if (isset($archivos['adjuntos'])) {
            error_log("   - adjuntos existe en \$_FILES");
            error_log("   - Estructura: " . print_r($archivos['adjuntos'], true));
            
            if (isset($archivos['adjuntos']['name'])) {
                if (is_array($archivos['adjuntos']['name'])) {
                    error_log("   - Múltiples archivos detectados: " . count($archivos['adjuntos']['name']));
                    for ($i = 0; $i < count($archivos['adjuntos']['name']); $i++) {
                        error_log("     * Archivo $i: {$archivos['adjuntos']['name'][$i]} (Error: {$archivos['adjuntos']['error'][$i]})");
                    }
                } else {
                    error_log("   - Un solo archivo: {$archivos['adjuntos']['name']} (Error: {$archivos['adjuntos']['error']})");
                }
            }
        } else {
            error_log("   - ❌ NO hay campo 'adjuntos' en \$_FILES");
        }

        // Obtener PQRS
        $pqrs = $pqrsModel->obtenerPqrsCompleta($id);
        if (!$pqrs) {
            throw new Exception('PQRS no encontrada');
        }

        // PROCESAR ADJUNTOS CON DEBUG DETALLADO
        $adjuntosProcesados = [];
        if (!empty($archivos['adjuntos'])) {
            error_log("3. INICIANDO PROCESAMIENTO DE ADJUNTOS...");
            
            $adjuntosProcesados = procesarAdjuntosDebug($archivos['adjuntos'], $id);
            
            error_log("4. RESULTADO PROCESAMIENTO:");
            error_log("   - Adjuntos procesados: " . count($adjuntosProcesados));
            
        } else {
            error_log("3. ❌ NO HAY ADJUNTOS PARA PROCESAR");
        }

        // Guardar respuesta
        $marcarResuelto = isset($datos['marcar_resuelto']) && $datos['marcar_resuelto'] == '1';
        $adminId = $_SESSION['usuario_cc'] ?? 1;
        
        error_log("5. GUARDANDO RESPUESTA EN BD...");
        $resultado = $pqrsModel->guardarRespuesta($id, $respuesta, $adminId, $marcarResuelto, $adjuntosProcesados);
        
        if (!$resultado) {
            throw new Exception('No se pudo guardar la respuesta');
        }
        error_log("   - ✅ Respuesta guardada en BD");

        // PREPARAR DATOS PARA CORREO
        $datosCorreo = [
            'id' => $id,
            'nombres' => $pqrs['nombres'],
            'apellidos' => $pqrs['apellidos'],
            'email' => $pqrs['email'],
            'tipo_pqr' => $pqrs['tipo_pqr'],
            'asunto' => $pqrs['asunto'],
            'mensaje' => $pqrs['mensaje'],
            'respuesta' => $respuesta,
            'fecha_creacion' => $pqrs['fecha_creacion'],
            'fecha_respuesta' => date('Y-m-d H:i:s'),
            'adjuntos' => $adjuntosProcesados
        ];
        
        error_log("6. DATOS PREPARADOS PARA CORREO:");
        error_log("   - Email destino: " . $datosCorreo['email']);
        error_log("   - Adjuntos incluidos: " . count($datosCorreo['adjuntos']));

        // ENVIAR CORREO CON DEBUG
        error_log("7. ENVIANDO CORREO...");
        $resultadoNotificacion = enviarNotificacionCorreoDebug($datosCorreo);
        
        error_log("8. RESULTADO ENVÍO CORREO:");
        error_log("   - Success: " . ($resultadoNotificacion ? 'SÍ' : 'NO'));

        // Respuesta final
        while (ob_get_level()) {
            ob_end_clean();
        }
        
        echo json_encode([
            'success' => true,
            'message' => 'Debug completado - Revisar logs detallados',
            'data' => [
                'id' => $id,
                'adjuntos_procesados' => count($adjuntosProcesados),
                'correo_enviado' => $resultadoNotificacion,
                'debug_completado' => true
            ]
        ]);
        exit;
        
    } catch (Exception $e) {
        error_log("ERROR EN DEBUG: " . $e->getMessage());
        throw $e;
    }
}

/**
 * Procesar archivos adjuntos (versión normal)
 */
function procesarAdjuntos($archivos, $pqrsId) {
    try {
        // Crear directorio si no existe
        $directorioDestino = '../uploads/respuestas_pqrs/';
        if (!is_dir($directorioDestino)) {
            if (!mkdir($directorioDestino, 0755, true)) {
                error_log("No se pudo crear directorio: $directorioDestino");
                return false;
            }
        }

        // Crear subdirectorio para esta PQRS
        $subdirectorio = $directorioDestino . $pqrsId . '/';
        if (!is_dir($subdirectorio)) {
            if (!mkdir($subdirectorio, 0755, true)) {
                error_log("No se pudo crear subdirectorio: $subdirectorio");
                return false;
            }
        }

        $adjuntosProcesados = [];
        $archivosParaProcesar = [];
        
        if (isset($archivos['name']) && is_array($archivos['name'])) {
            // Múltiples archivos
            $totalArchivos = count($archivos['name']);
            for ($i = 0; $i < $totalArchivos; $i++) {
                if (!empty($archivos['name'][$i]) && $archivos['error'][$i] === UPLOAD_ERR_OK) {
                    $archivosParaProcesar[] = [
                        'name' => $archivos['name'][$i],
                        'type' => $archivos['type'][$i],
                        'tmp_name' => $archivos['tmp_name'][$i],
                        'error' => $archivos['error'][$i],
                        'size' => $archivos['size'][$i]
                    ];
                }
            }
        } elseif (isset($archivos['name']) && !is_array($archivos['name'])) {
            // Un solo archivo
            if (!empty($archivos['name']) && $archivos['error'] === UPLOAD_ERR_OK) {
                $archivosParaProcesar[] = $archivos;
            }
        }

        foreach ($archivosParaProcesar as $archivo) {
            if (!validarArchivo($archivo)) {
                continue;
            }

            $extension = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));
            $nombreUnico = 'resp_' . $pqrsId . '_' . uniqid() . '.' . $extension;
            $rutaDestino = $subdirectorio . $nombreUnico;

            if (move_uploaded_file($archivo['tmp_name'], $rutaDestino)) {
                $rutaAbsoluta = realpath($rutaDestino);
                
                $adjuntosProcesados[] = [
                    'nombre_original' => $archivo['name'],
                    'nombre_archivo' => $nombreUnico,
                    'ruta' => $rutaAbsoluta,
                    'ruta_relativa' => $rutaDestino,
                    'tipo' => $archivo['type'],
                    'tamaño' => $archivo['size'],
                    'fecha_subida' => date('Y-m-d H:i:s')
                ];
                
                error_log("Archivo procesado: " . $archivo['name'] . " -> " . $rutaAbsoluta);
            } else {
                error_log("Error moviendo archivo: " . $archivo['name']);
            }
        }

        return $adjuntosProcesados;

    } catch (Exception $e) {
        error_log("Error procesando adjuntos: " . $e->getMessage());
        return false;
    }
}

/**
 * FUNCIÓN DEBUG: Procesar adjuntos con logging detallado
 */
function procesarAdjuntosDebug($archivos, $pqrsId) {
    error_log("=== PROCESANDO ADJUNTOS DEBUG ===");
    error_log("PQRS ID: $pqrsId");
    error_log("Estructura archivos recibida: " . print_r($archivos, true));
    
    try {
        // Verificar directorios
        $directorioDestino = '../uploads/respuestas_pqrs/';
        $subdirectorio = $directorioDestino . $pqrsId . '/';
        
        error_log("Verificando directorios:");
        error_log("- Base: $directorioDestino (existe: " . (is_dir($directorioDestino) ? 'SÍ' : 'NO') . ")");
        error_log("- Sub: $subdirectorio (existe: " . (is_dir($subdirectorio) ? 'SÍ' : 'NO') . ")");
        
        if (!is_dir($directorioDestino)) {
            $creado = mkdir($directorioDestino, 0755, true);
            error_log("- Creando directorio base: " . ($creado ? 'EXITOSO' : 'FALLÓ'));
        }
        
        if (!is_dir($subdirectorio)) {
            $creado = mkdir($subdirectorio, 0755, true);
            error_log("- Creando subdirectorio: " . ($creado ? 'EXITOSO' : 'FALLÓ'));
        }

        // Preparar archivos
        $archivosParaProcesar = [];
        
        if (isset($archivos['name']) && is_array($archivos['name'])) {
            error_log("Formato: Múltiples archivos");
            $totalArchivos = count($archivos['name']);
            for ($i = 0; $i < $totalArchivos; $i++) {
                if (!empty($archivos['name'][$i]) && $archivos['error'][$i] === UPLOAD_ERR_OK) {
                    $archivosParaProcesar[] = [
                        'name' => $archivos['name'][$i],
                        'type' => $archivos['type'][$i],
                        'tmp_name' => $archivos['tmp_name'][$i],
                        'error' => $archivos['error'][$i],
                        'size' => $archivos['size'][$i]
                    ];
                    error_log("  - ✅ Archivo $i agregado: {$archivos['name'][$i]}");
                }
            }
        } elseif (isset($archivos['name']) && !is_array($archivos['name'])) {
            error_log("Formato: Un solo archivo");
            if (!empty($archivos['name']) && $archivos['error'] === UPLOAD_ERR_OK) {
                $archivosParaProcesar[] = $archivos;
                error_log("  - ✅ Archivo único agregado: {$archivos['name']}");
            }
        }

        error_log("Archivos para procesar: " . count($archivosParaProcesar));

        $adjuntosProcesados = [];
        foreach ($archivosParaProcesar as $i => $archivo) {
            error_log("--- Procesando archivo $i: {$archivo['name']} ---");
            
            if (!validarArchivoDebug($archivo)) {
                error_log("  - ❌ Archivo no válido, saltando");
                continue;
            }
            
            $extension = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));
            $nombreUnico = 'resp_' . $pqrsId . '_' . uniqid() . '.' . $extension;
            $rutaDestino = $subdirectorio . $nombreUnico;
            
            error_log("  - Nombre único: $nombreUnico");
            error_log("  - Ruta destino: $rutaDestino");
            error_log("  - Archivo temporal existe: " . (file_exists($archivo['tmp_name']) ? 'SÍ' : 'NO'));
            
            if (move_uploaded_file($archivo['tmp_name'], $rutaDestino)) {
                $rutaAbsoluta = realpath($rutaDestino);
                
                error_log("  - ✅ Archivo movido exitosamente");
                error_log("  - Ruta absoluta: $rutaAbsoluta");
                error_log("  - Verificación final: " . (file_exists($rutaAbsoluta) ? 'EXISTE' : 'NO EXISTE'));
                
                $adjuntosProcesados[] = [
                    'nombre_original' => $archivo['name'],
                    'nombre_archivo' => $nombreUnico,
                    'ruta' => $rutaAbsoluta,
                    'ruta_relativa' => $rutaDestino,
                    'tipo' => $archivo['type'],
                    'tamaño' => $archivo['size'],
                    'fecha_subida' => date('Y-m-d H:i:s')
                ];
                
            } else {
                error_log("  - ❌ Error moviendo archivo");
            }
        }

        error_log("=== RESUMEN PROCESAMIENTO ===");
        error_log("Total procesados: " . count($adjuntosProcesados));
        
        return $adjuntosProcesados;

    } catch (Exception $e) {
        error_log("ERROR PROCESANDO ADJUNTOS DEBUG: " . $e->getMessage());
        return false;
    }
}

/**
 * Validar archivo adjunto (versión normal)
 */
function validarArchivo($archivo) {
    $tiposPermitidos = [
        'application/pdf',
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'application/vnd.ms-excel',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'image/jpeg',
        'image/png',
        'text/plain'
    ];

    if ($archivo['error'] !== UPLOAD_ERR_OK) {
        return false;
    }

    $maxSize = 15 * 1024 * 1024; // 15MB
    if ($archivo['size'] > $maxSize || $archivo['size'] < 1) {
        return false;
    }

    if (!in_array($archivo['type'], $tiposPermitidos)) {
        return false;
    }

    $extension = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));
    $extensionesPermitidas = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'jpg', 'jpeg', 'png', 'txt'];
    
    if (!in_array($extension, $extensionesPermitidas)) {
        return false;
    }

    if (!file_exists($archivo['tmp_name'])) {
        return false;
    }

    return true;
}

/**
 * FUNCIÓN DEBUG: Validar archivo con logging detallado
 */
function validarArchivoDebug($archivo) {
    error_log("    === VALIDANDO ARCHIVO ===");
    error_log("    Nombre: " . $archivo['name']);
    error_log("    Tipo: " . $archivo['type']);
    error_log("    Tamaño: " . $archivo['size'] . " bytes");
    error_log("    Error code: " . $archivo['error']);
    error_log("    Tmp name: " . $archivo['tmp_name']);
    
    if ($archivo['error'] !== UPLOAD_ERR_OK) {
        error_log("    ❌ Error de subida: " . $archivo['error']);
        return false;
    }
    
    if (!file_exists($archivo['tmp_name'])) {
        error_log("    ❌ Archivo temporal no existe");
        return false;
    }
    
    $maxSize = 15 * 1024 * 1024;
    if ($archivo['size'] > $maxSize) {
        error_log("    ❌ Archivo muy grande: {$archivo['size']} bytes");
        return false;
    }
    
    if ($archivo['size'] < 1) {
        error_log("    ❌ Archivo vacío");
        return false;
    }
    
    error_log("    ✅ Archivo válido");
    return true;
}

/**
 * Enviar notificación por correo (versión normal)
 */
function enviarNotificacionCorreo($datos) {
    try {
        if (empty($datos['email'])) {
            throw new Exception('Email no disponible para notificación');
        }
        
        $mailService = new MailService();
        
        error_log("Enviando notificación por correo a: " . $datos['email']);
        error_log("Con adjuntos: " . count($datos['adjuntos'] ?? []));
        
        $envioExitoso = $mailService->enviarNotificacionPqrsConAdjuntos($datos, 'respuesta');
        
        return [
            'success' => $envioExitoso,
            'detalle' => $envioExitoso ? 
                'Correo enviado exitosamente a ' . $datos['email'] :
                'Error al enviar correo a ' . $datos['email']
        ];
        
    } catch (Exception $e) {
        error_log("Error enviando notificación por correo: " . $e->getMessage());
        return [
            'success' => false,
            'detalle' => 'Error: ' . $e->getMessage()
        ];
    }
}

/**
 * FUNCIÓN DEBUG: Enviar notificación con logging máximo
 */
function enviarNotificacionCorreoDebug($datos) {
    error_log("=== ENVIANDO NOTIFICACIÓN DEBUG ===");
    
    try {
        if (empty($datos['email'])) {
            throw new Exception('Email no disponible');
        }
        
        // Verificar adjuntos recibidos
        error_log("Adjuntos recibidos en enviarNotificacionCorreoDebug:");
        if (!empty($datos['adjuntos'])) {
            foreach ($datos['adjuntos'] as $i => $adjunto) {
                error_log("  Adjunto $i: {$adjunto['nombre_original']}");
                error_log("    - Ruta: {$adjunto['ruta']}");
                error_log("    - Existe: " . (file_exists($adjunto['ruta']) ? 'SÍ' : 'NO'));
            }
        } else {
            error_log("  - No hay adjuntos en los datos");
        }
        
        $mailService = new MailService();
        
        // USAR MÉTODO DEBUG DEL MAILSERVICE
        $envioExitoso = $mailService->enviarNotificacionPqrsConAdjuntosDebug($datos, 'respuesta');
        
        error_log("Resultado envío debug: " . ($envioExitoso ? 'EXITOSO' : 'FALLIDO'));
        
        return $envioExitoso;
        
    } catch (Exception $e) {
        error_log("Error en notificación debug: " . $e->getMessage());
        return false;
    }
}

?>
