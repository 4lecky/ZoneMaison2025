<?php
header('Content-Type: application/json'); // Asegura que se devuelva JSON al frontend
ob_clean(); // Limpia cualquier salida previa inesperada (espacios, errores, etc.)

require_once '../controller/calculoparqueadero.php'; // o la ruta correcta a tu clase

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $placa = $_POST['placa'];
    $tipo = $_POST['tipo']; // Asegúrate de enviarlo desde JS si lo usas
    $costoPorHora = 5000; // Puedes personalizarlo
    $horaIngreso = $_POST['hora_ingreso'];
    $horaSalida = $_POST['hora_salida'];

    // Crear Vehiculo y Tarifa
    $vehiculo = new Vehiculo($placa, $tipo);
    $tarifa = new Tarifa($costoPorHora);
    $ticket = new Ticket($vehiculo, $tarifa);

    // Sobrescribe horaIngreso manualmente (porque el constructor lo pone con `new DateTime()`)
    $ticket->marcarIngreso(new DateTime($horaIngreso));
    $ticket->marcarSalida(new DateTime($horaSalida));


    $costo = $ticket->calcularCosto();

    echo json_encode([
        'costo' => number_format($costo, 0, ',', '.')
    ]);
}
