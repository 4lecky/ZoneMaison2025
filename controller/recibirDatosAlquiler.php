
<?php
session_start();

$pdo = require_once '../config/db.php';
require_once '../models/guardarDatosAlquiler.php';

$guardarDatosAlquiler = new guardarDatosAlquiler($pdo);

// Captura de datos del formulario
$numRecibo     = $_POST['numRecibo'] ?? '';
$nombre        = $_POST['nombre_residente'] ?? '';
$tipoDoc       = $_POST['tipo_doc'] ?? '';
$numDoc        = $_POST['num_doc'] ?? '';
$torre         = $_POST['torre'] ?? '';
$apartamento   = $_POST['apartamento'] ?? '';
$placa         = $_POST['placa'] ?? '';
$parqueadero   = $_POST['parqueadero'] ?? '';
$observaciones = $_POST['observaciones'] ?? '';
$fechaIngreso  = $_POST['fecha_ingreso'] ?? '';
$fechaSalida   = $_POST['fecha_salida'] ?? '';
$horaIngreso   = $_POST['hora_ingreso'] ?? '';
$horaSalida    = $_POST['hora_salida'] ?? '';
$costo         = $_POST['costo'] ?? '';

// Validación básica (puedes ampliarla)
if (empty($numRecibo) || empty($nombre) || empty($placa) || empty($horaIngreso) || empty($horaSalida)) {
    header('Location: ../views/error.php');
    exit;
}


// Guardar en la base de datos
$guardarDatosAlquiler->insertarAlquiler(
    $numRecibo, $nombre, $tipoDoc, $numDoc,
    $torre, $apartamento, $placa, $parqueadero,
    $observaciones, $fechaIngreso, $fechaSalida,
    $horaIngreso, $horaSalida, $costo
);


// Mensaje de éxito

echo "✅ Alquiler registrado correctamente.";
exit;


// Redirigir después del registro
// header("Location: ../views/exitoAlquiler.php"); // o la vista que desees mostrar
// exit;

