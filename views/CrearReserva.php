<?php
// views/ModuloReservas/reservas/CrearReserva.php

// Incluye los controladores necesarios
require_once __DIR__ . '/../config/db.php'; // retorna $pdo
require_once __DIR__ . '/../controller/ReservaController.php';
require_once __DIR__ . '/../controller/ZonaController.php';

$zonaCtrl     = new ZonaController($pdo);
$reservaCtrl  = new ReservaController($pdo);


// Instanciar controladores
$zonaCtrl    = new ZonaController();
$reservaCtrl = new ReservaController();


// Controladores

$zonaCtrl     = new ZonaController();
$reservaCtrl  = new ReservaController();

// Cargar zonas (activas)
try {
    $zonas = $zonaCtrl->obtenerZonasActivas();
} catch (Throwable $e) {
    $zonas = [];
    set_flash('danger', 'Error cargando zonas: ' . $e->getMessage());
}

// Helper corto para escapar
function h($s){ return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }

// Manejar envío del formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $datos = [
            'nombre_usuario'   => trim($_POST['nombre_usuario'] ?? ''),
            'apartamento'      => trim($_POST['apartamento'] ?? ''),
            'telefono'         => trim($_POST['telefono'] ?? ''),
            'email'            => trim($_POST['email'] ?? ''),
            'zona_id'          => (int)($_POST['zona_id'] ?? 0),
            'fecha_reserva'    => $_POST['fecha_reserva'] ?? '',
            'hora_inicio'      => $_POST['hora_inicio'] ?? '',
            'hora_fin'         => $_POST['hora_fin'] ?? '',
            'numero_personas'  => (int)($_POST['numero_personas'] ?? 1),
            'descripcion'      => trim($_POST['descripcion'] ?? ''),
            'estado'           => 'pendiente'
        ];

        $id = $reservaCtrl->crearReserva($datos);
        if ($id) {
            set_flash('success', '✅ Reserva creada correctamente (#' . $id . ').');
            header('Location: CrudReserva.php');
            exit;
        }
        set_flash('danger', '❌ No se pudo crear la reserva.');
    } catch (Throwable $e) {
        set_flash('danger', '⚠️ Error al crear: ' . $e->getMessage());
    }
}

$flash = get_flash();

// Header (usa helper layout del bootstrap)
layout('header.php');
?>

<!-- Carga de CSS específico (si existe) -->
<?php
$cssRel = 'css/areas-comunes/reservas.css'; // (renombrado desde reserva2.css a reservas.css)
if (file_exists(PROJECT_ROOT . '/assets/' . $cssRel)) {
    echo '<link rel="stylesheet" href="' . asset($cssRel) . '">' . PHP_EOL;
}
?>

<div class="container mt-4">
  <div class="row g-4">
    <!-- Columna izquierda: Formulario + Calendario -->
    <div class="col-12 col-lg-7">
      <div class="card card-soft">
        <div class="card-body">
          <!-- Título + acciones -->
          <div class="d-flex align-items-center justify-content-between mb-3">
            <h3 class="card-title mb-0">Nueva Reserva</h3>
            <div class="d-flex gap-2">
              <!-- Rutas pedidas -->
              <a href="CrudReserva.php" class="btn btn-outline-secondary btn-sm">
                <i class="ri-list-unordered"></i> Ver Reservas
              </a>
              <a href="../zonas/CrudZona.php" class="btn btn-outline-secondary btn-sm">
                <i class="ri-community-line"></i> Zonas Comunes
              </a>
              <a href="GestionarReserva.php" class="btn btn-outline-secondary btn-sm">
                <i class="ri-user-line"></i> Mis Reservas
              </a>
            </div>
          </div>

          <!-- Flash -->
          <?php if ($flash): ?>
            <div class="alert alert-<?= h($flash['type']) ?> mb-3">
              <?= h($flash['message']) ?>
            </div>
          <?php endif; ?>

          <!-- Formulario -->
          <form id="reservaForm" method="POST" action="">
            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label">Zona Común *</label>
                <select class="form-select" id="zona_id" name="zona_id" required>
                  <option value="">Seleccione una zona</option>
                  <?php foreach ($zonas as $z): ?>
                    <option
                      value="<?= h($z['id']) ?>"
                      data-capacidad="<?= h($z['capacidad_maxima'] ?? '') ?>"
                      data-apertura="<?= h($z['hora_apertura'] ?? '08:00') ?>"
                      data-cierre="<?= h($z['hora_cierre']   ?? '20:00') ?>"
                      data-duracion="<?= h($z['duracion_maxima'] ?? 2) ?>"
                      data-terminos="<?= h($z['terminos_y_condiciones'] ?? 'Sin términos y condiciones configurados para esta zona.') ?>"
                    >
                      <?= h($z['nombre']) ?> (Cap: <?= h($z['capacidad_maxima'] ?? '-') ?>) - $<?= number_format((float)($z['tarifa'] ?? 0), 0, ',', '.') ?>/h
                    </option>
                  <?php endforeach; ?>
                </select>
              </div>

              <div class="col-md-6">
                <label class="form-label">Fecha de Reserva *</label>
                <div class="input-group">
                  <input type="date" class="form-control" id="fecha_reserva" name="fecha_reserva" min="<?= date('Y-m-d') ?>" required>
                  <span class="input-group-text"><i class="ri-calendar-line"></i></span>
                </div>
              </div>

              <div class="col-md-6">
                <label class="form-label">Hora de Inicio *</label>
                <div class="input-group">
                  <input type="time" class="form-control" id="hora_inicio" name="hora_inicio" required>
                  <span class="input-group-text"><i class="ri-time-line"></i></span>
                </div>
              </div>

              <div class="col-md-6">
                <label class="form-label">Hora de Fin *</label>
                <div class="input-group">
                  <input type="time" class="form-control" id="hora_fin" name="hora_fin" required>
                  <span class="input-group-text"><i class="ri-time-line"></i></span>
                </div>
              </div>

              <div class="col-md-6">
                <label class="form-label">Nombre del Residente *</label>
                <input type="text" class="form-control" id="nombre_usuario" name="nombre_usuario" required>
              </div>

              <div class="col-md-3">
                <label class="form-label">Apartamento *</label>
                <input type="text" class="form-control" id="apartamento" name="apartamento" required>
              </div>

              <div class="col-md-3">
                <label class="form-label">Teléfono *</label>
                <input type="text" class="form-control" id="telefono" name="telefono" required>
              </div>

              <div class="col-12">
                <label class="form-label">Email *</label>
                <input type="email" class="form-control" id="email" name="email" required>
              </div>

              <div class="col-md-6">
                <label class="form-label">Número de Personas *</label>
                <input type="number" class="form-control" id="numero_personas" name="numero_personas" min="1" max="100" value="1" required>
              </div>

              <div class="col-12">
                <label class="form-label">Descripción/Observaciones (opcional)</label>
                <textarea class="form-control" id="descripcion" name="descripcion" rows="3" maxlength="500" placeholder="Detalle lo que necesite para su reserva (máx. 500)"></textarea>
              </div>
            </div>

            <div class="d-flex gap-2 justify-content-end mt-4">
              <a href="CrudReserva.php" class="btn btn-secondary">
                <i class="ri-arrow-go-back-line"></i> Volver
              </a>
              <button type="submit" class="btn btn-primary">
                <i class="ri-save-3-line"></i> Guardar Reserva
              </button>
            </div>
          </form>
        </div>
      </div>

      <!-- Calendario (demo visual) -->
      <div class="card mt-4">
        <div class="card-body">
          <div class="d-flex align-items-center justify-content-between mb-3">
            <h5 class="card-title mb-0">Disponibilidad</h5>
            <div class="d-flex gap-2">
              <button class="btn btn-outline-secondary btn-sm" id="calPrev"><i class="ri-arrow-left-s-line"></i> Anterior</button>
              <button class="btn btn-outline-secondary btn-sm" id="calNext">Siguiente <i class="ri-arrow-right-s-line"></i></button>
            </div>
          </div>
          <div class="mb-2 small text-muted">
            <strong>Zona:</strong> <span id="calZonaLabel">Seleccione una zona</span>
            <span class="ms-3"><span class="legend legend-free"></span> Libre</span>
            <span class="ms-2"><span class="legend legend-busy"></span> Con reservas</span>
          </div>

          <div id="calendarGrid" class="calendar-grid"></div>
        </div>
      </div>
    </div>

    <!-- Columna derecha: Tarjetas de Zonas -->
    <div class="col-12 col-lg-5">
      <h5 class="mb-3">Zonas Disponibles</h5>

      <?php if (empty($zonas)): ?>
        <div class="alert alert-warning">No hay zonas registradas.</div>
      <?php endif; ?>

      <?php foreach ($zonas as $zona): ?>
        <?php
          // Imagen (fallback SVG sin 404)
          $imgRel   = !empty($zona['imagen']) ? 'img/' . $zona['imagen'] : 'img/default.jpg';
          $imgPath  = realpath(PROJECT_ROOT . '/assets/' . $imgRel);
          $exists   = $imgPath && file_exists($imgPath);
          $placeholderSvg = "data:image/svg+xml;utf8," . rawurlencode(
            '<svg xmlns="http://www.w3.org/2000/svg" width="800" height="140">
               <rect width="100%" height="100%" fill="#f3f4f6"/>
               <text x="50%" y="50%" dominant-baseline="middle" text-anchor="middle"
                     fill="#99a3a0" font-size="20">Sin imagen</text>
             </svg>'
          );
          $imgUrl   = $exists ? asset($imgRel) : $placeholderSvg;
        ?>
        <div class="area-card">
          <div class="area-media">
            <img
              src="<?= h($imgUrl) ?>"
              alt="<?= h($zona['nombre'] ?? 'Zona común') ?>"
              loading="lazy"
              decoding="async"
              onerror="this.onerror=null;this.src='<?= $placeholderSvg ?>';"
            />
          </div>

          <div class="area-card-content">
            <h6 class="mb-1"><?= h($zona['nombre']) ?></h6>
            <?php if (!empty($zona['descripcion'])): ?>
              <p class="text-muted mb-2"><?= h($zona['descripcion']) ?></p>
            <?php endif; ?>

            <?php
              $estado = (isset($zona['activo']) && (int)$zona['activo'] === 1) ? 'activo' : 'inactivo';
            ?>
            <span class="status-indicator <?= $estado === 'activo' ? 'status-disponible' : 'status-mantenimiento' ?>">
              <?= $estado === 'activo' ? 'Disponible' : 'Inactiva' ?>
            </span>

            <div class="area-details mt-2">
              <div class="detail-item">
                <span class="detail-label">Capacidad:</span>
                <span class="detail-value"><?= h($zona['capacidad_maxima'] ?? '-') ?> personas</span>
              </div>
              <div class="detail-item">
                <span class="detail-label">Horario:</span>
                <span class="detail-value"><?= h(substr($zona['hora_apertura'] ?? '08:00',0,5)) ?> - <?= h(substr($zona['hora_cierre'] ?? '20:00',0,5)) ?></span>
              </div>
              <div class="detail-item">
                <span class="detail-label">Duración máx.:</span>
                <span class="detail-value"><?= h($zona['duracion_maxima'] ?? 2) ?> h</span>
              </div>
            </div>

            <div class="mt-3">
              <button
                type="button"
                class="btn btn-outline-primary btn-sm"
                data-bs-toggle="modal"
                data-bs-target="#terminosModal"
                data-zonanombre="<?= h($zona['nombre']) ?>"
                data-terminos="<?= h($zona['terminos_y_condiciones'] ?? 'Sin términos y condiciones configurados para esta zona.') ?>"
              >
                <i class="ri-file-text-line"></i> Ver Términos
              </button>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</div>

<!-- Modal Términos -->
<div class="modal fade" id="terminosModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="ri-file-list-3-line"></i> Términos y Condiciones</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body">
        <h6 id="termZona" class="mb-2"></h6>
        <div id="termTexto" class="text-secondary" style="white-space:pre-wrap"></div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>

<!-- JS de validación + calendario demo -->
<script>
(function(){
  const form  = document.getElementById('reservaForm');
  const zona  = document.getElementById('zona_id');
  const fecha = document.getElementById('fecha_reserva');
  const hi    = document.getElementById('hora_inicio');
  const hf    = document.getElementById('hora_fin');
  const np    = document.getElementById('numero_personas');

  const calLabel = document.getElementById('calZonaLabel');

  function buildCalendar(baseDate) {
    const grid = document.getElementById('calendarGrid');
    grid.innerHTML = '';

    const year  = baseDate.getFullYear();
    const month = baseDate.getMonth(); // 0-11
    const title = document.createElement('div');
    title.className = 'calendar-title';
    title.textContent = new Intl.DateTimeFormat('es-CO', {month:'long', year:'numeric'}).format(baseDate);
    grid.appendChild(title);

    const head = document.createElement('div');
    head.className = 'calendar-head';
    ['LUN','MAR','MIÉ','JUE','VIE','SÁB','DOM'].forEach(d=>{
      const c=document.createElement('div'); c.textContent=d; head.appendChild(c);
    });
    grid.appendChild(head);

    const first   = new Date(year, month, 1);
    const last    = new Date(year, month+1, 0);
    let startIdx  = (first.getDay()+6)%7; // Lunes=0
    const total   = startIdx + last.getDate();
    const weeks   = Math.ceil(total/7);

    const body = document.createElement('div');
    body.className = 'calendar-body';

    let day = 1;
    for (let w=0; w<weeks; w++){
      for (let d=0; d<7; d++){
        const cell = document.createElement('div');
        if ((w===0 && d<startIdx) || day>last.getDate()){
          cell.className='empty';
        } else {
          cell.className='day';
          cell.textContent = day;
          // demo: marca libres/ocupados alternados (no consulta BD aquí)
          if ((day + d) % 3 === 0) cell.classList.add('busy'); else cell.classList.add('free');
          day++;
        }
        body.appendChild(cell);
      }
    }
    grid.appendChild(body);
  }

  // Estado calendario
  let calDate = new Date();
  buildCalendar(calDate);

  document.getElementById('calPrev').addEventListener('click', function(e){
    e.preventDefault();
    calDate = new Date(calDate.getFullYear(), calDate.getMonth()-1, 1);
    buildCalendar(calDate);
  });
  document.getElementById('calNext').addEventListener('click', function(e){
    e.preventDefault();
    calDate = new Date(calDate.getFullYear(), calDate.getMonth()+1, 1);
    buildCalendar(calDate);
  });

  // Etiqueta de calendario y restricciones por zona
  zona.addEventListener('change', function(){
    const opt = zona.options[zona.selectedIndex];
    calLabel.textContent = opt && opt.value ? opt.text.replace(/\s*\(Cap.*$/,'') : 'Seleccione una zona';

    const apertura = opt ? (opt.dataset.apertura || '08:00') : '08:00';
    const cierre   = opt ? (opt.dataset.cierre   || '20:00') : '20:00';
    hi.min = apertura; hi.max = cierre;
    hf.min = apertura; hf.max = cierre;
  });

  // Validaciones
  form.addEventListener('submit', function(e){
    // horas
    const hiVal = hi.value, hfVal = hf.value;
    if (!hiVal || !hfVal || hiVal >= hfVal) {
      e.preventDefault(); alert('La hora final debe ser posterior a la inicial.'); return false;
    }
    // Duración máxima por zona
    const opt = zona.options[zona.selectedIndex];
    if (opt) {
      const maxH = parseInt(opt.dataset.duracion || '2', 10);
      const [h1,m1] = hiVal.split(':').map(Number);
      const [h2,m2] = hfVal.split(':').map(Number);
      const dur = (h2*60+m2) - (h1*60+m1);
      if (dur > maxH*60) {
        e.preventDefault(); alert('La duración máxima para esta zona es de ' + maxH + ' horas.'); return false;
      }
      // Capacidad
      const cap = parseInt(opt.dataset.capacidad || '0', 10);
      const num = parseInt(np.value || '1', 10);
      if (cap && num > cap) {
        e.preventDefault(); alert('El número de personas excede la capacidad de la zona ('+cap+').'); return false;
      }
    }
  });

  // Modal de términos: relleno dinámico
  const termModal = document.getElementById('terminosModal');
  termModal.addEventListener('show.bs.modal', function (ev) {
    const btn   = ev.relatedTarget;
    const nombre = btn.getAttribute('data-zonanombre') || 'Zona Común';
    const text   = btn.getAttribute('data-terminos') || 'Sin términos y condiciones configurados para esta zona.';
    document.getElementById('termZona').textContent = nombre;
    document.getElementById('termTexto').textContent = text;
  });
})();
</script>

<style>
/* ====== Estilos mínimos para el calendario / tarjetas ====== */
.calendar-grid{border:1px solid #e5e7eb;border-radius:12px;padding:12px;background:#fff}
.calendar-title{font-weight:600;margin-bottom:8px;text-transform:capitalize}
.calendar-head, .calendar-body{display:grid;grid-template-columns:repeat(7,1fr);gap:6px}
.calendar-head div{font-weight:600;color:#6b7280;text-align:center;padding:6px 0}
.calendar-body .day, .calendar-body .empty{height:52px;border:1px dashed #e5e7eb;border-radius:10px;
  display:flex;align-items:center;justify-content:center;font-weight:600}
.calendar-body .day.free{background:#f0fdf4;color:#166534;border-color:#bbf7d0}
.calendar-body .day.busy{background:#fff1f2;color:#9f1239;border-color:#fecdd3}
.legend{display:inline-block;width:10px;height:10px;border-radius:2px;margin-right:6px;vertical-align:middle}
.legend-free{background:#10b981}.legend-busy{background:#ef4444}
.card.card-soft{border:1px solid #e5e7eb;border-radius:16px}
.area-card{border:1px solid #e9e5dc;border-radius:16px;overflow:hidden;margin-bottom:16px;background:#fff}
.area-media img{width:100%;height:140px;object-fit:cover;display:block;background:#f3f4f6}
.area-card-content{padding:12px}
.status-indicator{display:inline-block;font-size:.75rem;padding:2px 8px;border-radius:9999px}
.status-disponible{background:#e6f7ee;color:#198754}
.status-mantenimiento{background:#fff1f2;color:#b4232d}
.detail-item{display:flex;gap:6px;font-size:.9rem}
.detail-label{color:#6b7280}
.detail-value{font-weight:600}
</style>

<?php layout('footer.php'); ?>
