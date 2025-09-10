<?php
session_start();
require_once("../config/db.php"); 
require_once("../models/insertaRegistroConsultaParqueadero.php");

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // ===== Validar datos obligatorios =====
    if (
        empty($_POST['consulParq_tipoVehiculo']) ||
        empty($_POST['consulParq_placa']) ||
        empty($_POST['consulParq_observaciones']) ||
        empty($_POST['consulParq_numeroParqueadero']) ||
        empty($_POST['consulParq_estado'])
    ) {
        die("⚠️ Faltan datos obligatorios en el formulario de consulta de parqueadero.");
    }

    // ===== Capturar datos del formulario =====
    $tipoVehiculo      = $_POST['consulParq_tipoVehiculo'];
    $placa             = $_POST['consulParq_placa'];
    $observaciones     = $_POST['consulParq_observaciones'] ?? "";
    $numeroParqueadero = $_POST['consulParq_numeroParqueadero'];
    $estado            = $_POST['consulParq_estado'];

    try {
        // Crear objeto modelo e insertar
        $modelo = new insertaRegistroConsultaParqueadero($pdo);

        if ($modelo->insertarConsultaParqueadero(
            $tipoVehiculo,
            $placa,
            $observaciones,
            $numeroParqueadero,
            $estado
        )) {
            echo "<script>alert('✅ Consulta de parqueadero registrada correctamente.'); window.location.href = '../views/parqueadero.php';</script>";
        } else {
            echo "<script>alert('❌ Error al registrar la consulta de parqueadero.'); history.back();</script>";
        }
    } catch (PDOException $e) {
        // error_log("❌ Error al insertar consulta de parqueadero: " . $e->getMessage());
        // echo "<script>alert('❌ Error en base de datos. Revisa el log para más detalles.'); history.back();</script>";
        echo "<pre>❌ Error SQL:\n" . $e->getMessage() . "</pre>";
        exit; // Detener ejecución para ver el error exacto
    }
}



