<?php
require_once __DIR__ . "/../../controller/EditCrudParqControl.php";

$control = new EditCrudParqControl();

// Guardar cambios
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'parq_id'                    => $_POST['parq_id'], // ← Cambiado de 'id' a 'parq_id'
        'parq_vehi_placa'            => $_POST['parq_vehi_placa'],
        'parq_nombre_propietario_vehi' => $_POST['parq_nombre_propietario_vehi'],
        'parq_tipo_doc_vehi'         => $_POST['parq_tipo_doc_vehi'],
        'parq_num_doc_vehi'          => $_POST['parq_num_doc_vehi'],
        'parq_numeroParqueadero'     => $_POST['parq_numeroParqueadero'],
        'parq_fecha_entrada'         => $_POST['parq_fecha_entrada'],
        'parq_fecha_salida'          => $_POST['parq_fecha_salida'],
        'parq_hora_entrada'          => $_POST['parq_hora_entrada']
    ];  

    $resultado = $control->actualizar($data);
    
    if ($resultado['success']) {
        header("Location: ../../views/parqueadero_crud.php?mensaje=" . urlencode($resultado['message']));
        exit();
    } else {
        $error = $resultado['message'];
    }
}

// Cargar datos para editar
$id = $_GET['id'];
$datos = $control->editar($id);

// Mostrar error si existe
if (isset($error)) {
    echo "<div style='color: red; padding: 10px; border: 1px solid red; margin: 10px;'>Error: $error</div>";
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Editar Parqueadero</title>
    <link rel="stylesheet" href="../../assets/Css/parqueadero_crud/editar.css">
</head>

<body>
    <div class="form-container">
        <h2>Editar Parqueadero</h2>
        
        <!-- Mostrar mensaje de éxito si viene por GET -->
        <?php if (isset($_GET['mensaje'])): ?>
            <div style="color: green; padding: 10px; border: 1px solid green; margin-bottom: 20px;">
                ✅ <?= htmlspecialchars($_GET['mensaje']) ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <!-- CORREGIDO: Cambiar name="id" por name="parq_id" -->
            <input type="hidden" name="parq_id" value="<?= $datos->parq_id ?>">

            <!-- Datos del Parqueadero -->
            <div class="input-group">
                <div class="input-box">
                    <label for="parq_vehi_placa">Placa</label>
                    <input type="text" name="parq_vehi_placa" id="parq_vehi_placa"
                        value="<?= htmlspecialchars($datos->parq_vehi_placa) ?>" required>
                </div>

                <div class="input-box">
                    <label for="parq_nombre_propietario_vehi">Propietario</label>
                    <input type="text" name="parq_nombre_propietario_vehi" id="parq_nombre_propietario_vehi"
                        value="<?= htmlspecialchars($datos->parq_nombre_propietario_vehi) ?>" required>
                </div>
            </div>

            <div class="input-group">
                <div class="input-box">
                    <label for="parq_tipo_doc_vehi">Tipo Documento</label>
                    <input type="text" name="parq_tipo_doc_vehi" id="parq_tipo_doc_vehi"
                        value="<?= htmlspecialchars($datos->parq_tipo_doc_vehi) ?>" required>
                </div>

                <div class="input-box">
                    <label for="parq_num_doc_vehi">Número Documento</label>
                    <input type="text" name="parq_num_doc_vehi" id="parq_num_doc_vehi"
                        value="<?= htmlspecialchars($datos->parq_num_doc_vehi) ?>" required>
                </div>
            </div>

            <div class="input-group">
                <div class="input-box">
                    <label for="parq_numeroParqueadero">N° Parqueadero</label>
                    <input type="text" name="parq_numeroParqueadero" id="parq_numeroParqueadero"
                        value="<?= htmlspecialchars($datos->parq_numeroParqueadero) ?>" required>
                </div>
            </div>

            <div class="input-group">
                <div class="input-box">
                    <label for="parq_fecha_entrada">Fecha Entrada</label>
                    <input type="date" name="parq_fecha_entrada" id="parq_fecha_entrada"
                        value="<?= $datos->parq_fecha_entrada ?>" required>
                </div>

                <div class="input-box">
                    <label for="parq_fecha_salida">Fecha Salida</label>
                    <input type="date" name="parq_fecha_salida" id="parq_fecha_salida"
                        value="<?= $datos->parq_fecha_salida ?>" required>
                </div>
            </div>

            <div class="input-group">
                <div class="input-box">
                    <label for="parq_hora_entrada">Hora Entrada</label>
                    <input type="time" name="parq_hora_entrada" id="parq_hora_entrada"
                        value="<?= $datos->parq_hora_entrada ?>" required>
                </div>
            </div>

            <div class="button-group">
                <button type="submit" class="boton confirmar">Confirmar</button>
                <a href="../parqueadero_crud.php" class="boton cancelar">Cancelar</a>
            </div>
        </form>
    </div>
</body>
</html>