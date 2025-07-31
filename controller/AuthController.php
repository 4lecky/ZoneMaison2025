<?php
require_once __DIR__ . '/../models/Usuario.php';
require_once __DIR__ . '/../config/db.php';
session_start();

$usuario = new Usuario($pdo);

// Registro
if (isset($_POST['registrar'])) {
    $data = [

        'NombreUsuario' => $_POST['NombreUsuario'],
        'NumeroCedula' => $_POST['NumeroCedula'],
        'TipoDocumento' => $_POST['TipoDocumento'],
        'NumeroTelefonico' => $_POST['NumeroTelefonico'],
        'Apartamento' => $_POST['Apartamento'],
        'Torre' => $_POST['Torre'],
        'Parqueadero' => $_POST['Parqueadero'],
        'Propiedades' => $_POST['Propiedades'],
        'Email' => $_POST['Email'],
        'Password' => $_POST['Password'],

    ];
    $usuario->registrar($data);
    header('Location: ../views/login.php ');

    exit;
}

// Login
if (isset($_POST['login'])) {
    $user = $usuario->login($_POST['Email'], $_POST['Password']);
    if ($user) {
        $_SESSION['usuario'] = $user;
        header('Location: ../views/home.php');
    } else {
        // echo "Credenciales incorrectas.";
        $_SESSION['errorLogin'] = true;
        header('Location: ../views/login.php'); // redirige con error
        exit;
    }
}
