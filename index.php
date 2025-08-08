<?php
session_start();

// Conexión a la base de datos
require_once __DIR__ . '/config/db.php';

// Autocargar modelos y controladores
spl_autoload_register(function ($class) {
    if (file_exists(__DIR__ . "/models/{$class}.php")) {
        require_once __DIR__ . "/models/{$class}.php";
    } elseif (file_exists(__DIR__ . "/controller/{$class}.php")) {//controller
        require_once __DIR__ . "/controller/{$class}.php";
    }
});

// Obtener parámetros de URL
if (!isset($_GET['controller']) && !isset($_GET['action'])) {
    // Entra aquí cuando no hay nada en la URL
    header("Location: views/home.php");
    exit();
}

$controller = $_GET['controller'] . 'Controller';
$action = $_GET['action'];


// echo "Buscando clase: $controller<br>";
// var_dump(get_declared_classes());

// Verificar si el controlador existe
if (!class_exists($controller)) {
    die("Controlador no encontrado: $controller");
}

$objController = new $controller($pdo); // $pdo viene de config/db.php

// Verificar si la acción existe en el controlador
if (!method_exists($objController, $action)) {
    die("Método no encontrado: $action en $controller");
}

// Llamar a la acción
$objController->$action();
