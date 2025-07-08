<?php
session_start();
require_once '../models/pqrsModel.php';

if (!isset($_SESSION['usuario_cc'])) {
    echo "<p>No has iniciado sesión.</p>";
    exit;
}

$pqrsModel = new PqrsModel();
$registros = $pqrsModel->obtenerPorUsuario($_SESSION['usuario_cc']);
?>

<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<table id="tabla-pqr" class="display">
    <thead>
        <tr>
            <th>Cédula</th>
            <th>Nombres y apellidos</th>
            <th>Teléfono</th>
            <th>Correo</th>
            <th>Estado</th>
            <th>Editar/Eliminar</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($registros as $pqr): ?>
            <tr>
                <td><?= $pqr['identificacion'] ?></td>
                <td><?= $pqr['nombres'] . ' ' . $pqr['apellidos'] ?></td>
                <td><?= $pqr['telefono'] ?></td>
                <td><?= $pqr['email'] ?></td>
                <td><?= $pqr['estado'] ?></td>
                <td>
                    <?php if ($pqr['estado'] === 'pendiente'): ?>
                        <a href="../controller/pqrsController.php?eliminar=<?= $pqr['id'] ?>" onclick="return confirm('¿Eliminar esta solicitud?')">Eliminar</a>
                    <?php else: ?>
                        -
                    <?php endif ?>
                </td>
            </tr>
        <?php endforeach ?>
    </tbody>
</table>

<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script>
    setTimeout(() => {
        new DataTable("#tabla-pqr", {
            language: {
                url: "//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json"
            }
        });
    }, 100);
</script>
