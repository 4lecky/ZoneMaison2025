<?php
// views/ModuloReservas/zonas/CrudZona.php
// Listado y gestión de Zonas Comunes

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

error_reporting(E_ALL);
ini_set('display_errors', '1');

/**
 * Bootstrap (usa el mismo que reservas)
 */
$bootstrap = __DIR__ . '/../reservas/bootstrap.php';
if (file_exists($bootstrap)) {
    require_once $bootstrap;
} else {
    require_once dirname(__DIR__, 3) . '/config/db.php';
    require_once dirname(__DIR__, 3) . '/models/ZonaModel.php';
    require_once dirname(__DIR__, 3) . '/controller/ZonaController.php';
}

try {
    $zonaController = new ZonaController();

    // Procesar POST (crear, actualizar, eliminar, cambiar estado)
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        try {
            $zonaController->manejarSolicitud();
            header('Location: CrudZona.php'); // prevenir reenvío
            exit;
        } catch (Throwable $e) {
            $_SESSION['mensaje'] = "Error: " . $e->getMessage();
            $_SESSION['tipo_mensaje'] = "danger";
        }
    }

    // Cargar zonas
    $zonas = [];
    try {
        $zonas = $zonaController->obtenerTodasLasZonas();
    } catch (Throwable $e) {
        $_SESSION['mensaje'] = "Error cargando zonas: " . $e->getMessage();
        $_SESSION['tipo_mensaje'] = "danger";
    }

} catch (Throwable $e) {
    echo "<h1>Error Crítico</h1>";
    echo "<pre>" . htmlspecialchars($e->getMessage()) . "</pre>";
    exit;
}

// Header layout
$headerPath = dirname(__DIR__, 2) . '/Layout/header.php';
if (file_exists($headerPath)) {
    include $headerPath;
}
?>

<div class="container mt-4">
    <h2 class="mb-4"><i class="fas fa-cubes me-2"></i>Gestión de Zonas Comunes</h2>

    <!-- Mensajes de sesión -->
    <?php if (isset($_SESSION['mensaje'])): ?>
        <div class="alert alert-<?= $_SESSION['tipo_mensaje'] === 'error' ? 'danger' : $_SESSION['tipo_mensaje'] ?>">
            <?= htmlspecialchars($_SESSION['mensaje']) ?>
        </div>
        <?php unset($_SESSION['mensaje'], $_SESSION['tipo_mensaje']); ?>
    <?php endif; ?>

    <div class="mb-3">
        <a href="CrearZona.php" class="btn btn-success">
            <i class="fas fa-plus-circle"></i> Nueva Zona
        </a>
        <a href="../reservas/CrudReserva.php" class="btn btn-info">
            <i class="fas fa-calendar-check"></i> Ver Reservas
        </a>
    </div>

    <?php if (empty($zonas)): ?>
        <div class="alert alert-info">
            <h5>No hay zonas registradas</h5>
            <p>
                <a href="CrearZona.php" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus"></i> Crear primera zona
                </a>
            </p>
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-striped table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Descripción</th>
                        <th>Capacidad</th>
                        <th>Tarifa</th>
                        <th>Estado</th>
                        <th>Reservas</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($zonas as $z): ?>
                        <tr>
                            <td><?= (int)($z['id'] ?? 0) ?></td>
                            <td><strong><?= htmlspecialchars($z['nombre'] ?? '') ?></strong></td>
                            <td><?= htmlspecialchars($z['descripcion'] ?? '—') ?></td>
                            <td><?= (int)($z['capacidad_maxima'] ?? 0) ?></td>
                            <td>$<?= number_format((float)($z['tarifa'] ?? 0), 0, ',', '.') ?></td>
                            <td>
                                <?php if (!empty($z['activo'])): ?>
                                    <span class="badge bg-success">Activa</span>
                                <?php else: ?>
                                    <span class="badge bg-danger">Inactiva</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="badge bg-info">
                                    <?= (int)($z['total_reservas'] ?? 0) ?>
                                </span>
                            </td>
                            <td class="text-center">
                                <!-- Editar -->
                                <a href="EditarZona.php?id=<?= (int)$z['id'] ?>" class="btn btn-sm btn-primary">
                                    <i class="fas fa-edit"></i> Editar
                                </a>

                                <!-- Eliminar -->
                                <form method="POST" action="" class="d-inline"
                                      onsubmit="return confirm('¿Eliminar la zona <?= htmlspecialchars($z['nombre']) ?>?');">
                                    <input type="hidden" name="accion" value="eliminar">
                                    <input type="hidden" name="id" value="<?= (int)$z['id'] ?>">
                                    <button type="submit" class="btn btn-sm btn-danger">
                                        <i class="fas fa-trash"></i> Eliminar
                                    </button>
                                </form>

                                <!-- Cambiar Estado -->
                                <form method="POST" action="" class="d-inline">
                                    <input type="hidden" name="accion" value="cambiar_estado">
                                    <input type="hidden" name="id" value="<?= (int)$z['id'] ?>">
                                    <input type="hidden" name="activo" value="<?= !empty($z['activo']) ? 0 : 1 ?>">
                                    <button type="submit" class="btn btn-sm btn-warning">
                                        <?= !empty($z['activo']) ? '<i class="fas fa-ban"></i> Desactivar' : '<i class="fas fa-check"></i> Activar' ?>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<?php
// Footer layout
$footerPath = dirname(__DIR__, 2) . '/Layout/footer.php';
if (file_exists($footerPath)) {
    include $footerPath;
}
?>
