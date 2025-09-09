public function obtenerReservasPorZona($zona_id) {
    try {
        $sql = "SELECT 
                    reserva_fecha, 
                    reserva_hora_inicio, 
                    reserva_hora_fin, 
                    reserva_nombre_residente,
                    reserva_apartamento
                FROM tbl_reservas 
                WHERE zona_id = :zona_id 
                AND reserva_estado = 'activa'
                ORDER BY reserva_fecha ASC, reserva_hora_inicio ASC";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':zona_id', $zona_id, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
        
    } catch (PDOException $e) {
        throw new Exception("Error al obtener reservas: " . $e->getMessage());
    }
}