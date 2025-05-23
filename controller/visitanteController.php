<?php
session_start();

require_once __DIR__."../config/db.php";          
require_once __DIR__.'../models/visitante.php'; 

$visitante = new Visitante($pdo);

// Registro
if (isset($_POST['registrarvisitante'])) {


    $visita_id = $_POST['visita_id'] ?? null;

    if (!$visita_id) {
        
        die("Error: No se recibió ID de la visita.");
    }

    $data = [
        'nombre'     => $_POST['nombre'],
        'tipoDoc'    => $_POST['tipoDoc'],
        'documento'  => $_POST['documento'],
        'email'      => $_POST['email'],
        'telefono'   => $_POST['telefono'],
        'visita_id'  => $visita_id 
    ];

    $visitante->registrarVisitante($data);


    header('Location: ./views/visitas.php');
    exit;
}
