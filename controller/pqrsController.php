<?php
// pqrsController.php - Versión DEBUG

// Control estricto de errores
error_reporting(E_ALL);
ini_set('display_errors', 0);

// Control de output
ob_start();

// Iniciar sesión
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Función simple de log
function logError($mensaje, $datos = []) {
    $log = date('Y-m-d H:i:s') . " - PQRS Controller: $mensaje";
    if (!empty($datos)) {
        $log .= " | Datos: " . json_encode($datos, JSON_UNESCAPED_UNICODE);
    }
    error_log($log);
}

// Función de redirección limpia
function redirectToPage($page, $message = null, $type = 'error') {
    // Limpiar output buffer
    while (ob_get_level()) {
        ob_end_clean();
    }
    
    // Establecer mensaje en sesión
    if ($message) {
        if ($type === 'error') {
            $_SESSION['errores_pqrs'] = is_array($message) ? $message : [$message];
        } else {
            $_SESSION['mensaje_pqrs'] = [
                'texto' => $message,
                'tipo' => $type
            ];
        }
    }
    
    // Redireccionar
    header("Location: $page");
    exit();
}

try {
    logError("=== INICIO PROCESAMIENTO PQRS (DEBUG) ===");
    
    // 1. VERIFICACIONES BÁSICAS
    if (!isset($_SESSION['usuario']) || empty($_SESSION['usuario'])) {
        logError("Usuario no logueado");
        redirectToPage("../views/login.php", "Debes iniciar sesión para crear una PQRS.");
    }

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        logError("Método incorrecto: " . $_SERVER['REQUEST_METHOD']);
        redirectToPage("../views/crear_pqr.php", "Acceso no válido.");
    }

    // Limpiar mensajes previos
    unset($_SESSION['errores_pqrs'], $_SESSION['mensaje_pqrs']);

    $usuario = $_SESSION['usuario'];
    
    // **DEBUG CRÍTICO: Verificar estructura del usuario en sesión**
    logError("DEBUG: Contenido completo de \$_SESSION['usuario']", $usuario);
    logError("DEBUG: Claves disponibles en usuario", array_keys($usuario));
    
    // **NUEVO: Obtener datos del usuario desde la BD como en crear_pqr.php**
    try {
        require_once '../config/db.php';
        
        if (!$pdo) {
            logError("ERROR: db.php no retornó conexión PDO válida");
            throw new Exception("db.php no retorna conexión válida");
        }
        
        // Hacer $pdo disponible globalmente para el modelo
        global $pdo;
        
        logError("Conexión PDO obtenida correctamente");
        
        // Usar el mismo método que crear_pqr.php para obtener datos del usuario
        $stmt = $pdo->prepare("SELECT 
            usuario_cc,
            usu_cedula,
            usu_nombre_completo,
            usu_correo,
            usu_telefono,
            usu_apartamento_residencia,
            usu_torre_residencia,
            usu_rol
            FROM tbl_usuario 
            WHERE usuario_cc = ? AND usu_estado = 'Activo'");
        
        $stmt->execute([$usuario['id']]);
        $usuario_completo = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$usuario_completo) {
            logError("ERROR: No se encontraron datos del usuario en BD", ['usuario_id' => $usuario['id']]);
            redirectToPage("../views/crear_pqr.php", "No se pudieron cargar los datos del usuario.");
        }
        
        logError("Datos del usuario obtenidos de BD", $usuario_completo);
        
        // Reemplazar datos del usuario con los de la BD
        $usuario = [
            'usuario_cc' => $usuario_completo['usuario_cc'],
            'usu_cedula' => $usuario_completo['usu_cedula'],
            'usu_nombre_completo' => $usuario_completo['usu_nombre_completo'],
            'usu_correo' => $usuario_completo['usu_correo'],
            'usu_telefono' => $usuario_completo['usu_telefono'],
            'usu_apartamento_residencia' => $usuario_completo['usu_apartamento_residencia'],
            'usu_torre_residencia' => $usuario_completo['usu_torre_residencia'],
            'usu_rol' => $usuario_completo['usu_rol']
        ];
        
        logError("Usuario actualizado con datos de BD", ['cc' => $usuario['usuario_cc'], 'nombre' => $usuario['usu_nombre_completo']]);
        
    } catch (Exception $e) {
        logError("ERROR obteniendo datos de usuario de BD: " . $e->getMessage());
        redirectToPage("../views/crear_pqr.php", "Error al cargar los datos del usuario.");
    }

    // 2. CARGAR MODELO 
    try {
        require_once '../models/pqrsModel.php';
        $pqrsModel = new PqrsModel();
        
        if (!$pqrsModel->verificarConexion()) {
            throw new Exception("Conexión a base de datos no válida en modelo");
        }
        
        logError("Modelo cargado correctamente");
        
    } catch (Exception $e) {
        logError("ERROR cargando modelo: " . $e->getMessage());
        redirectToPage("../views/crear_pqr.php", "Error del sistema. Intente más tarde. (Código: DB_001)");
    }

    // 3. VALIDACIONES DE DATOS (Ahora usando datos correctos de BD)
    $errores = [];

    // Verificar datos del usuario (CORREGIDO)
    $camposUsuario = ['usuario_cc', 'usu_cedula', 'usu_correo', 'usu_telefono', 'usu_nombre_completo'];
    foreach ($camposUsuario as $campo) {
        if (!isset($usuario[$campo]) || empty($usuario[$campo])) {
            $errores[] = "Datos de usuario incompletos: falta '$campo'. Vuelva a iniciar sesión.";
            logError("Campo faltante en usuario", ['campo' => $campo, 'valor' => $usuario[$campo] ?? 'NO_EXISTE']);
        }
    }

    // Si hay errores de usuario, salir temprano
    if (!empty($errores)) {
        logError("Errores de datos de usuario", $errores);
        redirectToPage("../views/crear_pqr.php", $errores);
    }

    // Verificar campos del formulario
    if (empty($_POST['tipo_pqr']) || !in_array($_POST['tipo_pqr'], ['peticion', 'queja', 'reclamo', 'sugerencia'])) {
        $errores[] = "Debe seleccionar un tipo de solicitud válido.";
    }

    if (empty(trim($_POST['asunto'] ?? ''))) {
        $errores[] = "El asunto es obligatorio.";
    } elseif (strlen(trim($_POST['asunto'])) < 5) {
        $errores[] = "El asunto debe tener al menos 5 caracteres.";
    }

    if (empty(trim($_POST['mensaje'] ?? ''))) {
        $errores[] = "La descripción es obligatoria.";
    } elseif (strlen(trim($_POST['mensaje'])) < 10) {
        $errores[] = "La descripción debe tener al menos 10 caracteres.";
    }

    // El medio de respuesta siempre será correo
    $medioRespuesta = ['correo'];

    // Si hay errores, redirigir
    if (!empty($errores)) {
        logError("Errores de validación", $errores);
        redirectToPage("../views/crear_pqr.php", $errores);
    }

    // 4. PROCESAR ARCHIVOS (SIMPLIFICADO)
    $archivosJson = '';
    if (isset($_FILES['archivos']) && !empty($_FILES['archivos']['name'][0])) {
        logError("Procesando archivos adjuntos");
        
        $archivosProcesados = procesarArchivosSimple($_FILES['archivos']);
        if ($archivosProcesados['success']) {
            $archivosJson = $archivosProcesados['json'];
            logError("Archivos procesados OK");
        } else {
            logError("Error procesando archivos", $archivosProcesados['errores']);
            redirectToPage("../views/crear_pqr.php", $archivosProcesados['errores']);
        }
    }

    // 5. PREPARAR DATOS PARA INSERCIÓN
    $nombreCompleto = trim($usuario['usu_nombre_completo']);
    $partesNombre = explode(' ', $nombreCompleto);
    
    // Separar nombres y apellidos de forma inteligente
    if (count($partesNombre) >= 2) {
        if (count($partesNombre) == 2) {
            $nombres = $partesNombre[0];
            $apellidos = $partesNombre[1];
        } else {
            // Asumir que los primeros 2 son nombres y el resto apellidos
            $nombres = $partesNombre[0] . ' ' . $partesNombre[1];
            $apellidos = implode(' ', array_slice($partesNombre, 2));
        }
    } else {
        $nombres = $nombreCompleto;
        $apellidos = '';
    }

    $datosPqrs = [
        'usuario_cc' => (int)$usuario['usuario_cc'],
        'nombres' => $nombres,
        'apellidos' => $apellidos,
        'identificacion' => $usuario['usu_cedula'],
        'email' => $usuario['usu_correo'],
        'telefono' => $usuario['usu_telefono'],
        'tipo_pqr' => $_POST['tipo_pqr'],
        'asunto' => trim($_POST['asunto']),
        'mensaje' => trim($_POST['mensaje']),
        'archivos' => $archivosJson,
        'medio_respuesta' => $medioRespuesta
    ];

    logError("Datos preparados para inserción", [
        'usuario_cc' => $datosPqrs['usuario_cc'],
        'tipo_pqr' => $datosPqrs['tipo_pqr'],
        'asunto' => substr($datosPqrs['asunto'], 0, 50) . '...',
        'medio_respuesta' => $medioRespuesta,
        'tiene_archivos' => !empty($archivosJson)
    ]);

    // 6. CREAR LA PQRS
    $idPqrs = $pqrsModel->crear($datosPqrs);

    if ($idPqrs && is_numeric($idPqrs) && $idPqrs > 0) {
        logError("PQRS creada exitosamente", ['id' => $idPqrs]);
        
        $numeroRadicado = 'PQRS-' . date('Y') . '-' . str_pad($idPqrs, 4, '0', STR_PAD_LEFT);
        $mensajeExito = "¡PQRS enviada exitosamente! Su número de radicado es: $numeroRadicado. Recibirá la respuesta por correo electrónico. Puede hacer seguimiento desde 'Mis PQRS'.";
        
        // Redirigir a pqrs.php con mensaje de éxito
        redirectToPage("../views/pqrs.php?success=1&radicado=" . urlencode($numeroRadicado), $mensajeExito, 'success');
        
    } else {
        logError("Error al crear PQRS", ['resultado_modelo' => $idPqrs]);
        redirectToPage("../views/crear_pqr.php", "Error al procesar su solicitud. Intente nuevamente. (Código: CR_001)");
    }

} catch (Exception $e) {
    logError("EXCEPCIÓN CRÍTICA", [
        'mensaje' => $e->getMessage(),
        'archivo' => $e->getFile(),
        'línea' => $e->getLine()
    ]);
    
    redirectToPage("../views/crear_pqr.php", "Error interno del sistema. Contacte al administrador. (Código: EX_001)");
}

/**
 * Función simplificada para procesar archivos
 */
function procesarArchivosSimple($archivos) {
    $directorioDestino = '../uploads/pqrs/';
    
    // Verificar que el directorio existe
    if (!file_exists($directorioDestino)) {
        if (!mkdir($directorioDestino, 0755, true)) {
            return [
                'success' => false,
                'errores' => ['No se pudo crear el directorio para archivos adjuntos.'],
                'json' => ''
            ];
        }
    }

    $archivosGuardados = [];
    $errores = [];
    $extensionesPermitidas = ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png'];
    $tamañoMaximo = 5 * 1024 * 1024; // 5MB

    // Procesar cada archivo
    if (is_array($archivos['name'])) {
        $totalArchivos = count($archivos['name']);
        
        for ($i = 0; $i < $totalArchivos; $i++) {
            // Saltar archivos con errores o vacíos
            if ($archivos['error'][$i] !== UPLOAD_ERR_OK || empty($archivos['name'][$i])) {
                continue;
            }

            $nombreOriginal = $archivos['name'][$i];
            $tamañoArchivo = $archivos['size'][$i];
            $archivoTemporal = $archivos['tmp_name'][$i];
            $extension = strtolower(pathinfo($nombreOriginal, PATHINFO_EXTENSION));

            // Validar extensión
            if (!in_array($extension, $extensionesPermitidas)) {
                $errores[] = "Archivo '$nombreOriginal': tipo no permitido. Use: " . implode(', ', $extensionesPermitidas);
                continue;
            }

            // Validar tamaño
            if ($tamañoArchivo > $tamañoMaximo) {
                $errores[] = "Archivo '$nombreOriginal': muy grande (máximo 5MB).";
                continue;
            }

            // Generar nombre único
            $nombreUnico = uniqid() . '_' . time() . '.' . $extension;
            $rutaDestino = $directorioDestino . $nombreUnico;

            // Mover archivo
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
                $errores[] = "No se pudo guardar el archivo '$nombreOriginal'.";
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


