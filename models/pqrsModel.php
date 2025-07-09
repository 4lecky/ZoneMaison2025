<?php
require_once __DIR__ . '/../config/db.php';

class PqrsModel
{
        private $pdo;

        public function __construct()
        {
                $this->pdo = require __DIR__ . '/../config/db.php';
        }

        public function registrar($data): mixed
        {
                $sql = "INSERT INTO tbl_pqrs (nombres, apellidos, identificacion, email, telefono, tipo_pqr, asunto, mensaje, archivos, medio_respuesta) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                $stmt = $this->pdo->prepare($sql);

                // Validar que medio_respuesta sea un array
                $medioRespuesta = is_array($data['medio_respuesta']) ? $data['medio_respuesta'] : [$data['medio_respuesta']];
                $medioRespuestaString = implode(',', $medioRespuesta);

                $params = [
                        $data['nombres'],
                        $data['apellidos'],
                        $data['identificacion'],
                        $data['email'],
                        $data['telefono'],
                        $data['tipo_pqr'],
                        $data['asunto'],
                        $data['mensaje'],
                        $data['archivos'],
                        $medioRespuestaString
                ];

                return $stmt->execute($params);
        }

        public function obtenerTodos()
        {
                $stmt = $this->pdo->query("SELECT * FROM tbl_pqrs");
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        public function obtenerPorId($id)
        {
                $stmt = $this->pdo->prepare("SELECT * FROM tbl_pqrs WHERE id = ?");
                $stmt->execute([$id]);
                return $stmt->fetch(PDO::FETCH_ASSOC);
        }

        public function actualizar($id, $data): bool
        {
                $sql = "UPDATE tbl_pqrs SET nombres = ?, apellidos = ?, identificacion = ?, email = ?, telefono = ?, tipo_pqr = ?, asunto = ?, mensaje = ?, archivos = ?, medio_respuesta = ? 
                WHERE id = ?";
                $stmt = $this->pdo->prepare($sql);
                $medioRespuesta = is_array($data['medio_respuesta']) ? $data['medio_respuesta'] : [$data['medio_respuesta']];
                $medioRespuestaString = implode(',', $medioRespuesta);

                $params = [
                        $data['nombres'],
                        $data['apellidos'],
                        $data['identificacion'],
                        $data['email'],
                        $data['telefono'],
                        $data['tipo_pqr'],
                        $data['asunto'],
                        $data['mensaje'],
                        $data['archivos'],
                        $medioRespuestaString,
                        $id
                ];
                return $stmt->execute($params);
        }

        public function eliminar($id): bool
        {
                $stmt = $this->pdo->prepare("DELETE FROM tbl_pqrs WHERE id = ?");
                return $stmt->execute([$id]);
        }

        public function obtenerPorIdentificacion($identificacion)
        {
                $stmt = $this->pdo->prepare("SELECT * FROM tbl_pqrs WHERE identificacion = ?");
                $stmt->execute([$identificacion]);
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
}
