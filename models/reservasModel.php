<?php
class ReservasModel
{
    private $pdo;

    public function __construct($conexion)
    {
        $this->pdo = $conexion;
    }

    // ================================
    // MÉTODOS PARA RESERVAS
    // ================================

    /**
     * Crear una nueva reserva
     */
    public function crearReserva($zona_id, $usuario_id, $apartamento, $nombre_residente, $fecha_reserva, $hora_inicio, $hora_fin, $estado = 'activa', $observaciones = null)
    {
        try {
            $sql = "INSERT INTO tbl_reservas 
                    (zona_id, usuario_id, reserva_apartamento, reserva_nombre_residente, 
                     reserva_fecha, reserva_hora_inicio, reserva_hora_fin, reserva_estado, reserva_observaciones)
                    VALUES (:zona_id, :usuario_id, :apartamento, :nombre_residente, 
                            :fecha_reserva, :hora_inicio, :hora_fin, :estado, :observaciones)";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':zona_id', $zona_id, PDO::PARAM_INT);
            $stmt->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
            $stmt->bindParam(':apartamento', $apartamento);
            $stmt->bindParam(':nombre_residente', $nombre_residente);
            $stmt->bindParam(':fecha_reserva', $fecha_reserva);
            $stmt->bindParam(':hora_inicio', $hora_inicio);
            $stmt->bindParam(':hora_fin', $hora_fin);
            $stmt->bindParam(':estado', $estado);
            $stmt->bindParam(':observaciones', $observaciones);
            
            if ($stmt->execute()) {
                return $this->pdo->lastInsertId();
            }
            
            return false;
            
        } catch (PDOException $e) {
            throw new Exception("Error al crear reserva: " . $e->getMessage());
        }
    }

    /**
     * Obtener reservas por zona (para el calendario)
     */
    public function obtenerReservasPorZona($zona_id)
    {
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

    /**
     * Obtener una reserva específica por ID
     */
    public function obtenerReservaPorId($reserva_id)
    {
        try {
            $sql = "SELECT r.*, z.zona_nombre 
                    FROM tbl_reservas r 
                    INNER JOIN tbl_zonas z ON r.zona_id = z.zona_id 
                    WHERE r.reserva_id = :reserva_id";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':reserva_id', $reserva_id, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            throw new Exception("Error al obtener reserva: " . $e->getMessage());
        }
    }

    /**
     * Obtener reservas de un usuario específico
     */
    public function obtenerReservasPorUsuario($usuario_id)
    {
        try {
            $sql = "SELECT r.*, z.zona_nombre, z.zona_imagen 
                    FROM tbl_reservas r 
                    INNER JOIN tbl_zonas z ON r.zona_id = z.zona_id 
                    WHERE r.usuario_id = :usuario_id 
                    ORDER BY r.reserva_fecha DESC, r.reserva_hora_inicio DESC";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            throw new Exception("Error al obtener reservas del usuario: " . $e->getMessage());
        }
    }

    /**
     * Obtener todas las reservas (para administradores)
     */
    public function obtenerTodasLasReservas()
    {
        try {
            $sql = "SELECT r.*, z.zona_nombre, z.zona_imagen, u.usu_nombre_completo, u.usu_telefono, u.usu_cedula
                    FROM tbl_reservas r 
                    INNER JOIN tbl_zonas z ON r.zona_id = z.zona_id 
                    LEFT JOIN tbl_usuario u ON r.usuario_id = u.usuario_cc
                    ORDER BY r.reserva_fecha DESC, r.reserva_hora_inicio DESC";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            throw new Exception("Error al obtener todas las reservas: " . $e->getMessage());
        }
    }

    /**
     * Cancelar una reserva (cambiar estado a 'cancelada')
     */
    public function cancelarReserva($reserva_id)
    {
        try {
            $sql = "UPDATE tbl_reservas 
                    SET reserva_estado = 'cancelada', 
                        reserva_fecha_actualizacion = CURRENT_TIMESTAMP 
                    WHERE reserva_id = :reserva_id";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':reserva_id', $reserva_id, PDO::PARAM_INT);
            
            return $stmt->execute();
            
        } catch (PDOException $e) {
            throw new Exception("Error al cancelar reserva: " . $e->getMessage());
        }
    }

    /**
     * Verificar conflictos de horario para nueva reserva
     */
    public function verificarConflictoHorario($zona_id, $fecha_reserva, $hora_inicio, $hora_fin, $reserva_id_excluir = null)
    {
        try {
            $sql = "SELECT COUNT(*) as conflictos 
                    FROM tbl_reservas 
                    WHERE zona_id = :zona_id 
                    AND reserva_fecha = :fecha_reserva 
                    AND reserva_estado = 'activa'
                    AND (
                        (reserva_hora_inicio < :hora_fin AND reserva_hora_fin > :hora_inicio)
                    )";
            
            if ($reserva_id_excluir) {
                $sql .= " AND reserva_id != :reserva_id_excluir";
            }
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':zona_id', $zona_id, PDO::PARAM_INT);
            $stmt->bindParam(':fecha_reserva', $fecha_reserva);
            $stmt->bindParam(':hora_inicio', $hora_inicio);
            $stmt->bindParam(':hora_fin', $hora_fin);
            
            if ($reserva_id_excluir) {
                $stmt->bindParam(':reserva_id_excluir', $reserva_id_excluir, PDO::PARAM_INT);
            }
            
            $stmt->execute();
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return $resultado['conflictos'] > 0;
            
        } catch (PDOException $e) {
            throw new Exception("Error al verificar conflictos de horario: " . $e->getMessage());
        }
    }

    // ================================
    // MÉTODOS PARA ZONAS COMUNES
    // ================================

    /**
     * Crear una nueva zona común
     */
    public function crearZona($nombre, $descripcion, $capacidad, $estado, $imagen, $hora_apertura, $hora_cierre, $duracion_maxima, $terminos_condiciones)
    {
        try {
            $sql = "INSERT INTO tbl_zonas 
                    (zona_nombre, zona_descripcion, zona_capacidad, zona_estado, zona_imagen, 
                     zona_hora_apertura, zona_hora_cierre, zona_duracion_maxima, zona_terminos_condiciones)
                    VALUES (:nombre, :descripcion, :capacidad, :estado, :imagen, 
                            :hora_apertura, :hora_cierre, :duracion_maxima, :terminos_condiciones)";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':nombre', $nombre);
            $stmt->bindParam(':descripcion', $descripcion);
            $stmt->bindParam(':capacidad', $capacidad, PDO::PARAM_INT);
            $stmt->bindParam(':estado', $estado);
            $stmt->bindParam(':imagen', $imagen);
            $stmt->bindParam(':hora_apertura', $hora_apertura);
            $stmt->bindParam(':hora_cierre', $hora_cierre);
            $stmt->bindParam(':duracion_maxima', $duracion_maxima, PDO::PARAM_INT);
            $stmt->bindParam(':terminos_condiciones', $terminos_condiciones);
            
            if ($stmt->execute()) {
                return $this->pdo->lastInsertId();
            }
            
            return false;
            
        } catch (PDOException $e) {
            throw new Exception("Error al crear zona: " . $e->getMessage());
        }
    }

    /**
     * Obtener información de una zona específica
     */
    public function obtenerZonaPorId($zona_id)
    {
        try {
            $sql = "SELECT * FROM tbl_zonas WHERE zona_id = :zona_id";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':zona_id', $zona_id, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            throw new Exception("Error al obtener zona: " . $e->getMessage());
        }
    }

    /**
     * Obtener todas las zonas disponibles
     */
    public function obtenerTodasLasZonas($solo_activas = false)
    {
        try {
            $sql = "SELECT * FROM tbl_zonas";
            
            if ($solo_activas) {
                $sql .= " WHERE zona_estado = 'activo'";
            }
            
            $sql .= " ORDER BY zona_nombre";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            throw new Exception("Error al obtener zonas: " . $e->getMessage());
        }
    }

    /**
     * Actualizar el estado de una zona
     */
    public function actualizarEstadoZona($zona_id, $nuevo_estado)
    {
        try {
            $sql = "UPDATE tbl_zonas 
                    SET zona_estado = :nuevo_estado, 
                        zona_fecha_actualizacion = CURRENT_TIMESTAMP 
                    WHERE zona_id = :zona_id";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':nuevo_estado', $nuevo_estado);
            $stmt->bindParam(':zona_id', $zona_id, PDO::PARAM_INT);
            
            return $stmt->execute();
            
        } catch (PDOException $e) {
            throw new Exception("Error al actualizar estado de zona: " . $e->getMessage());
        }
    }

    /**
     * Actualizar una zona completa
     */
    public function actualizarZona($zona_id, $nombre, $descripcion, $capacidad, $estado, $imagen, $hora_apertura, $hora_cierre, $duracion_maxima, $terminos_condiciones)
    {
        try {
            $sql = "UPDATE tbl_zonas 
                    SET zona_nombre = :nombre, 
                        zona_descripcion = :descripcion, 
                        zona_capacidad = :capacidad, 
                        zona_estado = :estado, 
                        zona_imagen = :imagen, 
                        zona_hora_apertura = :hora_apertura, 
                        zona_hora_cierre = :hora_cierre, 
                        zona_duracion_maxima = :duracion_maxima, 
                        zona_terminos_condiciones = :terminos_condiciones,
                        zona_fecha_actualizacion = CURRENT_TIMESTAMP 
                    WHERE zona_id = :zona_id";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':zona_id', $zona_id, PDO::PARAM_INT);
            $stmt->bindParam(':nombre', $nombre);
            $stmt->bindParam(':descripcion', $descripcion);
            $stmt->bindParam(':capacidad', $capacidad, PDO::PARAM_INT);
            $stmt->bindParam(':estado', $estado);
            $stmt->bindParam(':imagen', $imagen);
            $stmt->bindParam(':hora_apertura', $hora_apertura);
            $stmt->bindParam(':hora_cierre', $hora_cierre);
            $stmt->bindParam(':duracion_maxima', $duracion_maxima, PDO::PARAM_INT);
            $stmt->bindParam(':terminos_condiciones', $terminos_condiciones);
            
            return $stmt->execute();
            
        } catch (PDOException $e) {
            throw new Exception("Error al actualizar zona: " . $e->getMessage());
        }
    }

    /**
     * Eliminar una zona (físicamente de la base de datos)
     */
    public function eliminarZona($zona_id)
    {
        try {
            $sql = "DELETE FROM tbl_zonas WHERE zona_id = :zona_id";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':zona_id', $zona_id, PDO::PARAM_INT);
            
            return $stmt->execute();
            
        } catch (PDOException $e) {
            throw new Exception("Error al eliminar zona: " . $e->getMessage());
        }
    }

    /**
     * Verificar si una zona tiene reservas activas
     */
    public function verificarReservasActivasZona($zona_id)
    {
        try {
            $sql = "SELECT COUNT(*) as reservas_activas 
                    FROM tbl_reservas 
                    WHERE zona_id = :zona_id 
                    AND reserva_estado = 'activa'
                    AND reserva_fecha >= CURDATE()";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':zona_id', $zona_id, PDO::PARAM_INT);
            $stmt->execute();
            
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            return $resultado['reservas_activas'];
            
        } catch (PDOException $e) {
            throw new Exception("Error al verificar reservas activas de zona: " . $e->getMessage());
        }
    }

    // ================================
    // MÉTODOS DE VALIDACIÓN Y UTILIDADES
    // ================================

    /**
     * Buscar usuario por número de documento
     */
    public function buscarUsuarioPorDocumento($numero_documento)
    {
        try {
            $sql = "SELECT usuario_cc, usu_cedula, usu_nombre_completo, usu_apartamento_residencia, 
                           usu_torre_residencia, usu_telefono, usu_correo, usu_estado
                    FROM tbl_usuario 
                    WHERE usu_cedula = :numero_documento 
                    AND usu_estado = 'Activo'";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':numero_documento', $numero_documento);
            $stmt->execute();
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            throw new Exception("Error al buscar usuario: " . $e->getMessage());
        }
    }

    /**
     * Obtener estadísticas de reservas
     */
    public function obtenerEstadisticasReservas()
    {
        try {
            $sql = "SELECT 
                        reserva_estado,
                        COUNT(*) as cantidad
                    FROM tbl_reservas 
                    GROUP BY reserva_estado";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            
            $estadisticas = [];
            $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            foreach ($resultados as $resultado) {
                $estadisticas[$resultado['reserva_estado']] = $resultado['cantidad'];
            }
            
            return $estadisticas;
            
        } catch (PDOException $e) {
            throw new Exception("Error al obtener estadísticas: " . $e->getMessage());
        }
    }

    /**
     * Verificar si un usuario tiene reservas pendientes
     */
    public function usuarioTieneReservasPendientes($usuario_id)
    {
        try {
            $sql = "SELECT COUNT(*) as pendientes 
                    FROM tbl_reservas 
                    WHERE usuario_id = :usuario_id 
                    AND reserva_estado = 'activa' 
                    AND reserva_fecha >= CURDATE()";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
            $stmt->execute();
            
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            return $resultado['pendientes'] > 0;
            
        } catch (PDOException $e) {
            throw new Exception("Error al verificar reservas pendientes: " . $e->getMessage());
        }
    }
}