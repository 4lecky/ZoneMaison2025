<?php
require_once __DIR__ . '/../config/db.php';

class ReservaModel {
    private $conn;
    private $table = 'reservas';

    public function __construct() {
        global $pdo;
        $this->conn = $pdo;
    }

    public function obtenerTodas() {
        $sql = "SELECT r.*, z.nombre as zona_nombre, z.capacidad 
                FROM {$this->table} r 
                JOIN zonas_comunes z ON r.zona_id = z.id
                ORDER BY r.fecha_reserva DESC, r.hora_inicio DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerPorId($id) {
        $sql = "SELECT r.*, z.nombre as zona_nombre 
                FROM {$this->table} r 
                JOIN zonas_comunes z ON r.zona_id = z.id
                WHERE r.id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function crear($datos) {
        // Verificar disponibilidad primero
        if (!$this->verificarDisponibilidad($datos)) {
            throw new Exception("La zona ya está reservada en ese horario");
        }

        $sql = "INSERT INTO {$this->table} 
                (zona_id, apartamento, nombre_residente, fecha_reserva, hora_inicio, hora_fin, observaciones) 
                VALUES (:zona_id, :apartamento, :nombre_residente, :fecha_reserva, :hora_inicio, :hora_fin, :observaciones)";
        
        try {
            $stmt = $this->conn->prepare($sql);
            return $stmt->execute([
                ':zona_id' => $datos['zona_id'],
                ':apartamento' => $datos['apartamento'],
                ':nombre_residente' => $datos['nombre_residente'],
                ':fecha_reserva' => $datos['fecha_reserva'],
                ':hora_inicio' => $datos['hora_inicio'],
                ':hora_fin' => $datos['hora_fin'],
                ':observaciones' => $datos['observaciones'] ?? null
            ]);
        } catch (PDOException $e) {
            error_log("Error al crear reserva: " . $e->getMessage());
            return false;
        }
    }

    public function actualizar($id, $datos) {
        // Verificar disponibilidad excluyendo la reserva actual
        if (!$this->verificarDisponibilidad($datos, $id)) {
            throw new Exception("La zona ya está reservada en ese horario");
        }

        $sql = "UPDATE {$this->table} SET 
                zona_id = :zona_id, 
                apartamento = :apartamento, 
                nombre_residente = :nombre_residente, 
                fecha_reserva = :fecha_reserva, 
                hora_inicio = :hora_inicio, 
                hora_fin = :hora_fin, 
                observaciones = :observaciones,
                estado = :estado
                WHERE id = :id";
                
        try {
            $stmt = $this->conn->prepare($sql);
            return $stmt->execute([
                ':zona_id' => $datos['zona_id'],
                ':apartamento' => $datos['apartamento'],
                ':nombre_residente' => $datos['nombre_residente'],
                ':fecha_reserva' => $datos['fecha_reserva'],
                ':hora_inicio' => $datos['hora_inicio'],
                ':hora_fin' => $datos['hora_fin'],
                ':observaciones' => $datos['observaciones'] ?? null,
                ':estado' => $datos['estado'] ?? 'activa',
                ':id' => $id
            ]);
        } catch (PDOException $e) {
            error_log("Error al actualizar reserva: " . $e->getMessage());
            return false;
        }
    }

    public function eliminar($id) {
        $sql = "DELETE FROM {$this->table} WHERE id = ?";
        try {
            $stmt = $this->conn->prepare($sql);
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            error_log("Error al eliminar reserva: " . $e->getMessage());
            return false;
        }
    }

    private function verificarDisponibilidad($datos, $excluirId = null) {
        $sql = "SELECT COUNT(*) as count FROM {$this->table} 
                WHERE zona_id = :zona_id 
                AND fecha_reserva = :fecha_reserva
                AND (
                    (hora_inicio < :hora_fin AND hora_fin > :hora_inicio)
                )
                AND estado != 'cancelada'";
        
        if ($excluirId) {
            $sql .= " AND id != :excluir_id";
        }
        
        $stmt = $this->conn->prepare($sql);
        $params = [
            ':zona_id' => $datos['zona_id'],
            ':fecha_reserva' => $datos['fecha_reserva'],
            ':hora_inicio' => $datos['hora_inicio'],
            ':hora_fin' => $datos['hora_fin']
        ];
        
        if ($excluirId) {
            $params[':excluir_id'] = $excluirId;
        }
        
        $stmt->execute($params);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $result['count'] == 0;
    }
}
?>