<?php
require_once __DIR__ . "/../config/db.php";

class EditCrudVisiModel {

    private $pdo;

    public function __construct() {
        $this->pdo = $GLOBALS['pdo'];
    }

    // Obtener visita por id
    public function obtenerVisita($id) {
        $sql = "SELECT * FROM tbl_visita WHERE vis_id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    // Actualizar visita
    public function actualizarVisita($data) {
        $sql = "UPDATE tbl_visita 
                SET vis_fecha_entrada = :fecha_entrada,
                    vis_fecha_salida = :fecha_salida,
                    vis_hora_entrada = :hora_entrada,
                    vis_hora_salida = :hora_salida
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
}
