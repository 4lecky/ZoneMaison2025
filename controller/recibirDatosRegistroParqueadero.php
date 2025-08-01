<?php
$pdo = require_once '../config/db.php';
require_once '../models/guardarDatosRegistroParqueadero.php'; 

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Captura de datos
    $placa               = $_POST['placa'] ?? '';
    $nombrePropietarioVehi = $_POST['nombre_propietario_vehiculo'] ?? '';
    $tipoDocVehi         = $_POST['tipo_doc_vehiculo'] ?? '';
    $numDocVehi          = $_POST['numero_doc_vehiculo'] ?? '';
    $estadoIngreso       = $_POST['estado'] ?? '';
    $alquId              = $_POST['alqu_id'] ?? '';
    $usuarioCedula       = $_POST['usuario_cedula'] ?? '';
    $visitaId            = $_POST['visita_id'] ?? '';

    // Validación básica
    if (empty($placa) || empty($nombrePropietarioVehi) || empty($usuarioCedula) || empty($visitaId) || empty($alquId)) {
        echo "<script>alert('⚠️ Faltan datos obligatorios.'); history.back();</script>";
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
            $visitaId
        );

        if ($exito) {
            echo "<script>alert('✅ Registro guardado correctamente.'); window.location.href = '../views/parqueadero.php';</script>";
        } else {
            echo "<script>alert('❌ Error al guardar el registro.'); history.back();</script>";
        }
    } catch (Exception $e) {
        echo "<pre>ERROR DETECTADO:\n" . $e->getMessage() . "</pre>";
    }
}
