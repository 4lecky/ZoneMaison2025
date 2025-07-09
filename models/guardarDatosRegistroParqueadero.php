
<?php
class guardarDatosRegistroParqueadero
{
    private $db;

    public function __construct($conexion)
    {
        $this->db = $conexion;
    }

    public function insertarRegistroVehiculo(
        $email, $nombrePropietario, $tipoDoc, $numDoc,
        $torre, $apto, $nombrePropietarioVehiculo, $tipoDocVehiculo,
        $numDocVehiculo, $placa, $parqueadero, $fechaIngreso,
        $fechaSalida, $estadoIngreso
    ) {
        $sql = "INSERT INTO tbl_parqueadero (
                    parq_email_propietario,
                    parq_nombre_propietario,
                    parq_tipo_doc_propietario,
                    parq_num_doc_propietario,
                    parq_torre,
                    parq_apto,
                    parq_nombre_propietario_vehi,
                    parq_tipo_doc_vehi,
                    parq_num_doc_vehi,
                    parq_vehi_placa,
                    parq_num_parqueadero,
                    parq_fecha_ingreso,
                    parq_fecha_salida,
                    parq_vehi_estadiIngreso,
                    parq_vehi_alqu_id,
                    parq_usuario_cc
                ) VALUES (
                    :email,
                    :nombrePropietario,
                    :tipoDoc,
                    :numDoc,
                    :torre,
                    :apto,
                    :nombrePropVehiculo,
                    :tipoDocVehiculo,
                    :numDocVehiculo,
                    :placa,
                    :parqueadero,
                    :fechaIngreso,
                    :fechaSalida,
                    :estadoIngreso,
                    :alquId,
                    :usuarioCc
                )";

        $stmt = $this->db->prepare($sql);

        // Valores temporales mientras se conecta con los módulos de alquiler y usuario
        $alquId = 1;        // Asumido temporal, ajustar si luego enlazas con alquiler real
        $usuarioCc = 1;   // Asumido temporal, ajustar con el login del usuario

        // Enlazar parámetros
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':nombrePropietario', $nombrePropietario);
        $stmt->bindParam(':tipoDoc', $tipoDoc);
        $stmt->bindParam(':numDoc', $numDoc);
        $stmt->bindParam(':torre', $torre);
        $stmt->bindParam(':apto', $apto);
        $stmt->bindParam(':nombrePropVehiculo', $nombrePropietarioVehiculo);
        $stmt->bindParam(':tipoDocVehiculo', $tipoDocVehiculo);
        $stmt->bindParam(':numDocVehiculo', $numDocVehiculo);
        $stmt->bindParam(':placa', $placa);
        $stmt->bindParam(':parqueadero', $parqueadero);
        $stmt->bindParam(':fechaIngreso', $fechaIngreso);
        $stmt->bindParam(':fechaSalida', $fechaSalida);
        $stmt->bindParam(':estadoIngreso', $estadoIngreso);
        $stmt->bindParam(':alquId', $alquId);
        $stmt->bindParam(':usuarioCc', $usuarioCc);

        try {
            return $stmt->execute();
        } catch (PDOException $e) {
            echo "❌ Error al insertar el registro de vehículo: " . $e->getMessage();
            return false;
        }
    }
}
