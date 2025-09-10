<?php
/**
 * download.php - VERSIÓN CORREGIDA FINAL
 * Maneja archivos JSON y subcarpetas correctamente
 */

// Iniciar sesión
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verificar que el usuario esté logueado
if (!isset($_SESSION['rol']) || (!in_array($_SESSION['rol'], ['admin', 'residente', 'propietario', 'vigilante']))) {
    http_response_code(403);
    die('Acceso denegado');
}

// Obtener el archivo solicitado
$archivoParam = $_GET['file'] ?? '';

if (empty($archivoParam)) {
    http_response_code(400);
    die('Archivo no especificado');
}

// PROCESAR EL PARÁMETRO: puede ser JSON o nombre simple
$nombreArchivo = '';
$rutaRelativa = '';

// Verificar si es un JSON (como en tu caso)
if (str_starts_with(trim($archivoParam), '[') || str_starts_with(trim($archivoParam), '{')) {
    try {
        // Es un JSON, decodificarlo
        $archivoJson = json_decode($archivoParam, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception('JSON inválido');
        }
        
        // Si es un array, tomar el primer elemento
        if (is_array($archivoJson) && isset($archivoJson[0])) {
            $archivoJson = $archivoJson[0];
        }
        
        // Extraer la información del archivo
        if (isset($archivoJson['nombre_archivo'])) {
            $nombreArchivo = $archivoJson['nombre_archivo'];
        } elseif (isset($archivoJson['ruta'])) {
            // Si viene la ruta completa, extraer solo el nombre
            $nombreArchivo = basename($archivoJson['ruta']);
        }
        
        // Determinar la ruta (puede venir en el JSON)
        if (isset($archivoJson['ruta']) && str_contains($archivoJson['ruta'], '/')) {
            $rutaRelativa = dirname($archivoJson['ruta']) . '/';
        }
        
    } catch (Exception $e) {
        http_response_code(400);
        die('Error procesando información del archivo: ' . $e->getMessage());
    }
} else {
    // Es un nombre de archivo simple
    $nombreArchivo = basename($archivoParam);
}

if (empty($nombreArchivo)) {
    http_response_code(400);
    die('No se pudo determinar el nombre del archivo');
}

// CONSTRUIR LA RUTA COMPLETA DEL ARCHIVO
$posiblesRutas = [
    __DIR__ . '/' . $rutaRelativa . $nombreArchivo,  // Ruta desde JSON
    __DIR__ . '/uploads/pqrs/' . $nombreArchivo,     // PQRS (tu caso)
    __DIR__ . '/uploads/muro/' . $nombreArchivo,     // Muro
    __DIR__ . '/uploads/' . $nombreArchivo,          // Uploads raíz
];

$rutaArchivo = null;
foreach ($posiblesRutas as $ruta) {
    if (file_exists($ruta)) {
        $rutaArchivo = $ruta;
        break;
    }
}

// Verificar que el archivo existe
if (!$rutaArchivo || !file_exists($rutaArchivo)) {
    http_response_code(404);
    die('Archivo no encontrado: ' . htmlspecialchars($nombreArchivo));
}

// Verificar que esté dentro de las carpetas permitidas
$rutaReal = realpath($rutaArchivo);
$rutaUploads = realpath(__DIR__ . '/uploads/');

if (!$rutaReal || !str_starts_with($rutaReal, $rutaUploads)) {
    http_response_code(403);
    die('Acceso denegado al archivo');
}

// Verificar extensión permitida
$extensionesPermitidas = ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png', 'gif', 'txt'];
$extension = strtolower(pathinfo($rutaArchivo, PATHINFO_EXTENSION));

if (!in_array($extension, $extensionesPermitidas)) {
    http_response_code(403);
    die('Tipo de archivo no permitido: ' . $extension);
}

// Verificar permisos de administrador para ciertos archivos
if ($_SESSION['rol'] !== 'admin') {
    // Los usuarios normales solo pueden ver sus propios archivos
    // Aquí podrías agregar lógica adicional de verificación
}

// Determinar el tipo MIME
$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mimeType = finfo_file($finfo, $rutaArchivo);
finfo_close($finfo);

// Si no se pudo determinar el MIME, usar uno por defecto según la extensión
if (!$mimeType) {
    $mimeTypes = [
        'pdf' => 'application/pdf',
        'doc' => 'application/msword',
        'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'jpg' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'png' => 'image/png',
        'gif' => 'image/gif',
        'txt' => 'text/plain'
    ];
    $mimeType = $mimeTypes[$extension] ?? 'application/octet-stream';
}

// Headers para mostrar el archivo en el navegador
header('Content-Type: ' . $mimeType);
header('Content-Disposition: inline; filename="' . basename($nombreArchivo) . '"');
header('Content-Length: ' . filesize($rutaArchivo));
header('Cache-Control: private, max-age=0, must-revalidate');
header('Pragma: public');

// Leer y enviar el archivo
readfile($rutaArchivo);
exit;
?>