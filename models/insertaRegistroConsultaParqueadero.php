<?php
class insertaRegistroConsultaParqueadero {
    private $db;

    // Recibe la conexión PDO desde fuera
    public function __construct($conexion) {
        $this->db = $conexion;
    }

    // Método para insertar datos en la tabla
    public function insertarConsultaParqueadero($tipoVehiculo, $placa, $observaciones, $numeroParqueadero, $estado) {
        $sql = "INSERT INTO tbl_consultaParqueadero (
                    consulParq_tipoVehiculo,
                    consulParq_placa,
                    consulParq_observaciones,
                    consulParq_numeroParqueadero,
                    consulParq_estado
                ) VALUES (
                    :tipoVehiculo,
                    :placa,
                    :observaciones,
                    :numeroParqueadero,
                    :estado
                )";

        $stmt = $this->db->prepare($sql);

        // Asignar parámetros
        $stmt->bindParam(':tipoVehiculo', $tipoVehiculo, PDO::PARAM_STR);
        $stmt->bindParam(':placa', $placa, PDO::PARAM_STR);
        $stmt->bindParam(':observaciones', $observaciones, PDO::PARAM_STR);
        $stmt->bindParam(':numeroParqueadero', $numeroParqueadero, PDO::PARAM_INT);
        $stmt->bindParam(':estado', $estado, PDO::PARAM_STR);

        // Manejo de errores con log
        try {
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("❌ Error al insertar consulta de parqueadero: " . $e->getMessage());
            return false;
        }
    }
}
