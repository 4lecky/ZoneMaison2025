<?php
class muroModel
{
    private $db;

    public function __construct($conexion)
    {
        $this->db = $conexion;
    }

    public function insertarMuro($destinatario, $asunto, $fecha, $hora, $rutaImagen, $descripcion, $usuario_cc)
    {
        $sql = "INSERT INTO tbl_muro 
        (muro_Destinatario, muro_Asunto, muro_Fecha, muro_Hora, muro_image, muro_Descripcion, muro_usuario_cc)
        VALUES (:destinatario, :asunto, :fecha, :hora, :imagePath, :descripcion, :usuario_cc)";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':destinatario', $destinatario);
        $stmt->bindParam(':asunto', $asunto);
        $stmt->bindParam(':fecha', $fecha);
        $stmt->bindParam(':hora', $hora);
        $stmt->bindParam(':imagePath', $rutaImagen); // guarda ruta
        $stmt->bindParam(':descripcion', $descripcion);
        $stmt->bindParam(':usuario_cc', $usuario_cc, PDO::PARAM_INT);

        return $stmt->execute();
    }
}
?>