<?php
session_start();

$pdo = require_once '../config/db.php';
require_once '../models/guardarDatosRegistroParqueadero.php'; 

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // ===== Campos del formulario =====
    $placa                  = $_POST['placa'] ?? '';
    $nombrePropietarioVehi  = $_POST['nombrePropVehiculo'] ?? '';
    $tipoDocVehi            = $_POST['tipoDocVehiculo'] ?? '';
    $numDocVehi             = $_POST['numDocVehiculo'] ?? '';
    $estadoIngreso          = $_POST['estadoIngreso'] ?? '';
    $numeroParqueadero      = $_POST['numeroParqueadero'] ?? null;
    $fechaEntrada           = $_POST['fechaEntrada'] ?? null;
    $fechaSalida            = $_POST['fechaSalida'] ?? null;
    $horaEntrada            = $_POST['horaEntrada'] ?? null;

    // ===== Validación mínima =====
    if (empty($placa) || empty($nombrePropietarioVehi) || empty($tipoDocVehi) || 
        empty($numDocVehi) || empty($estadoIngreso) || empty($numeroParqueadero)) {
        echo "<script>alert('⚠️ Faltan datos obligatorios del formulario.'); history.back();</script>";
        exit;
    }

    try {
        $registro = new guardarDatosRegistroParqueadero($pdo);
        $exito = $registro->insertarRegistroVehiculo(
            $placa,
            $nombrePropietarioVehi,
            $tipoDocVehi,
            $numDocVehi,
            $estadoIngreso,
            $numeroParqueadero,
            $fechaEntrada,
            $fechaSalida,
            $horaEntrada
        );

        if ($exito) {
            echo "<script>alert('✅ Registro guardado correctamente.'); window.location.href = '../views/parqueadero.php';</script>";
        } else {
            echo "<script>alert('❌ Error al guardar el registro.'); history.back();</script>";
        }
    } catch (Exception $e) {
        echo "<pre>❌ ERROR DETECTADO:\n" . $e->getMessage() . "</pre>";
    }
}
