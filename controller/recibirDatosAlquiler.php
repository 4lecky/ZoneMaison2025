<?php
session_start();
require_once '../config/db.php';
require_once '../models/insertaRegistroAlquiler.php';
require_once '../models/logicaCalculoParqueadero.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Captura de campos del formulario
    $numRecibo     = $_POST['num_recibo'] ?? '';
    $observaciones = $_POST['observaciones'] ?? '';
    $placa         = $_POST['placa'] ?? '';
    $usuarioCedula = $_SESSION['usuario']['id'] ?? null; // usuario autenticado
    $visitaId      = $_POST['visita_id'] ?? '';
    $horaIngreso   = $_POST['hora_ingreso'] ?? '';
    $horaSalida    = $_POST['hora_salida'] ?? '';
    $tipoVehiculo  = $_POST['tipo'] ?? 'Carro'; // opcional, puedes agregar select en form
    $costoPorHora  = 5000;

    if (!$usuarioCedula) {
        die("⚠️ Usuario no autenticado.");
    }

    // Validación básica
    if (empty($numRecibo) || empty($observaciones) || empty($placa) || empty($horaIngreso) || empty($horaSalida)) {
        echo "<script>alert('⚠️ Faltan datos obligatorios.'); history.back();</script>";
        exit;
    }

    try {
        // Calcula costo usando la lógica
        $vehiculo = new Vehiculo($placa, $tipoVehiculo);
        $tarifa = new Tarifa($costoPorHora);
        $ticket = new Ticket($vehiculo, $tarifa);
        $ticket->marcarIngreso(new DateTime($horaIngreso));
        $ticket->marcarSalida(new DateTime($horaSalida));
        $costo = $ticket->calcularCosto();

        // Guardar en la BD
        $registro = new insertaRegistroAlquiler($pdo);
        $exito = $registro->insertarAlquiler(
            $numRecibo,
            $observaciones,
            $costo,
            $visitaId,
            $placa,
            $usuarioCedula
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
