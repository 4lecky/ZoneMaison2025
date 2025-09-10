<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['usuario'])) {
    header("Location: ../index.php");
    exit;
}

$id = $_GET['id'] ?? 0;
if (!$id) {
    echo "ID de PQRS no válido.";
    exit;
}

require_once '../models/pqrsModel.php';
$pqrsModel = new PqrsModel();
$pqr = $pqrsModel->obtenerPorId($id);

if (!$pqr || $pqr['usuario_cc'] != $_SESSION['usuario']['usuario_cc']) {
    echo "No tiene permisos para editar esta PQRS.";
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar PQRS</title>
    <link rel="stylesheet" href="../assets/Css/pqrs.css">
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
</head>
<body>
    <h1>Editar PQRS #<?= $id ?></h1>

    <form id="formEditar" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?= $pqr['id'] ?>">

        <label>Tipo:</label>
        <select name="tipo_pqr" required>
            <option value="peticion" <?= $pqr['tipo_pqr']=="peticion"?"selected":"" ?>>Petición</option>
            <option value="queja" <?= $pqr['tipo_pqr']=="queja"?"selected":"" ?>>Queja</option>
            <option value="reclamo" <?= $pqr['tipo_pqr']=="reclamo"?"selected":"" ?>>Reclamo</option>
            <option value="sugerencia" <?= $pqr['tipo_pqr']=="sugerencia"?"selected":"" ?>>Sugerencia</option>
        </select><br><br>

        <label>Asunto:</label>
        <input type="text" name="asunto" value="<?= htmlspecialchars($pqr['asunto']) ?>" required><br><br>

        <label>Mensaje:</label><br>
        <textarea name="mensaje" rows="5" required><?= htmlspecialchars($pqr['mensaje']) ?></textarea><br><br>

        <label>Medio de Respuesta:</label>
        <input type="text" name="medio_respuesta" value="<?= htmlspecialchars($pqr['medio_respuesta']) ?>"><br><br>

        <label>Archivos (opcional):</label>
        <input type="file" name="archivos[]" multiple><br><br>

        <button type="submit">Guardar Cambios</button>
    </form>

    <script>
    $("#formEditar").submit(function(e){
        e.preventDefault();
        var formData = new FormData(this);
        $.ajax({
            url: "../controller/editarPqr.php",
            type: "POST",
            data: formData,
            contentType: false,
            processData: false,
            success: function(res){
                try {
                    var r = JSON.parse(res);
                    alert(r.message);
                    if(r.success){
                        window.location.href = "mis_pqrs.php";
                    }
                } catch(e){
                    alert("Error inesperado: " + res);
                }
            }
        });
    });
    </script>
</body>
</html>
