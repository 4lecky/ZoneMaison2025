<?php
// views/ModuloReservas/reservas/EditarReserva.php
// Edición de una reserva existente (nombres oficiales)

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
    require_once dirname(__DIR__, 3) . '/models/ZonaComun.php';
    require_once dirname(__DIR__, 3) . '/controller/ReservaController.php';
    require_once dirname(__DIR__, 3) . '/controller/ZonaController.php';
}

$reservaController = new ReservaController();
$zonaController    = new ZonaController();

/** Validar ID por GET */
if (!isset($_GET['id']) || !ctype_digit($_GET['id'])) {
    $reservaController->setFlash('error', 'ID de reserva no válido');
    header('Location: CrudReserva.php');
    exit;
}
$id = (int)$_GET['id'];

/** Procesar POST (actualizar) */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Llamada directa al método de actualización del controlador
        $ok = $reservaController->actualizarReserva($id, $_POST);
        if ($ok) {
            $reservaController->setFlash('success', 'Reserva actualizada exitosamente');
            header('Location: CrudReserva.php');
            exit;
        } else {
            $reservaController->setFlash('error', 'No se pudo actualizar la reserva');
        }
    } catch (Throwable $e) {
        $reservaController->setFlash('error', $e->getMessage());
    }
}

/** Cargar datos de la reserva */
try {
    $reserva = $reservaController->obtenerReservaPorId($id);
    if (!$reserva) {
        $reservaController->setFlash('error', 'Reserva no encontrada');
        header('Location: CrudReserva.php');
        exit;
    }
} catch (Throwable $e) {
    $reservaController->setFlash('error', 'Error al obtener la reserva: ' . $e->getMessage());
    header('Location: CrudReserva.php');
    exit;
}

/** Zonas activas para el select */
try {
    $zonasActivas = $zonaController->obtenerZonasActivas();
} catch (Throwable $e) {
    $zonasActivas = [];
}

/** Mensajes flash */
$flash = $reservaController->takeFlash();

/** Layout header */
$headerPath = dirname(__DIR__, 2) . '/Layout/header.php';
if (file_exists($headerPath)) {
    include $headerPath;
}
?>
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="mb-0"><i class="fas fa-edit me-2"></i> Editar Reserva #<?= (int)$id ?></h2>
        <div class="d-flex gap-2">
            <a href="CrearReserva.php" class="btn btn-outline-primary">
                <i class="fas fa-plus"></i> Nueva
            </a>
            <a href="CrudReserva.php" class="btn btn-outline-secondary">
                <i class="fas fa-list"></i> Ver Reservas
            </a>
            <a href="GestionarReserva.php" class="btn btn-outline-secondary">
                <i class="fas fa-user-check"></i> Mis Reservas
            </a>
            <a href="../zonas/CrudZona.php" class="btn btn-outline-secondary">
                <i class="fas fa-layer-group"></i> Zonas
            </a>
        </div>
    </div>

    <!-- Flash messages -->
    <?php if (!empty($flash)): ?>
        <?php foreach ($flash as $tipo => $mensaje): ?>
            <div class="alert alert-<?= $tipo === 'success' ? 'success' : ($tipo === 'error' ? 'danger' : $tipo) ?> alert-dismissible fade show">
                <?= htmlspecialchars($mensaje) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-pen-to-square me-2"></i> Datos de la Reserva</h5>
                </div>
                <div class="card-body">
                    <form method="POST" class="needs-validation" novalidate>
                        <div class="row">
                            <!-- Apartamento -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="apartamento" class="form-label">
                                        <i class="fas fa-home"></i> Apartamento *
                                    </label>
                                    <input type="text"
                                           class="form-control"
                                           id="apartamento"
                                           name="apartamento"
                                           value="<?= htmlspecialchars($reserva['apartamento'] ?? '') ?>"
                                           required>
                                    <div class="invalid-feedback">El apartamento es requerido.</div>
                                </div>
                            </div>

                            <!-- Nombre Usuario -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="nombre_usuario" class="form-label">
                                        <i class="fas fa-user"></i> Nombre del Residente *
                                    </label>
                                    <input type="text"
                                           class="form-control"
                                           id="nombre_usuario"
                                           name="nombre_usuario"
                                           value="<?= htmlspecialchars($reserva['nombre_usuario'] ?? $reserva['nombre_residente'] ?? '') ?>"
                                           required>
                                    <div class="invalid-feedback">El nombre del residente es requerido.</div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <!-- Teléfono -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="telefono" class="form-label">
                                        <i class="fas fa-phone"></i> Teléfono *
                                    </label>
                                    <input type="tel"
                                           class="form-control"
                                           id="telefono"
                                           name="telefono"
                                           value="<?= htmlspecialchars($reserva['telefono'] ?? '') ?>"
                                           required>
                                    <div class="invalid-feedback">El teléfono es requerido.</div>
                                </div>
                            </div>

                            <!-- Email -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="email" class="form-label">
                                        <i class="fas fa-envelope"></i> Email *
                                    </label>
                                    <input type="email"
                                           class="form-control"
                                           id="email"
                                           name="email"
                                           value="<?= htmlspecialchars($reserva['email'] ?? '') ?>"
                                           required>
                                    <div class="invalid-feedback">El email es requerido y debe ser válido.</div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <!-- Zona -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="zona_id" class="form-label">
                                        <i class="fas fa-map-marker-alt"></i> Zona Común *
                                    </label>
                                    <select class="form-select" id="zona_id" name="zona_id" required>
                                        <option value="">-- Seleccionar Zona --</option>
                                        <?php foreach ($zonasActivas as $z): ?>
                                            <option value="<?= (int)$z['id'] ?>"
                                                <?= ((int)($reserva['zona_id'] ?? 0) === (int)$z['id']) ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($z['nombre'] ?? 'Zona') ?>
                                                (Cap: <?= (int)($z['capacidad_maxima'] ?? $z['capacidad'] ?? 0) ?>)
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <div class="invalid-feedback">Selecciona una zona válida.</div>
                                </div>
                            </div>

                            <!-- Número de Personas -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="numero_personas" class="form-label">
                                        <i class="fas fa-users"></i> Número de Personas *
                                    </label>
                                    <input type="number"
                                           class="form-control"
                                           id="numero_personas"
                                           name="numero_personas"
                                           value="<?= htmlspecialchars($reserva['numero_personas'] ?? '1') ?>"
                                           min="1" max="100" required>
                                    <div class="invalid-feedback">El número de personas debe ser mayor a 0.</div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <!-- Fecha -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="fecha_reserva" class="form-label">
                                        <i class="fas fa-calendar"></i> Fecha de Reserva *
                                    </label>
                                    <input type="date"
                                           class="form-control"
                                           id="fecha_reserva"
                                           name="fecha_reserva"
                                           value="<?= htmlspecialchars($reserva['fecha_reserva'] ?? '') ?>"
                                           min="<?= date('Y-m-d') ?>"
                                           required>
                                    <div class="invalid-feedback">La fecha es requerida (no anterior a hoy).</div>
                                </div>
                            </div>

                            <!-- Estado -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="estado" class="form-label">
                                        <i class="fas fa-toggle-on"></i> Estado *
                                    </label>
                                    <select class="form-select" id="estado" name="estado" required>
                                        <?php
                                        $est = $reserva['estado'] ?? 'pendiente';
                                        $opts = ['pendiente','confirmada','cancelada','completada'];
                                        foreach ($opts as $o) {
                                            $sel = $est === $o ? 'selected' : '';
                                            echo "<option value=\"$o\" $sel>" . ucfirst($o) . "</option>";
                                        }
                                        ?>
                                    </select>
                                    <div class="invalid-feedback">Selecciona un estado válido.</div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <!-- Hora Inicio -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="hora_inicio" class="form-label">
                                        <i class="fas fa-clock"></i> Hora de Inicio *
                                    </label>
                                    <input type="time"
                                           class="form-control"
                                           id="hora_inicio"
                                           name="hora_inicio"
                                           value="<?= htmlspecialchars($reserva['hora_inicio'] ?? '') ?>"
                                           required>
                                    <div class="invalid-feedback">La hora de inicio es requerida.</div>
                                </div>
                            </div>

                            <!-- Hora Fin -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="hora_fin" class="form-label">
                                        <i class="fas fa-clock"></i> Hora de Fin *
                                    </label>
                                    <input type="time"
                                           class="form-control"
                                           id="hora_fin"
                                           name="hora_fin"
                                           value="<?= htmlspecialchars($reserva['hora_fin'] ?? '') ?>"
                                           required>
                                    <div class="invalid-feedback">La hora de fin debe ser posterior a la de inicio.</div>
                                </div>
                            </div>
                        </div>

                        <!-- Descripción -->
                        <div class="mb-3">
                            <label for="descripcion" class="form-label">
                                <i class="fas fa-sticky-note"></i> Descripción / Observaciones
                            </label>
                            <textarea class="form-control"
                                      id="descripcion"
                                      name="descripcion"
                                      rows="3"
                                      maxlength="500"><?= htmlspecialchars($reserva['descripcion'] ?? $reserva['observaciones'] ?? '') ?></textarea>
                            <div class="form-text">Máximo 500 caracteres (opcional).</div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="CrudReserva.php" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Volver
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Actualizar Reserva
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Info rápida de la zona actual -->
            <?php if (!empty($reserva['zona_nombre'])): ?>
                <div class="card mt-3">
                    <div class="card-body">
                        <small class="text-muted d-block mb-1">Zona seleccionada</small>
                        <strong><?= htmlspecialchars($reserva['zona_nombre']) ?></strong>
                        <?php if (isset($reserva['zona_tarifa'])): ?>
                            <span class="ms-2 text-success">
                                Tarifa: $<?= number_format((float)$reserva['zona_tarifa'], 0, ',', '.') ?>/h
                            </span>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>

        </div>
    </div>
</div>

<!-- Validaciones mínimas del lado del cliente -->
<script>
// Bootstrap validation
(function(){
  'use strict';
  const forms = document.querySelectorAll('.needs-validation');
  Array.prototype.slice.call(forms).forEach(function(form){
    form.addEventListener('submit', function(event){
      if (!form.checkValidity()) {
        event.preventDefault();
        event.stopPropagation();
      }
      // hora fin > hora inicio
      const hi = document.getElementById('hora_inicio').value;
      const hf = document.getElementById('hora_fin').value;
      if (hi && hf && hf <= hi) {
        event.preventDefault();
        event.stopPropagation();
        document.getElementById('hora_fin').setCustomValidity('La hora de fin debe ser posterior a la de inicio');
      } else {
        document.getElementById('hora_fin').setCustomValidity('');
      }
      form.classList.add('was-validated');
    }, false);
  });
})();
</script>

<?php
// Layout footer
$footerPath = dirname(__DIR__, 2) . '/Layout/footer.php';
if (file_exists($footerPath)) {
    include $footerPath;
}
