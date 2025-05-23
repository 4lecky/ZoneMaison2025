<?php
session_start();

$pdo = require ("../config/db.php");          
require_once '../models/muro.php'; 

$muro = new muro($pdo); 

// Registro
if (isset($_POST['enviarmuro'])) {
    $data = [
        'muro_Id'     => $_POST['muro_Id'],
        'Destinatario' => $_POST['Destinatario'],
        'Asunto'      => $_POST['Asunto'],
        'Descripción' => $_POST['Descripción'],
        'Hora'        => $_POST['Hora'],
        'Fecha'       => $_POST['Fecha'],
        'imagen'      => $_POST['imagen'], 
        'usuario_cc'  => $_POST['usuario_cc'],
    ];

    $success = $muro->enviarmuro($data);

    if ($success) {
        header('Location: ./views/muro.php');
        exit;
    } else {
        echo "Error al guardar en la base de datos.";
    }
}
