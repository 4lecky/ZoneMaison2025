<?php
session_start();
$conn = require_once '../config/db.php';
require_once '../models/muroModel.php';

$muroModel = new muroModel($conn);

$destinatario = $_POST['destinatario'] ?? '';
$asunto = $_POST['asunto'] ?? '';
$fecha = $_POST['fecha'] ?? '';
$hora = $_POST['hora'] ?? '';
$descripcion = $_POST['descripcion'] ?? '';
$usu_cedula = $_POST['usu_cedula'] ?? 0;

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
// $stmt = $conn->prepare("SELECT COUNT(*) FROM tbl_usuario WHERE usu_cedula = ?");
// $stmt->execute([$usu_cedula]);
// if ($stmt->fetchColumn() == 0) {
//     die("Error: El usuario con CC {$usu_cedula} no existe.");
// }

// Guardar en BD
$muroModel->insertarMuro(
    $destinatario,
    $asunto,
    $fecha,
    $hora,
    $rutaRelativaBD,
    $descripcion,
    $usu_cedula
);

header("Location: ../views/muro.php");
exit;
?>