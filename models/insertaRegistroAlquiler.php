<?php
class insertaRegistroAlquiler {
    private $db;

    public function __construct($conexion) {
        $this->db = $conexion;
    }

    public function insertarAlquiler(
        $numRecibo, $tipoDoc, $numDoc, $nombrePropietario,
        $torre, $apartamento, $placa, $numParqueadero,
        $estadoSalida, $fechaEntrada, $fechaSalida, $horaSalida, 
        $precio = null, $usuarioCedula = null, $visitaId = null
    ) {
        $sql = "INSERT INTO tbl_alquiler (
                    alqu_num_recibo,
                    alqu_tipo_doc_vehi,
                    alqu_num_doc_vehi,
                    alqu_nombre_propietario,
                    alqu_torre,
                    alqu_apartamento,
                    alqu_placa,
                    alqu_numeroParqueadero,
                    alqu_estadoSalida,
                    alqu_fecha_entrada,
                    alqu_fecha_salida,
                    alqu_hora_salida,
                    alqu_precio,
                    alqu_usuario_cedula,
                    alqu_vis_id
                ) VALUES (
                    :recibo,
                    :tipoDoc,
                    :numDoc,
                    :nombrePropietario,
                    :torre,
                    :apartamento,
                    :placa,
                    :numParqueadero,
                    :estadoSalida,
                    :fechaEntrada,
                    :fechaSalida,
                    :horaSalida,
                    :precio,
                    :usuarioCedula,
                    :visitaId
                )";

        $stmt = $this->db->prepare($sql);

        $stmt->bindParam(':recibo', $numRecibo);
        $stmt->bindParam(':tipoDoc', $tipoDoc);
        $stmt->bindParam(':numDoc', $numDoc);
        $stmt->bindParam(':nombrePropietario', $nombrePropietario);
        $stmt->bindParam(':torre', $torre);
        $stmt->bindParam(':apartamento', $apartamento);
        $stmt->bindParam(':placa', $placa);
        $stmt->bindParam(':numParqueadero', $numParqueadero);
        $stmt->bindParam(':estadoSalida', $estadoSalida);
        $stmt->bindParam(':fechaEntrada', $fechaEntrada);
        $stmt->bindParam(':fechaSalida', $fechaSalida);
        $stmt->bindParam(':horaSalida', $horaSalida);
        $stmt->bindParam(':precio', $precio);
        $stmt->bindParam(':usuarioCedula', $usuarioCedula);
        $stmt->bindParam(':visitaId', $visitaId);

        try {
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("❌ Error al insertar alquiler: " . $e->getMessage());
            return false;
        }
    }
}
