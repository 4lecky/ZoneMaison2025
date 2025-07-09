<?php
// index.php - Router principal
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Iniciar sesión
session_start();

// Incluir configuración de base de datos
require_once 'config/db.php';

// Obtener parámetros de la URL
$controller = $_GET['controller'] ?? 'zona';
$action = $_GET['action'] ?? 'index';
$id = $_GET['id'] ?? null;

// Validar controlador
$allowedControllers = ['zona'];
if (!in_array($controller, $allowedControllers)) {
    $controller = 'zona';
}

// Validar acción
$allowedActions = ['index', 'crear', 'editar', 'eliminar'];
if (!in_array($action, $allowedActions)) {
    $action = 'index';
}

// Construir nombre del controlador
$controllerName = ucfirst($controller) . 'Controller';
$controllerFile = "controllers/{$controllerName}.php";

// Verificar que el archivo del controlador existe
if (file_exists($controllerFile)) {
    require_once $controllerFile;
    
    // Crear instancia del controlador
    if (class_exists($controllerName)) {
        $controllerInstance = new $controllerName();
        
        // Verificar que el método existe
        if (method_exists($controllerInstance, $action)) {
            // Ejecutar la acción
            if ($id !== null) {
                $controllerInstance->$action($id);
            } else {
                $controllerInstance->$action();
            }
        } else {
            // Método no encontrado, redirigir a index
            header('Location: index.php?controller=zona&action=index');
            exit;
        }
    } else {
        // Clase no encontrada
        die("Error: Clase del controlador no encontrada");
    }
} else {
    // Archivo del controlador no encontrado
    die("Error: Controlador no encontrado");
}
?>