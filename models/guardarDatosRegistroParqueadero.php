<?php
class guardarDatosRegistroParqueadero {
    private $db;

    public function __construct($conexion) {
        $this->db = $conexion;
    }

    public function insertarRegistroVehiculo($placa, $nombrePropietarioVehi, $tipoDocVehi, $numDocVehi, $estadoIngreso, $alquId, $usuarioCedula, $visitaId) {
        $sql = "INSERT INTO tbl_parqueadero (
                    parq_vehi_placa,
                    parq_nombre_propietario_vehi,
                    parq_tipo_doc_vehi,
                    parq_num_doc_vehi,
                    parq_vehi_estadiIngreso,
                    parq_vehi_alqu_id,
                    parq_usuario_cedula,
                    parq_visita_id
                ) VALUES (
                    :placa,
                    :nombrePropVehiculo,
                    :tipoDocVehiculo,
                    :numDocVehiculo,
                    :estadoIngreso,
                    :alquId,
                    :usuarioCedula,
                    :visitaId
                )";

        $stmt = $this->db->prepare($sql);

        $stmt->bindParam(':placa', $placa);
        $stmt->bindParam(':nombrePropVehiculo', $nombrePropietarioVehi);
        $stmt->bindParam(':tipoDocVehiculo', $tipoDocVehi);
        $stmt->bindParam(':numDocVehiculo', $numDocVehi);
        $stmt->bindParam(':estadoIngreso', $estadoIngreso);
        $stmt->bindParam(':alquId', $alquId);
        $stmt->bindParam(':usuarioCedula', $usuarioCedula);
        $stmt->bindParam(':visitaId', $visitaId);

        try {
            return $stmt->execute();
        } catch (PDOException $e) {
            echo "❌ Error al insertar parqueadero: " . $e->getMessage();
            return false;
        }
    }
}

