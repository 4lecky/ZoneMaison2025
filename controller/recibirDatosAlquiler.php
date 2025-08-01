<?php
session_start();

$pdo = require_once '../config/db.php';
require_once '../models/insertaRegistroAlquiler.php';

$guardarAlquiler = new insertaRegistroAlquiler($pdo);

// Captura de datos del formulario
$numRecibo     = $_POST['numRecibo'] ?? '';
$observaciones = $_POST['observaciones'] ?? '';
$precio        = $_POST['costo'] ?? '';
$visitaId      = $_POST['visita_id'] ?? '';
$placa         = $_POST['placa'] ?? '';
$usuarioCedula = $_POST['usuario_cedula'] ?? '';

// Validación básica
if (empty($numRecibo) || empty($placa) || empty($usuarioCedula) || empty($visitaId)) {
    echo "❌ Faltan datos obligatorios.";
    exit;
}

// Guardar en la base de datos
$exito = $guardarAlquiler->insertarAlquiler(
    $numRecibo,
    $observaciones,
    $precio,
    $visitaId,
    $placa,
    $usuarioCedula
);

if ($exito) {
    echo "✅ Alquiler registrado correctamente.";
} else {
    echo "❌ Error al registrar el alquiler.";
}

