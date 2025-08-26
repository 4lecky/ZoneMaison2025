<?php
class paquetesModel
{
    private $db;

    public function __construct($conexion)
    {
        $this->db = $conexion;
    }

    public function insertarPaquete($tipoDoc, $paqu_usuario_cedula, $nombreDestinatario, $asunto, $fecha, $hora, $rutaImagen, $descripcion, $estado)
    {
        $sql = "INSERT INTO tbl_paquetes 
            (paqu_TipoDoc, paqu_usuario_cedula, paqu_Destinatario, paqu_Asunto, paqu_FechaLlegada, paqu_Hora, paqu_image, paqu_Descripcion, paqu_estado)
            VALUES (:tipo_doc, :paqu_usuario_cedula, :destinatario, :asunto, :fecha, :hora, :imagen, :descripcion, :estado)";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':tipo_doc', $tipoDoc);
        $stmt->bindValue(':paqu_usuario_cedula', $paqu_usuario_cedula);
        $stmt->bindValue(':destinatario', $nombreDestinatario);
        $stmt->bindValue(':asunto', $asunto);
        $stmt->bindValue(':fecha', $fecha);
        $stmt->bindValue(':hora', $hora);
        $stmt->bindValue(':imagen', $rutaImagen);
        $stmt->bindValue(':descripcion', $descripcion);
        $stmt->bindValue(':estado', $estado);
        
        return $stmt->execute(); // Si es exitoso, retorna true
    }
}
?>