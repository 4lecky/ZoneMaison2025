<?php
session_start();
$pdo = require_once "../config/db.php";
require_once '../models/muroModel.php';

$muroModel = new muroModel($pdo);

$destinatario = $_POST['destinatario'] ?? '';  // Rol seleccionado
$asunto       = $_POST['asunto'] ?? '';
$descripcion  = $_POST['descripcion'] ?? '';
$usu_cedula   = $_SESSION['usu_cedula'] ?? null;

// Asignar automáticamente la fecha y hora actual
$fecha     = date('Y-m-d');
$hora      = date('H:i:s');
$EnviaHora = date('H:i:s');

// Validar destinatario
$rolesValidos = ['Administrador', 'Residente', 'Propietario', 'Vigilante', 'Usuario', 'Todos'];
if (empty($destinatario) || !in_array($destinatario, $rolesValidos)) {
    die("Error: Rol destinatario no válido.");
}

// Subida de imagen (obligatoria)
if (isset($_FILES['zone-images']) && $_FILES['zone-images']['error'] === UPLOAD_ERR_OK) {
    $directorio = '../uploads/';
    if (!is_dir($directorio)) {
        mkdir($directorio, 0777, true);
    }

    $archivoNombre = uniqid() . '_' . basename($_FILES['zone-images']['name']);
    $rutaDestino   = $directorio . $archivoNombre;

    if (!move_uploaded_file($_FILES['zone-images']['tmp_name'], $rutaDestino)) {
        die("Error: No se pudo guardar el archivo.");
    }

    $rutaRelativaBD = 'uploads/' . $archivoNombre;
} else {
    die("Error: No se recibió o subió correctamente la imagen.");
}

// Insertar solo una vez en el muro con el rol como destinatario
$exito = $muroModel->insertarMuro(
    $destinatario,  // Rol del destinatario
    $asunto,
    $fecha,
    $hora,
    $rutaRelativaBD,
    $descripcion,
    $EnviaHora,
    $usu_cedula  // Remitente
);

if ($exito) {
    header("Location: ../views/novedades.php?success=Publicación enviada exitosamente.");
} else {
    die("Error: No se pudo guardar la publicación.");
}
exit;