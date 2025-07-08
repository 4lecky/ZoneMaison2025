<?php
require_once '../models/pqrsModel.php';

if (!isset($_GET['cedula'])) {
    echo "<p>No se recibió la cédula.</p>";
    exit;
}

$cedula = $_GET['cedula'];
$model = new PqrsModel();
$resultados = $model->obtenerPorIdentificacion($cedula);

if (!$resultados) {
    echo "<p>No se encontraron PQR con esa cédula.</p>";
    exit;
}
?>

<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<table id="tabla-resultado" class="display">
    <thead>
        <tr>
            <th>ID</th>
            <th>Asunto</th>
            <th>Mensaje</th>
            <th>Tipo</th>
            <th>Estado</th>
            <th>Fecha</th>
            <th>Acciones</th>
        </tr>
    </thead>
<tbody>
    <?php foreach ($resultados as $pqr): ?>
        <tr>
            <td><?= $pqr['id'] ?></td>
            <td><?= htmlspecialchars($pqr['asunto']) ?></td>
            <td><?= htmlspecialchars($pqr['mensaje']) ?></td>
            <td><?= $pqr['tipo_pqr'] ?></td>
            <td><?= $pqr['estado'] ?></td>
            <td><?= $pqr['fecha_creacion'] ?></td>
            <td>
                <?php if ($pqr['estado'] === 'pendiente'): ?>
                    <!-- BOTÓN QUE NECESITAS AQUÍ -->
                    <button class="btn-eliminar" data-id="<?= $pqr['id'] ?>">Eliminar</button>
                <?php else: ?>
                    <span style="color: gray;">No disponible</span>
                <?php endif; ?>
            </td>
        </tr>
    <?php endforeach; ?>
</tbody>

</table>

<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script>
    setTimeout(() => {
        new DataTable("#tabla-resultado", {
            language: {
                url: "//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json"
            }
        });
    }, 100);
</script>

