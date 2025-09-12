<?php
require_once __DIR__ . "/../config/db.php";

class EditCrudVisiModel {

    private $pdo;

    public function __construct() {
        $this->pdo = $GLOBALS['pdo'];
    }

    // Obtener solo la visita por ID
    public function obtenerVisita($id) {
        $sql = "SELECT vis_id, vis_fecha_entrada, vis_fecha_salida, vis_hora_entrada, vis_hora_salida
                FROM tbl_visita 
                WHERE vis_id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    // Actualizar solo la visita
    public function actualizarVisita($data) {
        $sql = "UPDATE tbl_visita 
                SET vis_fecha_entrada = :fecha_entrada,
                    vis_fecha_salida  = :fecha_salida,
                    vis_hora_entrada  = :hora_entrada,
                    vis_hora_salida   = :hora_salida
                WHERE vis_id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':fecha_entrada' => $data['fecha_entrada'],
            ':fecha_salida'  => $data['fecha_salida'],
            ':hora_entrada'  => $data['hora_entrada'],
            ':hora_salida'   => $data['hora_salida'],
            ':id'            => $data['id']
        ]);
    }
    // ✅ Eliminar visitante y automáticamente visitas asociadas (ON DELETE CASCADE)
    public function eliminarVisitante($id) {
        $sql = "DELETE FROM tbl_visitante WHERE visi_id = :id";
        $stmt = $this->pdo->prepare($sql);

        if ($stmt->execute([":id" => $id])) {
            if ($stmt->rowCount() > 0) {
                echo "✅ Se eliminó el visitante con ID: " . $id . " y sus visitas asociadas.";
                return true;
            } else {
                echo "⚠️ No se encontró ningún visitante con visi_id = " . $id;
                return false;
            }
        } else {
            echo "❌ Error en DELETE: ";
            print_r($stmt->errorInfo());
            return false;
        }
    }

}
