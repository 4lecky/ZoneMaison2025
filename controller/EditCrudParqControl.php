<?php
require_once __DIR__ . "/../models/EditCrudParqModel.php";

class EditCrudParqControl {
    private $model;

    public function __construct() {
        $this->model = new EditCrudParqModel();
    }

    // Cargar datos en el formulario
    public function editar($id) {
        return $this->model->obtenerParqueadero($id);
    }

    // Guardar cambios - CORREGIDO
    public function actualizar($data) {
        try {
            $success = $this->model->actualizarParqueadero($data);
            
            if ($success) {
                return [
                    'success' => true,
                    'message' => "✅ Parqueadero actualizado correctamente."
                ];
            } else {
                return [
                    'success' => false,
                    'message' => "❌ Error al actualizar el parqueadero en la base de datos."
                ];
            }
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => "❌ Error: " . $e->getMessage()
            ];
        }
    }

    // Eliminar parqueadero
    public function eliminar($id) {
        $success = $this->model->eliminarParqueadero($id);
        if ($success) {
            return [
                'status' => 'success',
                'message' => "Parqueadero con ID $id eliminado correctamente."
            ];
        } else {
            return [
                'status' => 'error',
                'message' => "No se pudo eliminar el parqueadero con ID $id."
            ];
        }
    }
}