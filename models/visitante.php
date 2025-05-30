<?php
require_once __DIR__ . '/../config/db.php';
class Visitante {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function registrarVisitante($data) {
        $sql = "INSERT INTO tbl_Visitante (
                    visi_nombre,
                    visi_documento,
                    visi_Tipo_documento,
                    visi_telefono,
                    visi_email,
                    visi_vis_id
                ) VALUES (?, ?, ?, ?, ?, ?)";

        $stmt = $this->pdo->prepare($sql);

        $asignaciones = [
            $data['nombre'],
            $data['documento'],
            $data['tipoDoc'],
            $data['telefono'],
            $data['email'],
            $data['visita_id'] 
        ];

        return $stmt->execute($asignaciones);
    }
}
