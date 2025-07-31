
<?php
require_once '../config/db.php';

try {
    $stmt = $pdo->query("SELECT vis_id, vis_hora_entrada , vis_hora_salida, vis_fecha_entrada, vis_fecha_salida, vis_torre_visitada, vis_Apto_visitado  FROM tbl_visita ORDER BY vis_id DESC");
    $registros = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($registros);
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
