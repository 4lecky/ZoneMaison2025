<?php
require_once __DIR__ . '/../controller/ReservaController.php';
session_start();

$controller = new ReservaController();
$reservas = $controller->listarReservas();

require_once __DIR__ . '/Layout/header.php';
?>

<div class="container mt-4">
    <?php if (isset($_SESSION['mensaje_exito'])): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <?php echo $_SESSION['mensaje_exito']; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['mensaje_exito']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['mensaje_error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <?php echo $_SESSION['mensaje_error']; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['mensaje_error']); ?>
    <?php endif; ?>

    <h2 class="mb-4">Listado de Reservas</h2>
    <table class="table table-bordered table-hover">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Zona</th>
                <th>Apartamento</th>
                <th>Residente</th>
                <th>Fecha</th>
                <th>Hora Inicio</th>
                <th>Hora Fin</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($reservas as $reserva): ?>
                <tr>
                    <td><?php echo $reserva['id']; ?></td>
                    <td><?php echo htmlspecialchars($reserva['zona_nombre']); ?></td>
                    <td><?php echo htmlspecialchars($reserva['apartamento']); ?></td>
                    <td><?php echo htmlspecialchars($reserva['nombre_residente']); ?></td>
                    <td><?php echo date('d/m/Y', strtotime($reserva['fecha_reserva'])); ?></td>
                    <td><?php echo date('H:i', strtotime($reserva['hora_inicio'])); ?></td>
                    <td><?php echo date('H:i', strtotime($reserva['hora_fin'])); ?></td>
                    <td>
                        <span class="badge bg-<?php echo ($reserva['estado'] === 'activa') ? 'success' : 'secondary'; ?>">
                            <?php echo ucfirst($reserva['estado']); ?>
                        </span>
                    </td>
                    <td>
                        <a href="editar_reserva.php?id=<?php echo $reserva['id']; ?>" class="btn btn-sm btn-warning me-1">
                            <i class="fas fa-edit"></i>
                        </a>
                        <a href="eliminar_reserva.php?id=<?php echo $reserva['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('¿Estás seguro?')">
                            <i class="fas fa-trash-alt"></i>
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php require_once __DIR__ . '/Layout/footer.php'; ?>
