<?php
require_once __DIR__ . '/../config/db.php';

class Excel {
private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function insertar($cedula, $tipoDocumento, $nombre, $telefono, $correo, $contraseña, $apartamento, $torre, $parqueadero, $propiedades) {
        $stmt = $this->pdo->prepare("INSERT INTO tbl_usuario (usu_cedula, usu_tipo_documento, usu_nombre_completo, usu_telefono, usu_correo, usu_password, usu_apartamento_residencia,
        usu_torre_residencia, usu_parqueadero_asignado, usu_propiedades) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$cedula, $tipoDocumento, $nombre, $telefono, $correo, $contraseña, $apartamento, $torre, $parqueadero, $propiedades]);
    }
}
