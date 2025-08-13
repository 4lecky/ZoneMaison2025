<?php
require_once '../config/db.php';
require_once '../models/insertaRegistroAlquiler.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // ===== Captura de datos del formulario (coinciden con la BD) =====
    $alqu_num_recibo      = $_POST['numRecibo']       ?? '';
    $alqu_observaciones   = $_POST['observaciones']   ?? '';
    $alqu_precio          = $_POST['costo']           ?? '';
    $alqu_vis_id          = $_POST['visita_id']       ?? '';
    $alqu_placa           = $_POST['placa']           ?? '';
    $alqu_usuario_cedula  = $_POST['usuario_cedula']  ?? '';

    // ===== Validación de campos obligatorios =====
    if (
        empty($alqu_num_recibo) ||
        empty($alqu_observaciones) ||
        empty($alqu_precio) ||
        empty($alqu_vis_id) ||
        empty($alqu_placa) ||
        empty($alqu_usuario_cedula)
    ) {
        echo "<script>alert('⚠️ Faltan datos obligatorios.'); history.back();</script>";
        exit;
    }

    try {
        // Crear instancia del modelo
        $registro = new insertaRegistroAlquiler($pdo);

        // Guardar en la base de datos
        $exito = $registro->insertarAlquiler(
            $alqu_num_recibo,
            $alqu_observaciones,
            $alqu_precio,
            $alqu_vis_id,
            $alqu_placa,
            $alqu_usuario_cedula
        );

        if ($exito) {
            echo "<script>alert('✅ Alquiler registrado correctamente.'); window.location.href = '../views/alquiler.php';</script>";
        } else {
            echo "<script>alert('❌ Error al registrar el alquiler.'); history.back();</script>";
        }
    } catch (Exception $e) {
        echo "<pre>❌ ERROR DETECTADO:\n" . $e->getMessage() . "</pre>";
    }
}



