
<?php
require_once '../config/db.php';
require_once '../models/eliminarUsuarioModels.php'; // Asegúrate de que el nombre y la ruta sean correctos

$usuario = new EliminarUsuarioModels($pdo);

$accion = $_GET['accion'] ?? 'listar';

switch ($accion) {
    case 'eliminar':
        if (isset($_GET['usuario_cc'])) {
            $usuario->eliminar($_GET['$usuario_cc']);
        }
        header("Location: ../controllers/EliminarUsuarioController.php");
        break;
    default:
        $usuarios = $usuario->obtenerTodos();
        include '../views/crud.php';
        break;
}