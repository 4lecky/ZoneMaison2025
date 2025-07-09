<?php
// views/reservas/index.php
$title = 'Reservas - Sistema de Reservas';
ob_start();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1><i class="fas fa-calendar-alt"></i> Reservas</h1>
    <a href="index.php?controller=reserva&action=crear" class="btn btn-primary">
        <i class="fas fa-plus"></i> Nueva Reserva
    </a>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Zona</th>
                        <th>Residente</th>
                        <th>Apartamento</th>
                        <th>Fecha</th>
                        <th>Horario</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($reservas)): ?>
                        <?php foreach ($reservas as $reserva): ?>
                            <tr>
                                <td><?php echo $reserva['id']; ?></td>
                                <td><?php echo htmlspecialchars($reserva['zona_nombre']); ?></td>
                                <td><?php echo htmlspecialchars($reserva['nombre_residente']); ?></td>
                                <td><?php echo htmlspecialchars($reserva['apartamento']); ?></td>
                                <td><?php echo date('d/m/Y', strtotime($reserva['fecha_reserva'])); ?></td>
                                <td>
                                    <?php 
                                    echo date('H:i', strtotime($reserva['hora_inicio'])) . ' - ' . 
                                         date('H:i', strtotime($reserva['hora_fin'])); 
                                    ?>
                                </td>
                                <td>
                                    <span class="badge bg-<?php 
                                        echo $reserva['estado'] == 'activa' ? 'success' : 
                                            ($reserva['estado'] == 'cancelada' ? 'danger' : 'secondary'); 
                                    ?>">
                                        <?php echo ucfirst($reserva['estado']); ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="index.php?controller=reserva&action=editar&id=<?php echo $reserva['id']; ?>" 
                                       class="btn btn-sm btn-warning">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="index.php?controller=reserva&action=eliminar&id=<?php echo $reserva['id']; ?>" 
                                       class="btn btn-sm btn-danger" 
                                       onclick="return confirm('¿Está seguro de eliminar esta reserva?')">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="text-center">No hay reservas registradas</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include 'views/layout.php';
?>