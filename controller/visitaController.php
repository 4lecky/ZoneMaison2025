<?php
session_start();

$visitante = new Usuario($pdo);

// Registro
if (isset($_POST['registrarvisita'])) {
    $data = [

        'torre'         => $_POST['torre'],
        'apto'          => $_POST['apto'],
        'fechaEntrada'  => $_POST['fechaEntrada'],
        'fechaSalida'   => $_POST['fechaSalida'],
        'horaInicio'    => $_POST['horaInicio'],
        'horaSalida'    => $_POST['horaSalida'],
    ];
    $usuario->registrar($data);
    header('Location: ../views/login.php ');

    exit;
}
