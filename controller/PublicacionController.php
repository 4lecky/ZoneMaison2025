<?php
require_once __DIR__ . '/../models/PublicacionModel.php';

class PublicacionController {
    private $model;

    public function __construct($pdo) {
        $this->model = new PublicacionModel($pdo);
    }

    public function editar($id) {
        return $this->model->obtenerPorId($id);
    }

    public function eliminar($id) {
    return $this->model->eliminar($id);
}

public function actualizar($postData) {
    $id = intval($postData['id']);
    $destinatario = $postData['destinatario'];
    $asunto = trim($postData['asunto']);
    $descripcion = trim($postData['descripcion']);
    $fecha = date('Y-m-d');
    $hora = date('H:i:s');
    $nuevaRutaImagen = null;

    // Procesar imagen nueva
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
        $imagenTmp = $_FILES['imagen']['tmp_name'];
        $nombreImagen = uniqid('img_') . "_" . basename($_FILES['imagen']['name']);
        $rutaDestino = 'uploads/muro/' . $nombreImagen;

        if (move_uploaded_file($imagenTmp, __DIR__ . '/../' . $rutaDestino)) {
            $nuevaRutaImagen = $rutaDestino;
        } else {
            return ['success' => false, 'mensaje' => "Error al guardar la imagen."];
        }
    }

    if (!empty($asunto) && !empty($descripcion) && !empty($destinatario)) {
        $actualizado = $this->model->actualizar($id, $destinatario, $asunto, $descripcion, $fecha, $hora, $nuevaRutaImagen);
        if ($actualizado) {
            header("Location: ../views/novedades.php");
            exit;
        } else {
            return ['success' => false, 'mensaje' => "No se realizaron cambios en la publicación."];
        }
    } else {
        return ['success' => false, 'mensaje' => "Por favor, complete todos los campos requeridos."];
    }
}


    public function obtenerRoles() {
        return $this->model->obtenerRolesActivos();
    }
}
