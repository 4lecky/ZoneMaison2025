<?php
// models/ZonaModel.php
// Modelo oficial de Zonas Comunes (BD: zonemaisons)

class ZonaModel {
    private PDO $pdo;
    private string $tableZonas    = 'zonas_comunes';
    private string $tableReservas = 'reservas';

    // ✅ Inyección de PDO desde el controlador
    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    /** =========================
     *  SELECTS PRINCIPALES
     *  ========================= */
    public function obtenerTodas(): array {
        try {
            $sql = "
                SELECT z.*,
                       COUNT(r.id) AS total_reservas,
                       COUNT(CASE WHEN r.estado = 'pendiente' THEN 1 END) AS reservas_pendientes
                FROM {$this->tableZonas} z
                LEFT JOIN {$this->tableReservas} r ON z.id = r.zona_id
                GROUP BY z.id
                ORDER BY z.fecha_creacion DESC, z.id DESC
            ";
            $stmt = $this->pdo->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("ZonaModel::obtenerTodas => " . $e->getMessage());
            throw new Exception("Error al obtener las zonas");
        }
    }

    public function obtenerActivas(): array {
        try {
            $sql  = "SELECT * FROM {$this->tableZonas} WHERE activo = 1 ORDER BY nombre ASC";
            $stmt = $this->pdo->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("ZonaModel::obtenerActivas => " . $e->getMessage());
            throw new Exception("Error al obtener zonas activas");
        }
    }

    public function obtenerPorId(int $id): ?array {
        try {
            $sql = "
                SELECT z.*,
                       COUNT(r.id) AS total_reservas
                FROM {$this->tableZonas} z
                LEFT JOIN {$this->tableReservas} r ON z.id = r.zona_id
                WHERE z.id = ?
                GROUP BY z.id
                LIMIT 1
            ";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$id]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row ?: null;
        } catch (PDOException $e) {
            error_log("ZonaModel::obtenerPorId => " . $e->getMessage());
            throw new Exception("Error al obtener la zona");
        }
    }

    /** =========================
     *  INSERT / UPDATE / DELETE
     *  ========================= */
    public function crear(array $datos) {
        try {
            $sql = "
                INSERT INTO {$this->tableZonas}
                    (nombre, descripcion, capacidad_maxima, tarifa, activo, fecha_creacion)
                VALUES
                    (:nombre, :descripcion, :capacidad, :tarifa, :activo, NOW())
            ";
            $stmt = $this->pdo->prepare($sql);
            $ok = $stmt->execute([
                ':nombre'      => $datos['nombre'],
                ':descripcion' => $datos['descripcion'] ?? null,
                ':capacidad'   => $datos['capacidad_maxima'],
                ':tarifa'      => $datos['tarifa'],
                ':activo'      => $datos['activo'] ?? 0,
            ]);
            return $ok ? $this->pdo->lastInsertId() : false;
        } catch (PDOException $e) {
            error_log("ZonaModel::crear => " . $e->getMessage());
            throw new Exception("Error al crear la zona");
        }
    }

    public function actualizar(int $id, array $datos): bool {
        try {
            $sql = "
                UPDATE {$this->tableZonas}
                   SET nombre = :nombre,
                       descripcion = :descripcion,
                       capacidad_maxima = :capacidad,
                       tarifa = :tarifa,
                       activo = :activo,
                       fecha_actualizacion = NOW()
                 WHERE id = :id
            ";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([
                ':nombre'      => $datos['nombre'],
                ':descripcion' => $datos['descripcion'] ?? null,
                ':capacidad'   => $datos['capacidad_maxima'],
                ':tarifa'      => $datos['tarifa'],
                ':activo'      => $datos['activo'] ?? 0,
                ':id'          => $id
            ]);
        } catch (PDOException $e) {
            error_log("ZonaModel::actualizar => " . $e->getMessage());
            throw new Exception("Error al actualizar la zona");
        }
    }

    public function eliminar(int $id): bool {
        try {
            $stmt = $this->pdo->prepare("DELETE FROM {$this->tableZonas} WHERE id = ?");
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            error_log("ZonaModel::eliminar => " . $e->getMessage());
            throw new Exception("Error al eliminar la zona");
        }
    }

    public function cambiarEstado(int $id, int $activo): bool {
        try {
            $stmt = $this->pdo->prepare("UPDATE {$this->tableZonas} SET activo = ? , fecha_actualizacion = NOW() WHERE id = ?");
            return $stmt->execute([$activo ? 1 : 0, $id]);
        } catch (PDOException $e) {
            error_log("ZonaModel::cambiarEstado => " . $e->getMessage());
            throw new Exception("Error al cambiar estado de la zona");
        }
    }

    /** =========================
     *  VALIDACIONES / HELPERS
     *  ========================= */
    public function existeNombre(string $nombre, ?int $excluirId = null): bool {
        try {
            $sql = "SELECT COUNT(*) FROM {$this->tableZonas} WHERE nombre = ?";
            $params = [$nombre];

            if (!empty($excluirId)) {
                $sql .= " AND id <> ?";
                $params[] = $excluirId;
            }

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return (int)$stmt->fetchColumn() > 0;
        } catch (PDOException $e) {
            error_log("ZonaModel::existeNombre => " . $e->getMessage());
            return false;
        }
    }

    public function tieneReservasActivas(int $id): bool {
        try {
            $sql = "
                SELECT COUNT(*) 
                FROM {$this->tableReservas}
                WHERE zona_id = ?
                  AND estado IN ('pendiente','confirmada')
                  AND fecha_reserva >= CURDATE()
            ";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$id]);
            return (int)$stmt->fetchColumn() > 0;
        } catch (PDOException $e) {
            error_log("ZonaModel::tieneReservasActivas => " . $e->getMessage());
            return false;
        }
    }

    /** =========================
     *  MÉTRICAS / CONTADORES
     *  ========================= */
    public function contarTotal(): int {
        try {
            $stmt = $this->pdo->query("SELECT COUNT(*) FROM {$this->tableZonas}");
            return (int)$stmt->fetchColumn();
        } catch (PDOException $e) {
            error_log("ZonaModel::contarTotal => " . $e->getMessage());
            return 0;
        }
    }

    public function contarActivas(): int {
        try {
            $stmt = $this->pdo->query("SELECT COUNT(*) FROM {$this->tableZonas} WHERE activo = 1");
            return (int)$stmt->fetchColumn();
        } catch (PDOException $e) {
            error_log("ZonaModel::contarActivas => " . $e->getMessage());
            return 0;
        }
    }

    public function contarInactivas(): int {
        try {
            $stmt = $this->pdo->query("SELECT COUNT(*) FROM {$this->tableZonas} WHERE activo = 0");
            return (int)$stmt->fetchColumn();
        } catch (PDOException $e) {
            error_log("ZonaModel::contarInactivas => " . $e->getMessage());
            return 0;
        }
    }

    public function contarConReservas(): int {
        try {
            $sql = "
                SELECT COUNT(DISTINCT z.id)
                FROM {$this->tableZonas} z
                INNER JOIN {$this->tableReservas} r ON r.zona_id = z.id
                WHERE r.fecha_reserva >= CURDATE()
            ";
            $stmt = $this->pdo->query($sql);
            return (int)$stmt->fetchColumn();
        } catch (PDOException $e) {
            error_log("ZonaModel::contarConReservas => " . $e->getMessage());
            return 0;
        }
    }

    /** =========================
     *  RANKINGS
     *  ========================= */
    public function obtenerMasReservadas(int $limite = 5): array {
        try {
            $sql = "
                SELECT z.*, COUNT(r.id) AS total_reservas
                FROM {$this->tableZonas} z
                LEFT JOIN {$this->tableReservas} r ON z.id = r.zona_id
                WHERE z.activo = 1
                GROUP BY z.id
                ORDER BY total_reservas DESC, z.nombre ASC
                LIMIT :lim
            ";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':lim', $limite, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("ZonaModel::obtenerMasReservadas => " . $e->getMessage());
            return [];
        }
    }
}
