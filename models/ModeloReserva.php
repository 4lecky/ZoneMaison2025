<?php
require_once __DIR__ . '/../config/db.php'; // Incluir la conexión a la base de datos

class ModeloReserva {
    public function crear($nombre_usuario, $numero_apartamento, $fecha_fin, $hora_inicio, $hora_fin, $area_id, $usuario_cc) {
        global $conn; // Usar la conexión global

        $sql = "INSERT INTO tbl_reserva (rese_usuario_nombre, rese_numero_apartamento, rese_fecha_fin, rese_hora_inicio, rese_hora_fin, rese_area_id, rese_usuario_cc) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssiis", $nombre_usuario, $numero_apartamento, $fecha_fin, $hora_inicio, $hora_fin, $area_id, $usuario_cc);

        if ($stmt->execute()) {
            echo "Nueva reserva creada correctamente";
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }

        $stmt->close();
    }
}