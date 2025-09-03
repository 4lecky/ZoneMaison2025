<?php
require_once "../../guardarDatosRegistroParqueadero.php"; // conexión PDO

// Guardar cambios
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $sql = "UPDATE tbl_parqueadero 
            SET parq_vehi_placa=?, parq_nombre_propietario_vehi=?, 
                parq_tipo_doc_vehi=?, parq_num_doc_vehi=?, 
                parq_vehi_estadoIngreso=?, parq_numeroParqueadero=?, 
                parq_fecha_entrada=?, parq_fecha_salida=?, parq_hora_entrada=? 
            WHERE parq_id=?";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        $_POST['placa'],
        $_POST['propietario'],
        $_POST['tipo_doc'],
        $_POST['num_doc'],
        $_POST['estado'],
        $_POST['num_parqueadero'],
        $_POST['fecha_entrada'],
        $_POST['fecha_salida'],
        $_POST['hora_entrada'],
        $_POST['id']
    ]);

    header("Location: ../parqueadero.php?msg=actualizado");
    exit();
}

// Cargar datos
$id = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM tbl_parqueadero WHERE parq_id=?");
$stmt->execute([$id]);
$datos = $stmt->fetch(PDO::FETCH_OBJ);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Parqueadero</title>
    <link rel="stylesheet" href="../../assets/css/visitas/editar.css">
</head>
<body>
<div class="form-container">
    <h2>Editar Parqueadero</h2>
    <form method="POST">

        <input type="hidden" name="id" value="<?= $datos->parq_id ?>">

        <label>Placa</label>
        <input type="text" name="placa" value="<?= $datos->parq_vehi_placa ?>" required>

        <label>Propietario</label>
        <input type="text" name="propietario" value="<?= $datos->parq_nombre_propietario_vehi ?>" required>

        <label>Tipo Documento</label>
        <input type="text" name="tipo_doc" value="<?= $datos->parq_tipo_doc_vehi ?>" required>

        <label>Número Documento</label>
        <input type="text" name="num_doc" value="<?= $datos->parq_num_doc_vehi ?>" required>

        <label>Estado</label>
        <select name="estado">
            <option <?= $datos->parq_vehi_estadoIngreso=="OCUPADO"?"selected":"" ?>>OCUPADO</option>
            <option <?= $datos->parq_vehi_estadoIngreso=="DISPONIBLE"?"selected":"" ?>>DISPONIBLE</option>
            <option <?= $datos->parq_vehi_estadoIngreso=="RESERVADO"?"selected":"" ?>>RESERVADO</option>
        </select>

        <label>N° Parqueadero</label>
        <input type="number" name="num_parqueadero" value="<?= $datos->parq_numeroParqueadero ?>" required>

        <label>Fecha Entrada</label>
        <input type="date" name="fecha_entrada" value="<?= $datos->parq_fecha_entrada ?>">

        <label>Fecha Salida</label>
        <input type="date" name="fecha_salida" value="<?= $datos->parq_fecha_salida ?>">

        <label>Hora Entrada</label>
        <input type="time" name="hora_entrada" value="<?= $datos->parq_hora_entrada ?>">

        <button type="submit" class="boton confirmar">Confirmar</button>
        <a href="../parqueadero.php" class="boton cancelar">Cancelar</a>    
    </form>
</div>
</body>
</html>
