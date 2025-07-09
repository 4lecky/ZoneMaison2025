<?php
require_once '../model/pqrsModel.php';

if (isset($_GET['id'])) {
    $model = new PqrsModel();
    $model->eliminar($_GET['id']);
    header('Location: ../view/listar_pqr.php');
} else {
    echo "ID no válido";
}
