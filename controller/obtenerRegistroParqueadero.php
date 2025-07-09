
<?php
require_once '../config/db.php';

try {
    $stmt = $pdo->query("SELECT parq_id, parq_vehi_placa, parq_nombre_propietario, parq_num_parqueadero, parq_vehi_estadiIngreso FROM tbl_parqueadero ORDER BY parq_id DESC");
    $registros = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($registros);
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
