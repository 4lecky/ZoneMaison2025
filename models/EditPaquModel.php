<?php
require_once __DIR__ . '/../config/db.php';

class EditPaquModel {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

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
     * MÉTODO ACTUALIZAR COMPLETAMENTE CORREGIDO
     */
    public function actualizar($id, $descripcion, $fecha, $hora, $estado, $image = null) {
        try {
            if ($image !== null) {
                // Si hay nueva imagen, eliminar la anterior
                $stmt = $this->pdo->prepare("SELECT paqu_image FROM tbl_paquetes WHERE paqu_Id = ?");
                $stmt->execute([$id]);
                $publicacion = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($publicacion && !empty($publicacion['paqu_image'])) {
                    $rutaAntigua = __DIR__ . '/../' . $publicacion['paqu_image'];
                    if (file_exists($rutaAntigua)) {
                        unlink($rutaAntigua);
                    }
                }

                // CORRECCIÓN: Actualizar con nueva imagen - PARÁMETROS EN ORDEN CORRECTO
                $stmt = $this->pdo->prepare("UPDATE tbl_paquetes 
                    SET paqu_Descripcion = ?, paqu_FechaLlegada = ?, paqu_Hora = ?, paqu_image = ?, paqu_estado = ?
                    WHERE paqu_Id = ?");
                $stmt->execute([$descripcion, $fecha, $hora, $image, $estado, $id]);
            } else {
                // CORRECCIÓN: Actualizar sin tocar imagen - PARÁMETROS EN ORDEN CORRECTO
                $stmt = $this->pdo->prepare("UPDATE tbl_paquetes 
                    SET paqu_Descripcion = ?, paqu_FechaLlegada = ?, paqu_Hora = ?, paqu_estado = ?
                    WHERE paqu_Id = ?");
                $stmt->execute([$descripcion, $fecha, $hora, $estado, $id]);
            }

            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            error_log("Error al actualizar publicación: " . $e->getMessage());
            return false;
        }
    }
   
    /**
     * MÉTODO ELIMINAR MEJORADO
     */
    public function eliminar($id) {
        try {
            // Verificar si existe la publicación
            $stmt = $this->pdo->prepare("SELECT paqu_image FROM tbl_paquetes WHERE paqu_Id = ?");
            $stmt->execute([$id]);
            $publicacion = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$publicacion) {
                return ['success' => false, 'mensaje' => 'Publicación no encontrada.'];
            }

            // Eliminar imagen del servidor si existe
            if (!empty($publicacion['paqu_image'])) {
                $rutaImagen = __DIR__ . '/../' . $publicacion['paqu_image'];
                if (file_exists($rutaImagen)) {
                    unlink($rutaImagen);
                }
            }

            // Eliminar la publicación de la base de datos
            $stmt = $this->pdo->prepare("DELETE FROM tbl_paquetes WHERE paqu_Id = ?");
            $stmt->execute([$id]);

            if ($stmt->rowCount() > 0) {
                return ['success' => true, 'mensaje' => 'Publicación eliminada correctamente.'];
            } else {
                return ['success' => false, 'mensaje' => 'No se pudo eliminar la publicación.'];
            }

        } catch (PDOException $e) {
            error_log("Error al eliminar publicación: " . $e->getMessage());
            return ['success' => false, 'mensaje' => 'Error en el servidor.'];
        }
    }

    /**
     * MÉTODO ADICIONAL: Obtener roles activos (si es necesario)
     */
    public function obtenerRolesActivos() {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM tbl_roles WHERE activo = 1");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al obtener roles: " . $e->getMessage());
            return [];
        }
    }
}
