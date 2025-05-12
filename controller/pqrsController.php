<?php
require_once '../model/pqrsModel.php';

// Establecer encabezados para manejar respuestas JSON
header('Content-Type: application/json');

$response = [
    'status' => 'error',
    'message' => 'Operación no realizada'
];

$pqrs = new PqrsModel();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $archivoNombre = '';

    // Verificar si se subió un archivo
    if (isset($_FILES['archivos']) && $_FILES['archivos']['error'] === UPLOAD_ERR_OK) {
        $directorio = '../uploads/';
        
        // Crear directorio si no existe
        if (!is_dir($directorio)) {
            mkdir($directorio, 0777, true);
        }

        $archivoNombre = uniqid() . '_' . basename($_FILES['archivos']['name']);
        $rutaDestino = $directorio . $archivoNombre;

        // Mover el archivo al directorio de uploads
        if (!move_uploaded_file($_FILES['archivos']['tmp_name'], $rutaDestino)) {
            $response['message'] = 'Error al subir el archivo.';
            echo json_encode($response);
            exit;
        }
    }

    // Verificar que 'respuesta' esté como array
    if (isset($_POST['respuesta'])) {
        $medio_respuesta = implode(',', $_POST['respuesta']);
    } else {
        $medio_respuesta = ''; // O asignar un valor predeterminado
    }

    // Recoger los datos del formulario
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
        // Verificar si es una actualización o un nuevo registro
        if (isset($_POST['id'])) {
            $id = $_POST['id'];
            $pqrs->actualizar($id, $datos);
            $response['status'] = 'success';
            $response['message'] = 'Registro actualizado correctamente';
            $response['redirect'] = 'listar_pqr.php?editado=1';
        } else {
            $pqrs->registrar($datos);
            $response['status'] = 'success';
            $response['message'] = 'Registro creado correctamente';
            $response['redirect'] = 'crear_pqr.php?exito=1';
        }
    } catch (Exception $e) {
        $response['message'] = 'Error en el registro: ' . $e->getMessage();
    }
} elseif (isset($_GET['eliminar'])) {
    try {
        $id = $_GET['eliminar'];
        $pqrs->eliminar($id);
        $response['status'] = 'success';
        $response['message'] = 'Registro eliminado correctamente';
        $response['redirect'] = 'listar_pqr.php?eliminado=1';
    } catch (Exception $e) {
        $response['message'] = 'Error al eliminar: ' . $e->getMessage();
    }
}

// Devolver respuesta JSON
echo json_encode($response);
exit;