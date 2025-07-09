<?php
require_once __DIR__ . '/../config/db.php';

class PqrsModel {
    private $pdo;

    public function __construct() {
        $this->pdo = require __DIR__ . '/../config/db.php';
    }

    public function registrar($data): mixed {
<<<<<<< HEAD
        $sql = "INSERT INTO pqrs (nombres, apellidos, identificacion, email, telefono, tipo_pqr, asunto, mensaje, archivos, medio_respuesta) 
=======
        $sql = "INSERT INTO tbl_pqrs (nombres, apellidos, identificacion, email, telefono, tipo_pqr, asunto, mensaje, archivos, medio_respuesta) 
>>>>>>> b749a1aeeb17e11874b55fe17b55fa3d883dc79d
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);

        // Validar que medio_respuesta sea un array
        $medioRespuesta = is_array($data['medio_respuesta']) ? $data['medio_respuesta'] : [$data['medio_respuesta']];
        $medioRespuestaString = implode(',', $medioRespuesta);

        $params = [
            $data['nombres'], $data['apellidos'], $data['identificacion'], $data['email'],
            $data['telefono'], $data['tipo_pqr'], $data['asunto'], $data['mensaje'],
            $data['archivos'], $medioRespuestaString
        ];

        return $stmt->execute($params);
    }

    public function obtenerTodos() {
<<<<<<< HEAD
        $stmt = $this->pdo->query("SELECT * FROM pqrs");
=======
        $stmt = $this->pdo->query("SELECT * FROM tbl_pqrs");
>>>>>>> b749a1aeeb17e11874b55fe17b55fa3d883dc79d
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerPorId($id) {
<<<<<<< HEAD
        $stmt = $this->pdo->prepare("SELECT * FROM pqrs WHERE id = ?");
=======
        $stmt = $this->pdo->prepare("SELECT * FROM tbl_pqrs WHERE id = ?");
>>>>>>> b749a1aeeb17e11874b55fe17b55fa3d883dc79d
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function actualizar($id, $data): bool {
<<<<<<< HEAD
        $sql = "UPDATE pqrs SET nombres = ?, apellidos = ?, identificacion = ?, email = ?, telefono = ?, tipo_pqr = ?, asunto = ?, mensaje = ?, archivos = ?, medio_respuesta = ? 
=======
        $sql = "UPDATE tbl_pqrs SET nombres = ?, apellidos = ?, identificacion = ?, email = ?, telefono = ?, tipo_pqr = ?, asunto = ?, mensaje = ?, archivos = ?, medio_respuesta = ? 
>>>>>>> b749a1aeeb17e11874b55fe17b55fa3d883dc79d
                WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        $medioRespuesta = is_array($data['medio_respuesta']) ? $data['medio_respuesta'] : [$data['medio_respuesta']];
        $medioRespuestaString = implode(',', $medioRespuesta);
        
        $params = [
            $data['nombres'], $data['apellidos'], $data['identificacion'], $data['email'],
            $data['telefono'], $data['tipo_pqr'], $data['asunto'], $data['mensaje'],
            $data['archivos'], $medioRespuestaString, $id
        ];
        return $stmt->execute($params);
    }

    public function eliminar($id): bool {
<<<<<<< HEAD
        $stmt = $this->pdo->prepare("DELETE FROM pqrs WHERE id = ?");
        return $stmt->execute([$id]);
    }
=======
        $stmt = $this->pdo->prepare("DELETE FROM tbl_pqrs WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function obtenerPorIdentificacion($identificacion) {
    $stmt = $this->pdo->prepare("SELECT * FROM tbl_pqrs WHERE identificacion = ?");
    $stmt->execute([$identificacion]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
>>>>>>> b749a1aeeb17e11874b55fe17b55fa3d883dc79d
}



<<<<<<< HEAD
=======

>>>>>>> b749a1aeeb17e11874b55fe17b55fa3d883dc79d
