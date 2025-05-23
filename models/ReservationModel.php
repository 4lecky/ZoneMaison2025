<?php
// app/Models/ReservationModel.php

class ReservationModel {
    private $pdo;
    
    public function __construct($database) {
        $this->pdo = $database;
    }
    
    /**
     * Obtiene todas las reservas para un rango de fechas
     */
    public function getReservationsByDateRange($startDate, $endDate, $areaId = 1) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT 
                    rese_id,
                    rese_usuario_nombre,
                    rese_numero_apartamento,
                    rese_fecha_inicio,
                    rese_fecha_fin,
                    rese_hora_inicio,
                    rese_hora_fin,
                    rese_area_id,
                    rese_usuario_cc
                FROM tbl_reserva 
                WHERE rese_fecha_inicio >= ? 
                AND rese_fecha_inicio <= ?
                AND rese_area_id = ?
                ORDER BY rese_fecha_inicio, rese_hora_inicio
            ");
            
            $stmt->execute([$startDate, $endDate, $areaId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch(PDOException $e) {
            throw new Exception("Error al obtener reservas: " . $e->getMessage());
        }
    }
    
    /**
     * Verifica si un horario está disponible
     */
    public function isTimeSlotAvailable($date, $startTime, $endTime, $areaId = 1) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT COUNT(*) as count 
                FROM tbl_reserva 
                WHERE rese_fecha_inicio = ? 
                AND rese_area_id = ?
                AND (
                    (rese_hora_inicio < ? AND rese_hora_fin > ?) 
                    OR (rese_hora_inicio < ? AND rese_hora_fin > ?)
                    OR (rese_hora_inicio >= ? AND rese_hora_fin <= ?)
                )
            ");
            
            $stmt->execute([
                $date, $areaId,
                $endTime, $startTime,     // Conflicto tipo 1
                $endTime, $endTime,       // Conflicto tipo 2  
                $startTime, $startTime    // Conflicto tipo 3
            ]);
            
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['count'] == 0;
            
        } catch(PDOException $e) {
            throw new Exception("Error al verificar disponibilidad: " . $e->getMessage());
        }
    }
    
    /**
     * Crea una nueva reserva
     */
    public function createReservation($data) {
        try {
            // Validaciones de negocio
            $this->validateReservationData($data);
            
            // Verificar disponibilidad
            if (!$this->isTimeSlotAvailable($data['fecha'], $data['hora_inicio'], $data['hora_fin'], $data['area_id'])) {
                throw new Exception("El horario seleccionado ya está ocupado");
            }
            
            // Generar nuevo ID
            $newId = $this->getNextReservationId();
            
            // Insertar reserva
            $stmt = $this->pdo->prepare("
                INSERT INTO tbl_reserva (
                    rese_id,
                    rese_usuario_nombre,
                    rese_numero_apartamento,
                    rese_fecha_inicio,
                    rese_fecha_fin,
                    rese_hora_inicio,
                    rese_hora_fin,
                    rese_area_id,
                    rese_usuario_cc
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            
            $result = $stmt->execute([
                $newId,
                $data['nombre'],
                $data['apartamento'],
                $data['fecha'],
                $data['fecha'], // Mismo día para inicio y fin
                $data['hora_inicio'],
                $data['hora_fin'],
                $data['area_id'],
                $data['documento']
            ]);
            
            if (!$result) {
                throw new Exception("Error al crear la reserva");
            }
            
            return [
                'success' => true,
                'reservation_id' => $newId,
                'message' => 'Reserva creada exitosamente'
            ];
            
        } catch(Exception $e) {
            throw $e;
        } catch(PDOException $e) {
            throw new Exception("Error de base de datos: " . $e->getMessage());
        }
    }
    
    /**
     * Valida los datos de la reserva
     */
    private function validateReservationData($data) {
        $required_fields = ['nombre', 'apartamento', 'documento', 'fecha', 'hora_inicio', 'hora_fin', 'area_id'];
        
        foreach ($required_fields as $field) {
            if (empty($data[$field])) {
                throw new Exception("Campo requerido faltante: $field");
            }
        }
        
        // Validar formato de fecha
        $reservationDate = DateTime::createFromFormat('Y-m-d', $data['fecha']);
        if (!$reservationDate) {
            throw new Exception("Formato de fecha inválido");
        }
        
        // Validar que no sea fecha pasada
        $today = new DateTime();
        $today->setTime(0, 0, 0);
        
        if ($reservationDate < $today) {
            throw new Exception("No se pueden hacer reservas para fechas pasadas");
        }
        
        // Validar anticipación mínima de 48 horas
        $minDate = new DateTime('+2 days');
        $minDate->setTime(0, 0, 0);
        
        if ($reservationDate < $minDate) {
            throw new Exception("Las reservas deben hacerse con mínimo 48 horas de anticipación");
        }
        
        // Validar formato de horarios
        if (!preg_match('/^\d{2}:\d{2}:\d{2}$/', $data['hora_inicio']) || 
            !preg_match('/^\d{2}:\d{2}:\d{2}$/', $data['hora_fin'])) {
            throw new Exception("Formato de hora inválido");
        }
        
        // Validar que la hora de fin sea posterior a la de inicio
        if ($data['hora_inicio'] >= $data['hora_fin']) {
            throw new Exception("La hora de fin debe ser posterior a la hora de inicio");
        }
    }
    
    /**
     * Obtiene el próximo ID disponible para reserva
     */
    private function getNextReservationId() {
        $stmt = $this->pdo->prepare("SELECT MAX(rese_id) as max_id FROM tbl_reserva");
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return ($result['max_id'] ?? 0) + 1;
    }
    
    /**
     * Obtiene una reserva por ID
     */
    public function getReservationById($id) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT * FROM tbl_reserva 
                WHERE rese_id = ?
            ");
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
            
        } catch(PDOException $e) {
            throw new Exception("Error al obtener reserva: " . $e->getMessage());
        }
    }
    
    /**
     * Cancela una reserva (soft delete o actualización de estado)
     */
    public function cancelReservation($id, $userId) {
        try {
            // Verificar que la reserva pertenece al usuario
            $reservation = $this->getReservationById($id);
            if (!$reservation || $reservation['rese_usuario_cc'] != $userId) {
                throw new Exception("Reserva no encontrada o no autorizada");
            }
            
            // Verificar que sea con 24 horas de anticipación
            $reservationDate = new DateTime($reservation['rese_fecha_inicio']);
            $minCancelDate = new DateTime('+1 day');
            
            if ($reservationDate < $minCancelDate) {
                throw new Exception("Las cancelaciones deben hacerse con mínimo 24 horas de anticipación");
            }
            
            // Eliminar la reserva
            $stmt = $this->pdo->prepare("DELETE FROM tbl_reserva WHERE rese_id = ?");
            $result = $stmt->execute([$id]);
            
            if (!$result) {
                throw new Exception("Error al cancelar la reserva");
            }
            
            return ['success' => true, 'message' => 'Reserva cancelada exitosamente'];
            
        } catch(Exception $e) {
            throw $e;
        } catch(PDOException $e) {
            throw new Exception("Error de base de datos: " . $e->getMessage());
        }
    }
}