<?php
session_start();

require_once __DIR__ . '../config/db.php';
require_once __DIR__ . '../models/visita.php'; 

$visita = new Visita($pdo);

if (isset($_POST['registrarvisita'])) {

    $usuario_cc = $_SESSION['usuario_cc'] ?? null;

    if (!$usuario_cc) {
        die("Error: No se ha identificado el usuario.");
    }

    $data = [
        'torre'         => $_POST['torre'],
        'apto'          => $_POST['apto'],
        'fechaEntrada'  => $_POST['fechaEntrada'],
        'fechaSalida'   => $_POST['fechaSalida'],
        'horaInicio'    => $_POST['horaInicio'],
        'horaSalida'    => $_POST['horaSalida'],
        'usuario_cc'    => $usuario_cc,
    ];

    $visitaId = $visita->registrarVisita($data);

    $_SESSION['ultima_visita_id'] = $visitaId;

    header('Location: ../views/registrar_visitante.php');
    exit;
}