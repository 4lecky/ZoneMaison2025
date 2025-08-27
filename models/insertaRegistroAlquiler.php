<?php
class insertaRegistroAlquiler {
    private $db;

    public function __construct($conexion) {
        $this->db = $conexion;
    }

    public function insertarAlquiler($numRecibo, $observaciones, $precio, $visitaId, $placa, $usuarioCedula) {
        $sql = "INSERT INTO tbl_alquiler (
                    alqu_num_recibo,
                    alqu_observaciones,
                    alqu_precio,
                    alqu_vis_id,
                    alqu_placa,
                    alqu_usuario_cedula
                ) VALUES (
                    :recibo,
                    :observaciones,
                    :precio,
                    :visitaId,
                    :placa,
                    :usuarioCedula
                )";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':recibo', $numRecibo);
        $stmt->bindParam(':observaciones', $observaciones);
        $stmt->bindParam(':precio', $precio);
        $stmt->bindParam(':visitaId', $visitaId);
        $stmt->bindParam(':placa', $placa);
        $stmt->bindParam(':usuarioCedula', $usuarioCedula);

        try {
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("❌ Error al insertar alquiler: " . $e->getMessage());
            return false;
        }
    }
}
