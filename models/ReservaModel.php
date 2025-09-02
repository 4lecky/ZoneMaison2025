<?php
// models/ReservaModel.php
// Modelo oficial de Reservas (BD: zonemaisons)

class ReservaModel {
    private PDO $pdo;
    private string $table = 'reservas';
    private string $tableZonas = 'zonas_comunes';

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    /* ============================================================
     * SELECTS BÁSICOS / LISTADOS
     * ============================================================ */

    /** Obtener todas las reservas con info de la zona */
    public function obtenerTodas(): array {
        $sql = "
            SELECT r.*, z.nombre AS zona_nombre, z.tarifa AS zona_tarifa
            FROM {$this->table} r
            INNER JOIN {$this->tableZonas} z ON z.id = r.zona_id
            ORDER BY r.fecha_reserva DESC, r.hora_inicio DESC, r.id DESC
        ";
        return $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerPorEstado(string $estado): array {
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
    }

    public function obtenerPorRangoFechas(string $fechaInicio, string $fechaFin): array {
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
    }

    public function obtenerDelMesActual(): array {
        $sql = "
            SELECT r.*, z.nombre AS zona_nombre, z.tarifa AS zona_tarifa
            FROM {$this->table} r
            INNER JOIN {$this->tableZonas} z ON z.id = r.zona_id
            WHERE YEAR(r.fecha_reserva) = YEAR(CURDATE())
              AND MONTH(r.fecha_reserva) = MONTH(CURDATE())
            ORDER BY r.fecha_reserva DESC, r.hora_inicio DESC
        ";
        return $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerProximas(int $limite = 10): array {
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
    }

    public function obtenerPorId(int $id): ?array {
        $sql = "
            SELECT r.*, z.nombre AS zona_nombre, z.descripcion AS zona_descripcion,
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
    }

    /* ============================================================
     * INSERT / UPDATE / DELETE / ESTADO
     * ============================================================ */

    public function crear(array $datos) {
        $sql = "
            INSERT INTO {$this->table}
                (nombre_usuario, apartamento, telefono, email, cedula, zona_id,
                 fecha_reserva, hora_inicio, hora_fin,
                 numero_personas, descripcion, estado, fecha_creacion)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
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
    }

    public function actualizar(int $id, array $datos): bool {
        $sql = "
            UPDATE {$this->table}
               SET nombre_usuario = ?, apartamento = ?, telefono = ?, email = ?,
                   cedula = ?, zona_id = ?, fecha_reserva = ?, hora_inicio = ?,
                   hora_fin = ?, numero_personas = ?, descripcion = ?, estado = ?,
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
    }

    public function eliminar(int $id): bool {
        $stmt = $this->pdo->prepare("DELETE FROM {$this->table} WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function cambiarEstado(int $id, string $estado): bool {
        $stmt = $this->pdo->prepare("
            UPDATE {$this->table}
               SET estado = ?, fecha_actualizacion = NOW()
             WHERE id = ?
        ");
        return $stmt->execute([$estado, $id]);
    }

    /* ============================================================
     * DISPONIBILIDAD / SOLAPES
     * ============================================================ */

    public function verificarDisponibilidad(int $zonaId, string $fecha, string $horaInicio, string $horaFin, ?int $excluirId = null): bool {
        $sql = "
            SELECT COUNT(*)
              FROM {$this->table}
             WHERE zona_id = ?
               AND fecha_reserva = ?
               AND estado IN ('pendiente','confirmada')
               AND (hora_inicio < ? AND hora_fin > ?)
        ";
        $params = [$zonaId, $fecha, $horaFin, $horaInicio];
        if ($excluirId) {
            $sql .= " AND id <> ?";
            $params[] = $excluirId;
        }
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return (int)$stmt->fetchColumn() === 0;
    }

    /* ============================================================
     * CONTADORES / MÉTRICAS
     * ============================================================ */

    public function contarTotal(): int {
        return (int)$this->pdo->query("SELECT COUNT(*) FROM {$this->table}")->fetchColumn();
    }

    public function contarPorEstado(string $estado): int {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM {$this->table} WHERE estado = ?");
        $stmt->execute([$estado]);
        return (int)$stmt->fetchColumn();
    }

    public function contarPorFecha(string $fecha): int {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM {$this->table} WHERE fecha_reserva = ?");
        $stmt->execute([$fecha]);
        return (int)$stmt->fetchColumn();
    }

    public function contarProximas(): int {
        $sql = "
            SELECT COUNT(*)
              FROM {$this->table}
             WHERE fecha_reserva >= CURDATE()
               AND estado IN ('pendiente','confirmada')
        ";
        return (int)$this->pdo->query($sql)->fetchColumn();
    }
}
