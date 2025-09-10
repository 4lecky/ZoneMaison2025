<?php

require_once("../config/db.php"); 

class insertaRegistroConsultaParqueadero {
    private $pdo;

    // Recibe la conexión PDO desde fuera
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // Método para insertar datos en la tabla
    public function insertarConsultaParqueadero($tipoVehiculo, $placa, $observaciones, $numeroParqueadero, $estado) {
        //ATRIBUTOS DE LAS TABLAS EN MYSQL
        $sql = "INSERT INTO tbl_consultaParqueadero (
                    consulParq_tipoVehiculo,
                    consulParq_placa,
                    consulParq_observaciones,
                    consulParq_numeroParqueadero,   
                    consulParq_estado
                ) VALUES (
                    :consulParq_tipoVehiculo,
                    :consulParq_placa,
                    :consulParq_observaciones,
                    :consulParq_numeroParqueadero,
                    :consulParq_estado
                )";
                //NAMES DE LOS FORMULARIOS

        $stmt = $this->pdo->prepare($sql);

        // Asignar parámetros
        $stmt->bindParam(':consulParq_tipoVehiculo', $tipoVehiculo, PDO::PARAM_STR);
        $stmt->bindParam(':consulParq_placa', $placa, PDO::PARAM_STR);
        $stmt->bindParam(':consulParq_observaciones', $observaciones, PDO::PARAM_STR);
        $stmt->bindParam(':consulParq_numeroParqueadero', $numeroParqueadero, PDO::PARAM_INT);
        $stmt->bindParam(':consulParq_estado', $estado, PDO::PARAM_STR);

        // Manejo de errores con log
        try {
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("❌ Error al insertar consulta de parqueadero: " . $e->getMessage());
            return false;
        }

        if (!$stmt->execute()) {
            $error = $stmt->errorInfo();
            echo "<pre>❌ Error SQL:\n" . print_r($error, true) . "</pre>";
            return false;
        }

    }
}
