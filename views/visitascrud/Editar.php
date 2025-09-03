<?php
require_once __DIR__ . "/../../controller/EditCrudVisiControl.php";

$control = new EditCrudVisiControl();

// Guardar cambios
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'id'            => $_POST['id'],
        'fecha_entrada' => $_POST['fecha_entrada'],
        'fecha_salida'  => $_POST['fecha_salida'],
        'hora_entrada'  => $_POST['hora_entrada'],
        'hora_salida'   => $_POST['hora_salida']
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
    <link rel="stylesheet" href="../../assets/css/visitas/editar.css">
</head>
<body>
<div class="form-container">
    <h2>Editar Visita</h2>
    <form method="POST">

        <input type="hidden" name="id" value="<?= $datos->vis_id ?>">

        <!-- Datos de la Visita -->
        <div class="input-group">
            <div class="input-box">
                <label for="fecha_entrada">Fecha Entrada</label>
                <input type="date" class="form-control" name="fecha_entrada" id="fecha_entrada" 
                       value="<?= $datos->vis_fecha_entrada ?>" required>
            </div>

            <div class="input-box">
                <label for="fecha_salida">Fecha Salida</label>
                <input type="date" class="form-control" name="fecha_salida" id="fecha_salida" 
                       value="<?= $datos->vis_fecha_salida ?>" required>
            </div>
        </div>

        <div class="input-group">
            <div class="input-box">
                <label for="hora_entrada">Hora Entrada</label>
                <input type="time" class="form-control" name="hora_entrada" id="hora_entrada" 
                       value="<?= $datos->vis_hora_entrada ?>" required>
            </div>

            <div class="input-box">
                <label for="hora_salida">Hora Salida</label>
                <input type="time" class="form-control" name="hora_salida" id="hora_salida" 
                       value="<?= $datos->vis_hora_salida ?>" required>
            </div>
        </div>

        <button type="submit" class="boton confirmar">Confirmar</button>
        <a href="" class="boton cancelar">Cancelar</a>    
    </form>
</div>
</body>
</html>
    