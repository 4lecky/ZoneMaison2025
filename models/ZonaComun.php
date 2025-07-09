<?php
// models/ZonaComun.php
require_once __DIR__ . '/../config/db.php';

class ZonaComun {
    private $conn;
    private $table = 'zonas_comunes';

    public function __construct() {
        global $pdo;

        if (!isset($pdo)) {
            throw new RuntimeException("Conexión a la base de datos no disponible");
        }

        $this->conn = $pdo;
        $this->verificarEstructuraTabla();
    }

    private function verificarEstructuraTabla() {
        $sql = "SHOW TABLES LIKE '{$this->table}'";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();

        if ($stmt->rowCount() === 0) {
            throw new RuntimeException("La tabla {$this->table} no existe en la base de datos");
        }
    }

    /**
     * Obtiene todas las zonas comunes (para el index del controlador)
     */
    public function obtenerTodas() {
        $sql = "SELECT id, nombre, descripcion, capacidad,
                TIME_FORMAT(hora_apertura, '%H:%i') as hora_apertura,
                TIME_FORMAT(hora_cierre, '%H:%i') as hora_cierre,
                duracion_maxima, estado, imagen
                FROM {$this->table}
                ORDER BY nombre ASC";

        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al obtener todas las zonas: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Obtiene todas las zonas comunes (para compatibilidad con controlador)
     */
    public function listarZonasComunes() {
        // Cambiado para devolver todas las zonas, no sólo activas
        return $this->obtenerTodas();
    }

    /**
     * Obtiene zonas activas para reservas
     */
    public function obtenerActivas() {
        $sql = "SELECT id, nombre, descripcion, capacidad,
                TIME_FORMAT(hora_apertura, '%H:%i') as hora_apertura,
                TIME_FORMAT(hora_cierre, '%H:%i') as hora_cierre,
                duracion_maxima, estado, imagen
                FROM {$this->table}
                WHERE estado = 'activo'
                ORDER BY nombre ASC";

        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al obtener zonas activas: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Obtiene una zona por su ID
     */
    public function obtenerPorId($id) {
        if (!is_numeric($id)) {
            throw new InvalidArgumentException("El ID debe ser numérico");
        }

        $sql = "SELECT id, nombre, descripcion, capacidad,
                TIME_FORMAT(hora_apertura, '%H:%i') as hora_apertura,
                TIME_FORMAT(hora_cierre, '%H:%i') as hora_cierre,
                duracion_maxima, estado, imagen
                FROM {$this->table}
                WHERE id = :id";

        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al obtener zona por ID: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Crea una nueva zona común
     * Retorna el ID insertado o false en caso de error
     */
    public function crear($datos) {
        $required = ['nombre', 'capacidad', 'estado'];
        foreach ($required as $field) {
            if (empty($datos[$field])) {
                throw new InvalidArgumentException("El campo $field es requerido");
            }
        }

        $sql = "INSERT INTO {$this->table}
                (nombre, descripcion, capacidad, hora_apertura, hora_cierre, duracion_maxima, estado, imagen)
                VALUES (:nombre, :descripcion, :capacidad, :hora_apertura, :hora_cierre, :duracion_maxima, :estado, :imagen)";

        try {
            $stmt = $this->conn->prepare($sql);

            $params = [
                ':nombre' => $datos['nombre'],
                ':descripcion' => $datos['descripcion'] ?? null,
                ':capacidad' => $datos['capacidad'],
                ':hora_apertura' => $datos['hora_apertura'] ?? '08:00:00',
                ':hora_cierre' => $datos['hora_cierre'] ?? '20:00:00',
                ':duracion_maxima' => $datos['duracion_maxima'] ?? 2,
                ':estado' => $datos['estado'],
                ':imagen' => $datos['imagen'] ?? null
            ];

            if ($stmt->execute($params)) {
                return $this->conn->lastInsertId();
            } else {
                return false;
            }
        } catch (PDOException $e) {
            error_log("Error al crear zona común: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Actualiza una zona existente
     */
    public function actualizar($id, $datos) {
        if (!is_numeric($id)) {
            throw new InvalidArgumentException("El ID debe ser numérico");
        }

        $sql = "UPDATE {$this->table}
                SET nombre = :nombre,
                    descripcion = :descripcion,
                    capacidad = :capacidad,
                    hora_apertura = :hora_apertura,
                    hora_cierre = :hora_cierre,
                    duracion_maxima = :duracion_maxima,
                    estado = :estado";

        if (isset($datos['imagen'])) {
            $sql .= ", imagen = :imagen";
        }

        $sql .= " WHERE id = :id";

        try {
            $stmt = $this->conn->prepare($sql);

            $params = [
                ':nombre' => $datos['nombre'],
                ':descripcion' => $datos['descripcion'] ?? null,
                ':capacidad' => $datos['capacidad'],
                ':hora_apertura' => $datos['hora_apertura'] ?? '08:00:00',
                ':hora_cierre' => $datos['hora_cierre'] ?? '20:00:00',
                ':duracion_maxima' => $datos['duracion_maxima'] ?? 2,
                ':estado' => $datos['estado'],
                ':id' => $id
            ];

            if (isset($datos['imagen'])) {
                $params[':imagen'] = $datos['imagen'];
            }

            return $stmt->execute($params);
        } catch (PDOException $e) {
            error_log("Error al actualizar zona común: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Elimina una zona común
     */
    public function eliminar($id) {
        if (!is_numeric($id)) {
            throw new InvalidArgumentException("El ID debe ser numérico");
        }

        $sql = "DELETE FROM {$this->table} WHERE id = :id";

        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error al eliminar zona común: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Verifica si una zona tiene reservas asociadas
     */
    public function tieneReservas($id) {
        if (!is_numeric($id)) {
            throw new InvalidArgumentException("El ID debe ser numérico");
        }

        $sql = "SELECT COUNT(*) as count FROM reservas WHERE zona_id = :id";

        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['count'] > 0;
        } catch (PDOException $e) {
            error_log("Error al verificar reservas: " . $e->getMessage());
            return true; // Por seguridad, asumir que tiene reservas si hay error
        }
    }

    /**
     * Obtiene el último ID insertado
     */
    public function obtenerUltimoId() {
        return $this->conn->lastInsertId();
    }
}
?>
