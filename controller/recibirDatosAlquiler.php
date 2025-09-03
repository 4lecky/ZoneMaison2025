<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../models/insertaRegistroAlquiler.php';
require_once __DIR__ . '/../models/logicaCalculoParqueadero.php';

$model = new insertaRegistroAlquiler($pdo);

// Recibir datos del formulario
$numRecibo       = $_POST['alqu_num_recibo'] ?? null;
$tipoDoc         = $_POST['alqu_tipo_doc_vehi'] ?? null;
$numDoc          = $_POST['alqu_num_doc_vehi'] ?? null;
$nombrePropietario = $_POST['alqu_nombre_propietario'] ?? null;
$torre           = $_POST['alqu_torre'] ?? null;
$apartamento     = $_POST['alqu_apartamento'] ?? null;
$placa           = $_POST['alqu_placa'] ?? null;
$numParqueadero  = $_POST['alqu_numeroParqueadero'] ?? null;
$estadoSalida    = $_POST['alqu_estadoSalida'] ?? null;
$fechaEntrada    = $_POST['alqu_fecha_entrada'] ?? null;
$fechaSalida     = $_POST['alqu_fecha_salida'] ?? null;
$horaSalida      = $_POST['alqu_hora_salida'] ?? null;

// Calcular precio
try {
    $vehiculo = new Vehiculo($placa, ''); 
    $tarifa = new Tarifa(5000); // Tarifa fija
    $ticket = new Ticket($vehiculo, $tarifa);

    $ticket->marcarIngreso(new DateTime($fechaEntrada));
    $ticket->marcarSalida(new DateTime("$fechaSalida $horaSalida"));

    $horas = $ticket->calcularHoras();
    $precio = $ticket->calcularCosto();

} catch (Exception $e) {
    echo json_encode(['error' => "❌ Error en el cálculo: " . $e->getMessage()]);
    exit;
}

// Insertar en la base de datos
$resultado = $model->insertarAlquiler(
    $numRecibo, $tipoDoc, $numDoc, $nombrePropietario,
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
            'costo' => number_format($precio, 0, ',', '.'),
            'costo_neto' => $precio
        ]
    ]);
} else {
    // Para depurar mejor mostramos el error de PDO
    $errorInfo = $pdo->errorInfo();
    echo json_encode(['error' => '❌ Error al insertar registro: ' . $errorInfo[2]]);
}
