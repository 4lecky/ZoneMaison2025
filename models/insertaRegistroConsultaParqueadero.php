<?php
class insertaRegistroConsultaParqueadero {
    private $db;

    // Recibe la conexión PDO desde fuera
    public function __construct($conexion) {
        $this->db = $conexion;
    }

    // Método para insertar datos en la tabla
    public function insertarConsultaParqueadero($tipoVehiculo, $placa, $observaciones, $estadoSalida, $usuarioCedula) {
        $sql = "INSERT INTO tbl_consultaParqueadero (
                    consulParq_tipoVehiculo,
                    consulParq_placa,
                    consulParq_observaciones,
                    consulParq_estadoSalida,
                    consulParq_usuario_cedula
                ) VALUES (
                    :tipoVehiculo,
                    :placa,
                    :observaciones,
                    :estadoSalida,
                    :usuarioCedula
                )";

        $stmt = $this->db->prepare($sql);

        // Asignar parámetros
        $stmt->bindParam(':tipoVehiculo', $tipoVehiculo, PDO::PARAM_STR);
        $stmt->bindParam(':placa', $placa, PDO::PARAM_STR);
        $stmt->bindParam(':observaciones', $observaciones, PDO::PARAM_STR);
        $stmt->bindParam(':estadoSalida', $estadoSalida, PDO::PARAM_STR);
        $stmt->bindParam(':usuarioCedula', $usuarioCedula, PDO::PARAM_INT);

        try {
            return $stmt->execute();
        } catch (PDOException $e) {
            echo "❌ Error al insertar consulta de parqueadero: " . $e->getMessage();
            return false;
        }
    }
}
