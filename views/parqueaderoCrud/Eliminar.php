<?php
require_once "../../models/EditCrudParqModel.php"; // ajusta la ruta según tu proyecto

if (isset($_GET['id'])) {
    $id = intval($_GET['id']); // sanitizar

    $modelo = new EditCrudParqModel();
    $resultado = $modelo->eliminarParqueadero($id); // ✅ método adaptado para parqueaderos

    if ($resultado) {
        // ✅ Eliminado con éxito
        header("Location: ../parqueadero.php?msg=eliminado");
        exit;
    } else {
        // ❌ Error al eliminar
        header("Location: ../parqueadero.php?msg=error");
        exit;
    }
} else {
    header("Location: ../parqueadero.php?msg=sin_id");
    exit;
}