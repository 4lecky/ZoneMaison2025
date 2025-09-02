<?php
// views/ModuloReservas/reservas/GestionarReserva.php
// Gestión de reservas por apartamento (oficial)

declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

error_reporting(E_ALL);
ini_set('display_errors', '1');

/** Bootstrap del módulo de reservas */
$bootstrap = __DIR__ . '/bootstrap.php';
if (file_exists($bootstrap)) {
    require_once $bootstrap;
} else {
    // Fallback mínimo si faltara el bootstrap del módulo
    require_once dirname(__DIR__, 3) . '/config/db.php';
    require_once dirname(__DIR__, 3) . '/models/ReservaModel.php';
    require_once dirname(__DIR__, 3) . '/controller/ReservaController.php';
}

$reservaController = new ReservaController();
$reservaModel      = new ReservaModel();

$apartamento = '';
$errores     = [];

/** Manejo de acciones POST (cambiar estado / eliminar) */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Delega en el controlador genérico que ya procesa: crear/actualizar/eliminar/cambiar_estado
        $reservaController->manejarSolicitud();
        // Mantener el filtro de apartamento en el redirect (si venía por GET)
        $q = isset($_GET['apartamento']) ? '?apartamento=' . urlencode($_GET['apartamento']) : '';
        header('Location: ' . basename(__FILE__) . $q);
        exit;
    } catch (Throwable $e) {
        $_SESSION['mensaje']      = $e->getMessage();
        $_SESSION['tipo_mensaje'] = 'error';
    }
}

/** Filtro por apartamento (GET) */
if (!empty($_GET['apartamento'])) {
    $apartamento = trim((string)$_GET['apartamento']);
}

/** Traer reservas del apartamento (si hay filtro) */
$reservas = [];
if ($apartamento !== '') {
    try {
        $reservas = $reservaModel->obtenerPorApartamento($apartamento);
    } catch (Throwable $e) {
        $errores[] = 'Error consultando reservas: ' . $e->getMessage();
    }
}

/** Incluir header layout bonito */
$headerPath = dirname(__DIR__, 2) . '/Layout/header.php';
if (file_exists($headerPath)) {
    include $headerPath;
}
?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="mb-0"><i class="fas fa-calendar-day me-2"></i> Gestionar Reservas</h2>
        <div class="d-flex gap-2">
            <a href="CrearReserva.php" class="btn btn-outline-primary">
                <i class="fas fa-plus"></i> Nueva Reserva
            </a>
            <a href="CrudReserva.php" class="btn btn-outline-secondary">
                <i class="fas fa-list"></i> Ver Todas
            </a>
            <a href="../zonas/CrudZona.php" class="btn btn-outline-secondary">
                <i class="fas fa-layer-group"></i> Zonas
            </a>
        </div>
    </div>

    <!-- Flash messages -->
    <?php if (!empty($_SESSION['mensaje'])): ?>
        <div class="alert alert-<?= ($_SESSION['tipo_mensaje'] ?? '') === 'success' ? 'success' : 'danger' ?> alert-dismissible fade show">
            <?= htmlspecialchars($_SESSION['mensaje']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php unset($_SESSION['mensaje'], $_SESSION['tipo_mensaje']); ?>
    <?php endif; ?>

    <?php if ($errores): ?>
        <div class="alert alert-danger">
            <ul class="mb-0">
                <?php foreach ($errores as $e): ?>
                    <li><?= htmlspecialchars($e) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <!-- Formulario de búsqueda por apartamento -->
    <div class="card shadow-sm mb-3">
        <div class="card-body">
            <form method="get" class="row g-3 align-items-end">
                <div class="col-sm-6 col-md-4">
                    <label for="apartamento" class="form-label">
                        <i class="fas fa-home text-primary"></i> Apartamento
                    </label>
                    <input type="text"
                           class="form-control"
                           id="apartamento"
                           name="apartamento"
                           placeholder="Ej: 302"
                           value="<?= htmlspecialchars($apartamento) ?>">
                    <div class="form-text">Consulta las reservas por número de apartamento.</div>
                </div>
                <div class="col-auto">
                    <button class="btn btn-primary">
                        <i class="fas fa-search"></i> Consultar
                    </button>
                </div>
                <?php if ($apartamento !== ''): ?>
                    <div class="col-auto">
                        <a class="btn btn-outline-secondary" href="<?= basename(__FILE__) ?>">
                            <i class="fas fa-eraser"></i> Limpiar
                        </a>
                    </div>
                <?php endif; ?>
            </form>
        </div>
    </div>

    <!-- Resultado -->
    <?php if ($apartamento === ''): ?>
        <div class="alert alert-info">
            <i class="fas fa-info-circle"></i>
            Digita un <strong>apartamento</strong> para listar sus reservas.
        </div>

    <?php elseif (empty($reservas)): ?>
        <div class="alert alert-warning">
            No hay reservas registradas para el apartamento
            <strong><?= htmlspecialchars($apartamento) ?></strong>.
        </div>

    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Zona</th>
                        <th>Fecha</th>
                        <th>Hora Inicio</th>
                        <th>Hora Fin</th>
                        <th>Estado</th>
                        <th>Personas</th>
                        <th width="200" class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($reservas as $r): ?>
                        <?php
                        $estado = $r['estado'] ?? 'pendiente';
                        $badge  = [
                            'pendiente'  => 'warning',
                            'confirmada' => 'success',
                            'cancelada'  => 'danger',
                            'completada' => 'secondary'
                        ][$estado] ?? 'secondary';
                        ?>
                        <tr>
                            <td><strong><?= (int)$r['id'] ?></strong></td>
                            <td><?= htmlspecialchars($r['zona_nombre'] ?? '') ?></td>
                            <td><?= htmlspecialchars(date('d/m/Y', strtotime($r['fecha_reserva']))) ?></td>
                            <td><?= htmlspecialchars(date('H:i', strtotime($r['hora_inicio']))) ?></td>
                            <td><?= htmlspecialchars(date('H:i', strtotime($r['hora_fin']))) ?></td>
                            <td>
                                <span class="badge bg-<?= $badge ?>">
                                    <?= ucfirst($estado) ?>
                                </span>
                            </td>
                            <td><?= (int)($r['numero_personas'] ?? 0) ?></td>
                            <td class="text-center">
                                <a href="EditarReserva.php?id=<?= (int)$r['id'] ?>"
                                   class="btn btn-sm btn-warning me-1">
                                    <i class="fas fa-edit"></i> Editar
                                </a>

                                <?php if (in_array($estado, ['pendiente','confirmada'])): ?>
                                    <!-- Cancelar (cambiar estado) -->
                                    <form method="post" class="d-inline">
                                        <input type="hidden" name="accion" value="cambiar_estado">
                                        <input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
                                        <input type="hidden" name="estado" value="cancelada">
                                        <button class="btn btn-sm btn-outline-danger me-1"
                                                onclick="return confirm('¿Cancelar la reserva #<?= (int)$r['id'] ?>?');">
                                            <i class="fas fa-ban"></i> Cancelar
                                        </button>
                                    </form>
                                <?php endif; ?>

                                <!-- Eliminar -->
                                <form method="post" class="d-inline">
                                    <input type="hidden" name="accion" value="eliminar">
                                    <input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
                                    <button class="btn btn-sm btn-danger"
                                            onclick="return confirm('¿Eliminar definitivamente la reserva #<?= (int)$r['id'] ?>?');">
                                        <i class="fas fa-trash"></i> Eliminar
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
