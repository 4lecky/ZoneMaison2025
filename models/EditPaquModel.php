<?php
require_once __DIR__ . '/../config/db.php';

class EditPaquModel {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    /**
     * Obtiene una publicación por ID
     */
    public function obtenerPorId($id) {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM tbl_paquetes WHERE paqu_Id = ?");
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al obtener publicación: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Actualiza una publicación en la base de datos
     */
    public function actualizar($id, $descripcion, $fecha, $hora) {
        try {
            $stmt = $this->pdo->prepare("UPDATE tbl_paquetes SET paqu_Descripcion = ?, paqu_FechaLlegada = ?, paqu_Hora = ?, paqu_estado= ?; WHERE paqu_Id = ?");
            $stmt->execute([$descripcion, $fecha, $hora, $id, $estado]);
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            error_log("Error al actualizar publicación: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Elimina una publicación de la base de datos
     */
    public function eliminar($id) {
        try {
            // Primero obtener la publicación para verificar si existe
            $publicacion = $this->obtenerPorId($id);
            if (!$publicacion) {
                return false;
            }

            // Eliminar la publicación de la base de datos
            $stmt = $this->pdo->prepare("DELETE FROM tbl_paquetes WHERE paqu_Id = ?");
            $stmt->execute([$id]);
            
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            error_log("Error al eliminar publicación: " . $e->getMessage());
            return false;
        }
    }
}