<?php
class RegistrarVisitaModel {

    private $pdo;

    public function __construct() {
        $this->pdo = require __DIR__ . '/../config/db.php';
    }

    /* Inserta visitante y visita en la misma transacción */
    public function insertarVisitaCompleta(array $datos) {
        try {
            $this->pdo->beginTransaction();

            // Insertar visitante
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
                $datos['usuario']   // FK hacia tbl_usuario.usuario_cc
            ]);

            // Recuperar ID del visitante insertado
            $idVisitante = $this->pdo->lastInsertId();

            // Insertar visita vinculada al visitante
            $stmtVisita = $this->pdo->prepare("
                INSERT INTO tbl_visita (
                    vis_fecha_entrada,
                    vis_hora_entrada,
                    vis_fecha_salida,
                    vis_hora_salida,
                    vis_visi_id
                ) VALUES (?, ?, ?, ?, ?)
            ");
            $stmtVisita->execute([
                $datos['fechaEntrada'],
                $datos['horaEntrada'],
                $datos['fechaSalida'],
                $datos['horaSalida'],
                $idVisitante  // aquí va la FK
            ]);

            $this->pdo->commit();
            return true;

        } catch (Exception $e) {
            $this->pdo->rollBack();
            return 'Error en la base de datos: ' . $e->getMessage();
        }
    }
        public function buscarResidentePorCedula($cedula) {
            $stmt = $this->pdo->prepare("SELECT usu_cedula, usu_nombre_completo, usu_correo, usu_telefono, usu_torre_residencia, usu_apartamento_residencia 
                                        FROM tbl_usuario 
                                        WHERE usu_cedula = ?");
            $stmt->execute([$cedula]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }


}