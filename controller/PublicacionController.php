<?php
require_once __DIR__ . '/../models/PublicacionModel.php';

class PublicacionController {
    private $model;

    public function __construct($pdo) {
        $this->model = new PublicacionModel($pdo);
    }

    /**
     * Obtiene una publicación por ID para editarla
     */
    public function editar($id) {
        try {
            $id = intval($id);
            if ($id <= 0) {
                return ['success' => false, 'mensaje' => 'ID inválido'];
            }
            
            return $this->model->obtenerPorId($id);
        } catch (Exception $e) {
            error_log("Error en editar: " . $e->getMessage());
            return ['success' => false, 'mensaje' => 'Error al obtener la publicación'];
        }
    }

    /**
     * Elimina una publicación
     */
    public function eliminar($id) {
        try {
            $id = intval($id);
            if ($id <= 0) {
                return ['success' => false, 'mensaje' => 'ID inválido'];
            }
            
            // Obtener la publicación antes de eliminarla para borrar la imagen
            $publicacion = $this->model->obtenerPorId($id);
            if ($publicacion && !empty($publicacion['imagen'])) {
                $rutaImagen = __DIR__ . '/../' . $publicacion['imagen'];
                if (file_exists($rutaImagen)) {
                    unlink($rutaImagen);
                }
            }
            
            $resultado = $this->model->eliminar($id);
            return ['success' => $resultado, 'mensaje' => $resultado ? 'Publicación eliminada' : 'Error al eliminar'];
        } catch (Exception $e) {
            error_log("Error en eliminar: " . $e->getMessage());
            return ['success' => false, 'mensaje' => 'Error al eliminar la publicación'];
        }
    }

    /**
     * Actualiza una publicación
     */
    public function actualizar($postData) {
        try {
            // Validar datos de entrada
            if (!isset($postData['id']) || !is_numeric($postData['id'])) {
                return ['success' => false, 'mensaje' => 'ID inválido'];
            }

            $id = intval($postData['id']);
            $destinatario = isset($postData['destinatario']) ? trim($postData['destinatario']) : '';
            $asunto = isset($postData['asunto']) ? trim($postData['asunto']) : '';
            $descripcion = isset($postData['descripcion']) ? trim($postData['descripcion']) : '';
            $fecha = date('Y-m-d');
            $hora = date('H:i:s');
            $nuevaRutaImagen = null;

            // Validar campos requeridos
            if (empty($asunto) || empty($descripcion) || empty($destinatario)) {
                return ['success' => false, 'mensaje' => 'Por favor, complete todos los campos requeridos.'];
            }

            // Procesar imagen nueva
            if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
                $resultado = $this->procesarImagen($_FILES['imagen']);
                if (!$resultado['success']) {
                    return $resultado;
                }
                $nuevaRutaImagen = $resultado['ruta'];
            }

            // Actualizar en la base de datos
            $actualizado = $this->model->actualizar($id, $destinatario, $asunto, $descripcion, $fecha, $hora, $nuevaRutaImagen);
            
            if ($actualizado) {
                // Eliminar imagen antigua si se subió una nueva
                if ($nuevaRutaImagen) {
                    $this->eliminarImagenAnterior($id);
                }
                
                return ['success' => true, 'mensaje' => 'Publicación actualizada correctamente'];
            } else {
                return ['success' => false, 'mensaje' => 'No se realizaron cambios en la publicación.'];
            }
            
        } catch (Exception $e) {
            error_log("Error en actualizar: " . $e->getMessage());
            return ['success' => false, 'mensaje' => 'Error interno del servidor'];
        }
    }

    /**
     * Procesa la imagen subida
     */
    private function procesarImagen($archivo) {
        // Validar archivo
        $tiposPermitidos = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        if (!in_array($archivo['type'], $tiposPermitidos)) {
            return ['success' => false, 'mensaje' => 'Tipo de archivo no permitido. Solo se permiten imágenes.'];
        }

        // Validar tamaño (máximo 5MB)
        $tamañoMaximo = 5 * 1024 * 1024; // 5MB en bytes
        if ($archivo['size'] > $tamañoMaximo) {
            return ['success' => false, 'mensaje' => 'El archivo es demasiado grande. Máximo 5MB.'];
        }

        // Crear directorio si no existe
        $directorioDestino = __DIR__ . '/../uploads/muro/';
        if (!is_dir($directorioDestino)) {
            if (!mkdir($directorioDestino, 0755, true)) {
                return ['success' => false, 'mensaje' => 'Error al crear directorio de destino.'];
            }
        }

        // Generar nombre único y mover archivo
        $extension = pathinfo($archivo['name'], PATHINFO_EXTENSION);
        $nombreImagen = uniqid('img_') . '_' . time() . '.' . $extension;
        $rutaDestino = 'uploads/muro/' . $nombreImagen;
        $rutaCompleta = $directorioDestino . $nombreImagen;

        if (move_uploaded_file($archivo['tmp_name'], $rutaCompleta)) {
            return ['success' => true, 'ruta' => $rutaDestino];
        } else {
            return ['success' => false, 'mensaje' => 'Error al guardar la imagen.'];
        }
    }

    /**
     * Elimina la imagen anterior de una publicación
     */
    private function eliminarImagenAnterior($id) {
        try {
            $publicacion = $this->model->obtenerPorId($id);
            if ($publicacion && !empty($publicacion['imagen'])) {
                $rutaImagen = __DIR__ . '/../' . $publicacion['imagen'];
                if (file_exists($rutaImagen)) {
                    unlink($rutaImagen);
                }
            }
        } catch (Exception $e) {
            error_log("Error al eliminar imagen anterior: " . $e->getMessage());
        }
    }

    /**
     * Redirige a la página de novedades
     */
    public function redirigir() {
        header("Location: ../views/novedades.php");
        exit;
    }

    /**
     * Obtiene los roles activos
     */
    public function obtenerRoles() {
        try {
            return $this->model->obtenerRolesActivos();
        } catch (Exception $e) {
            error_log("Error al obtener roles: " . $e->getMessage());
            return [];
        }
    }
}
