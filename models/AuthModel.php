<?php
require_once __DIR__ . '/../config/db.php';

//Creamos la clase usuario
class Usuario
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function registrar($data)
    {

        $sql = "INSERT INTO tbl_usuario (
            usu_nombre_completo, usu_cedula, usu_tipo_documento, usu_telefono, usu_apartamento_residencia,
            usu_torre_residencia, usu_parqueadero_asignado, usu_propiedades, usu_correo, usu_password
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        //Evitamos inyecciones sql

        $stmt = $this->pdo->prepare($sql);
        $data['Password'] = password_hash($data['Password'], PASSWORD_DEFAULT); //Contraseña encriptada

        // Crear un array indexado con los valores en el mismo orden que en la consulta
        $params = [
            $data['NombreUsuario'],
            $data['NumeroCedula'],
            $data['TipoDocumento'],
            $data['NumeroTelefonico'],
            $data['Apartamento'],
            $data['Torre'],
            $data['Parqueadero'],
            $data['Propiedades'],
            $data['Email'],
            $data['Password']
        ];

        return $stmt->execute($params);
    }

    public function login($email, $password)
    {
        // Solo puede ingresar si es un usuario activo
        $stmt = $this->pdo->prepare("SELECT usuario_cc AS id, 
        usu_nombre_completo AS nombre,
        usu_correo AS email,
        usu_password AS contraseña,
        usu_rol AS rol
        FROM tbl_usuario WHERE usu_correo = ? AND usu_estado = 'Activo' LIMIT 1");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        if ($user && password_verify($password, $user['contraseña'])) {
            unset($user['contraseña']); // No guardar nunca el hash en sesión
            return $user;
        }
        return false;
    }

    public function cedulaDuplicada($cedula)
    {
        $stmt = $this->pdo->prepare("SELECT 1 FROM tbl_usuario WHERE usu_cedula = ?");
        $stmt->execute([$cedula]);
        return $stmt->fetch() ? true : false;
    }

    public function telefonoDuplicado($telefono)
    {
        $stmt = $this->pdo->prepare("SELECT 1 FROM tbl_usuario WHERE usu_telefono = ?");
        $stmt->execute([$telefono]);
        return $stmt->fetch() ? true : false;
    }

    public function correoDuplicado($email)
    {
        $stmt = $this->pdo->prepare("SELECT 1 FROM tbl_usuario WHERE usu_correo = ?");
        $stmt->execute([$email]);
        return $stmt->fetch() ? true : false;
    }
}
