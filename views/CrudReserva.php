<?php
// views/ModuloReservas/reservas/CrudReserva.php
// Lista/CRUD de reservas con filtros y acciones

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

/** Manejar POST (crear/actualizar/eliminar/cambiar_estado) */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $reservaController->manejarSolicitud();
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}

/** Filtros */
$filtroEstado = $_GET['estado'] ?? 'todas';
$filtroFecha  = $_GET['fecha']  ?? '';

try {
    if ($filtroEstado === 'todas') {
        $reservas = $reservaController->obtenerTodasLasReservas();
    } else {
        $reservas = $reservaController->obtenerReservasPorEstado($filtroEstado);
    }

    if (!empty($filtroFecha)) {
        $reservas = array_values(array_filter($reservas, function($r) use ($filtroFecha) {
            return ($r['fecha_reserva'] ?? null) === $filtroFecha;
        }));
    }

    $estadisticas = $reservaController->obtenerEstadisticas();
} catch (Throwable $e) {
    $error = $e->getMessage();
    $reservas = [];
    $estadisticas = [
        'total' => 0, 'pendientes' => 0, 'confirmadas' => 0,
        'canceladas' => 0, 'completadas' => 0, 'hoy' => 0, 'proximas' => 0
    ];
}

/** Tomar flashes (si tu header ya los muestra, puedes omitir esto y dejar que el layout los lea de $_SESSION) */
$flash = $reservaController->takeFlash();

/** Header layout */
$headerPath = dirname(__DIR__, 2) . '/Layout/header.php';
if (file_exists($headerPath)) {
    include $headerPath;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <title>Reservas - CRUD</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .card-stats{border:none;border-radius:10px}
        .btn-action{margin:0 2px;padding:5px 10px;font-size:12px}
        .table-hover tbody tr:hover{background:#f8f9fa}
        .badge-custom{font-size:.75em;padding:.35em .65em}
        .stats-icon{font-size:2.2rem;opacity:.8}
        .filter-section{background:#f8f9fa;border-radius:10px;padding:1rem;margin-bottom:1.5rem}
    </style>
</head>
<body class="bg-light">
<div class="container-fluid py-4">

    <!-- Migas + acciones -->
    <div class="row mb-4">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-2">
                    <li class="breadcrumb-item"><a href="CrearReserva.php">Crear Reserva</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Reservas</li>
                </ol>
            </nav>
            <div class="d-flex justify-content-between align-items-center flex-wrap">
                <h1 class="h3 mb-0"><i class="bi bi-calendar-check"></i> Reservas</h1>
                <div class="btn-group">
                    <a href="CrearReserva.php" class="btn btn-success">
                        <i class="bi bi-plus-circle"></i> Crear Reserva
                    </a>
                    <a href="GestionarReserva.php" class="btn btn-outline-primary">
                        <i class="bi bi-person-check"></i> Mis Reservas
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Mensajes Flash (si no los muestra el header) -->
    <?php if (!empty($flash)): ?>
        <div class="row mb-3">
            <div class="col-12">
                <?php foreach ($flash as $tipo => $mensaje): ?>
                    <div class="alert alert-<?= $tipo === 'success' ? 'success' : ($tipo === 'error' ? 'danger' : $tipo) ?> alert-dismissible fade show">
                        <i class="bi bi-<?= $tipo === 'success' ? 'check-circle' : 'exclamation-triangle' ?>"></i>
                        <?= htmlspecialchars($mensaje) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>

    <!-- Error de carga -->
    <?php if (isset($error)): ?>
        <div class="row mb-3">
            <div class="col-12">
                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-triangle"></i>
                    Error: <?= htmlspecialchars($error) ?>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Filtros -->
    <div class="filter-section">
        <form method="GET" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label for="estado" class="form-label"><i class="bi bi-funnel"></i> Filtrar por Estado</label>
                <select name="estado" id="estado" class="form-select">
                    <option value="todas"     <?= $filtroEstado === 'todas' ? 'selected' : '' ?>>Todas las reservas</option>
                    <option value="pendiente" <?= $filtroEstado === 'pendiente' ? 'selected' : '' ?>>Pendientes</option>
                    <option value="confirmada"<?= $filtroEstado === 'confirmada' ? 'selected' : '' ?>>Confirmadas</option>
                    <option value="cancelada" <?= $filtroEstado === 'cancelada' ? 'selected' : '' ?>>Canceladas</option>
                    <option value="completada"<?= $filtroEstado === 'completada' ? 'selected' : '' ?>>Completadas</option>
                </select>
            </div>
            <div class="col-md-4">
                <label for="fecha" class="form-label"><i class="bi bi-calendar"></i> Filtrar por Fecha</label>
                <input type="date" name="fecha" id="fecha" class="form-control" value="<?= htmlspecialchars($filtroFecha) ?>">
            </div>
            <div class="col-md-4">
                <div class="btn-group w-100">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-search"></i> Filtrar
                    </button>
                    <a href="CrudReserva.php" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-clockwise"></i> Limpiar
                    </a>
                </div>
            </div>
        </form>
    </div>

    <!-- Tarjetas de Estadísticas -->
    <div class="row mb-4">
        <div class="col-xl-2 col-md-4 col-sm-6 mb-3">
            <div class="card card-stats bg-primary text-white h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h6 class="card-title mb-0">Total</h6>
                        <h3 class="mb-0"><?= (int)$estadisticas['total'] ?></h3>
                    </div>
                    <div class="stats-icon"><i class="bi bi-calendar2-check"></i></div>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-sm-6 mb-3">
            <div class="card card-stats bg-warning text-white h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h6 class="card-title mb-0">Pendientes</h6>
                        <h3 class="mb-0"><?= (int)$estadisticas['pendientes'] ?></h3>
                    </div>
                    <div class="stats-icon"><i class="bi bi-clock-history"></i></div>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-sm-6 mb-3">
            <div class="card card-stats bg-success text-white h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h6 class="card-title mb-0">Confirmadas</h6>
                        <h3 class="mb-0"><?= (int)$estadisticas['confirmadas'] ?></h3>
                    </div>
                    <div class="stats-icon"><i class="bi bi-check-circle"></i></div>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-sm-6 mb-3">
            <div class="card card-stats bg-danger text-white h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h6 class="card-title mb-0">Canceladas</h6>
                        <h3 class="mb-0"><?= (int)$estadisticas['canceladas'] ?></h3>
                    </div>
                    <div class="stats-icon"><i class="bi bi-x-circle"></i></div>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-sm-6 mb-3">
            <div class="card card-stats bg-secondary text-white h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h6 class="card-title mb-0">Completadas</h6>
                        <h3 class="mb-0"><?= (int)$estadisticas['completadas'] ?></h3>
                    </div>
                    <div class="stats-icon"><i class="bi bi-check2-all"></i></div>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-sm-6 mb-3">
            <div class="card card-stats bg-info text-white h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h6 class="card-title mb-0">Próximas</h6>
                        <h3 class="mb-0"><?= (int)$estadisticas['proximas'] ?></h3>
                    </div>
                    <div class="stats-icon"><i class="bi bi-calendar-plus"></i></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla de Reservas -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="bi bi-table"></i>
                        <?php if ($filtroEstado !== 'todas'): ?>
                            Reservas <?= ucfirst($filtroEstado) ?>s
                        <?php else: ?>
                            Todas las Reservas
                        <?php endif; ?>
                        <?php if (!empty($filtroFecha)): ?>
                            - <?= date('d/m/Y', strtotime($filtroFecha)) ?>
                        <?php endif; ?>
                    </h5>
                    <small class="text-muted"><?= count($reservas) ?> reservas encontradas</small>
                </div>
                <div class="card-body p-0">
                    <?php if (empty($reservas)): ?>
                        <div class="text-center py-5">
                            <i class="bi bi-calendar-x display-4 text-muted"></i>
                            <h5 class="mt-3 text-muted">No hay reservas</h5>
                            <p class="text-muted">
                                <?php if ($filtroEstado !== 'todas' || !empty($filtroFecha)): ?>
                                    No se encontraron reservas con los filtros aplicados
                                <?php else: ?>
                                    Aún no se han creado reservas en el sistema
                                <?php endif; ?>
                            </p>
                            <a href="CrearReserva.php" class="btn btn-primary">
                                <i class="bi bi-plus-circle"></i> Crear Reserva
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-dark">
                                    <tr>
                                        <th>ID</th>
                                        <th>Usuario</th>
                                        <th>Apto.</th>
                                        <th>Zona</th>
                                        <th>Fecha</th>
                                        <th>Horario</th>
                                        <th>Personas</th>
                                        <th>Estado</th>
                                        <th>Tarifa</th>
                                        <th class="text-center" width="180">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php foreach ($reservas as $r): ?>
                                    <tr>
                                        <td><strong><?= (int)$r['id'] ?></strong></td>
                                        <td>
                                            <div>
                                                <strong><?= htmlspecialchars($r['nombre_usuario']) ?></strong><br>
                                                <small class="text-muted"><i class="bi bi-envelope"></i> <?= htmlspecialchars($r['email']) ?></small><br>
                                                <small class="text-muted"><i class="bi bi-telephone"></i> <?= htmlspecialchars($r['telefono']) ?></small>
                                            </div>
                                        </td>
                                        <td><span class="badge bg-secondary"><?= htmlspecialchars($r['apartamento']) ?></span></td>
                                        <td><strong><?= htmlspecialchars($r['zona_nombre']) ?></strong></td>
                                        <td>
                                            <strong><?= date('d/m/Y', strtotime($r['fecha_reserva'])) ?></strong><br>
                                            <small class="text-muted">
                                                <?php
                                                $fecha = new DateTime($r['fecha_reserva']);
                                                $dias = ['Dom','Lun','Mar','Mié','Jue','Vie','Sáb'];
                                                echo $dias[(int)$fecha->format('w')];
                                                ?>
                                            </small>
                                        </td>
                                        <td>
                                            <i class="bi bi-clock"></i>
                                            <strong>
                                                <?= date('H:i', strtotime($r['hora_inicio'])) ?> -
                                                <?= date('H:i', strtotime($r['hora_fin'])) ?>
                                            </strong>
                                        </td>
                                        <td><i class="bi bi-people"></i> <?= (int)$r['numero_personas'] ?></td>
                                        <td>
                                            <?php
                                            $badge = [
                                                'pendiente' => 'bg-warning',
                                                'confirmada' => 'bg-success',
                                                'cancelada' => 'bg-danger',
                                                'completada' => 'bg-secondary'
                                            ];
                                            $icon = [
                                                'pendiente' => 'clock-history',
                                                'confirmada' => 'check-circle',
                                                'cancelada' => 'x-circle',
                                                'completada' => 'check2-all'
                                            ];
                                            $estado = $r['estado'];
                                            ?>
                                            <span class="badge <?= $badge[$estado] ?? 'bg-light' ?> badge-custom">
                                                <i class="bi bi-<?= $icon[$estado] ?? 'info-circle' ?>"></i>
                                                <?= ucfirst($estado) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="fw-bold text-success">
                                                $<?= number_format((float)($r['zona_tarifa'] ?? 0), 0, ',', '.') ?>
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <!-- Editar -->
                                            <a href="EditarReserva.php?id=<?= (int)$r['id'] ?>"
                                               class="btn btn-warning btn-sm btn-action" title="Editar">
                                               <i class="bi bi-pencil"></i>
                                            </a>

                                            <!-- Cambiar estado -->
                                            <?php if ($r['estado'] === 'pendiente'): ?>
                                                <button type="button"
                                                        class="btn btn-success btn-sm btn-action"
                                                        title="Confirmar"
                                                        onclick="cambiarEstado(<?= (int)$r['id'] ?>,'<?= htmlspecialchars($r['nombre_usuario']) ?>','confirmada')">
                                                    <i class="bi bi-check-circle"></i>
                                                </button>
                                            <?php elseif ($r['estado'] === 'confirmada'): ?>
                                                <button type="button"
                                                        class="btn btn-secondary btn-sm btn-action"
                                                        title="Marcar completada"
                                                        onclick="cambiarEstado(<?= (int)$r['id'] ?>,'<?= htmlspecialchars($r['nombre_usuario']) ?>','completada')">
                                                    <i class="bi bi-check2-all"></i>
                                                </button>
                                            <?php endif; ?>

                                            <?php if (in_array($r['estado'], ['pendiente','confirmada'], true)): ?>
                                                <button type="button"
                                                        class="btn btn-outline-danger btn-sm btn-action"
                                                        title="Cancelar"
                                                        onclick="cambiarEstado(<?= (int)$r['id'] ?>,'<?= htmlspecialchars($r['nombre_usuario']) ?>','cancelada')">
                                                    <i class="bi bi-x-circle"></i>
                                                </button>
                                            <?php endif; ?>

                                            <!-- Eliminar -->
                                            <button type="button"
                                                    class="btn btn-danger btn-sm btn-action"
                                                    title="Eliminar"
                                                    onclick="confirmarEliminacion(<?= (int)$r['id'] ?>,'<?= htmlspecialchars($r['nombre_usuario']) ?>','<?= htmlspecialchars($r['zona_nombre']) ?>','<?= date('d/m/Y', strtotime($r['fecha_reserva'])) ?>')">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

</div>

<!-- Footer layout -->
<?php
$footerPath = dirname(__DIR__, 2) . '/Layout/footer.php';
if (file_exists($footerPath)) {
    include $footerPath;
}
?>

<!-- JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Confirmar eliminación
function confirmarEliminacion(id, nombreUsuario, zonaNombre, fecha) {
    Swal.fire({
        title: '¿Eliminar reserva?',
        html: `
            <div class="text-start">
                <p>Se eliminará permanentemente la reserva:</p>
                <ul class="list-unstyled">
                    <li><strong>Usuario:</strong> ${nombreUsuario}</li>
                    <li><strong>Zona:</strong> ${zonaNombre}</li>
                    <li><strong>Fecha:</strong> ${fecha}</li>
                </ul>
                <p class="text-danger"><strong>Esta acción no se puede deshacer</strong></p>
            </div>`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        focusCancel: true
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.innerHTML = `
                <input type="hidden" name="accion" value="eliminar">
                <input type="hidden" name="id" value="${id}">
            `;
            document.body.appendChild(form);

            Swal.fire({title:'Eliminando...',allowOutsideClick:false,showConfirmButton:false,willOpen:()=>Swal.showLoading()});
            form.submit();
        }
    });
}

// Cambiar estado
function cambiarEstado(id, nombreUsuario, nuevoEstado) {
    const acciones = {
        'confirmada': { texto: 'confirmar', color: '#28a745' },
        'cancelada' : { texto: 'cancelar',  color: '#dc3545' },
        'completada': { texto: 'marcar como completada', color: '#6c757d' }
    };
    const acc = acciones[nuevoEstado];

    Swal.fire({
        title: `¿${acc.texto.charAt(0).toUpperCase() + acc.texto.slice(1)} reserva?`,
        html: `La reserva de <strong>${nombreUsuario}</strong> será <strong>${nuevoEstado}</strong>`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: `Sí, ${acc.texto}`,
        cancelButtonText: 'Cancelar',
        confirmButtonColor: acc.color,
        cancelButtonColor: '#6c757d'
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.innerHTML = `
                <input type="hidden" name="accion" value="cambiar_estado">
                <input type="hidden" name="id" value="${id}">
                <input type="hidden" name="estado" value="${nuevoEstado}">
            `;
            document.body.appendChild(form);

            Swal.fire({title:'Actualizando...',allowOutsideClick:false,showConfirmButton:false,willOpen:()=>Swal.showLoading()});
            form.submit();
        }
    });
}
</script>
</body>
</html>
