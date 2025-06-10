<?php
class paquetesModel
{
    private $db;

    public function __construct($conexion)
    {
        $this->db = $conexion;
    }

public function insertarMuro($TipoDoc, $usu_cedula, $destinatario, $asunto, $fecha, $hora, $rutaImagen, $descripcion, $estado,)
{

    // Insertar en la tabla paqutes
    $sql = "INSERT INTO tbl_paquetes 
        (Paqu_TipoDoc, paqu_usuario_cedula, paqu_Asunto, paqu_FechaLlegada, paqu_image, paqu_Descripcion, paqu_estado)
        VALUES (:TipoDoc, :paqu_usuario_cedula,:usuario, :asunto, :fecha, :hora, :imagePath, :descripcion, :estado,)";

    $stmtInsert = $this->db->prepare($sql);
    $stmtInsert->bindParam(':TipoDoc', $TipoDoc);
    $stmtInsert->bindParam(':paqu_usuario_cedula', $usu_cedula, PDO::PARAM_INT);
    $stmtInsert->bindParam(':destinatario', $destinatario);
    $stmtInsert->bindParam(':asunto', $asunto);
    $stmtInsert->bindParam(':fecha', $fecha);
    $stmtInsert->bindParam(':hora', $hora);
    $stmtInsert->bindParam(':imagePath', $rutaImagen);
    $stmtInsert->bindParam(':descripcion', $descripcion);
    

    return $stmtInsert->execute();
}

}
?>