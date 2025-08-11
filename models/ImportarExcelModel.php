<?php
require_once __DIR__ . '/../config/db.php';

class ImportarExcelModel
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function insertar($cedula, $tipoDocumento, $nombre, $telefono, $correo, $contraseña, $apartamento, $torre, $parqueadero, $propiedades)
    {
        $stmt = $this->pdo->prepare("INSERT INTO tbl_usuario (usu_cedula, 
        usu_tipo_documento, usu_nombre_completo, usu_telefono, usu_correo, 
        usu_password, usu_apartamento_residencia,
        usu_torre_residencia, usu_parqueadero_asignado, usu_propiedades) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

        // Exceute asignamos valores a los '?' respectivamente  
        $hash = password_hash($contraseña, PASSWORD_DEFAULT); // Contraseña encriptada
        $stmt->execute([$cedula, $tipoDocumento, $nombre, $telefono, $correo, $hash, $apartamento, $torre, $parqueadero, $propiedades]);
    }

    public function existeUsuario($cedula, $telefono, $correo)
    {
        $stmt = $this->pdo->prepare("
        SELECT COUNT(*) as total 
        FROM tbl_usuario 
        WHERE usu_cedula = ? OR usu_telefono = ? OR usu_correo = ?");
        $stmt->execute([$cedula, $telefono, $correo]);
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        return $resultado['total'] > 0; // true si existe
    }
}
