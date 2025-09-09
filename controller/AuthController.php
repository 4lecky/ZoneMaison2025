<?php
require_once __DIR__ . '/../models/AuthModel.php';
require_once __DIR__ . '/../config/db.php';

class AuthController {
    private $usuario;

    public function __construct($pdo) {
        $this->usuario = new Usuario($pdo);
        session_start();
    }

    public function registrar() {
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

        $errores = [];

        if ($this->usuario->cedulaDuplicada($data['NumeroCedula'])) {
            $errores[] = "La cédula ya está registrada.";
        }
        if ($this->usuario->telefonoDuplicado($data['NumeroTelefonico'])) {
            $errores[] = "El número de teléfono ya está registrado.";
        }
        if ($this->usuario->correoDuplicado($data['Email'])) {
            $errores[] = "El correo electrónico ya está registrado.";
        }

        if (!empty($errores)) {
            $_SESSION['errorRegistro'] = implode("<br>", $errores);
            header('Location: views/signUp.php');
            exit;
        }

        $this->usuario->registrar($data);
        header('Location: views/login.php');
        exit;
    }

    public function login() {
        $user = $this->usuario->login($_POST['Email'], $_POST['Password']);

        if ($user) {
            // $_SESSION['usuario'] = $user;
            $_SESSION['usuario'] = [
                'id' => $user['id'],
                'cedula'=> $user['cedula'],
                'nombre' => $user['nombre'],
                'email' => $user['email'],
                'rol' => $user['rol'],
            ];

            header('Location: views/novedades.php');
            exit;
        } else {
            $_SESSION['errorLogin'] = true;
            header('Location: views/login.php');
            exit;
        }
    }
}



