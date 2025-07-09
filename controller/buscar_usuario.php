<?php
header('Content-Type: application/json'); // MUY IMPORTANTE

if (!isset($_GET['cedula'])) {
    echo json_encode(['error' => 'Cédula no proporcionada']);
    exit;
}

$cedula = $_GET['cedula'];

$pdo = require_once "../config/db.php"; // o ajusta el path si no carga

$stmt = $pdo->prepare("SELECT usu_nombre_completo FROM tbl_usuario WHERE usu_cedula = ?");
$stmt->execute([$cedula]);

$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

if ($usuario) {
    echo json_encode(['nombre' => $usuario['usu_nombre_completo']]);
} else {
    echo json_encode(['nombre' => null]);
}
