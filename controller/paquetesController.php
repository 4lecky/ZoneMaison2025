<?php
session_start();
$pdo = require_once "../config/db.php";
require_once '../models/paquetesModel.php';

$paquetesModel = new paquetesModel($pdo);

$tipoDoc = $_POST['tipo_doc'] ?? '';
$numeroDoc = $_POST['numero_doc'] ?? '';
$cedulaDestinatario = $_POST['paqu_usuario_cedula'] ?? '';
$nombreDestinatario = $_POST['paqu_Destinatario'] ?? '';
$asunto = $_POST['asunto'] ?? '';
$fecha = $_POST['fecha'] ?? '';
$hora = $_POST['hora'] ?? '';
$descripcion = $_POST['descripcion'] ?? '';
$estado = $_POST['estado'] ?? '';

// Validación básica
if (!$tipoDoc || !$numeroDoc || !$cedulaDestinatario) {
    die("Faltan datos obligatorios.");
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

    if (!move_uploaded_file($_FILES['zone-images']['tmp_name'], $rutaDestino)) {
        die("Error al guardar la imagen.");
    }

    $rutaRelativaBD = 'uploads/' . $archivoNombre;
}

$paquetesModel->insertarPaquete(
    $tipoDoc,
    $cedulaDestinatario,
    $nombreDestinatario,
    $asunto,
    $fecha,
    $hora,
    $rutaRelativaBD,
    $descripcion,
    $estado
);


header("Location: ../views/novedades.php");
exit;
