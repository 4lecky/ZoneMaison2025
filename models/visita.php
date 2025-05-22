<?php

require_once __DIR__. "../config/db.php";
class Visita {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function registrarVisita($data) {
        $sql = "INSERT INTO tbl_visita (
                    vis_hora_entrada,
                    vis_hora_salida,
                    vis_fecha_entrada,
                    vis_fecha_salida,
                    vis_torre_visitada,
                    vis_Apto_visitado,
                    vis_usuario_cc
                ) VALUES (?, ?, ?, ?, ?, ?, ?)";

        $stmt = $this->pdo->prepare($sql);

        $asignaciones = [
            $data['horaInicio'],     // vis_hora_entrada
            $data['horaSalida'],     // vis_hora_salida
            $data['fechaEntrada'],   // vis_fecha_entrada
            $data['fechaSalida'],    // vis_fecha_salida
            $data['torre'],          // vis_torre_visitada
            $data['apto'],           // vis_Apto_visitado
            $data['usuario_cc']      // vis_usuario_cc
        ];

        $stmt->execute($asignaciones);

        return $this->pdo->lastInsertId();
    }
}
