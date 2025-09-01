<?php
session_start();

$pdo = require_once '../config/db.php';
require_once '../models/guardarDatosRegistroParqueadero.php'; 

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // ===== Campos del formulario =====
    $placa                  = $_POST['parq_vehi_placa'] ?? '';
    $nombrePropietarioVehi  = $_POST['parq_nombre_propietario_vehi'] ?? '';
    $tipoDocVehi            = $_POST['parq_tipo_doc_vehi'] ?? '';
    $numDocVehi             = $_POST['parq_num_doc_vehi'] ?? '';
    $estadoIngreso          = $_POST['parq_vehi_estadoIngreso'] ?? '';
    $alquId                 = $_POST['parq_vehi_alqu_id'] ?? null; 
    $numeroParqueadero      = $_POST['parq_numeroParqueadero'] ?? null;
    $fechaEntrada           = $_POST['parq_fecha_entrada'] ?? null;
    $fechaSalida            = $_POST['parq_fecha_salida'] ?? null;
    $horaEntrada            = $_POST['parq_hora_entrada'] ?? null;

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
            $alquId,
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
