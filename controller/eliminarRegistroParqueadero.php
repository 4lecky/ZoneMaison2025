
<?php
require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];

    try {
        $stmt = $pdo->prepare("DELETE FROM tbl_parqueadero WHERE parq_id = ?");
        $stmt->execute([$id]);
        echo json_encode(['success' => true]);
    } catch (PDOException $e) {
        echo json_encode(['error' => $e->getMessage()]);
    }
}

