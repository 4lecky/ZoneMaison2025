<?php
require_once "../../guardarDatosRegistroParqueadero.php"; // conexión PDO

if (isset($_GET['id'])) {
    $id = intval($_GET['id']); // sanitizar el id

    try {
        $stmt = $pdo->prepare("DELETE FROM tbl_parqueadero WHERE parq_id = ?");
        $resultado = $stmt->execute([$id]);

        if ($resultado) {
            // ✅ Eliminado con éxito
            header("Location: ../parqueadero.php?msg=eliminado");
            exit;
        } else {
            // ❌ Error al eliminar
            header("Location: ../parqueadero.php?msg=error");
            exit;
        }
    } catch (PDOException $e) {
        error_log("Error al eliminar parqueadero: " . $e->getMessage());
        header("Location: ../parqueadero.php?msg=error");
        exit;
    }
} else {
    header("Location: ../parqueadero.php?msg=sin_id");
    exit;
}
