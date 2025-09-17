<?php
session_start();

require_once __DIR__ . '/../models/RegistrarVisitaModel.php';

/* Solo procesamos si llega por POST */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $modelo = new RegistrarVisitaModel();

    /* 🔍 Validar si llega petición de búsqueda por AJAX */
    if (isset($_POST['action']) && $_POST['action'] === 'buscarResidente') {
        $cedula = $_POST['cedula'] ?? null;

        if ($cedula) {
            // 🔹 Ahora buscamos en tbl_usuario (residentes)
            $residente = $modelo->buscarResidentePorCedula($cedula);

            if ($residente) {
                echo json_encode([
                    "status" => "ok",
                    "data"   => $residente
                ]);
            } else {
                echo json_encode([
                    "status"  => "error",
                    "message" => "No existe un residente con la cédula $cedula"
                ]);
            }
        } else {
            echo json_encode([
                "status"  => "error",
                "message" => "Cédula vacía"
            ]);
        }
        exit;
    }

    /* 📝 Si no es búsqueda, asumimos registro de visita */
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
