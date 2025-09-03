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
        $precio = null
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
                    alqu_precio
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
                    :precio
                )";

        $stmt = $this->db->prepare($sql);

        // Vincular parámetros (manejo de NULL correctamente)
        $stmt->bindValue(':recibo', $numRecibo ?: null, PDO::PARAM_NULL | PDO::PARAM_STR);
        $stmt->bindValue(':tipoDoc', $tipoDoc);
        $stmt->bindValue(':numDoc', $numDoc);
        $stmt->bindValue(':nombrePropietario', $nombrePropietario);
        $stmt->bindValue(':torre', $torre ?: null, PDO::PARAM_NULL | PDO::PARAM_STR);
        $stmt->bindValue(':apartamento', $apartamento ?: null, PDO::PARAM_NULL | PDO::PARAM_STR);
        $stmt->bindValue(':placa', $placa);
        $stmt->bindValue(':numParqueadero', $numParqueadero ?: null, PDO::PARAM_NULL | PDO::PARAM_INT);
        $stmt->bindValue(':estadoSalida', $estadoSalida);
        $stmt->bindValue(':fechaEntrada', $fechaEntrada ?: null, PDO::PARAM_NULL | PDO::PARAM_STR);
        $stmt->bindValue(':fechaSalida', $fechaSalida ?: null, PDO::PARAM_NULL | PDO::PARAM_STR);
        $stmt->bindValue(':horaSalida', $horaSalida ?: null, PDO::PARAM_NULL | PDO::PARAM_STR);
        $stmt->bindValue(':precio', $precio ?: null, PDO::PARAM_NULL | PDO::PARAM_STR);

        try {
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("❌ Error al insertar alquiler: " . $e->getMessage());
            return false;
        }
    }
}
