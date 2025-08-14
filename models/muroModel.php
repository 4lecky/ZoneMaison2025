<?php
class muroModel
{
    private $db;

    public function __construct($conexion)
    {
        $this->db = $conexion;
    }

    public function insertarMuro($destinatario, $asunto, $fecha, $hora, $rutaImagen, $descripcion, $EnviaHora, $usu_cedula)
    {
        // Validar que el destinatario tenga rol permitido
        $stmtCheck = $this->db->prepare("SELECT usu_rol FROM tbl_usuario WHERE usu_cedula = ?");
        $stmtCheck->execute([$destinatario]);
        $rol = $stmtCheck->fetchColumn();

        $rolesPermitidos = ['Administrador', 'Residente', 'Propietario', 'Vigilante'];
        if (!$rol || !in_array($rol, $rolesPermitidos)) {
            return false;
        }

        // Insertar en la tabla muro - FIXED: Parameter names must match exactly
        $sql = "INSERT INTO tbl_muro 
            (muro_Destinatario, muro_Asunto, muro_Fecha, muro_Hora, muro_image, muro_Descripcion, muro_EnviaHora, muro_usuario_cedula)
            VALUES (:destinatario, :asunto, :fecha, :hora, :imagePath, :descripcion, :muro_EnviaHora, :muro_usuario_cedula)";

        $stmtInsert = $this->db->prepare($sql);
        $stmtInsert->bindParam(':destinatario', $destinatario);
        $stmtInsert->bindParam(':asunto', $asunto);
        $stmtInsert->bindParam(':fecha', $fecha);
        $stmtInsert->bindParam(':hora', $hora);
        $stmtInsert->bindParam(':imagePath', $rutaImagen);
        $stmtInsert->bindParam(':descripcion', $descripcion);
        $stmtInsert->bindParam(':muro_EnviaHora', $EnviaHora);
        $stmtInsert->bindParam(':muro_usuario_cedula', $usu_cedula, PDO::PARAM_INT);

        return $stmtInsert->execute();
    }
}