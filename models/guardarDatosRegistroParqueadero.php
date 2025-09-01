<?php
class guardarDatosRegistroParqueadero {
    private $db;

    public function __construct($conexion) {
        $this->db = $conexion;
    }

    public function insertarRegistroVehiculo(
        $placa,
        $nombrePropietarioVehi,
        $tipoDocVehi,
        $numDocVehi,
        $estadoIngreso,
        $alquId,
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
                    parq_vehi_alqu_id,
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
                    :alquId,
                    :numeroParqueadero,
                    :fechaEntrada,
                    :fechaSalida,
                    :horaEntrada
                )";

        $stmt = $this->db->prepare($sql);

        // Bind de parámetros
        $stmt->bindParam(':placa', $placa);
        $stmt->bindParam(':nombrePropVehiculo', $nombrePropietarioVehi);
        $stmt->bindParam(':tipoDocVehiculo', $tipoDocVehi);
        $stmt->bindParam(':numDocVehiculo', $numDocVehi);
        $stmt->bindParam(':estadoIngreso', $estadoIngreso);
        $stmt->bindParam(':alquId', $alquId);
        $stmt->bindParam(':numeroParqueadero', $numeroParqueadero);
        $stmt->bindParam(':fechaEntrada', $fechaEntrada);
        $stmt->bindParam(':fechaSalida', $fechaSalida);
        $stmt->bindParam(':horaEntrada', $horaEntrada);

        try {
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("❌ Error al insertar parqueadero: " . $e->getMessage());
            return false;
        }
    }
}
