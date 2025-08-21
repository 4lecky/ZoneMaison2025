<?php
// require_once __DIR__ . '/../config/db.php';

class RegistrarVisitaModel {

    private $pdo;

    public function __construct() {
    
        $this->pdo = require __DIR__ . '/../config/db.php';
    }

    /* Inserta visitante y visita en la misma transacción */
    public function insertarVisitaCompleta(array $datos) {
        try {
            $this->pdo->beginTransaction();

        
            $stmtVisitante = $this->pdo->prepare("
                INSERT INTO tbl_Visitante (
                    visi_Tipo_documento,
                    visi_documento,
                    visi_nombre,
                    visi_email,
                    visi_telefono,
                    visi_usuario_cedula
                ) VALUES (?, ?, ?, ?, ?, ?)
            ");
            $stmtVisitante->execute([
                $datos['tipo_doc'],
                $datos['numero_doc'],
                $datos['nombre'],
                $datos['correo'],
                $datos['telefono'],
                $datos['usuario']
            ]);

          
            $idVisitante = $this->pdo->lastInsertId();

            $stmtVisita = $this->pdo->prepare("
                INSERT INTO tbl_visita (
                    vis_id,
                    vis_fecha_entrada,
                    vis_hora_entrada,
                    vis_fecha_salida,
                    vis_hora_salida            
                ) VALUES (?, ?, ?, ?, ?)
            ");
            $stmtVisita->execute([
                $idVisitante,
                $datos['fechaEntrada'],
                $datos['horaEntrada'],
                $datos['fechaSalida'],
                $datos['horaSalida'],
        
            ]);

            $this->pdo->commit();
            return true;

        } catch (Exception $e) {
            $this->pdo->rollBack();
          
            return 'Error en la base de datos: ' . $e->getMessage();
        }
    }




}


