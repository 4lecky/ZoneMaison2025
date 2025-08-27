<?php
session_start();

$pdo = require_once '../config/db.php';
require_once '../models/guardarDatosRegistroParqueadero.php'; 

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // ===== Tomar usuario de sesión si no viene del formulario =====
    $usuarioCedula = $_POST['parq_usuario_cedula'] ?? $_SESSION['usuario']['cedula'] ?? null;
    if (!$usuarioCedula) {
        die("⚠️ Usuario no autenticado o cédula no disponible en sesión.");
    }

    // ===== Campos del formulario =====
    $placa                  = $_POST['parq_vehi_placa'] ?? '';
    $nombrePropietarioVehi  = $_POST['parq_nombre_propietario_vehi'] ?? '';
    $tipoDocVehi            = $_POST['parq_tipo_doc_vehi'] ?? '';
    $numDocVehi             = $_POST['parq_num_doc_vehi'] ?? '';
    $estadoIngreso          = $_POST['parq_vehi_estadoIngreso'] ?? '';
    $alquId                 = $_POST['parq_consulParq_numeroParqueadero'] ?? null;
    $visitaId               = $_POST['parq_visita_id'] ?? null;
    $fechaIngreso           = $_POST['fecha_ingreso'] ?? null;
    $fechaSalida            = $_POST['fecha_salida'] ?? null;
    $observaciones          = $_POST['observaciones'] ?? null;

    // ===== Validación mínima =====
    if (empty($placa) || empty($nombrePropietarioVehi) || empty($tipoDocVehi) || 
        empty($numDocVehi) || empty($estadoIngreso)) {
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
            $alquId,
            $usuarioCedula,
            $visitaId,
            $fechaIngreso,
            $fechaSalida,
            $observaciones
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
