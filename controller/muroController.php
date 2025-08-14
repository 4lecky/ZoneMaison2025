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

// Validación básica
if (empty($destinatario)) {
    die("Error: No se seleccionó ningún destinatario.");
}

// Subida de imagen obligatoria
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

// Consultar usuarios con el rol seleccionado
$query = "SELECT usu_cedula FROM tbl_usuario WHERE usu_rol = :rol AND usu_estado = 'Activo'";
$stmt  = $pdo->prepare($query);
$stmt->execute(['rol' => $destinatario]);
$usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Guardar el muro para cada destinatario
foreach ($usuarios as $usuarios) {
    $muroModel->insertarMuro(
        $usuarios['usu_cedula'],  // Destinatario
        $asunto,
        $fecha,
        $hora,
        $rutaRelativaBD,
        $descripcion,
        $EnviaHora,
        $usu_cedula  // Remitente
    );
}

// Redirigir al listado
header("Location: ../views/novedades.php");
exit;
?>

