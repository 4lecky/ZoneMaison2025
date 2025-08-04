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
<!-- Incluye los íconos Remix -->
<link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">

<script src="https://kit.fontawesome.com/your_kit_code.js" crossorigin="anonymous"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-papmYjKPYLmf7O/93+uFZxTRXz2fp4QWxC5EY4j2gGLbV5Nc1uuj+yDJ+Hi9XpN+gctqq1UnP+yY9tvb++IbmA==" crossorigin="anonymous" referrerpolicy="no-referrer" />





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
                        <a href="?eliminar=<?= $pqr['id']; ?>" onclick="return confirm('¿Estás seguro de eliminar este registro?')" title="Eliminar">
                            <i class="fas fa-trash-alt" style="color: red; font-size: 18px;"></i>
                        </a>
                        <a href="?editar=<?= $pqr['id']; ?>" title="Editar">
                            <i class="fas fa-edit" style="color: blue; font-size: 18px;"></i>
                        </a>
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
