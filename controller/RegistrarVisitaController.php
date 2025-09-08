<?php
session_start();

require_once __DIR__ . '/../models/RegistrarVisitaModel.php';



/*  Solo procesamos si llega por POST y desde el botón
    name="registrarFormVisi" del formulario */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $modelo = new RegistrarVisitaModel();

    $datos = [
        'tipo_doc'      => $_POST['tipo_doc']      ?? null,
        'numero_doc'    => $_POST['numero_doc']    ?? null,
        'nombre'        => $_POST['nombre']        ?? null,
        'correo'        => $_POST['correo']        ?? null,
        'telefono'      => $_POST['telefono']      ?? null,
        'fechaEntrada'  => $_POST['fechaEntrada']  ?? null,
        'horaEntrada'   => $_POST['horaEntrada']   ?? null,
        'fechaSalida'   => $_POST['fechaSalida']   ?? null,
        'horaSalida'    => $_POST['horaSalida']    ?? null,
        'usuario'       => $_POST['usuario']       ?? null
    ];

    $ok = $modelo->insertarVisitaCompleta($datos);

    if ($ok === true) {
        /* Ruta absoluta: evita 404 y problemas con “..” */
        header('Location: ../views/visitas.php');
        exit;
    }

    /* Si $ok trae un mensaje de error lo mostramos */
    echo $ok;
    exit;
}

