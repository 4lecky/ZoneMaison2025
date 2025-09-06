<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}
require_once "../config/db.php";
require_once "../controller/PublicacionController.php";
require_once "./Layout/header.php";


$controller = new PublicacionController($pdo);
$mensaje = '';
$publicacion = null;

// Procesar eliminación
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['eliminar'])) {
    $resultado = $controller->eliminar($_POST['id']);
    if ($resultado['success']) {
        header("Location: novedades.php?success=" . urlencode($resultado['mensaje']));
        exit();
    } else {
        $mensaje = $resultado['mensaje'];
    }
}

// Procesar edición
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['editar'])) {
    $resultado = $controller->actualizar($_POST);
    $mensaje = $resultado['mensaje'];
    if (isset($resultado['data'])) {
        $publicacion = $resultado['data'];
    }
} elseif (isset($_GET['id'])) {
    $publicacion = $controller->editar(intval($_GET['id']));
    if (!$publicacion) {
        $mensaje = "Publicación no encontrada.";
    }
}

$roles = $controller->obtenerRoles();
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Publicación - ZONEMAISONS</title>
    <link rel="stylesheet" href="../assets/Css/globals.css" />
    <link rel="stylesheet" href="../assets/Css/Layout/header.css" />
    <link rel="stylesheet" href="../assets/Css/Layout/footer.css" />
    <link rel="stylesheet" href="../assets/Css/ComunicaciondeNovedades/edit_paqueteria.css" />

</head>

<body>
    <main>
        <section class="principal-page">
            <h2>Editar Publicación</h2>

            <?php if ($mensaje): ?>
                <div class="alert <?= strpos($mensaje, 'Error') !== false ? 'alert-error' : 'alert-success' ?>">
                    <?= htmlspecialchars($mensaje) ?>
                </div>
            <?php endif; ?>

            <?php if ($publicacion): ?>
                <form method="POST" enctype="multipart/form-data" class="editar-form">
                    <fieldset>
                        <legend>Formulario de Muro</legend>

                        <input type="hidden" name="id" value="<?= htmlspecialchars($publicacion['muro_Id']) ?>">

                        <label for="destinatario">Selecciona el rol destinatario<span class="asterisco">*</span></label>
                        <select class="form-control" id="destinatario" name="destinatario" required>
                            <option value="" disabled>Seleccione un Rol</option>
                            <?php foreach ($roles as $role): ?>
                                <option value="<?= htmlspecialchars($role['usu_rol']) ?>"
                                    <?= ($publicacion['muro_Destinatario'] === $role['usu_rol']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($role['usu_rol']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>

                        <label for="asunto">Asunto<span class="asterisco">*</span></label>
                        <input type="text" id="asunto" name="asunto" class="form-control"
                            value="<?= htmlspecialchars($publicacion['muro_Asunto']) ?>"
                            required maxlength="255">

                        <label for="descripcion">Descripción <span class="asterisco">*</span></label>
                        <textarea id="descripcion" name="descripcion" class="form-control"
                            rows="10" required maxlength="1000"><?= htmlspecialchars($publicacion['muro_Descripcion']) ?></textarea>

                        <label for="imagen">Reemplazar imagen (opcional)</label>
                        <input type="file" id="imagen" name="imagen" accept="image/*" class="form-control">

                        <?php if (!empty($publicacion['muro_image'])): ?>
                            <div class="image-container">
                                <label>Imagen actual:</label>
                                <br>
                                <img src="../<?= htmlspecialchars($publicacion['muro_image']) ?>"
                                    alt="Imagen actual" class="current-image">
                            </div>
                        <?php endif; ?>

                        <div class="btn-container">
                            <button type="submit" name="editar" class="enviar">Guardar Cambios</button>
                            <button type="button" class="eliminar" onclick="abrirModal()">Eliminar</button>
                            <a href="novedades.php" class="cancelar">Volver</a>
                        </div>
                    </fieldset>
                </form>

                <!-- Modal de confirmación para eliminar -->
                <div id="modalEliminar" class="modal">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h3>Confirmar eliminación</h3>
                            <span class="close" onclick="cerrarModal()">&times;</span>
                        </div>
                        <div class="modal-body">
                            <p>¿Estás seguro de que deseas eliminar esta publicación?</p>
                            <p><strong>Esta acción no se puede deshacer.</strong></p>
                        </div>
                        <div class="modal-footer">
                            <button class="btn-modal-cancelar" onclick="cerrarModal()">Cancelar</button>
                            <button class="btn-modal-eliminar" onclick="eliminarPublicacion()">Eliminar</button>
                        </div>
                    </div>
                </div>

                <!-- Formulario oculto para eliminación -->
                <form id="formEliminar" method="POST" style="display: none;">
                    <input type="hidden" name="id" value="<?= htmlspecialchars($publicacion['muro_Id']) ?>">
                    <input type="hidden" name="eliminar" value="1">
                </form>

            <?php else: ?>
                <fieldset>
                    <div class="btn-container">
                        <a href="novedades.php" class="cancelar">Volver</a>
                    </div>
                </fieldset>
            <?php endif; ?>
        </section>
    </main>

    <?php require_once "./Layout/footer.php"; ?>
    <script src="../assets/Js/edit_paqueteria.js"></script>

</body>

</html>