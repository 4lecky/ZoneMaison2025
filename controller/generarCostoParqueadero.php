<?php
header('Content-Type: application/json'); // Asegura que se devuelva JSON al frontend
ob_clean(); // Limpia cualquier salida previa inesperada

require_once '../controller/calculoParqueadero.php'; // Incluye la lógica del cálculo

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $placa = $_POST['placa'] ?? '';
    $tipo = $_POST['tipo'] ?? '';
    $horaIngreso = $_POST['hora_ingreso'] ?? '';
    $horaSalida = $_POST['hora_salida'] ?? '';
    $costoPorHora = 5000; // Tarifa fija

    // ✅ Validar campos obligatorios
    if (empty($placa) || empty($tipo) || empty($horaIngreso) || empty($horaSalida)) {
        echo json_encode(['error' => 'Faltan datos requeridos.']);
        exit;
    }

    try {
        // Crear clases
        $vehiculo = new Vehiculo($placa, $tipo);
        $tarifa = new Tarifa($costoPorHora);
        $ticket = new Ticket($vehiculo, $tarifa);

        // Marcar horas manualmente
        $ticket->marcarIngreso(new DateTime($horaIngreso));
        $ticket->marcarSalida(new DateTime($horaSalida));

        // Calcular costo
        $costo = $ticket->calcularCosto();

        // ✅ Devolver respuesta JSON con y sin formato
        echo json_encode([
            'costo' => number_format($costo, 0, ',', '.'), // $5.000
            'costo_neto' => $costo                          // 5000 (numérico)
        ]);
    } catch (Exception $e) {
        echo json_encode(['error' => 'Error al calcular el costo: ' . $e->getMessage()]);
    }
}
