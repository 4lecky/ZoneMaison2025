<?php
session_start();
$pdo = require_once "../config/db.php";
require_once '../models/paquetesModel.php';

$paquetesModel = new paquetesModel($pdo);

// Obtener datos del formulario
$tipoDoc = $_POST['tipo_doc'] ?? '';
$paqu_usuario_cedula = $_POST['paqu_usuario_cedula'] ?? '';
$nombreDestinatario = $_POST['paqu_Destinatario'] ?? '';
$asunto = $_POST['asunto'] ?? '';
$fecha = $_POST['fecha'] ?? '';
$hora = $_POST['hora'] ?? '';
$descripcion = $_POST['descripcion'] ?? '';
$estado = $_POST['estado'] ?? '';

// Validación básica
if (!$tipoDoc || !$paqu_usuario_cedula || !$nombreDestinatario || !$asunto || !$fecha || !$hora || !$estado) {
    die("Faltan datos obligatorios.");
}

// Validar que el número de documento sea numérico y de longitud apropiada
if (!is_numeric($paqu_usuario_cedula) || strlen($paqu_usuario_cedula) < 9) {
    die("Número de documento inválido.");
}

// Imagen
$rutaRelativaBD = null;
if (isset($_FILES['zone-images']) && $_FILES['zone-images']['error'] === UPLOAD_ERR_OK) {
    $directorio = '../uploads/';
    if (!is_dir($directorio)) {
        mkdir($directorio, 0777, true);
    }

    $archivoNombre = uniqid() . '_' . basename($_FILES['zone-images']['name']);
    $rutaDestino = $directorio . $archivoNombre;

    // Validar tipo de imagen (solo imágenes JPG, PNG, o GIF)
    $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
    $ext = pathinfo($_FILES['zone-images']['name'], PATHINFO_EXTENSION);
    if (!in_array(strtolower($ext), $allowedExtensions)) {
        die("Solo se permiten imágenes JPG, PNG, o GIF.");
    }

    if (!move_uploaded_file($_FILES['zone-images']['tmp_name'], $rutaDestino)) {
        die("Error al guardar la imagen.");
    }

    $rutaRelativaBD = 'uploads/' . $archivoNombre;
}

// Insertar el paquete en la base de datos
$insertado = $paquetesModel->insertarPaquete(
    $tipoDoc,
    $paqu_usuario_cedula,
    $nombreDestinatario,
    $asunto,
    $fecha,
    $hora,
    $rutaRelativaBD,
    $descripcion,
    $estado
);

if ($insertado) {
    // Redirigir a novedades.php después de la inserción exitosa
    header("Location: ../views/novedades.php");
    exit;
} else {
    // Si hubo un error al insertar
    die("Hubo un error al insertar el paquete.");
}
?>