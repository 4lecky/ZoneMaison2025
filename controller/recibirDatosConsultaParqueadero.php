<?php
require_once '../config/db.php';
require_once '../models/insertaRegistroConsultaParqueadero.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // ===== Captura de datos del formulario =====
    $consulParq_tipoVehiculo   = $_POST['tipo_vehiculo']     ?? '';
    $consulParq_placa          = $_POST['placa']             ?? '';
    $consulParq_observaciones  = $_POST['observaciones']     ?? '';
    $consulParq_estadoSalida   = $_POST['estado']            ?? '';
    $consulParq_usuario_cedula = $_POST['usuario_cedula']    ?? '';

    // ===== Validación de campos obligatorios =====
    if (
        empty($consulParq_tipoVehiculo) ||
        empty($consulParq_placa) ||
        empty($consulParq_observaciones) ||
        empty($consulParq_estadoSalida) ||
        empty($consulParq_usuario_cedula)
    ) {
        echo "<script>alert('⚠️ Faltan datos obligatorios en el formulario de consulta de parqueadero.'); history.back();</script>";
        exit;
    }

    try {
        // Instancia del modelo
        $registro = new insertaRegistroConsultaParqueadero($pdo);

        // Llamada al método de inserción
        $exito = $registro->insertarConsultaParqueadero(
            $consulParq_tipoVehiculo,
            $consulParq_placa,
            $consulParq_observaciones,
            $consulParq_estadoSalida,
            $consulParq_usuario_cedula
        );

        if ($exito) {
            echo "<script>alert('✅ Consulta de parqueadero registrada correctamente.'); window.location.href = '../views/consultaParqueadero.php';</script>";
        } else {
            echo "<script>alert('❌ Error al registrar la consulta de parqueadero.'); history.back();</script>";
        }
    } catch (Exception $e) {
        echo "<pre>❌ ERROR DETECTADO:\n" . $e->getMessage() . "</pre>";
    }
}
