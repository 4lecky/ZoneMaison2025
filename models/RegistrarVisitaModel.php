<?php
require_once 'db.php';

class RegistrarVisitaModelo {
    private $pdo;

    public function __construct() {
        $this->pdo = require 'db.php';
    }

    public function insertarVisitaCompleta($datos) {
        try {
            $this->pdo->beginTransaction();

            // Insertar visitante
            $stmtVisitante = $this->pdo->prepare("
                INSERT INTO visitante (tipo_doc, numero_doc, nombre, correo, telefono)
                VALUES (?, ?, ?, ?, ?)
            ");
            $stmtVisitante->execute([
                $datos['tipo_doc'],
                $datos['numero_doc'],
                $datos['nombre'],
                $datos['correo'],
                $datos['telefono']
            ]);

            // Obtener ID del visitante
            $idVisitante = $this->pdo->lastInsertId();

            // Insertar visita
            $stmtVisita = $this->pdo->prepare("
                INSERT INTO visita (id_visitante, fecha_entrada, hora_entrada, fecha_salida, hora_salida, torre_visitada, apto_visitado, cedula_usuario)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmtVisita->execute([
                $idVisitante,
                $datos['fechaEntrada'],
                $datos['horaEntrada'],
                $datos['fechaSalida'],
                $datos['horaSalida'],
                $datos['torreVisitada'],
                $datos['aptoVisitado'],
                $datos['usuario']
            ]);

            $this->pdo->commit();
            return true;

        } catch (Exception $e) {
            $this->pdo->rollBack();
            return "Error: " . $e->getMessage();
        }
    }
}
?>
