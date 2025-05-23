<?php
require_once '../config/db.php';

class EliminarUsuarioModels{
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function eliminar($usuario_cc) {
        $stmt = $this->pdo->prepare("DELETE FROM tbl_usuario WHERE usuario_cc = cc:");
        return $stmt->execute([$usuario_cc]);
    }

    // Obtener todos los usuarios
    public function obtenerTodos() {
        $stmt = $this->pdo->query("SELECT * FROM tbl_usuario");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
