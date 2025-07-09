<?php
require_once '../models/pqrsModel.php';

$pqrs = new PqrsModel();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $archivoNombre = '';

    if (isset($_FILES['archivos']) && $_FILES['archivos']['error'] === UPLOAD_ERR_OK) {
        $directorio = '../uploads/';
        if (!is_dir($directorio)) {
            mkdir($directorio, 0777, true);
        }

        $archivoNombre = uniqid() . '_' . basename($_FILES['archivos']['name']);
        $rutaDestino = $directorio . $archivoNombre;

        if (!move_uploaded_file($_FILES['archivos']['tmp_name'], $rutaDestino)) {
            echo "<p>Error al subir el archivo.</p>";
            exit;
        }
    }

    $medio_respuesta = isset($_POST['respuesta']) ? implode(',', $_POST['respuesta']) : '';

    $datos = [
        'nombres' => $_POST['nombres'],
        'apellidos' => $_POST['apellidos'],
        'identificacion' => $_POST['identificacion'],
        'email' => $_POST['email'],
        'telefono' => $_POST['telefono'],
        'tipo_pqr' => $_POST['tipo_pqr'],
        'asunto' => $_POST['asunto'],
        'mensaje' => $_POST['mensaje'],
        'archivos' => $archivoNombre,
        'medio_respuesta' => $medio_respuesta
    ];

    try {
        if (isset($_POST['id'])) {
            $pqrs->actualizar($_POST['id'], $datos);
            echo "<p>Actualizado con éxito</p>";
        } else {
            $resultado = $pqrs->registrar($datos);

            if ($resultado) {
                echo "OK";
            } else {
                echo "ERROR_BD";
            }
        }
        exit;
    } catch (Exception $e) {
        echo "<h3>Error de excepción:</h3>";
        echo "<pre>" . $e->getMessage() . "</pre>";
        exit;
    }
} 

// Bloque de eliminar
if (isset($_GET['eliminar'])) {
    try {
        $pqrs->eliminar($_GET['eliminar']);
        echo "OK";
        exit;
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
        exit;
    }
}
