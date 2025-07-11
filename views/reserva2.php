<?php
require_once '../controller/ReservaController.php';
require_once '../controller/ZonaController.php';

// -------------------------------
// Inicialización y configuración
// -------------------------------
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$controladorReservas = new ReservaController();
$controladorZonas = new ZonaController();
$zonas = $controladorZonas->listarZonasComunes();

// -------------------------------
// Procesamiento del formulario
// -------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['crear_reserva'])) {
    try {
        $datosReserva = [
            'zona_id' => $_POST['zona_id'],
            'apartamento' => $_POST['apartamento'],
            'nombre_residente' => $_POST['nombre_residente'],
            'fecha_reserva' => $_POST['fecha_reserva'],
            'hora_inicio' => $_POST['hora_inicio'],
            'hora_fin' => $_POST['hora_fin'],
            'observaciones' => $_POST['observaciones'] ?? null,
            'telefono' => $_POST['telefono'] ?? null
        ];

        if ($controladorReservas->crearReserva($datosReserva)) {
            header('Location: reservas.php');
            exit;
        }
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

require_once './Layout/header.php';
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ZONEMAISONS - Sistema de Reservas</title>

    <!-- Hojas de estilo -->
    <link rel="stylesheet" href="../assets/css/areas-comunes/reserva2.css">
    <link rel="stylesheet" href="../assets/Css/globals.css">
    <link rel="stylesheet" href="../assets/Css/Layout/header.css">
    <link rel="stylesheet" href="../assets/Css/Layout/footer.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Libreria de iconos RemixIcon-->
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
</head>

<body>
    <div class="main-container">
        <!-- ======================
            SECCIÓN DE RESERVAS
            ====================== -->
        <div class="reservas-section">
            <?php if (isset($error)): ?>
                <div class="alert alert-danger alert-dismissible fade show">
                    <?php echo htmlspecialchars($error); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <!-- Encabezado con título y botones -->
            <div class="header-container">
                <h1 class="reservas-title"><i class=></i> RESERVAS</h1>
                <div class="botones-container">
                    <a href="reservas.php" class="btn-icon">
                        <i class="fas fa-list"></i> Ver Todas las Reservas
                    </a>
                    <a href="../views/areas comunes/zonas comunes/index.php" class="btn-icon">
                        <i class="fas fa-plus-circle"></i> Crear Zonas Comunes
                    </a>
                </div>
            </div>

            <!-- Formulario de reserva -->
            <form id="reservaForm" method="POST" novalidate>
                <div class="row">
                    <!-- Columna izquierda -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="zona_id" class="form-label">Zona Común *</label>
                            <select class="form-select" id="zona_id" name="zona_id" required>
                                <option value="">Seleccione una zona</option>
                                <?php foreach ($zonas as $zona): ?>
                                    <option value="<?php echo $zona['id']; ?>"
                                        data-capacidad="<?php echo htmlspecialchars($zona['capacidad']); ?>"
                                        data-hora-apertura="<?php echo htmlspecialchars($zona['hora_apertura']); ?>"
                                        data-hora-cierre="<?php echo htmlspecialchars($zona['hora_cierre']); ?>"
                                        data-duracion-maxima="<?php echo htmlspecialchars($zona['duracion_maxima'] ?? 2); ?>">
                                        <?php echo htmlspecialchars($zona['nombre']); ?>
                                        (Capacidad: <?php echo htmlspecialchars($zona['capacidad']); ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="nombre_residente" class="form-label">Nombre del Residente *</label>
                            <input type="text" class="form-control" id="nombre_residente" name="nombre_residente" required>
                        </div>
                    </div>

                    <!-- Columna derecha -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="apartamento" class="form-label">Apartamento *</label>
                            <input type="text" class="form-control" id="apartamento" name="apartamento" required>
                        </div>

                        <div class="form-group">
                            <label for="telefono" class="form-label">Teléfono</label>
                            <input type="tel" class="form-control" id="telefono" name="telefono">
                        </div>
                    </div>
                </div>

                <!-- Fila de fechas y horarios -->
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="fecha_reserva" class="form-label">Fecha de Reserva *</label>
                            <input type="date" class="form-control" id="fecha_reserva" name="fecha_reserva"
                                min="<?php echo date('Y-m-d'); ?>" required>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="hora_inicio" class="form-label">Hora de Inicio *</label>
                            <input type="time" class="form-control" id="hora_inicio" name="hora_inicio" required>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="hora_fin" class="form-label">Hora de Fin *</label>
                            <input type="time" class="form-control" id="hora_fin" name="hora_fin" required>
                        </div>
                    </div>
                </div>

                <!-- Observaciones -->
                <div class="form-group">
                    <label for="observaciones" class="form-label">Observaciones</label>
                    <textarea class="form-control" id="observaciones" name="observaciones" rows="3"></textarea>
                </div>

                <!-- Botones de acción -->
                <div class="form-actions">
                    <button type="reset" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Limpiar
                    </button>
                    <button type="submit" name="crear_reserva" class="btn btn-primary">
                        <i class="fas fa-save"></i> Guardar Reserva
                    </button>
                </div>
            </form>
        </div>

        <!-- ======================
             SECCIÓN DE ÁREAS COMUNES
             ====================== -->
        <div class="areas-section">
            <h2 class="section-title">Áreas Comunes</h2>

            <div class="areas-grid">
                <?php if (empty($zonas)): ?>
                    <p class="text-muted">No hay zonas disponibles.</p>
                <?php else: ?>
                    <?php foreach ($zonas as $zona): ?>
                        <div class="area-card">
                            <a href=../views/zona-comun1.php
                                <!-- <?php echo $zona['id']; ?> style="display: block;"> -->
                                <img src="../assets/img/<?php echo $zona['imagen'] ?? 'default.jpg'; ?>"
                                    alt="<?php echo htmlspecialchars($zona['nombre']); ?>">
                            </a>
                            <div class="area-card-content">
                                <h3><?php echo htmlspecialchars($zona['nombre']); ?></h3>
                                <p><?php echo htmlspecialchars($zona['descripcion']); ?></p>

                                <span class="status-indicator <?php echo ($zona['estado'] == 'activo') ? 'status-disponible' : 'status-mantenimiento'; ?>">
                                    <?php echo ($zona['estado'] == 'activo') ? 'Disponible' : 'Mantenimiento'; ?>
                                </span>

                                <div class="area-details">
                                    <div class="detail-item">
                                        <span class="detail-label">Capacidad:</span>
                                        <span class="detail-value"><?php echo $zona['capacidad']; ?> personas</span>
                                    </div>
                                    <div class="detail-item">
                                        <span class="detail-label">Horario:</span>
                                        <span class="detail-value">
                                            <?php echo $zona['hora_apertura']; ?> - <?php echo $zona['hora_cierre']; ?>
                                        </span>
                                    </div>
                                    <div class="detail-item">
                                        <span class="detail-label">Duración máxima:</span>
                                        <span class="detail-value"><?php echo $zona['duracion_maxima'] ?? 2; ?> horas</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Scripts JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('reservaForm').addEventListener('submit', function(e) {
            const fecha = new Date(document.getElementById('fecha_reserva').value);
            const hoy = new Date();
            hoy.setHours(0, 0, 0, 0);

            if (fecha < hoy) {
                e.preventDefault();
                alert('La fecha no puede ser anterior a hoy');
                return false;
            }

            const inicio = document.getElementById('hora_inicio').value;
            const fin = document.getElementById('hora_fin').value;

            if (inicio >= fin) {
                e.preventDefault();
                alert('La hora final debe ser posterior a la inicial');
                return false;
            }

            const zonaSelect = document.getElementById('zona_id');
            const zonaSeleccionada = zonaSelect.options[zonaSelect.selectedIndex];

            if (zonaSeleccionada.value) {
                const horaApertura = zonaSeleccionada.dataset.horaApertura;
                const horaCierre = zonaSeleccionada.dataset.horaCierre;
                const duracionMaxima = parseInt(zonaSeleccionada.dataset.duracionMaxima) || 2;

                if (inicio < horaApertura || fin > horaCierre) {
                    e.preventDefault();
                    const nombreZona = zonaSeleccionada.text.split(' (')[0];
                    alert(`La zona ${nombreZona} solo está disponible entre ${horaApertura} y ${horaCierre}`);
                    return false;
                }

                const horaInicioParts = inicio.split(':');
                const horaFinParts = fin.split(':');
                const minutosInicio = parseInt(horaInicioParts[0]) * 60 + parseInt(horaInicioParts[1]);
                const minutosFin = parseInt(horaFinParts[0]) * 60 + parseInt(horaFinParts[1]);
                const duracionHoras = (minutosFin - minutosInicio) / 60;

                if (duracionHoras > duracionMaxima) {
                    e.preventDefault();
                    alert(`La duración máxima para esta zona es de ${duracionMaxima} horas`);
                    return false;
                }
            }

            return true;
        });

        document.getElementById('zona_id').addEventListener('change', function() {
            const zonaSeleccionada = this.options[this.selectedIndex];
            if (zonaSeleccionada.value) {
                console.log(`Capacidad: ${zonaSeleccionada.dataset.capacidad} personas`);
            }
        });
    </script>
</body>

</html>