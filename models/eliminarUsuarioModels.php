<?php
require_once '../config/db.php';

//Creamos la clase
class EliminarUsuarioModels{
    //$pdo es el objeto de conexión a la base de datos.
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    //Definimos la función para eliminar
    public function eliminar($cc) {
        $stmt = $this->pdo->prepare("UPDATE tbl_usuario SET usu_estado = 'Inactivo'  WHERE usuario_cc = ?");
        return $stmt->execute([$cc]);
    }

    // public function ordenar($cc){
    //     $stmt = $this->pdo->prepare("SELECT * FROM tbl_usuario ORDER BY 
    //         CASE WHEN usu_estado = 'Inactivo' THEN 1 ELSE 0 END, usu_nombre_completo ASC");
    // }

}
