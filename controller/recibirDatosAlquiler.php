<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../models/insertaRegistroAlquiler.php';

// Crear la conexión
// $conexion = (new pdo())->getConexion();
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

// Opcionales
$precio          = $_POST['alqu_precio'] ?? null;
$usuarioCedula   = $_POST['alqu_usuario_cedula'] ?? null;
$visitaId        = $_POST['alqu_vis_id'] ?? null;

// Insertar en la base de datos
// $modelo = new insertaRegistroAlquiler($conexion);
$resultado = $model->insertarAlquiler(
    $numRecibo, $tipoDoc, $numDoc, $nombrePropietario,
    $torre, $apartamento, $placa, $numParqueadero,
    $estadoSalida, $fechaEntrada, $fechaSalida, $horaSalida,
    $precio, $usuarioCedula, $visitaId
);

// Redirigir o mostrar mensaje
if ($resultado) {
    echo "✅ Registro insertado correctamente.";
    // header("Location: ../view/exito.php");
} else {
    echo "❌ Error al insertar registro.";
    // header("Location: ../view/error.php");
}
