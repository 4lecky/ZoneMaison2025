<?php
// views/ModuloReservas/zonas/EditarZona.php
// Página para editar una Zona Común

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

error_reporting(E_ALL);
ini_set('display_errors', '1');

/**
 * Bootstrap del módulo (usa el que tenemos en /views/ModuloReservas/reservas/)
 * Esto te carga: getConnection(), modelos y controladores (ZonaModel, ZonaController, etc.)
 */
$bootstrap = __DIR__ . '/../reservas/bootstrap.php';
if (file_exists($bootstrap)) {
    require_once $bootstrap;
} else {
    // Fallback por si cambiaste rutas
    require_once dirname(__DIR__, 3) . '/config/db.php';
    require_once dirname(__DIR__, 3) . '/models/ZonaModel.php';
    require_once dirname(__DIR__, 3) . '/controller/ZonaController.php';
}

// Instanciar controlador
$zonaController = new ZonaController();

// Validar ID en query
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['mensaje'] = 'ID de zona inválido';
    $_SESSION['tipo_mensaje'] = 'error';
    header('Location: CrudZona.php'); // listado/gestión de zonas
    exit;
}

$zonaId = (int) $_GET['id'];

// Cargar datos actuales
try {
    $zona = $zonaController->obtenerZonaPorId($zonaId);
    if (!$zona) {
        $_SESSION['mensaje'] = 'La zona no fue encontrada';
        $_SESSION['tipo_mensaje'] = 'error';
        header('Location: CrudZona.php');
        exit;
    }
} catch (Throwable $e) {
    $_SESSION['mensaje'] = $e->getMessage();
    $_SESSION['tipo_mensaje'] = 'error';
    header('Location: CrudZona.php');
    exit;
}

// Procesar POST (actualizar)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $datos = [
            'nombre'            => trim($_POST['nombre'] ?? ''),
            'descripcion'       => trim($_POST['descripcion'] ?? ''),
            'capacidad_maxima'  => (int) ($_POST['capacidad_maxima'] ?? 1),
            'tarifa'            => (float) ($_POST['tarifa'] ?? 0),
            'activo'            => isset($_POST['activo']) ? 1 : 0,
        ];

        $ok = $zonaController->actualizarZona($zonaId, $datos);

        if ($ok) {
            $_SESSION['mensaje'] = 'Zona actualizada exitosamente';
            $_SESSION['tipo_mensaje'] = 'success';
            header('Location: CrudZona.php');
            exit;
        } else {
            $error = 'No se pudo actualizar la zona';
        }
    } catch (Throwable $e) {
        $error = $e->getMessage();
    }
}

// Header layout
$headerPath = dirname(__DIR__, 2) . '/Layout/header.php';
if (file_exists($headerPath)) {
    include $headerPath;
}
?>
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-9">
            <div class="card shadow-sm">
                <div class="card-header text-white" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                    <h5 class="mb-0">
                        <i class="fas fa-edit me-2"></i>Editar Zona Común
                    </h5>
                    <small class="opacity-75">Modifica la información de la zona común</small>
                </div>

                <div class="card-body">

                    <?php if (!empty($error)): ?>
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-triangle me-1"></i><?= htmlspecialchars($error) ?>
                        </div>
                    <?php endif; ?>

                    <!-- Info actual de la zona -->
                    <div class="row g-3 p-3 mb-3" style="background:#f8f9ff;border:1px solid #e2e8f0;border-radius:10px;">
                        <div class="col-md-4">
                            <small class="text-muted d-block">Creada:</small>
                            <div><?= !empty($zona['fecha_creacion']) ? date('d/m/Y', strtotime($zona['fecha_creacion'])) : '—' ?></div>
                        </div>
                        <div class="col-md-4">
                            <small class="text-muted d-block">Última actualización:</small>
                            <div><?= !empty($zona['fecha_actualizacion']) ? date('d/m/Y', strtotime($zona['fecha_actualizacion'])) : 'Nunca' ?></div>
                        </div>
                        <div class="col-md-4">
                            <small class="text-muted d-block">Reservas totales:</small>
                            <span class="badge bg-info"><?= (int)($zona['total_reservas'] ?? 0) ?></span>
                        </div>
                    </div>

                    <!-- Formulario -->
                    <form method="POST" novalidate>
                        <div class="row">
                            <div class="col-md-8 mb-3">
                                <label for="nombre" class="form-label">
                                    <i class="fas fa-map-marker-alt text-primary me-1"></i>Nombre de la Zona *
                                </label>
                                <input
                                    type="text"
                                    id="nombre"
                                    name="nombre"
                                    class="form-control"
                                    value="<?= htmlspecialchars($zona['nombre']) ?>"
                                    required
                                    minlength="3"
                                    maxlength="100"
                                >
                                <div class="form-text">Nombre descriptivo y único</div>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label">
                                    <i class="fas fa-toggle-on text-primary me-1"></i>Estado
                                </label>
                                <div class="form-check form-switch mt-2">
                                    <input class="form-check-input" type="checkbox" id="activo" name="activo" <?= !empty($zona['activo']) ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="activo">
                                        <span id="estadoBadge" class="badge bg-<?= !empty($zona['activo']) ? 'success' : 'secondary' ?>">
                                            <?= !empty($zona['activo']) ? 'Activa' : 'Inactiva' ?>
                                        </span>
                                    </label>
                                </div>
                                <div class="form-text">Las zonas inactivas no se pueden reservar</div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="descripcion" class="form-label">
                                <i class="fas fa-align-left text-primary me-1"></i>Descripción
                            </label>
                            <textarea
                                id="descripcion"
                                name="descripcion"
                                class="form-control"
                                rows="3"
                                maxlength="500"
                            ><?= htmlspecialchars($zona['descripcion'] ?? '') ?></textarea>
                            <div class="form-text">Máximo 500 caracteres</div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="capacidad_maxima" class="form-label">
                                    <i class="fas fa-users text-primary me-1"></i>Capacidad Máxima *
                                </label>
                                <input
                                    type="number"
                                    id="capacidad_maxima"
                                    name="capacidad_maxima"
                                    class="form-control"
                                    value="<?= (int)($zona['capacidad_maxima'] ?? 1) ?>"
                                    min="1"
                                    max="500"
                                    required
                                >
                                <div class="form-text">Número máximo de personas permitidas</div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="tarifa" class="form-label">
                                    <i class="fas fa-money-bill-wave text-primary me-1"></i>Tarifa por Hora
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input
                                        type="number"
                                        id="tarifa"
                                        name="tarifa"
                                        class="form-control"
                                        value="<?= (float)($zona['tarifa'] ?? 0) ?>"
                                        min="0"
                                        step="1000"
                                    >
                                </div>
                                <div class="form-text">0 = gratuita</div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <a href="CrudZona.php" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-1"></i>Volver
                            </a>

                            <div class="d-flex gap-2">
                                <!-- Eliminación con confirmación por modal simple (POST a CrudZona.php) -->
                                <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#modalEliminar">
                                    <i class="fas fa-trash me-1"></i>Eliminar
                                </button>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-1"></i>Actualizar Zona
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Modal eliminar -->
            <div class="modal fade" id="modalEliminar" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog">
                    <form method="POST" action="CrudZona.php" class="modal-content">
                        <div class="modal-header bg-danger text-white">
                            <h5 class="modal-title">
                                <i class="fas fa-exclamation-triangle me-1"></i>Confirmar Eliminación
                            </h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                        </div>
                        <div class="modal-body">
                            <p>¿Eliminar la zona <strong><?= htmlspecialchars($zona['nombre']) ?></strong>?</p>
                            <div class="alert alert-warning mb-0">
                                Esta acción no se puede deshacer.
                            </div>
                        </div>
                        <div class="modal-footer">
                            <input type="hidden" name="accion" value="eliminar">
                            <input type="hidden" name="id" value="<?= (int)$zona['id'] ?>">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-danger">
                                <i class="fas fa-trash me-1"></i>Eliminar Definitivamente
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>

<?php
// Footer layout
$footerPath = dirname(__DIR__, 2) . '/Layout/footer.php';
if (file_exists($footerPath)) {
    include $footerPath;
}
?>

<!-- Pequeños scripts de UX -->
<script>
document.getElementById('activo').addEventListener('change', function () {
    const badge = document.getElementById('estadoBadge');
    if (this.checked) {
        badge.textContent = 'Activa';
        badge.className = 'badge bg-success';
    } else {
        badge.textContent = 'Inactiva';
        badge.className = 'badge bg-secondary';
    }
});

// Validación básica en cliente
document.querySelector('form').addEventListener('submit', function(e){
    const nombre = document.getElementById('nombre').value.trim();
    const capacidad = +document.getElementById('capacidad_maxima').value;
    const tarifa = +document.getElementById('tarifa').value;

    const errores = [];
    if (nombre.length < 3) errores.push('El nombre debe tener al menos 3 caracteres');
    if (capacidad < 1 || capacidad > 500) errores.push('La capacidad debe estar entre 1 y 500');
    if (tarifa < 0) errores.push('La tarifa no puede ser negativa');

    if (errores.length) {
        e.preventDefault();
        alert('Corrige los siguientes errores:\n\n' + errores.join('\n'));
    }
});
</script>
