<?php
require_once __DIR__ . '/../config/db.php';

//Creamos la clase usuario
class Usuario {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function registrar($data) {

        $sql = "INSERT INTO tbl_usuario (
            usu_nombre_completo, usu_telefono, usu_apartamento_residencia,
            usu_torre_residencia, usu_parqueadero_asignado, usu_propiedades,
            usu_rol, usu_correo, usu_password
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)"; 
        //Evitamos inyecciones sql

        $stmt = $this->pdo->prepare($sql);
        $data['Password'] = password_hash($data['Password'], PASSWORD_DEFAULT); //Contraseña encriptada

    // Crear un array indexado con los valores en el mismo orden que en la consulta
    $params = [
        $data['NombreUsuario'],
        $data['NumeroTelefonico'],
        $data['Apartamento'],
        $data['Torre'], 
        $data['Parqueadero'],
        $data['Propiedades'],
        $data['Rol'],
        $data['Email'],
        $data['Password']
    ];

        return $stmt->execute($params);
    }

    public function login($email, $password) {
        $stmt = $this->pdo->prepare("SELECT * FROM tbl_usuario WHERE usu_correo = ? ");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        if ($user && password_verify($password, $user['usu_password'])) {
            return $user;
        }
        return false;
    }
}

