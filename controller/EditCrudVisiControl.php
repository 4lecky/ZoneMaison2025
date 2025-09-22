<?php
require_once __DIR__ . "/../models/EditCrudVisiModel.php";

class EditCrudVisiControl {

    private $model;

    public function __construct() {
        $this->model = new EditCrudVisiModel();
    }

    // Cargar datos en el formulario
    public function editar($id) {
        return $this->model->obtenerVisita($id);
    }

    // Guardar cambios
    public function actualizar($data) {
        return $this->model->actualizarVisita($data);

        if ($resultado) {
        header("Location: ../views/visitas.php?msg=editado");
        exit;
    } else {
        header("Location: ../views/visitas.php?msg=error");
        exit;
        }
    }
}