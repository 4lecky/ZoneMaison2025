<?php
header('Content-Type: application/json');
ob_clean();

require_once '../models/logicaCalculoParqueadero.php';
require_once '../config/db.php'; // archivo con $pdo o conexión PDO

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $placa = $_POST['placa'] ?? '';
    $horaSalida = $_POST['hora_salida'] ?? '';
    $costoPorHora = 5000; // tarifa fija (puedes parametrizarlo luego)

    if (empty($placa) || empty($horaSalida)) {
        echo json_encode(['error' => '⚠️ Faltan datos requeridos.']);
        exit;
    }

    try {
        // Obtener datos del alquiler
        $stmtAlquiler = $pdo->prepare("
            SELECT alqu_fecha_entrada
            FROM tbl_alquiler
            WHERE alqu_placa = :placa
            ORDER BY alqu_id DESC
            LIMIT 1
        ");
        $stmtAlquiler->execute(['placa' => $placa]);
        $alquilerDB = $stmtAlquiler->fetch(PDO::FETCH_ASSOC);

        if (!$alquilerDB) {
            echo json_encode(['error' => '❌ No se encontró registro de alquiler para esta placa.']);
            exit;
        }

        // Obtener hora de entrada desde tbl_parqueadero
        $stmtParqueadero = $pdo->prepare("
            SELECT parq_hora_entrada
            FROM tbl_parqueadero
            WHERE parq_vehi_placa = :placa
            ORDER BY parq_id DESC
            LIMIT 1
        ");
        $stmtParqueadero->execute(['placa' => $placa]);
        $parqueaderoDB = $stmtParqueadero->fetch(PDO::FETCH_ASSOC);

        if (!$parqueaderoDB || empty($parqueaderoDB['parq_hora_entrada'])) {
            echo json_encode(['error' => '⚠️ No se encontró hora de entrada en parqueadero.']);
            exit;
        }

        // Unimos fecha + hora para el ingreso
        $horaIngreso = $alquilerDB['alqu_fecha_entrada'] . ' ' . $parqueaderoDB['parq_hora_entrada'];

        // Calcular costo con el modelo
        $vehiculo = new Vehiculo($placa, ''); // el tipo no lo necesitamos aquí
        $tarifa = new Tarifa($costoPorHora);
        $ticket = new Ticket($vehiculo, $tarifa);

        $ticket->marcarIngreso(new DateTime($horaIngreso));
        $ticket->marcarSalida(new DateTime($horaSalida));

        $costo = $ticket->calcularCosto();
        $horas = $ticket->calcularHoras();

        echo json_encode([
            'costo' => number_format($costo, 0, ',', '.'),
            'costo_neto' => $costo,
            'horas' => $horas
        ]);
    } catch (Exception $e) {
        echo json_encode(['error' => '❌ Error al calcular el costo: ' . $e->getMessage()]);
    }
}
