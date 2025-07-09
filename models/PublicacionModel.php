<?php
require_once __DIR__ . '/../config/db.php';

class PublicacionModel {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function obtenerPorId($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM tbl_muro WHERE muro_id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

public function actualizar($id, $destinatario, $asunto, $descripcion, $fecha, $hora, $nuevaImagen = null) {
    try {
        if ($nuevaImagen) {
            // Eliminar imagen anterior
            $stmt = $this->pdo->prepare("SELECT muro_image FROM tbl_muro WHERE muro_id = ?");
            $stmt->execute([$id]);
            $publicacion = $stmt->fetch();
            if ($publicacion && !empty($publicacion['muro_image'])) {
                $rutaAntigua = __DIR__ . '/../' . $publicacion['muro_image'];
                if (file_exists($rutaAntigua)) {
                    unlink($rutaAntigua);
                }
            }

            // Actualizar con nueva imagen
            $stmt = $this->pdo->prepare("UPDATE tbl_muro 
                SET muro_Destinatario = ?, muro_Asunto = ?, muro_Descripcion = ?, muro_Fecha = ?, muro_Hora = ?, muro_image = ?
                WHERE muro_id = ?");
            $stmt->execute([$destinatario, $asunto, $descripcion, $fecha, $hora, $nuevaImagen, $id]);
        } else {
            // Actualizar sin tocar imagen
            $stmt = $this->pdo->prepare("UPDATE tbl_muro 
                SET muro_Destinatario = ?, muro_Asunto = ?, muro_Descripcion = ?, muro_Fecha = ?, muro_Hora = ?
                WHERE muro_id = ?");
            $stmt->execute([$destinatario, $asunto, $descripcion, $fecha, $hora, $id]);
        }

        return $stmt->rowCount() > 0;
    } catch (PDOException $e) {
        error_log("Error al actualizar publicación: " . $e->getMessage());
        return false;
    }
}

    public function obtenerRolesActivos() {
        $stmt = $this->pdo->prepare("SELECT DISTINCT usu_rol
            FROM tbl_usuario 
            WHERE usu_rol IN ('Administrador', 'Residente', 'Propietario', 'Vigilante') AND usu_estado = 'Activo'
            ORDER BY usu_rol");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function eliminar($id) {
    try {
        //saber si tiene imagen
        $stmt = $this->pdo->prepare("SELECT muro_image FROM tbl_muro WHERE muro_id = ?");
        $stmt->execute([$id]);
        $publicacion = $stmt->fetch();

        if (!$publicacion) {
            return ['success' => false, 'mensaje' => 'Publicación no encontrada.'];
        }

        // eliminarla del servidor
        if (!empty($publicacion['muro_image'])) {
            $rutaImagen = __DIR__ . '/../' . $publicacion['muro_image'];
            if (file_exists($rutaImagen)) {
                unlink($rutaImagen);
            }
        }

        // Eliminar la publicación de la base de datos
        $stmt = $this->pdo->prepare("DELETE FROM tbl_muro WHERE muro_id = ?");
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

}
