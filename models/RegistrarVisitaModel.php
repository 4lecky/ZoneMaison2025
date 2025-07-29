<?php
require_once __DIR__ . '/../config/db.php';

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
                    visi_telefono
                ) VALUES (?, ?, ?, ?, ?)
            ");
            $stmtVisitante->execute([
                $datos['tipo_doc'],
                $datos['numero_doc'],
                $datos['nombre'],
                $datos['correo'],
                $datos['telefono']
            ]);

          
            $idVisitante = $this->pdo->lastInsertId();

            $stmtVisita = $this->pdo->prepare("
                INSERT INTO tbl_visita (
                    vis_id,
                    vis_fecha_entrada,
                    vis_hora_entrada,
                    vis_fecha_salida,
                    vis_hora_salida,
                    vis_torre_visitada,
                    vis_Apto_visitado,
                    vis_usuario_cedula
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)
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
          
            return 'Error en la base de datos: ' . $e->getMessage();
        }
    }



public function obtenerTodasLasVisitas() {
    try {
        $stmt = $this->pdo->prepare("
            SELECT 
                v.vis_id,
                vi.visi_nombre AS nombre,
                v.vis_fecha_entrada AS fechaEntrada,
                v.vis_torre_visitada AS torreVisitada,
                v.vis_Apto_visitado AS aptoVisitado
            FROM 
                tbl_visita v
            INNER JOIN 
                tbl_Visitante vi ON v.vis_id = vi.visi_id
            ORDER BY v.vis_fecha_entrada DESC
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        return ['error' => 'Error al obtener visitas: ' . $e->getMessage()];
    }
}
}