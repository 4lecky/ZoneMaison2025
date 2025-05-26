<?php
class muroModel
{
    private $db;

    public function __construct($conexion)
    {
        $this->db = $conexion;
    }

public function insertarMuro($destinatario, $asunto, $fecha, $hora, $imagen, $descripcion, $usuario_cc,) {
    $sql = "INSERT INTO muro (destinatario, asunto, fecha, hora, imagen, descripcion, usuario_cc, nombre_imagen)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $this->db->prepare($sql);

    $null = null; // imagen inicialmente null para enviar long data

    $stmt->bind_param("sssssbis", $destinatario, $asunto, $fecha, $hora, $imagen, $descripcion, $usuario_cc,);
    $stmt->send_long_data(4, $imagen); // índice 4 porque es el quinto parámetro (índice empieza en 0)

    return $stmt->execute();
}

}
?>