<?php
require_once __DIR__ . '/../config/db.php';

class guardarDatosRegistroParqueadero {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function insertarRegistroVehiculo(
        $placa,
        $nombrePropietarioVehi,
        $tipoDocVehi,
        $numDocVehi,
        $estadoIngreso,
        $numeroParqueadero,
        $fechaEntrada = null,
        $fechaSalida = null,
        $horaEntrada = null
    ) {
        $sql = "INSERT INTO tbl_parqueadero (
                    parq_vehi_placa,
                    parq_nombre_propietario_vehi,
                    parq_tipo_doc_vehi,
                    parq_num_doc_vehi,
                    parq_vehi_estadoIngreso,
                    parq_numeroParqueadero,
                    parq_fecha_entrada,
                    parq_fecha_salida,
                    parq_hora_entrada
                ) VALUES (
                    :placa,
                    :nombrePropVehiculo,
                    :tipoDocVehiculo,
                    :numDocVehiculo,
                    :estadoIngreso,
                    :numeroParqueadero,
                    :fechaEntrada,
                    :fechaSalida,
                    :horaEntrada
                )";

        $stmt = $this->pdo->prepare($sql);

        // Bind de parámetros
        $stmt->bindParam(':placa', $placa);
        $stmt->bindParam(':nombrePropVehiculo', $nombrePropietarioVehi);
        $stmt->bindParam(':tipoDocVehiculo', $tipoDocVehi);
        $stmt->bindParam(':numDocVehiculo', $numDocVehi);
        $stmt->bindParam(':estadoIngreso', $estadoIngreso);
        $stmt->bindParam(':numeroParqueadero', $numeroParqueadero);
        $stmt->bindParam(':fechaEntrada', $fechaEntrada);
        $stmt->bindParam(':fechaSalida', $fechaSalida);
        $stmt->bindParam(':horaEntrada', $horaEntrada);

        try {
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log(" Error al insertar parqueadero: " . $e->getMessage());
            return false;
        }
    }
}
