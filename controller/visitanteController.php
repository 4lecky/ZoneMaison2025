<?php
session_start();

require_once __DIR__."/../config/db.php";          
require_once __DIR__.'/../models/visitante.php'; 

$visitante = new Visitante($pdo);

// Registro
if (isset($_POST['registrarFormVisi'])) {


    $visi_vis_id  = $_POST['visi_vis_id'] ?? null;

    if (!$visi_vis_id ) {
        
        die("Error: No se recibió ID de la visita.");
    }

    $data = [
        'nombre'     => $_POST['nombre'],
        'tipoDoc'    => $_POST['tipoDoc'],
        'documento'  => $_POST['documento'],
        'email'      => $_POST['email'],
        'telefono'   => $_POST['telefono'],
        'visi_vis_id'  => $visi_vis_id  
    ];

    $visitante->registrarVisitante($data);


    header('Location: ./views/visitas.php');
    exit;
}
