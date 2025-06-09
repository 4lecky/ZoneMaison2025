<?php
session_start();
$pdo = require_once "../config/db.php";
require_once '../models/muroModel.php';

$muroModel = new muroModel($pdo);

$destinatario = $_POST['destinatario'] ?? '';  // El rol seleccionado
$asunto = $_POST['asunto'] ?? '';
$fecha = $_POST['fecha'] ?? '';
$hora = $_POST['hora'] ?? '';
$descripcion = $_POST['descripcion'] ?? '';
$usu_cedula = $_SESSION['usu_cedula'] ?? null;

// Validación básica
if (empty($destinatario)) {
    die("Error: No se seleccionó ningún destinatario.");
}

if (!$usu_cedula) {
    die("Error: Usuario no autenticado o no definido.");
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

// Consultar usuarios con el rol seleccionado
$query = "SELECT usu_cedula FROM tbl_usuario WHERE usu_rol = :rol AND usu_estado = 'Activo'";
$stmt = $pdo->prepare($query);
$stmt->execute(['rol' => $destinatario]);
$usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Guardar en la base de datos para cada destinatario (enviando a múltiples usuarios)
foreach ($usuarios as $usuario) {
    // Guardar mensaje en la base de datos
    $muroModel->insertarMuro(
        $usuario['usu_cedula'],  // Este es el usuario destinatario
        $asunto,
        $fecha,
        $hora,
        $rutaRelativaBD,
        $descripcion,
        $usu_cedula  // El usuario que está enviando el mensaje
    );
}

// Redirigir después de guardar los datos
header("Location: ../views/muro.php");
exit;
?>