<?php
class guardarDatosAlquiler
{
    private $db;

    public function __construct($conexion)
    {
        $this->db = $conexion;
    }

    public function insertarAlquiler(
        $numRecibo, $nombre, $tipoDoc, $numDoc,
        $torre, $apartamento, $placa, $parqueadero,
        $observaciones, $fechaIngreso, $fechaSalida,
        $horaIngreso, $horaSalida, $costo
    ) {
        $sql = "INSERT INTO tbl_alquiler (
                    alqu_num_recibo,
                    alqu_nombre_propietario,
                    alqu_tipo_doc_propietario,
                    alqu_num_doc_propietario,
                    alqu_torre,
                    alqu_apto,
                    alqu_placa,
                    alqu_num_parqueadero,
                    alqu_observaciones,
                    alqu_fechaIngreso,
                    alqu_fechaSalida,
                    alqu_horaIngreso,
                    alqu_horaSalida,
                    alqu_precio,
                    alqu_impuesto,
                    alqu_vis_id
                ) VALUES (
                    :recibo,
                    :nombre,
                    :tipoDoc,
                    :numDoc,
                    :torre,
                    :apartamento,
                    :placa,
                    :parqueadero,
                    :observaciones,
                    :fechaIngreso,
                    :fechaSalida,
                    :horaIngreso,
                    :horaSalida,
                    :costo,
                    :impuesto,
                    :visitaId
                )";

        $stmt = $this->db->prepare($sql);

        // Valores por defecto para campos no enviados desde el formulario
        $impuesto = 0;     // Puedes cambiar esto si luego lo calculas
        $visitaId = 1;     // Temporal, hasta que lo relaciones con una visita real

        // Asignación de parámetros
        $stmt->bindParam(':recibo', $numRecibo);
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':tipoDoc', $tipoDoc);
        $stmt->bindParam(':numDoc', $numDoc);
        $stmt->bindParam(':torre', $torre);
        $stmt->bindParam(':apartamento', $apartamento);
        $stmt->bindParam(':placa', $placa);
        $stmt->bindParam(':parqueadero', $parqueadero);
        $stmt->bindParam(':observaciones', $observaciones);
        $stmt->bindParam(':fechaIngreso', $fechaIngreso);
        $stmt->bindParam(':fechaSalida', $fechaSalida);
        $stmt->bindParam(':horaIngreso', $horaIngreso);
        $stmt->bindParam(':horaSalida', $horaSalida);
        $stmt->bindParam(':costo', $costo);
        $stmt->bindParam(':impuesto', $impuesto);
        $stmt->bindParam(':visitaId', $visitaId);

        try {
            return $stmt->execute();
        } catch (PDOException $e) {
            echo "❌ Error al insertar: " . $e->getMessage();
            return false;
        }
    }
}