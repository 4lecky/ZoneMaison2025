<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../models/insertaRegistroAlquiler.php';
require_once __DIR__ . '/../models/logicaCalculoParqueadero.php';

$model = new insertaRegistroAlquiler($pdo);

// Recibir datos del formulario
$tipoDoc         = $_POST['tipoDoc'] ?? null;
$numDoc          = $_POST['numDoc'] ?? null;
$nombrePropietario = $_POST['nombrePropietario'] ?? null;
$torre           = $_POST['torre'] ?? null;
$apartamento     = $_POST['apartamento'] ?? null;
$placa           = $_POST['placa'] ?? null;
$numParqueadero  = $_POST['numParqueadero'] ?? null;
$estadoSalida    = $_POST['estadoSalida'] ?? null;
$fechaEntrada    = $_POST['fechaEntrada'] ?? null;
$fechaSalida     = $_POST['fechaSalida'] ?? null;
$horaSalida      = $_POST['horaSalida'] ?? null;

// Calcular precio
try {
    $vehiculo = new Vehiculo($placa, ''); 
    $tarifa = new Tarifa(5000); // Tarifa fija
    $ticket = new Ticket($vehiculo, $tarifa);

    $ticket->marcarIngreso(new DateTime($fechaEntrada));
    $ticket->marcarSalida(new DateTime("$fechaSalida $horaSalida"));
    $ticket->marcarHoraSalida(new DateTime("$horaSalida"));

    $horas = $ticket->calcularHoras();
    $precio = $ticket->calcularCosto();

} catch (Exception $e) {
    echo json_encode(['error' => "❌ Error en el cálculo: " . $e->getMessage()]);
    exit;
}

// Insertar en la base de datos
$resultado = $model->insertarAlquiler(
    $tipoDoc, $numDoc, $nombrePropietario,
    $torre, $apartamento, $placa, $numParqueadero,
    $estadoSalida, $fechaEntrada, $fechaSalida, $horaSalida,
    $precio, null, null
);

if ($resultado) {
    echo json_encode([
        'success' => true,
        'alquiler' => [
            'nombre' => $nombrePropietario,
            'placa' => $placa,
            'numParqueadero' => $numParqueadero,
            'horaIngreso' => $fechaEntrada,
            'fechaSalida' => $fechaSalida,
            'horaSalida' => $horaSalida
        ],
        'calculo' => [
            'horas' => $horas,
            'precio' => number_format($precio, 0, ',', '.'),
            'costo_neto' => $precio
        ]
    ]);
} else {
    // Para depurar mejor mostramos el error de PDO
    $errorInfo = $pdo->errorInfo();
    echo json_encode(['error' => '❌ Error al insertar registro: ' . $errorInfo[2]]);
}
