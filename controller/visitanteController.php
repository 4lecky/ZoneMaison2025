<?php
session_start();

$usuario = new Usuario($pdo);

// Registro
if (isset($_POST['registrarvisitante'])) {
    $data = [

        'nombre'    => $_POST['nombre'],
        'tipoDoc'   => $_POST['tipoDoc'],
        'documento' => $_POST['documento'],
        'email'     => $_POST['email'],
        'telefono'  => $_POST['telefono'],
    
    ];
    $usuario->registrar($data);
    header('Location: ../views/login.php ');

    exit;
}
