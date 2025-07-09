<?php
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/controller/ZonaController.php';

$controller = new ZonaController();

// ruta: zonas.php?action=crear
$action = $_GET['action'] ?? 'index';
$id = $_GET['id'] ?? null;

switch ($action) {
    case 'crear':
        $controller->crear();
        break;
    case 'editar':
        if ($id) {
            $controller->editar($id);
        }
        break;
    case 'eliminar':
        if ($id) {
            $controller->eliminar($id);
        }
        break;
    default:
        $controller->index();
        break;
}
