<?php
session_start();
$conn = require_once '../config/db.php';       // ← así se recibe el objeto PDO
require_once '../models/muroModel.php';

$muroModel = new muroModel($conn);

// Obtener datos del formulario
$destinatario = $_POST['destinatario'];
$asunto = $_POST['asunto'];
$fecha = $_POST['fecha'];
$hora = $_POST['hora'];
$descripcion = $_POST['descripcion'];
$usuario_cc = $_POST['usuario_cc'];

// Obtener imagen como binario
$imagenBinaria = file_get_contents($_FILES['zone-images']['tmp_name']);

// Llamar al modelo con los 8 parámetros
$muroModel->insertarMuro(
    $destinatario,
    $asunto,
    $fecha,
    $hora,
    $imagenBinaria,
    $descripcion,
    $usuario_cc,
    $_FILES['zone-images']['name']
);

// Redirigir o mostrar mensaje
header("Location: ../views/muro.php");
exit;
