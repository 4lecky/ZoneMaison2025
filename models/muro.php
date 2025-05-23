<?php

$pdo = require ("../config/db.php");
class muro {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function enviarmuro($data) {
       $sql = "INSERT INTO tbl_muro (
            muro_Id,
            muro_Destinatario,
            muro_Asunto,
            muro_Descripción, 
            muro_Hora,
            muro_Fecha,
            muro_image,
            muro_usuario_cc
) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";


        $stmt = $this->pdo->prepare($sql);

        $asignaciones = [
            $data['muro_Id'],
            $data['Destinatario'], 
            $data['Asunto'],
            $data['Descripción'],
            $data['Hora'],
            $data['Fecha'],
            $data['imagen'],
            $data['usuario_cc'] 
        ];

        return $stmt->execute($asignaciones);
    }
}
