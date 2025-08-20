<?php
require_once __DIR__ . '/../config/db.php';

class Perfil {
    
    private $pdo;


    public function __construct($pdo){
        $this->pdo = $pdo;
    }

    public function findById(int $id): ?array
    {
        $sql = "SELECT usuario_cc AS id,
                       usu_nombre_completo AS nombre,
                       usu_correo AS email,
                       usu_rol AS rol,
                       usu_estado AS estado
                FROM {$this->table}
                WHERE usuario_cc = :id
                LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ?: null;
    }

   // Obtenemos el usuario

   //UPDATE tbl_usuario SET usu_nombre_completo='Sofia Pendragon' WHERE usuario_cc = 114;

    public function editar($data){
        $sql = "UPDATE tbl_usuario 
        SET usu_cedula = ?,
        usu_tipo_documento = ?,
        usu_nombre_completo = ?,
        usu_telefono = ?,
        usu_correo = ?,
        usu_apartamento_residencia = ?,
        usu_torre_residencia = ?,
        usu_propiedades = ?

        WHERE usuario_cc = ?";
    }

    public function eliminar($id){

    }

}