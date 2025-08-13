<?php
$pdo = require_once '../config/db.php';
require_once '../models/guardarDatosRegistroParqueadero.php'; 

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // ===== Campos que van a tbl_parqueadero =====
    $placa               = $_POST['parq_vehi_placa']            ?? '';
    $nombrePropietarioVehi = $_POST['parq_nombre_propietario_vehi'] ?? '';
    $tipoDocVehi         = $_POST['parq_tipo_doc_vehi']         ?? '';
    $numDocVehi          = $_POST['parq_num_doc_vehi']          ?? '';
    $estadoIngreso       = $_POST['parq_vehi_estadiIngreso']     ?? '';
    $alquId              = $_POST['parq_vehi_alqu_id']          ?? '';
    $usuarioCedula       = $_POST['parq_usuario_cedula']        ?? '';
    $visitaId            = $_POST['parq_visita_id']             ?? '';

    // // ===== Campos UI (no se guardan aquí) =====
    // $email         = $_POST['email']        ?? null;
    // $tipoDocRes    = $_POST['tipo_doc']     ?? null;
    // $numDocRes     = $_POST['numero_doc']   ?? null;
    // $nombreRes     = $_POST['nombre']       ?? null;
    // $torre         = $_POST['torre']        ?? null;
    // $apto          = $_POST['apto']         ?? null;
    // $parqueaderoUi = $_POST['parqueadero']  ?? null;
    // $fIngreso      = $_POST['fecha_ingreso']?? null;
    // $fSalida       = $_POST['fecha_salida'] ?? null;
    // // (Si luego quieres usar estos para autorelleno o registrar en otras tablas, aquí ya los tienes.)

    // Validación mínima de obligatorios (BD)
    if (empty($placa) || empty($nombrePropietarioVehi) || empty($tipoDocVehi) || 
        empty($numDocVehi) || empty($estadoIngreso) || empty($usuarioCedula)) {
        echo "<script>alert('⚠️ Faltan datos obligatorios de BD.'); history.back();</script>";
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
