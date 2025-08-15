<?php
header('Content-Type: application/json');
ob_clean();

require_once '../models/logicaCalculoParqueadero.php'; 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $placa       = $_POST['placa'] ?? '';
    $tipo        = $_POST['tipo'] ?? ''; // ← agregado
    $horaIngreso = $_POST['hora_ingreso'] ?? '';
    $horaSalida  = $_POST['hora_salida'] ?? '';
    $costoPorHora = 5000; // Tarifa fija

    // Validación
    if (empty($placa) || empty($tipo) || empty($horaIngreso) || empty($horaSalida)) {
        echo json_encode(['error' => '⚠️ Faltan datos requeridos.']);
        exit;
    }

    try {
        // Crear objetos usando tipo recibido
        $vehiculo = new Vehiculo($placa, $tipo);
        $tarifa = new Tarifa($costoPorHora);
        $ticket = new Ticket($vehiculo, $tarifa);

        $ticket->marcarIngreso(new DateTime($horaIngreso));
        $ticket->marcarSalida(new DateTime($horaSalida));

        $costo = $ticket->calcularCosto();

        echo json_encode([
            'costo'       => number_format($costo, 0, ',', '.'),
            'costo_neto'  => $costo,
            'tipo'        => $tipo // ← opcional si quieres devolverlo
        ]);
    } catch (Exception $e) {
        echo json_encode(['error' => '❌ Error al calcular el costo: ' . $e->getMessage()]);
    }
}
