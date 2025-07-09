
<?php
require_once '../config/db.php';
require_once '../models/eliminarUsuarioModels.php'; // Asegúrate de que el nombre y la ruta sean correctos
session_start();

//Llamamos la clase del momedo
$usuario = new EliminarUsuarioModels($pdo);

if (isset($_GET['cc'])) {
    $cc = $_GET['cc']; // Aquí se obtiene el valor pasado en la URL

    // Llamar al método eliminar del modelo y pasarle el valor de cc
    if ($usuario->eliminar($cc)) {
        $_SESSION['mensaje'] = [
            'tipo' => 'success',
            'texto' => 'Usuario inactivado correctamente.'
        ];
    } else {
        $_SESSION['mensaje'] = [
            'tipo' => 'danger',
            'texto' => 'Error al inactivar el usuario.'
        ];
    }

    // $usuarios = $usuario->ordenar($cc);
    
    // Redireccionar a la lista de usuarios
    header("Location: ../views/crud.php");
    exit;
}

