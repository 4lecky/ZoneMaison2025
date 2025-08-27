<?php
class guardarDatosRegistroParqueadero {
    private $db;

    public function __construct($conexion) {
        $this->db = $conexion;
    }

    public function insertarRegistroVehiculo($placa, $nombrePropietarioVehi, $tipoDocVehi, $numDocVehi, $estadoIngreso, $alquId, $usuarioCedula, $visitaId, $fechaIngreso = null, $fechaSalida = null, $observaciones = null) {
        $sql = "INSERT INTO tbl_parqueadero (
                    parq_vehi_placa,
                    parq_nombre_propietario_vehi,
                    parq_tipo_doc_vehi,
                    parq_num_doc_vehi,
                    parq_vehi_estadoIngreso,
                    parq_vehi_alqu_id,
                    parq_usuario_cedula,
                    parq_visita_id,
                    parq_fecha_ingreso,
                    parq_fecha_salida,
                    parq_observaciones
                ) VALUES (
                    :placa,
                    :nombrePropVehiculo,
                    :tipoDocVehiculo,
                    :numDocVehiculo,
                    :estadoIngreso,
                    :alquId,
                    :usuarioCedula,
                    :visitaId,
                    :fechaIngreso,
                    :fechaSalida,
                    :observaciones
                )";

        $stmt = $this->db->prepare($sql);

        // Bind de parámetros
        $stmt->bindParam(':placa', $placa);
        $stmt->bindParam(':nombrePropVehiculo', $nombrePropietarioVehi);
        $stmt->bindParam(':tipoDocVehiculo', $tipoDocVehi);
        $stmt->bindParam(':numDocVehiculo', $numDocVehi);
        $stmt->bindParam(':estadoIngreso', $estadoIngreso);
        $stmt->bindParam(':alquId', $alquId);
        $stmt->bindParam(':usuarioCedula', $usuarioCedula);
        $stmt->bindParam(':visitaId', $visitaId);
        $stmt->bindParam(':fechaIngreso', $fechaIngreso);
        $stmt->bindParam(':fechaSalida', $fechaSalida);
        $stmt->bindParam(':observaciones', $observaciones);

        try {
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("❌ Error al insertar parqueadero: " . $e->getMessage());
            return false;
        }
    }
}
