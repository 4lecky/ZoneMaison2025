<?php
require_once __DIR__ . '/../config/db.php';

class Perfil {
    
    private $pdo;


    public function __construct($pdo){
        $this->pdo = $pdo;
    }

    // Obtenemos la información 
    public function findById(int $id): ?array
    {
        $sql = "SELECT * FROM tbl_usuario WHERE usuario_cc =  ? LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

   //UPDATE tbl_usuario SET usu_nombre_completo='Sofia Pendragon' WHERE usuario_cc = 114;

    public function editar($data, $id){
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
        $stmt = $this->pdo->prepare($sql);

        return $stmt ->execute([
            $data ['NumeroDocumento'],
            $data ['TipoDocumento'],
            $data ['NombreCompleto'],
            $data ['Telefono'],
            $data ['Correo'],
            $data ['Apartamento'],
            $data ['Torre'],
            $data ['Propiedades'],
            $id
        ]); 
    }

    public function eliminar($id){
        $stmt = $this->pdo->prepare("UPDATE tbl_usuario SET usu_estado = 'Inactivo' WHERE usuario_cc = ?");
        return $stmt->execute([$id]);

    }

}