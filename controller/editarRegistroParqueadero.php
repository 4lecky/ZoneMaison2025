
<?php
require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $campo = $_POST['campo'];
    $valor = $_POST['valor'];

    $camposPermitidos = [
        'placa' => 'parq_vehi_placa',
        'propietario' => 'parq_nombre_propietario',
        'parqueadero' => 'parq_num_parqueadero',
        'estado' => 'parq_vehi_estadiIngreso'
    ];

    if (!isset($camposPermitidos[$campo])) {
        echo json_encode(['error' => 'Campo no permitido']);
        exit;
    }

    try {
        $sql = "UPDATE tbl_parqueadero SET " . $camposPermitidos[$campo] . " = :valor WHERE parq_id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['valor' => $valor, 'id' => $id]);
        echo json_encode(['success' => true]);
    } catch (PDOException $e) {
        echo json_encode(['error' => $e->getMessage()]);
    }
}
