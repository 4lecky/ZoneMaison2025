<?php
class PqrsModel {
    private $conn;
    private $table_name = "tbl_pqrs";

    public function __construct() {
        try {
            // SOLUCIÓN: Usar la conexión global existente o crear nueva
            global $pdo;
            
            if (!$pdo || !($pdo instanceof PDO)) {
                // Si no existe $pdo global, incluir db.php
                $pdo = require_once __DIR__ . '/../config/db.php';
                
                if (!$pdo || !($pdo instanceof PDO)) {
                    throw new Exception("No se pudo establecer conexión a la base de datos");
                }
            }

            $this->conn = $pdo;
            error_log("PqrsModel: Conexión establecida correctamente");

        } catch (Exception $e) {
            error_log("Error en constructor PqrsModel: " . $e->getMessage());
            throw new Exception("Error de conexión a base de datos: " . $e->getMessage());
        }
    }

    /**
     * Crear nueva PQRS - MODIFICADO PARA SOLO CORREO
     */
    public function crear($datos) {
        try {
            $query = "INSERT INTO " . $this->table_name . " 
                     (usuario_cc, nombres, apellidos, identificacion, email, telefono, 
                      tipo_pqr, asunto, mensaje, archivos, medio_respuesta, estado, fecha_creacion) 
                     VALUES 
                     (:usuario_cc, :nombres, :apellidos, :identificacion, :email, :telefono, 
                      :tipo_pqr, :asunto, :mensaje, :archivos, :medio_respuesta, 'pendiente', NOW())";

            $stmt = $this->conn->prepare($query);

            // CAMBIO: Solo manejar correo como medio de respuesta
            $mediosRespuesta = 'correo'; // Siempre correo
            if (is_array($datos['medio_respuesta'])) {
                // Filtrar solo correo por si viene algo más
                $mediosValidos = array_intersect($datos['medio_respuesta'], ['correo']);
                $mediosRespuesta = !empty($mediosValidos) ? 'correo' : 'correo';
            }

            $stmt->bindParam(':usuario_cc', $datos['usuario_cc'], PDO::PARAM_INT);
            $stmt->bindParam(':nombres', $datos['nombres'], PDO::PARAM_STR);
            $stmt->bindParam(':apellidos', $datos['apellidos'], PDO::PARAM_STR);
            $stmt->bindParam(':identificacion', $datos['identificacion'], PDO::PARAM_STR);
            $stmt->bindParam(':email', $datos['email'], PDO::PARAM_STR);
            $stmt->bindParam(':telefono', $datos['telefono'], PDO::PARAM_STR);
            $stmt->bindParam(':tipo_pqr', $datos['tipo_pqr'], PDO::PARAM_STR);
            $stmt->bindParam(':asunto', $datos['asunto'], PDO::PARAM_STR);
            $stmt->bindParam(':mensaje', $datos['mensaje'], PDO::PARAM_STR);
            $stmt->bindParam(':archivos', $datos['archivos'], PDO::PARAM_STR);
            $stmt->bindParam(':medio_respuesta', $mediosRespuesta, PDO::PARAM_STR);

            if ($stmt->execute()) {
                $pqrsId = $this->conn->lastInsertId();
                error_log("PQRS creada con ID: $pqrsId, medio_respuesta: $mediosRespuesta");
                return $pqrsId;
            } else {
                error_log("Error ejecutando query de creación PQRS: " . print_r($stmt->errorInfo(), true));
                return false;
            }
        } catch (Exception $e) {
            error_log("Error creando PQRS: " . $e->getMessage());
            return false;
        }
    }

    public function verificarConexion() {
        return ($this->conn instanceof PDO);
    }

    /**
     * Actualizar PQRS pendiente - MODIFICADO PARA SOLO CORREO
     */
    public function actualizar($datos) {
        try {
            $query = "UPDATE " . $this->table_name . " 
                     SET tipo_pqr = :tipo_pqr,
                         asunto = :asunto,
                         mensaje = :mensaje,
                         archivos = :archivos,
                         medio_respuesta = :medio_respuesta
                     WHERE id = :id AND estado = 'pendiente'";

            $stmt = $this->conn->prepare($query);

            // CAMBIO: Solo correo como medio de respuesta
            $mediosRespuesta = 'correo';
            if (is_array($datos['medio_respuesta'])) {
                $mediosRespuesta = 'correo'; // Forzar correo siempre
            }

            $stmt->bindParam(':id', $datos['id'], PDO::PARAM_INT);
            $stmt->bindParam(':tipo_pqr', $datos['tipo_pqr'], PDO::PARAM_STR);
            $stmt->bindParam(':asunto', $datos['asunto'], PDO::PARAM_STR);
            $stmt->bindParam(':mensaje', $datos['mensaje'], PDO::PARAM_STR);
            $stmt->bindParam(':archivos', $datos['archivos'], PDO::PARAM_STR);
            $stmt->bindParam(':medio_respuesta', $mediosRespuesta, PDO::PARAM_STR);

            return $stmt->execute();
        } catch (Exception $e) {
            error_log("Error actualizando PQRS: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Obtener todas las PQRS de un usuario
     */
    public function obtenerPorUsuario($usuario_cc) {
        try {
            $query = "SELECT * FROM " . $this->table_name . " 
                      WHERE usuario_cc = :usuario_cc 
                      ORDER BY fecha_creacion DESC";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':usuario_cc', $usuario_cc, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Error obteniendo PQRS por usuario: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Obtener PQRS por ID
     */
    public function obtenerPorId($id) {
        try {
            $query = "SELECT * FROM " . $this->table_name . " WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Error obteniendo PQRS por ID: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Obtener todas las PQRS (para administradores)
     */
    public function obtenerTodos() {
        try {
            $query = "SELECT p.*, 
                             u.usu_nombre_completo, u.usu_correo, u.usu_telefono,
                             u.usu_apartamento_residencia, u.usu_torre_residencia
                      FROM " . $this->table_name . " p
                      LEFT JOIN tbl_usuario u ON p.usuario_cc = u.usuario_cc
                      ORDER BY p.fecha_creacion DESC";
            
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (Exception $e) {
            error_log("Error obteniendo todas las PQRS: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Eliminar PQRS pendiente
     */
     public function eliminar($id, $usuario_cc = null) {
        try {
            // Si se proporciona usuario_cc, validar que la PQRS pertenezca al usuario
            if ($usuario_cc !== null) {
                $query = "DELETE FROM " . $this->table_name . " 
                          WHERE id = :id AND usuario_cc = :usuario_cc AND estado = 'pendiente'";
                $stmt = $this->conn->prepare($query);
                $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                $stmt->bindParam(':usuario_cc', $usuario_cc, PDO::PARAM_INT);
            } else {
                // Método original para compatibilidad con otros usos
                $query = "DELETE FROM " . $this->table_name . " 
                          WHERE id = :id AND estado = 'pendiente'";
                $stmt = $this->conn->prepare($query);
                $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            }
            
            return $stmt->execute();
        } catch (Exception $e) {
            error_log("Error eliminando PQRS: " . $e->getMessage());
            return false;
        }
    }


    /**
     * Actualizar estado de PQRS
     */
    public function actualizarEstado($id, $estado) {
        try {
            $query = "UPDATE " . $this->table_name . " 
                      SET estado = :estado 
                      WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':estado', $estado, PDO::PARAM_STR);
            return $stmt->execute();
        } catch (Exception $e) {
            error_log("Error actualizando estado de PQRS: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Obtener PQRS con información completa (para respuestas)
     */
    public function obtenerPqrsCompleta($id) {
        try {
            $query = "SELECT p.*, 
                             u.usu_nombre_completo, u.usu_correo, u.usu_telefono,
                             u.usu_apartamento_residencia, u.usu_torre_residencia,
                             admin.usu_nombre_completo as nombre_admin
                      FROM " . $this->table_name . " p
                      LEFT JOIN tbl_usuario u ON p.usuario_cc = u.usuario_cc
                      LEFT JOIN tbl_usuario admin ON p.respondido_por = admin.usuario_cc
                      WHERE p.id = :id";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
            
        } catch (Exception $e) {
            error_log("Error obteniendo PQRS completa: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Guardar respuesta de PQRS - MODIFICADO PARA SOPORTAR ADJUNTOS
     */
    public function guardarRespuesta($id, $respuesta, $admin_id = null, $cambiarEstado = true, $adjuntos = []) {
        try {
            $this->conn->beginTransaction();

            // Preparar información de adjuntos (si los hay)
            $adjuntosJson = null;
            if (!empty($adjuntos)) {
                // Guardar solo la información necesaria, no las rutas completas
                $infoAdjuntos = [];
                foreach ($adjuntos as $adjunto) {
                    $infoAdjuntos[] = [
                        'nombre_original' => $adjunto['nombre_original'],
                        'nombre_archivo' => $adjunto['nombre_archivo'],
                        'tipo' => $adjunto['tipo'],
                        'tamaño' => $adjunto['tamaño'],
                        'fecha_subida' => $adjunto['fecha_subida'] ?? date('Y-m-d H:i:s')
                    ];
                }
                $adjuntosJson = json_encode($infoAdjuntos);
            }

            // Actualizar la respuesta con adjuntos
            $query = "UPDATE " . $this->table_name . " 
                      SET respuesta = :respuesta,
                          fecha_respuesta = NOW(),
                          respondido_por = :respondido_por";
            
            // Agregar adjuntos si los hay
            if ($adjuntosJson !== null) {
                $query .= ", adjuntos_respuesta = :adjuntos_respuesta";
            }
            
            // Si se debe cambiar el estado
            if ($cambiarEstado) {
                $query .= ", estado = 'resuelto'";
            } else {
                $query .= ", estado = 'en_proceso'";
            }
            
            $query .= " WHERE id = :id";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':respuesta', $respuesta, PDO::PARAM_STR);
            $stmt->bindParam(':respondido_por', $admin_id, PDO::PARAM_INT);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            
            // Bind adjuntos si existen
            if ($adjuntosJson !== null) {
                $stmt->bindParam(':adjuntos_respuesta', $adjuntosJson, PDO::PARAM_STR);
            }
            
            $success = $stmt->execute();
            
            if ($success && $stmt->rowCount() > 0) {
                $this->conn->commit();
                
                // Log para debug
                error_log("Respuesta guardada exitosamente para PQRS ID: $id");
                if (!empty($adjuntos)) {
                    error_log("Adjuntos guardados: " . count($adjuntos));
                }
                
                return true;
            } else {
                $this->conn->rollback();
                error_log("No se pudo guardar la respuesta - rowCount: " . $stmt->rowCount());
                return false;
            }
            
        } catch (Exception $e) {
            $this->conn->rollback();
            error_log("Error guardando respuesta PQRS: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Marcar notificación como enviada - MODIFICADO PARA SOLO CORREO
     */
    public function marcarNotificacionEnviada($id, $tipoNotificacion = 'correo') {
        try {
            // CAMBIO: Solo manejar 'correo' como tipo de notificación
            $query = "UPDATE " . $this->table_name . " 
                      SET notificacion_enviada = 'correo' 
                      WHERE id = :id";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            
            return $stmt->execute();
            
        } catch (Exception $e) {
            error_log("Error marcando notificación enviada: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Buscar PQRS por filtros
     */
    public function buscarPqrs($filtros = []) {
        try {
            $query = "SELECT p.*, u.usu_nombre_completo, u.usu_correo 
                      FROM " . $this->table_name . " p
                      LEFT JOIN tbl_usuario u ON p.usuario_cc = u.usuario_cc
                      WHERE 1=1";
            
            $params = [];
            
            if (!empty($filtros['estado'])) {
                $query .= " AND p.estado = :estado";
                $params[':estado'] = $filtros['estado'];
            }
            
            if (!empty($filtros['tipo_pqr'])) {
                $query .= " AND p.tipo_pqr = :tipo_pqr";
                $params[':tipo_pqr'] = $filtros['tipo_pqr'];
            }
            
            if (!empty($filtros['fecha_desde'])) {
                $query .= " AND DATE(p.fecha_creacion) >= :fecha_desde";
                $params[':fecha_desde'] = $filtros['fecha_desde'];
            }
            
            if (!empty($filtros['fecha_hasta'])) {
                $query .= " AND DATE(p.fecha_creacion) <= :fecha_hasta";
                $params[':fecha_hasta'] = $filtros['fecha_hasta'];
            }
            
            $query .= " ORDER BY p.fecha_creacion DESC";
            
            $stmt = $this->conn->prepare($query);
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }
            
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (Exception $e) {
            error_log("Error buscando PQRS: " . $e->getMessage());
            return [];
        }
    }

    /**
     * NUEVO MÉTODO: Obtener adjuntos de respuesta
     */
    public function obtenerAdjuntosRespuesta($pqrsId) {
        try {
            $query = "SELECT adjuntos_respuesta FROM " . $this->table_name . " WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $pqrsId, PDO::PARAM_INT);
            $stmt->execute();
            
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($resultado && !empty($resultado['adjuntos_respuesta'])) {
                return json_decode($resultado['adjuntos_respuesta'], true);
            }
            
            return [];
            
        } catch (Exception $e) {
            error_log("Error obteniendo adjuntos de respuesta: " . $e->getMessage());
            return [];
        }
    }
}
?>