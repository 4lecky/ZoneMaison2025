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


}
