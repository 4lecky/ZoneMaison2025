<?php
require_once __DIR__ . '/../models/EditPaquModel.php';

class EditPaquController {
    private $model;

    public function __construct($pdo) {
        $this->model = new EditPaquModel($pdo);
    }

    /**
     * Obtiene una publicación para editar
     */
    public function editar($id) {
        try {
            $id = intval($id);
            if ($id <= 0) {
                return false;
            }
            
            return $this->model->obtenerPorId($id);
        } catch (Exception $e) {
            error_log("Error en editar: " . $e->getMessage());
            return false;
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
            
            $resultado = $this->model->eliminar($id);
            
            if ($resultado) {
                return ['success' => true, 'mensaje' => 'Publicación eliminada correctamente'];
            } else {
                return ['success' => false, 'mensaje' => 'No se pudo eliminar la publicación o no existe'];
            }
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
            // Validar y obtener datos del POST
            if (!isset($postData['id']) || !is_numeric($postData['id'])) {
                return ['success' => false, 'mensaje' => 'ID inválido'];
            }

            $id = intval($postData['id']);
            $descripcion = isset($postData['descripcion']) ? trim($postData['descripcion']) : '';
            
            // Obtener fecha del formulario o usar la fecha actual si está vacía
            $fecha = isset($postData['fecha']) && !empty($postData['fecha']) 
                   ? trim($postData['fecha']) 
                   : date('Y-m-d');
            
            // Obtener hora del formulario o usar la hora actual si está vacía
            $hora = isset($postData['hora']) && !empty($postData['hora']) 
                  ? trim($postData['hora']) 
                  : date('H:i:s');

            // Validar ID
            if ($id <= 0) {
                return ['success' => false, 'mensaje' => 'ID inválido'];
            }

            // Validar campos requeridos
            if (empty($descripcion)) {
                return ['success' => false, 'mensaje' => 'La descripción es requerida'];
            }

            // Validar formato de fecha
            if (!$this->validarFecha($fecha)) {
                return ['success' => false, 'mensaje' => 'Formato de fecha inválido'];
            }

            // Validar formato de hora si se proporciona
            if (!empty($hora) && !$this->validarHora($hora)) {
                return ['success' => false, 'mensaje' => 'Formato de hora inválido'];
            }

            // Actualizar en la base de datos
            $actualizado = $this->model->actualizar($id, $descripcion, $fecha, $hora);
            
            if ($actualizado) {
                // Obtener los datos actualizados para devolver
                $publicacionActualizada = $this->model->obtenerPorId($id);
                return [
                    'success' => true, 
                    'mensaje' => 'Publicación actualizada correctamente',
                    'data' => $publicacionActualizada
                ];
            } else {
                return ['success' => false, 'mensaje' => 'No se realizaron cambios en la publicación'];
            }
            
        } catch (Exception $e) {
            error_log("Error en actualizar: " . $e->getMessage());
            return ['success' => false, 'mensaje' => 'Error interno del servidor'];
        }
    }

    /**
     * Valida el formato de fecha (YYYY-MM-DD)
     */
    private function validarFecha($fecha) {
        $date = DateTime::createFromFormat('Y-m-d', $fecha);
        return $date && $date->format('Y-m-d') === $fecha;
    }

    /**
     * Valida el formato de hora (HH:MM o HH:MM:SS)
     */

        private function validarHora($hora) {
            return preg_match('/^(?:[01][0-9]|2[0-3]):[0-5][0-9](?::[0-5][0-9])?$/', $hora);
        }

    /**
     * Redirige a la página de novedades
     */
    public function redirigir() {
        header("Location: ../views/novedades.php");
        exit;
    }
}
