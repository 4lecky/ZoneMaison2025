<?php
require_once __DIR__ . '../../../../models/ZonaComun.php';

$zonaModel = new ZonaComun();
$zonas = $zonaModel->obtenerTodas();
?>


<!DOCTYPE html>
<html lang="es">
<head>

    <!-- Hojas de estilo -->
    <link rel="stylesheet" href="../assets/css/areas-comunes/reserva2.css">
    <link rel="stylesheet" href="../assets/Css/globals.css">
    <link rel="stylesheet" href="../assets/Css/Layout/header.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>


<div class="table-responsive">
    <table class="table table-bordered table-striped align-middle">
        <thead class="table-dark text-center">
            <tr>
                <th>ID</th>
                <th>Zona</th>
                <th>Capacidad</th>
                <th>Horario</th>
                <th>Duración Máx</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody class="text-center">
            <?php if (!empty($zonas)): ?>
                <?php foreach ($zonas as $zona): ?>
                    <tr>
                        <td><?= htmlspecialchars($zona['id']) ?></td>
                        <td><?= htmlspecialchars($zona['nombre']) ?></td>
                        <td><?= $zona['capacidad'] ?></td>
                        <td><?= substr($zona['hora_apertura'], 0, 5) ?> - <?= substr($zona['hora_cierre'], 0, 5) ?></td>
                        <td><?= $zona['duracion_maxima'] ?>h</td>
                        <td>
                            <span class="badge bg-<?= $zona['estado'] === 'activo' ? 'success' : 'danger' ?>">
                                <?= ucfirst($zona['estado']) ?>
                            </span>
                        </td>
                        <td>
                            <a href="/ZoneMaison2025/controllers/ZonaController.php?action=editar&id=<?= $zona['id'] ?>" class="btn btn-warning btn-sm me-1">
                                <i class="fas fa-edit"></i>
                            </a>

                            <a href="/ZoneMaison2025/controllers/ZonaController.php?action=eliminar&id=<?= $zona['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Seguro que quieres eliminar esta zona?')">
                            <title="Eliminar">
                                <i class="fas fa-trash-alt"></i>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7" class="text-center text-muted">
                        <i class="fas fa-inbox fa-2x"></i><br>
                        No hay zonas comunes registradas.
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
