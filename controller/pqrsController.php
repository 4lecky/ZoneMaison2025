<?php
require_once '../models/pqrsModel.php';

$pqrs = new PqrsModel();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $archivoNombre = '';

    // Subida de archivo
    if (isset($_FILES['archivos']) && $_FILES['archivos']['error'] === UPLOAD_ERR_OK) {
        $directorio = '../uploads/';
        if (!is_dir($directorio)) {
            mkdir($directorio, 0777, true);
        }

        $archivoNombre = uniqid() . '_' . basename($_FILES['archivos']['name']);
        $rutaDestino = $directorio . $archivoNombre;

        if (!move_uploaded_file($_FILES['archivos']['tmp_name'], $rutaDestino)) {
            header("Location: ../views/crear_pqr.php?error=archivo");
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
            header("Location: ../views/pqrs.php?editado=1");
        } else {
            $pqrs->registrar($datos);
            header("Location: ../views/crear_pqr.php?exito=1");
        }
        exit;
    } catch (Exception $e) {
        header("Location: ../views/crear_pqr.php?error=bd");
        exit;
    }
}

if (isset($_GET['eliminar'])) {
    try {
        $pqrs->eliminar($_GET['eliminar']);
        header("Location: ../views/pqrs.php?eliminado=1");
        exit;
    } catch (Exception $e) {
        header("Location: ../views/pqrs.php?error=eliminando");
        exit;
    }
}
