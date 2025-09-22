<?php
require_once "../../models/EditCrudVisiModel.php"; // ajusta la ruta según tu proyecto

if (isset($_GET['id'])) {
    $id = intval($_GET['id']); // sanitizar

    $modelo = new EditCrudVisiModel();
    $resultado = $modelo->eliminarVisita($id);
    $resultado = $modelo->eliminarVisitante($id); // ✅ ahora borra visitante

    if ($resultado) {
        // ✅ Eliminado con éxito
        header("Location: ../visitas.php?msg=eliminado");
        exit;
    } else {
        // ❌ Error al eliminar
        header("Location: ../visitas.php?msg=error");
        exit;
    }
} else {
    header("Location: ../visitas.php?msg=sin_id");
    exit;
}
