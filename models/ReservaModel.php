<?php
// models/ReservaModel.php
// Modelo oficial de Reservas (BD: zonemaisons)

require_once __DIR__ . '/../config/db.php';

class ReservaModel {
    private PDO $pdo;
    private string $table = 'reservas';
    private string $tableZonas = 'zonas_comunes';

    public function __construct() {
        try {
            $this->pdo = getConnection(); // Debe conectar a la BD 'zonemaisons'
        } catch (Exception $e) {
            error_log("Error conexión BD ReservaModel: " . $e->getMessage());
            throw new Exception("Error de conexión a la base de datos");
        }
    }

    /* ============================================================
     * SELECTS BÁSICOS / LISTADOS
     * ============================================================ */

    /** Obtener todas las reservas con info de la zona */
    public function obtenerTodas(): array {
        try {
            $sql = "
                SELECT r.*, z.nombre AS zona_nombre, z.tarifa AS zona_tarifa
                FROM {$this->table} r
                INNER JOIN {$this->tableZonas} z ON z.id = r.zona_id
                ORDER BY r.fecha_reserva DESC, r.hora_inicio DESC, r.id DESC
            ";
            return $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("ReservaModel::obtenerTodas => " . $e->getMessage());
            throw new Exception("Error al obtener las reservas");
        }
    }

    /** Obtener reservas por estado */
    public function obtenerPorEstado(string $estado): array {
        try {
            $sql = "
                SELECT r.*, z.nombre AS zona_nombre, z.tarifa AS zona_tarifa
                FROM {$this->table} r
                INNER JOIN {$this->tableZonas} z ON z.id = r.zona_id
                WHERE r.estado = ?
                ORDER BY r.fecha_reserva DESC, r.hora_inicio DESC, r.id DESC
            ";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$estado]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("ReservaModel::obtenerPorEstado => " . $e->getMessage());
            throw new Exception("Error al obtener reservas por estado");
        }
    }

    /** Obtener reservas por rango de fechas (inclusive) */
    public function obtenerPorRangoFechas(string $fechaInicio, string $fechaFin): array {
        try {
            $sql = "
                SELECT r.*, z.nombre AS zona_nombre, z.tarifa AS zona_tarifa
                FROM {$this->table} r
                INNER JOIN {$this->tableZonas} z ON z.id = r.zona_id
                WHERE r.fecha_reserva BETWEEN ? AND ?
                ORDER BY r.fecha_reserva ASC, r.hora_inicio ASC
            ";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$fechaInicio, $fechaFin]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("ReservaModel::obtenerPorRangoFechas => " . $e->getMessage());
            throw new Exception("Error al obtener reservas por rango de fechas");
        }
    }

    /** Obtener reservas del mes actual */
    public function obtenerDelMesActual(): array {
        try {
            $sql = "
                SELECT r.*, z.nombre AS zona_nombre, z.tarifa AS zona_tarifa
                FROM {$this->table} r
                INNER JOIN {$this->tableZonas} z ON z.id = r.zona_id
                WHERE YEAR(r.fecha_reserva) = YEAR(CURDATE())
                  AND MONTH(r.fecha_reserva) = MONTH(CURDATE())
                ORDER BY r.fecha_reserva DESC, r.hora_inicio DESC
            ";
            return $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("ReservaModel::obtenerDelMesActual => " . $e->getMessage());
            throw new Exception("Error al obtener reservas del mes");
        }
    }

    /** Obtener próximas reservas (>= hoy) con estados activos */
    public function obtenerProximas(int $limite = 10): array {
        try {
            $sql = "
                SELECT r.*, z.nombre AS zona_nombre, z.tarifa AS zona_tarifa
                FROM {$this->table} r
                INNER JOIN {$this->tableZonas} z ON z.id = r.zona_id
                WHERE r.fecha_reserva >= CURDATE()
                  AND r.estado IN ('pendiente','confirmada')
                ORDER BY r.fecha_reserva ASC, r.hora_inicio ASC
                LIMIT ?
            ";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(1, $limite, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("ReservaModel::obtenerProximas => " . $e->getMessage());
            throw new Exception("Error al obtener próximas reservas");
        }
    }

    /** Obtener por ID */
    public function obtenerPorId(int $id): ?array {
        try {
            $sql = "
                SELECT r.*, 
                       z.nombre AS zona_nombre, z.descripcion AS zona_descripcion,
                       z.capacidad_maxima, z.tarifa AS zona_tarifa
                FROM {$this->table} r
                INNER JOIN {$this->tableZonas} z ON z.id = r.zona_id
                WHERE r.id = ?
                LIMIT 1
            ";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$id]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row ?: null;
        } catch (PDOException $e) {
            error_log("ReservaModel::obtenerPorId => " . $e->getMessage());
            throw new Exception("Error al obtener la reserva");
        }
    }

    /** Obtener por apartamento */
    public function obtenerPorApartamento(string $apartamento): array {
        try {
            $sql = "
                SELECT r.*, z.nombre AS zona_nombre, z.tarifa AS zona_tarifa
                FROM {$this->table} r
                INNER JOIN {$this->tableZonas} z ON z.id = r.zona_id
                WHERE r.apartamento = ?
                ORDER BY r.fecha_reserva DESC, r.hora_inicio DESC
            ";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$apartamento]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("ReservaModel::obtenerPorApartamento => " . $e->getMessage());
            throw new Exception("Error al obtener reservas por apartamento");
        }
    }

    /** Obtener por email (útil para búsquedas rápidas) */
    public function obtenerPorEmail(string $email): array {
        try {
            $sql = "
                SELECT r.*, z.nombre AS zona_nombre, z.tarifa AS zona_tarifa
                FROM {$this->table} r
                INNER JOIN {$this->tableZonas} z ON z.id = r.zona_id
                WHERE r.email = ?
                ORDER BY r.fecha_reserva DESC, r.hora_inicio DESC
            ";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$email]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("ReservaModel::obtenerPorEmail => " . $e->getMessage());
            throw new Exception("Error al obtener reservas por email");
        }
    }

    /** Obtener por documento/cedula (para Mis Reservas) */
    public function obtenerPorDocumento(string $cedula): array {
        try {
            $sql = "
                SELECT r.*, z.nombre AS zona_nombre, z.tarifa AS zona_tarifa
                FROM {$this->table} r
                INNER JOIN {$this->tableZonas} z ON z.id = r.zona_id
                WHERE r.cedula = ?
                ORDER BY r.fecha_reserva DESC, r.hora_inicio DESC
            ";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$cedula]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("ReservaModel::obtenerPorDocumento => " . $e->getMessage());
            throw new Exception("Error al obtener reservas por documento");
        }
    }

    /** Obtener por fecha exacta */
    public function obtenerPorFecha(string $fecha): array {
        try {
            $sql = "
                SELECT r.*, z.nombre AS zona_nombre, z.tarifa AS zona_tarifa
                FROM {$this->table} r
                INNER JOIN {$this->tableZonas} z ON z.id = r.zona_id
                WHERE r.fecha_reserva = ?
                ORDER BY r.hora_inicio ASC
            ";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$fecha]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("ReservaModel::obtenerPorFecha => " . $e->getMessage());
            throw new Exception("Error al obtener reservas por fecha");
        }
    }

    /** Conflictos del día para una zona (agenda del día) */
    public function obtenerConflictosHorario(int $zonaId, string $fecha): array {
        try {
            $sql = "
                SELECT r.*, z.nombre AS zona_nombre
                FROM {$this->table} r
                INNER JOIN {$this->tableZonas} z ON z.id = r.zona_id
                WHERE r.zona_id = ?
                  AND r.fecha_reserva = ?
                  AND r.estado IN ('pendiente','confirmada')
                ORDER BY r.hora_inicio ASC
            ";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$zonaId, $fecha]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("ReservaModel::obtenerConflictosHorario => " . $e->getMessage());
            throw new Exception("Error al obtener conflictos de horario");
        }
    }

    /* ============================================================
     * INSERT / UPDATE / DELETE / ESTADO
     * ============================================================ */

    /** Crear reserva
     *  Espera claves:
     *  nombre_usuario, apartamento, telefono, email, cedula (opcional), zona_id,
     *  fecha_reserva (Y-m-d), hora_inicio (H:i), hora_fin (H:i),
     *  numero_personas, descripcion (opcional), estado (default pendiente)
     */
    public function crear(array $datos) {
        try {
            $sql = "
                INSERT INTO {$this->table}
                    (nombre_usuario, apartamento, telefono, email, cedula, zona_id,
                     fecha_reserva, hora_inicio, hora_fin,
                     numero_personas, descripcion, estado, fecha_creacion)
                VALUES
                    (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
            ";
            $estado = $datos['estado'] ?? 'pendiente';
            $stmt = $this->pdo->prepare($sql);
            $ok = $stmt->execute([
                $datos['nombre_usuario'],
                $datos['apartamento'],
                $datos['telefono'],
                $datos['email'],
                $datos['cedula'] ?? null,
                $datos['zona_id'],
                $datos['fecha_reserva'],
                $datos['hora_inicio'],
                $datos['hora_fin'],
                $datos['numero_personas'],
                $datos['descripcion'] ?? null,
                $estado
            ]);
            return $ok ? $this->pdo->lastInsertId() : false;
        } catch (PDOException $e) {
            error_log("ReservaModel::crear => " . $e->getMessage());
            throw new Exception("Error al crear la reserva");
        }
    }

    /** Actualizar reserva */
    public function actualizar(int $id, array $datos): bool {
        try {
            $sql = "
                UPDATE {$this->table}
                   SET nombre_usuario = ?,
                       apartamento = ?,
                       telefono = ?,
                       email = ?,
                       cedula = ?,
                       zona_id = ?,
                       fecha_reserva = ?,
                       hora_inicio = ?,
                       hora_fin = ?,
                       numero_personas = ?,
                       descripcion = ?,
                       estado = ?,
                       fecha_actualizacion = NOW()
                 WHERE id = ?
            ";
            $estado = $datos['estado'] ?? 'pendiente';
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([
                $datos['nombre_usuario'],
                $datos['apartamento'],
                $datos['telefono'],
                $datos['email'],
                $datos['cedula'] ?? null,
                $datos['zona_id'],
                $datos['fecha_reserva'],
                $datos['hora_inicio'],
                $datos['hora_fin'],
                $datos['numero_personas'],
                $datos['descripcion'] ?? null,
                $estado,
                $id
            ]);
        } catch (PDOException $e) {
            error_log("ReservaModel::actualizar => " . $e->getMessage());
            throw new Exception("Error al actualizar la reserva");
        }
    }

    /** Eliminar reserva */
    public function eliminar(int $id): bool {
        try {
            $stmt = $this->pdo->prepare("DELETE FROM {$this->table} WHERE id = ?");
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            error_log("ReservaModel::eliminar => " . $e->getMessage());
            throw new Exception("Error al eliminar la reserva");
        }
    }

    /** Cambiar estado */
    public function cambiarEstado(int $id, string $estado): bool {
        try {
            $stmt = $this->pdo->prepare("
                UPDATE {$this->table}
                   SET estado = ?, fecha_actualizacion = NOW()
                 WHERE id = ?
            ");
            return $stmt->execute([$estado, $id]);
        } catch (PDOException $e) {
            error_log("ReservaModel::cambiarEstado => " . $e->getMessage());
            throw new Exception("Error al cambiar estado de la reserva");
        }
    }

    /* ============================================================
     * DISPONIBILIDAD / SOLAPES
     * ============================================================ */

    /**
     * Verificar disponibilidad de una zona en fecha/hora.
     * Acepta:
     *   - verificarDisponibilidad($datosArray)  // con claves: zona_id, fecha_reserva, hora_inicio, hora_fin, (opcional) excluir_reserva_id
     *   - verificarDisponibilidad($zonaId, $fecha, $horaInicio, $horaFin, $excluirId = null)
     * Retorna true si está libre, false si hay choque.
     */
    public function verificarDisponibilidad($arg1, $fecha = null, $horaInicio = null, $horaFin = null, $excluirId = null): bool {
        // Normalizar argumentos
        if (is_array($arg1)) {
            $zonaId    = (int)($arg1['zona_id'] ?? 0);
            $fecha     = (string)($arg1['fecha_reserva'] ?? '');
            $horaInicio= (string)($arg1['hora_inicio'] ?? '');
            $horaFin   = (string)($arg1['hora_fin'] ?? '');
            $excluirId = isset($arg1['excluir_reserva_id']) ? (int)$arg1['excluir_reserva_id'] : null;
        } else {
            $zonaId = (int)$arg1;
        }

        try {
            // Solape de intervalos de tiempo (cualquier cruce)
            $sql = "
                SELECT COUNT(*)
                  FROM {$this->table}
                 WHERE zona_id = ?
                   AND fecha_reserva = ?
                   AND estado IN ('pendiente','confirmada')
                   AND (
                        (hora_inicio < ? AND hora_fin > ?) OR
                        (hora_inicio < ? AND hora_fin > ?) OR
                        (hora_inicio >= ? AND hora_fin <= ?) OR
                        (hora_inicio <= ? AND hora_fin >= ?)
                   )
            ";
            $params = [
                $zonaId, $fecha,
                $horaFin, $horaInicio,     // intervalo existente cruza al nuevo
                $horaFin, $horaInicio,     // (repetido por seguridad; se puede condensar)
                $horaInicio, $horaFin,     // existente dentro del nuevo
                $horaInicio, $horaFin      // cubre borde exacto
            ];

            if (!empty($excluirId)) {
                $sql .= " AND id <> ?";
                $params[] = $excluirId;
            }

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return ((int)$stmt->fetchColumn()) === 0;
        } catch (PDOException $e) {
            error_log("ReservaModel::verificarDisponibilidad => " . $e->getMessage());
            return false;
        }
    }

    /* ============================================================
     * CONTADORES / MÉTRICAS
     * ============================================================ */

    public function contarTotal(): int {
        try {
            return (int)$this->pdo->query("SELECT COUNT(*) FROM {$this->table}")->fetchColumn();
        } catch (PDOException $e) {
            error_log("ReservaModel::contarTotal => " . $e->getMessage());
            return 0;
        }
    }

    public function contarPorEstado(string $estado): int {
        try {
            $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM {$this->table} WHERE estado = ?");
            $stmt->execute([$estado]);
            return (int)$stmt->fetchColumn();
        } catch (PDOException $e) {
            error_log("ReservaModel::contarPorEstado => " . $e->getMessage());
            return 0;
        }
    }

    public function contarPorFecha(string $fecha): int {
        try {
            $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM {$this->table} WHERE fecha_reserva = ?");
            $stmt->execute([$fecha]);
            return (int)$stmt->fetchColumn();
        } catch (PDOException $e) {
            error_log("ReservaModel::contarPorFecha => " . $e->getMessage());
            return 0;
        }
    }

    public function contarProximas(): int {
        try {
            $sql = "
                SELECT COUNT(*)
                  FROM {$this->table}
                 WHERE fecha_reserva >= CURDATE()
                   AND estado IN ('pendiente','confirmada')
            ";
            return (int)$this->pdo->query($sql)->fetchColumn();
        } catch (PDOException $e) {
            error_log("ReservaModel::contarProximas => " . $e->getMessage());
            return 0;
        }
    }

    /* ============================================================
     * ESTADÍSTICAS Y RANKINGS
     * ============================================================ */

    /** Serie diaria últimos 30 días por estado */
    public function obtenerEstadisticasAvanzadas(): array {
        try {
            $sql = "
                SELECT 
                    DATE(fecha_reserva) AS fecha,
                    COUNT(*) AS total_reservas,
                    COUNT(CASE WHEN estado = 'confirmada' THEN 1 END) AS confirmadas,
                    COUNT(CASE WHEN estado = 'pendiente'  THEN 1 END) AS pendientes,
                    COUNT(CASE WHEN estado = 'cancelada'  THEN 1 END) AS canceladas
                FROM {$this->table}
                WHERE fecha_reserva >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
                GROUP BY DATE(fecha_reserva)
                ORDER BY fecha DESC
            ";
            return $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("ReservaModel::obtenerEstadisticasAvanzadas => " . $e->getMessage());
            return [];
        }
    }

    /** Zonas más reservadas (top N) */
    public function obtenerZonasMasReservadas(int $limite = 5): array {
        try {
            $sql = "
                SELECT z.id, z.nombre, COUNT(r.id) AS total_reservas
                FROM {$this->tableZonas} z
                LEFT JOIN {$this->table} r ON z.id = r.zona_id
                WHERE z.activo = 1
                GROUP BY z.id, z.nombre
                ORDER BY total_reservas DESC, z.nombre ASC
                LIMIT :lim
            ";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':lim', $limite, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("ReservaModel::obtenerZonasMasReservadas => " . $e->getMessage());
            return [];
        }
    }
}
