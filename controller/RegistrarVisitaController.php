<?php
require_once 'RegistrarVisitaModelo.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $modelo = new RegistrarVisitaModelo();

    $datos = [
        'tipo_doc'      => $_POST['tipo_doc'],
        'numero_doc'    => $_POST['numero_doc'],
        'nombre'        => $_POST['nombre'],
        'correo'        => $_POST['correo'],
        'telefono'      => $_POST['telefono'],
        'fechaEntrada'  => $_POST['fechaEntrada'],
        'horaEntrada'   => $_POST['horaEntrada'],
        'fechaSalida'   => $_POST['fechaSalida'],
        'horaSalida'    => $_POST['horaSalida'],
        'torreVisitada' => $_POST['torreVisitada'],
        'aptoVisitado'  => $_POST['aptoVisitado'],
        'usuario'       => $_POST['usuario']
    ];

    $resultado = $modelo->insertarVisitaCompleta($datos);

    if ($resultado === true) {
        header("Location: visita.php?registro=exitoso");
        exit();
    } else {
        echo $resultado;
    }
}
?>
