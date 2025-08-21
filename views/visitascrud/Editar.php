<?php
require_once __DIR__ . "/../../controller/EditCrudVisiControl.php";

$control = new EditCrudVisiControl();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Guardar cambios
    $data = [
        'id' => $_POST['id'],
        'fecha_entrada' => $_POST['fecha_entrada'],
        'fecha_salida'  => $_POST['fecha_salida'],
        'hora_entrada'  => $_POST['hora_entrada'],
        'hora_salida'   => $_POST['hora_salida'],
    ];

    if ($control->actualizar($data)) {
        header("Location: /zonemaison2025/views/visitas.php?mensaje=Visita actualizada correctamente");
    exit();

    } else {
        echo "Error al actualizar";
    }
}

// Cargar datos para editar
$id = $_GET['id'];
$datos = $control->editar($id);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Visita</title>
    <link rel="stylesheet" href="../../assets/css/visitas.css">
</head>
<body>
<div class="form-container">
    <h2>Editar Visita</h2>
    <form method="POST">
        <input type="hidden" name="id" value="<?= $datos->vis_id ?>">

        <label>Fecha Entrada</label>
        <input type="date" name="fecha_entrada" value="<?= $datos->vis_fecha_entrada ?>">

        <label>Fecha Salida</label>
        <input type="date" name="fecha_salida" value="<?= $datos->vis_fecha_salida ?>">

        <label>Hora Entrada</label>
        <input type="time" name="hora_entrada" value="<?= $datos->vis_hora_entrada ?>">

        <label>Hora Salida</label>
        <input type="time" name="hora_salida" value="<?= $datos->vis_hora_salida ?>">

        <button type="submit">Confirmar</button>
        <a href="visitas.php">Cancelar</a>
    </form>
</div>
</body>
</html>
