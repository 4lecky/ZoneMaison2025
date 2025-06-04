<?php
session_start();
$pdo = require_once "../config/db.php";
require_once '../models/muroModel.php';

$muroModel = new muroModel($pdo);

$destinatario = $_POST['destinatario'] ?? '';
$asunto = $_POST['asunto'] ?? '';
$fecha = $_POST['fecha'] ?? '';
$hora = $_POST['hora'] ?? '';
$descripcion = $_POST['descripcion'] ?? '';
$usuario_cc = $_POST['usuario_cc'] ?? 0;

// Validación básica
if (empty($destinatario)) {
    die("Error: No se seleccionó ningún destinatario.");
}

// Subida de imagen
if (isset($_FILES['zone-images']) && $_FILES['zone-images']['error'] === UPLOAD_ERR_OK) {
    $directorio = '../uploads/';
    if (!is_dir($directorio)) {
        mkdir($directorio, 0777, true);
    }

    $archivoNombre = uniqid() . '_' . basename($_FILES['zone-images']['name']);
    $rutaDestino = $directorio . $archivoNombre;

    if (!move_uploaded_file($_FILES['zone-images']['tmp_name'], $rutaDestino)) {
        die("Error: No se pudo guardar el archivo.");
    }

    $rutaRelativaBD = 'uploads/' . $archivoNombre;
} else {
    die("Error: No se recibió o subió correctamente la imagen.");
}

// // Validar existencia de usuario
$usuario_cc = $_SESSION['usuario_cc'] ?? null;

if (!$usuario_cc) {
    die("Error: usuario no autenticado o no definido.");
}

// Validar que el destinatario seleccionado sea un usuario válido con rol permitido

if (!$rol || !in_array($rol, $rolesPermitidos)) {
    die("Error: El destinatario seleccionado no tiene un rol válido para recibir mensajes.");
}


// Guardar en BD
$muroModel->insertarMuro(
    $destinatario,
    $asunto,
    $fecha,
    $hora,
    $rutaRelativaBD,
    $descripcion,
    $usuario_cc
);

header("Location: ../views/muro.php");
exit;
?>