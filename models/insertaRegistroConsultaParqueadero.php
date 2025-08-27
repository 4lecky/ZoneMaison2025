<?php
class insertaRegistroConsultaParqueadero {
    private $db;

    // Recibe la conexión PDO desde fuera
    public function __construct($conexion) {
        $this->db = $conexion;
    }

    // Método para insertar datos en la tabla
    public function insertarConsultaParqueadero($tipoVehiculo, $placa, $observaciones, $estadoIngreso, $usuarioCedula, $numeroParqueadero, $estado) {
        $sql = "INSERT INTO tbl_consultaParqueadero (
                    consulParq_tipoVehiculo,
                    consulParq_placa,
                    consulParq_observaciones,
                    consulParq_estadoIngreso,
                    consulParq_usuario_cedula,
                    consulParq_numeroParqueadero,
                    consulParq_estado,
                    consulParq_fecha
                ) VALUES (
                    :tipoVehiculo,
                    :placa,
                    :observaciones,
                    :estadoIngreso,
                    :usuarioCedula,
                    :numeroParqueadero,
                    :estado,
                    NOW()
                )";

        $stmt = $this->db->prepare($sql);

        // Asignar parámetros
        $stmt->bindParam(':tipoVehiculo', $tipoVehiculo, PDO::PARAM_STR);
        $stmt->bindParam(':placa', $placa, PDO::PARAM_STR);
        $stmt->bindParam(':observaciones', $observaciones, PDO::PARAM_STR);
        $stmt->bindParam(':estadoIngreso', $estadoIngreso, PDO::PARAM_STR);
        $stmt->bindParam(':usuarioCedula', $usuarioCedula, PDO::PARAM_STR);
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

