<?php
class paquetesModel
{
    private $db;

    public function __construct($conexion)
    {
        $this->db = $conexion;
    }

public function insertarPaquete($tipoDoc, $cedulaUsuario, $nombreDestinatario, $asunto, $fecha, $hora, $rutaImagen, $descripcion, $estado)
{
    $sql = "INSERT INTO tbl_paquetes 
        (paqu_TipoDoc, paqu_usuario_cedula, paqu_Destinatario, paqu_Asunto, paqu_FechaLlegada, paqu_Hora, paqu_image, paqu_Descripcion, paqu_estado)
        VALUES (:tipo_doc, :cedula_usuario, :destinatario, :asunto, :fecha, :hora, :imagen, :descripcion, :estado)";
    
    $stmt = $this->db->prepare($sql);
    $stmt->bindParam(':tipo_doc', $tipoDoc);
    $stmt->bindParam(':cedula_usuario', $cedulaUsuario);
    $stmt->bindParam(':destinatario', $nombreDestinatario);
    $stmt->bindParam(':asunto', $asunto);
    $stmt->bindParam(':fecha', $fecha);
    $stmt->bindParam(':hora', $hora);
    $stmt->bindParam(':imagen', $rutaImagen);
    $stmt->bindParam(':descripcion', $descripcion);
    $stmt->bindParam(':estado', $estado);
    
    if ($stmt->execute()) {
        return true; // Inserción exitosa
    } else {
        return false; // Hubo un problema
    }
}

 }