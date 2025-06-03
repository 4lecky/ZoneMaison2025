<?php
class muroModel
{
    private $db;

    public function __construct($conexion)
    {
        $this->db = $conexion;
    }

    public function insertarMuro($destinatario, $asunto, $fecha, $hora, $rutaImagen, $descripcion, $usu_cedula)
    {
        $sql = "INSERT INTO tbl_muro 
        (muro_Destinatario, muro_Asunto, muro_Fecha, muro_Hora, muro_image, muro_Descripcion, muro_usuario_cedula)
        VALUES (:destinatario, :asunto, :fecha, :hora, :imagePath, :descripcion, :usu_cedula)";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':destinatario', $destinatario);
        $stmt->bindParam(':asunto', $asunto);
        $stmt->bindParam(':fecha', $fecha);
        $stmt->bindParam(':hora', $hora);
        $stmt->bindParam(':imagePath', $rutaImagen); // guarda ruta
        $stmt->bindParam(':descripcion', $descripcion);
        $stmt->bindParam(':usu_cedula', $usu_cedula, PDO::PARAM_INT);

        return $stmt->execute();
    }
}
?>