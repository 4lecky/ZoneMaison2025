<?php

require_once __DIR__ . '/../config/db.php';


class RegistrarVisitaModel {
    private $pdo;

    public function __construct() {
        $this->pdo = require __DIR__ . '/../config/db.php';
    }

    //Usa prepare y execute para evitar inyecciones SQL.

    public function insertarVisitaCompleta($datos) {
        try {
            $this->pdo->beginTransaction();
            // var_dump($this->pdo);
            // exit;


            // Insertar visitante
            $stmtVisitante = $this->pdo->prepare("
                INSERT INTO tbl_Visitante (
                    visi_Tipo_documento, 
                    visi_documento, 
                    visi_nombre, 
                    visi_email , 
                    visi_telefono)
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
                INSERT INTO tbl_visita (
                    vis_id, 
                    vis_fecha_entrada, 
                    vis_hora_entrada, 
                    vis_fecha_salida, 
                    vis_hora_salida, 
                    vis_torre_visitada, 
                    vis_Apto_visitado, 
                    vis_usuario_cedula)
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
            echo "Error en la base de datos: " . $e->getMessage();
            exit;
        }
        
    }
}
?>
