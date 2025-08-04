<?php
require_once __DIR__ . '/../models/EditPaquModel.php';

class EditPaquController {
    private $model;

    public function __construct($pdo) {
        $this->model = new EditPaquModel($pdo);
    }

    /**
     * Obtiene una publicación por ID para editarla
     */
    public function editar($id) {
        try {
            $id = intval($id);
            if ($id <= 0) {
                return null; // Cambio: retorna null para consistencia con la vista
            }
            
            return $this->model->obtenerPorId($id);
        } catch (Exception $e) {
            error_log("Error en editar: " . $e->getMessage());
            return null;
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
            if ($publicacion && !empty($publicacion['paqu_image'])) {
                $rutaImagen = __DIR__ . '/../' . $publicacion['paqu_image'];
                if (file_exists($rutaImagen)) {
                    unlink($rutaImagen);
                }
            }
            
            $resultado = $this->model->eliminar($id);
            return $resultado; // El modelo ya retorna el formato correcto
        } catch (Exception $e) {
            error_log("Error en eliminar: " . $e->getMessage());
            return ['success' => false, 'mensaje' => 'Error al eliminar la publicación'];
        }
    }

    /**
     * Actualiza una publicación - CORREGIDO
     */
    public function actualizar($postData) {
        try {
            // Validar datos de entrada
            if (!isset($postData['id']) || !is_numeric($postData['id'])) {
                return ['success' => false, 'mensaje' => 'ID inválido'];
            }

            $id = intval($postData['id']);
            $descripcion = isset($postData['descripcion']) ? trim($postData['descripcion']) : '';
            $estado = isset($postData['estado']) ? trim($postData['estado']) : '';
            $fecha = date('Y-m-d');
            $hora = date('H:i:s');
            $image = null; // Inicializar correctamente

            // Validar campos requeridos
            if (empty($descripcion) || empty($estado)) {
                return ['success' => false, 'mensaje' => 'Por favor, complete todos los campos requeridos.'];
            }

            // CORRECCIÓN: Procesar imagen nueva correctamente
            if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
                $resultado = $this->procesarImagen($_FILES['imagen']);
                if (!$resultado['success']) {
                    return $resultado;
                }
                $image = $resultado['ruta']; // ✅ CORRECCIÓN: Asignar la ruta a $image
            }

            // Actualizar en la base de datos - PASAR $image CORRECTAMENTE
            $actualizado = $this->model->actualizar($id, $descripcion, $fecha, $hora, $estado, $image);
            
            if ($actualizado) {
                // Obtener los datos actualizados para mostrar
                $publicacion = $this->model->obtenerPorId($id);
                return [
                    'success' => true, 
                    'mensaje' => 'Publicación actualizada correctamente',
                    'data' => $publicacion
                ];
            } else {
                return ['success' => false, 'mensaje' => 'No se realizaron cambios en la publicación.'];
            }
            
        } catch (Exception $e) {
            error_log("Error en actualizar: " . $e->getMessage());
            return ['success' => false, 'mensaje' => 'Error interno del servidor'];
        }
    }

    /**
     * Procesa la imagen subida - MEJORADO
     */
    private function procesarImagen($archivo) {
        // Validar tipo MIME
        $tiposPermitidos = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        if (!in_array($archivo['type'], $tiposPermitidos)) {
            return ['success' => false, 'mensaje' => 'Tipo de archivo no permitido. Solo se permiten imágenes.'];
        }

        // MEJORA: Validar también la extensión real
        $extension = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));
        $extensionesPermitidas = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        if (!in_array($extension, $extensionesPermitidas)) {
            return ['success' => false, 'mensaje' => 'Extensión de archivo no permitida.'];
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
            if ($publicacion && !empty($publicacion['paqu_image'])) {
                $rutaImagen = __DIR__ . '/../' . $publicacion['paqu_image'];
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
