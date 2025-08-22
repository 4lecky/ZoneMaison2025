<?php

session_start();
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../models/perfilUsuario.php';

if (!isset($_SESSION['usuario'])) {
    header("Location: ../views/login.php");
    exit();
}

$model = new Perfil($pdo);

$id = $_SESSION['usuario']['id'];
$user = $model->findById($id);

$mensajesPerfil = [];
$TieneError = false;
                                                                                           
if (!$user) {   
    // OJO: si ves este mensaje, el problema es el SELECT (id incoherente o filtro)
    die("No se encontró usuario con id = " . htmlspecialchars($id));
}

/* Boton para editar */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btn-editar'])) {
    $data = [
        'NumeroDocumento' => trim($_POST['NumeroDocumento'] ?? ''),
        'TipoDocumento' => trim($_POST['TipoDocumento'] ?? ''),
        'NombreCompleto'        => trim($_POST['NombreCompleto'] ?? ''),
        'Telefono'      => trim($_POST['Telefono'] ?? ''),
        'Correo'        => trim($_POST['Correo'] ?? ''),
        'Apartamento'   => trim($_POST['Apartamento'] ?? ''),
        'Torre'         => trim($_POST['Torre'] ?? ''),
        'Propiedades'   => trim($_POST['Propiedades'] ?? ''),

    ];

    if ($model->editar($data, $id)) {
        // $_SESSION['flash_ok'] = "Perfil actualizado";
        $mensajesPerfil[] = "Perfil actualizado";
    } else {
        // $_SESSION['flash_err'] = "Error al actualizar";
        $mensajesPerfil[] = "Error al actualizar";
        $TieneError = true; 
    }

    $_SESSION['mensajesPerfil'] = [
        'tipo' => $TieneError ? 'error' : 'success',
        'texto' => implode("<br>", $mensajesPerfil)
    ];

    /* Refresca la pagina */
    header("Location: perfilUsuarioController.php");
    exit;
}

/* Boton eliminar */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btn-eliminar'])) {
    if ($model->eliminar($id)) {
        session_destroy();
        header("Location: ../views/login.php");
        exit;
    } else {
        // $flash['err'] = "No se pudo eliminar la cuenta";
        $mensajesPerfil[] = "No se pudo eliminar la cuenta";
        $TieneError = true; 
    }

        $_SESSION['mensajesPerfil'] = [
        'tipo'  => 'error',
        'texto' => implode("<br>", $mensajesPerfil)
    ];
}


// var_dump('FLASH EN SESIÓN =>', $_SESSION['mensajesPerfil'] ?? null);
// var_dump('FLASH EN VARIABLE =>', $flash ?? null);
// var_dump('USER =>', $user ? 'ok' : 'null');

require_once __DIR__ . '/../views/perfil_usuario.php';



