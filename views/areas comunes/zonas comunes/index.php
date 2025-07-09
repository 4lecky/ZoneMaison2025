<?php require_once __DIR__ . '/../Layout/header.php'; ?>

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

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Zonas Comunes</h2>
        <a href="index.php?controller=zona&action=crear" class="btn btn-primary">
            <i class="fas fa-plus"></i> Nueva Zona
        </a>
    </div>

    <table class="table table-striped table-bordered">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Capacidad</th>
                <th>Horario</th>
                <th>Duración Máx.</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($zonas as $zona): ?>
            <tr>
                <td><?php echo $zona['id']; ?></td>
                <td><?php echo htmlspecialchars($zona['nombre']); ?></td>
                <td><?php echo $zona['capacidad']; ?> personas</td>
                <td><?php echo substr($zona['hora_apertura'], 0, 5); ?> - <?php echo substr($zona['hora_cierre'], 0, 5); ?></td>
                <td><?php echo $zona['duracion_maxima']; ?> horas</td>
                <td>
                    <span class="badge bg-<?php echo ($zona['estado'] == 'activo') ? 'success' : 'secondary'; ?>">
                        <?php echo ucfirst($zona['estado']); ?>
                    </span>
                </td>
                <td>
                    <a href="index.php?controller=zona&action=editar&id=<?php echo $zona['id']; ?>" class="btn btn-sm btn-warning">
                        <i class="fas fa-edit"></i>
                    </a>
                    <a href="index.php?controller=zona&action=eliminar&id=<?php echo $zona['id']; ?>" 
                    class="btn btn-sm btn-danger" 
                    onclick="return confirm('¿Estás seguro de eliminar esta zona?')">
                        <i class="fas fa-trash"></i>
                    </a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php require_once __DIR__ . '/../Layout/footer.php'; ?>