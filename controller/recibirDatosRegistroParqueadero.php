<?php
$conexion = require_once '../config/db.php';
require_once '../models/guardarDatosRegistroParqueadero.php'; // Importar la clase

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Validar que todos los campos requeridos están presentes
    $camposRequeridos = [
        'email', 'nombre', 'tipo_doc', 'numero_doc',
        'torre', 'apto', 'nombre_propietario_vehiculo',
        'tipo_doc_vehiculo', 'numero_doc_vehiculo',
        'placa', 'parqueadero', 'estado',
        'fecha_ingreso', 'fecha_salida'
    ];

    foreach ($camposRequeridos as $campo) {
        if (empty($_POST[$campo])) {
            echo "<script>alert('⚠️ El campo \"$campo\" es obligatorio.'); history.back();</script>";
            exit;
        }
    }

    // Captura de datos
    $email = $_POST['email'];
    $nombre = $_POST['nombre'];
    $tipo_doc = $_POST['tipo_doc'];
    $numero_doc = $_POST['numero_doc'];
    $torre = $_POST['torre'];
    $apto = $_POST['apto'];
    $nombre_propietario_vehi = $_POST['nombre_propietario_vehiculo'];
    $tipo_doc_vehi = $_POST['tipo_doc_vehiculo'];
    $num_doc_vehi = $_POST['numero_doc_vehiculo'];
    $placa = $_POST['placa'];
    $parqueadero = $_POST['parqueadero'];
    $estado = $_POST['estado'];
    $fecha_ingreso = $_POST['fecha_ingreso'];
    $fecha_salida = $_POST['fecha_salida'];

    try {
        // Instanciar y usar la clase
        $registro = new guardarDatosRegistroParqueadero($conexion);

        $exito = $registro->insertarRegistroVehiculo(
            $email, $nombre, $tipo_doc, $numero_doc,
            $torre, $apto, $nombre_propietario_vehi, $tipo_doc_vehi,
            $num_doc_vehi, $placa, $parqueadero, $fecha_ingreso,
            $fecha_salida, $estado
        );

        if ($exito) {
            echo "<script>alert('✅ Registro guardado correctamente.'); window.location.href = '../views/parqueadero.php';</script>";
        } else {
            echo "<script>alert('❌ Error al guardar el registro.'); history.back();</script>";
        }
    } catch (Exception $e) {
        echo "<pre>ERROR DETECTADO:\n" . $e->getMessage() . "</pre>";
    }
}
?>
