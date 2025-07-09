<?php
<<<<<<< HEAD
require_once '../controller/ReservaController.php';
require_once '../controller/ZonaController.php';

// Iniciar sesión si no está iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$controladorReservas = new ReservaController();
$controladorZonas = new ZonaController();
$zonas = $controladorZonas->listarZonasComunes();


if (!empty($zonas)): ?>
    <?php foreach ($zonas as $zona): ?>
        <!-- tu tarjeta o <option> -->
    <?php endforeach; ?>
<?php else: ?>
    <p class="text-muted">No hay zonas disponibles.</p>
<?php endif; 


// Procesar formulario
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

=======
require_once '../controller/ControladorReservas.php';

$controlador = new ControladorReservas();

if (isset($_GET['accion'])) {
    $accion = $_GET['accion'];
    switch ($accion) {
        case 'crear_reserva':
            $controlador->crearReserva();
            break;
        // Agregar más casos si es necesario
    }
}
?>

<?php

require_once './Layout/header.php'
?>


>>>>>>> b749a1aeeb17e11874b55fe17b55fa3d883dc79d
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ZONEMAISONS - Sistema de Reservas</title>
    <link rel="stylesheet" href="../assets/css/areas-comunes/reserva2.css">
    <link rel="stylesheet" href="../assets/Css/globals.css" />
    <link rel="stylesheet" href="../assets/Css/Layout/header.css" />
    <link rel="stylesheet" href="../assets/Css/Layout/footer.css" />
<<<<<<< HEAD
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
=======

>>>>>>> b749a1aeeb17e11874b55fe17b55fa3d883dc79d
</head>
<body>
    <div class="main-container">
        <!-- Sección de Reservas -->
        <div class="reservas-section">
<<<<<<< HEAD
            <?php if (isset($error)): ?>
                <div class="alert alert-danger alert-dismissible fade show">
                    <?php echo htmlspecialchars($error); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
            
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1><i class="fas fa-calendar-alt"></i> Sistema de Reservas</h1>
                <a href="reservas.php" class="btn btn-secondary">
                    <i class="fas fa-list"></i> Ver Todas las Reservas
                </a>
            </div>
            
            <form id="reservaForm" method="POST" novalidate>
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
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
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="apartamento" class="form-label">Apartamento *</label>
                            <input type="text" class="form-control" id="apartamento" name="apartamento" required>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="nombre_residente" class="form-label">Nombre del Residente *</label>
                            <input type="text" class="form-control" id="nombre_residente" name="nombre_residente" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="telefono" class="form-label">Teléfono</label>
                            <input type="tel" class="form-control" id="telefono" name="telefono">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="fecha_reserva" class="form-label">Fecha de Reserva *</label>
                            <input type="date" class="form-control" id="fecha_reserva" name="fecha_reserva" 
                                   min="<?php echo date('Y-m-d'); ?>" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="hora_inicio" class="form-label">Hora de Inicio *</label>
                            <input type="time" class="form-control" id="hora_inicio" name="hora_inicio" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="hora_fin" class="form-label">Hora de Fin *</label>
                            <input type="time" class="form-control" id="hora_fin" name="hora_fin" required>
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="observaciones" class="form-label">Observaciones</label>
                    <textarea class="form-control" id="observaciones" name="observaciones" rows="3"></textarea>
                </div>

                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <button type="reset" class="btn btn-secondary me-md-2">
                        <i class="fas fa-times"></i> Limpiar
                    </button>
                    <button type="submit" name="crear_reserva" class="btn btn-primary">
                        <i class="fas fa-save"></i> Guardar Reserva
                    </button>
                </div>
            </form>
=======
            <h2 class="section-title">Sistema de Reservas</h2>
            
            <form id="reservaForm">
                <div class="form-group">
                    <label for="nombre">Nombre Completo:</label>
                    <input type="text" id="nombre" name="nombre" required>
                </div>
                
                <div class="form-group">
                    <label for="documento">Número de Documento:</label>
                    <input type="text" id="documento" name="documento" required>
                </div>
                
                <div class="form-group">
                    <label for="apartamento">Número de Apartamento:</label>
                    <input type="text" id="apartamento" name="apartamento" required>
                </div>
                
                <div class="form-group">
                    <label for="areaSeleccionada">Área a Reservar:</label>
                    <select id="areaSeleccionada" name="areaSeleccionada" required>
                        <option value="">Seleccione un área</option>
                        <option value="salon-comunal">Salón Comunal</option>
                        <option value="piscina">Piscina</option>
                        <option value="gimnasio">Gimnasio</option>
                    </select>
                </div>
            </form>

            <div class="calendar-container">
                <div class="calendar-header">
                    <button class="calendar-nav" onclick="previousMonth()">‹</button>
                    <h3 id="currentMonth">Febrero 2025</h3>
                    <button class="calendar-nav" onclick="nextMonth()">›</button>
                </div>
                
                <div class="calendar-grid" id="calendar">
                    <!-- Calendario generado dinámicamente -->
                </div>
                
                <div class="legend">
                    <div class="legend-item">
                        <div class="legend-color" style="background: #e8f5e8;"></div>
                        <span>Disponible</span>
                    </div>
                    <div class="legend-item">
                        <div class="legend-color" style="background: #ffe8e8;"></div>
                        <span>Con reservas</span>
                    </div>
                    <div class="legend-item">
                        <div class="legend-color" style="background: #f5f5f5;"></div>
                        <span>No disponible</span>
                    </div>
                </div>
            </div>

            <div class="horarios-disponibles" id="horariosContainer" style="display: none;">
                <h4>Horarios Disponibles</h4>
                <div class="horarios-grid" id="horariosGrid">
                    <!-- Horarios generados dinámicamente -->
                </div>
            </div>

            <button class="reservar-btn" id="reservarBtn" disabled onclick="realizarReserva()">
                RESERVAR
            </button>
>>>>>>> b749a1aeeb17e11874b55fe17b55fa3d883dc79d
        </div>

        <!-- Sección de Áreas Comunes -->
        <div class="areas-section">
            <h2 class="section-title">Áreas Comunes</h2>
            
            <div class="areas-grid">
<<<<<<< HEAD
                <?php foreach ($zonas as $zona): ?>
                <div class="area-card">
                    <a href="zona-comun-detalle.php?id=<?php echo $zona['id']; ?>" style="display: block;">
                        <img src="../assets/img/<?php echo $zona['imagen'] ?? 'default.jpg'; ?>" alt="<?php echo htmlspecialchars($zona['nombre']); ?>">
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
                                <span class="detail-value"><?php echo $zona['hora_apertura']; ?> - <?php echo $zona['hora_cierre']; ?></span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Duración máxima:</span>
                                <span class="detail-value"><?php echo $zona['duracion_maxima'] ?? 2; ?> horas</span>
=======
                <div class="area-card">
                    <a href="zona-comun1.php" style="display: block;">
                    <img src="../assets/img/salon-comunal.jpg" alt="Salon Comunal">
                    </a>
                    <div class="area-card-content" onclick="toggleAreaDetails('salon-comunal')">
                        <h3>Salón Comunal</h3>
                        <p>Amplio salón para eventos sociales y reuniones comunitarias. Capacidad para 60 personas.</p>
                        <span class="status-indicator status-disponible">Disponible</span>
                        
                        <div class="area-details" id="details-salon-comunal">
                            <div class="detail-item">
                                <span class="detail-label">Capacidad:</span>
                                <span class="detail-value">60 personas</span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Horario:</span>
                                <span class="detail-value">8:00 AM - 10:00 PM</span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Duración máxima:</span>
                                <span class="detail-value">5 horas</span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Incluye:</span>
                                <span class="detail-value">Mesas, sillas, audio básico</span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Depósito:</span>
                                <span class="detail-value">$200.000</span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Restricciones:</span>
                                <span class="detail-value">No fumar, volumen moderado</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="area-card">
                    <a href="zona-comun1.php" style="display: block;">
                    <img src="../assets/img/piscina.jpg" alt="Piscina">
                    </a>
                    <div class="area-card-content" onclick="toggleAreaDetails('piscina')">
                        <h3>Piscina</h3>
                        <p>Piscina olímpica con área de relajación y zona para niños. Perfecta para uso familiar.</p>
                        <span class="status-indicator status-disponible">Disponible</span>
                        
                        <div class="area-details" id="details-piscina">
                            <div class="detail-item">
                                <span class="detail-label">Tipo:</span>
                                <span class="detail-value">Piscina olímpica + infantil</span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Horario:</span>
                                <span class="detail-value">6:00 AM - 9:00 PM</span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Duración máxima:</span>
                                <span class="detail-value">4 horas</span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Incluye:</span>
                                <span class="detail-value">Salvavidas, duchas, vestidores</span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Depósito:</span>
                                <span class="detail-value">$150.000</span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Restricciones:</span>
                                <span class="detail-value">Máximo 15 invitados, no vidrio</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="area-card">
                    <a href="zona-comun1.php" style="display: block;">
                    <img src="../assets/img/gimnasio.jpg" alt="Gimnasio">
                    </a>
                    <div class="area-card-content" onclick="toggleAreaDetails('gimnasio')">
                        <h3>Gimnasio</h3>
                        <p>Gimnasio completamente equipado con máquinas de cardio, pesas y área de entrenamiento funcional.</p>
                        <span class="status-indicator status-mantenimiento">Mantenimiento</span>
                        
                        <div class="area-details" id="details-gimnasio">
                            <div class="detail-item">
                                <span class="detail-label">Equipamiento:</span>
                                <span class="detail-value">Cardio, pesas, funcional</span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Horario:</span>
                                <span class="detail-value">5:00 AM - 11:00 PM</span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Duración máxima:</span>
                                <span class="detail-value">2 horas</span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Incluye:</span>
                                <span class="detail-value">Equipos, vestuarios, agua</span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Depósito:</span>
                                <span class="detail-value">$100.000</span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Estado:</span>
                                <span class="detail-value">Mantenimiento hasta 20/02</span>
>>>>>>> b749a1aeeb17e11874b55fe17b55fa3d883dc79d
                            </div>
                        </div>
                    </div>
                </div>
<<<<<<< HEAD
                <?php endforeach; ?>
=======
>>>>>>> b749a1aeeb17e11874b55fe17b55fa3d883dc79d
            </div>
        </div>
    </div>

<<<<<<< HEAD
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('reservaForm').addEventListener('submit', function(e) {
            // Validación básica del cliente
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

            // Validación según zona seleccionada
            const zonaSelect = document.getElementById('zona_id');
            const zonaSeleccionada = zonaSelect.options[zonaSelect.selectedIndex];
            
            if (zonaSeleccionada.value) {
                const horaApertura = zonaSeleccionada.dataset.horaApertura;
                const horaCierre = zonaSeleccionada.dataset.horaCierre;
                const duracionMaxima = parseInt(zonaSeleccionada.dataset.duracionMaxima) || 2;
                
                // Validar horario de apertura/cierre
                if (inicio < horaApertura || fin > horaCierre) {
                    e.preventDefault();
                    const nombreZona = zonaSeleccionada.text.split(' (')[0];
                    alert(`La zona ${nombreZona} solo está disponible entre ${horaApertura} y ${horaCierre}`);
                    return false;
                }
                
                // Validar duración máxima
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

        // Validación en tiempo real
        document.getElementById('zona_id').addEventListener('change', function() {
            const zonaSeleccionada = this.options[this.selectedIndex];
            if (zonaSeleccionada.value) {
                const capacidad = zonaSeleccionada.dataset.capacidad;
                console.log(`Capacidad: ${capacidad} personas`);
            }
        });
    </script>
</body>
</html>
=======
    <script>
        let currentDate = new Date();
        let selectedDate = null;
        let selectedTime = null;
        let selectedArea = null;

        // Datos de ejemplo para disponibilidad
        const availability = {
            'salon-comunal': {
                '2025-02-15': ['09:00', '10:00', '14:00', '15:00', '16:00'],
                '2025-02-16': ['08:00', '09:00', '10:00', '11:00'],
                '2025-02-17': [],
                '2025-02-18': ['14:00', '15:00', '16:00', '17:00', '18:00'],
                '2025-02-19': ['09:00', '10:00', '11:00', '14:00', '15:00']
            },
            'piscina': {
                '2025-02-15': ['08:00', '09:00', '10:00', '15:00', '16:00'],
                '2025-02-16': ['07:00', '08:00', '14:00', '15:00', '16:00'],
                '2025-02-17': ['09:00', '10:00', '11:00'],
                '2025-02-18': ['08:00', '09:00', '15:00', '16:00'],
                '2025-02-19': ['07:00', '08:00', '09:00', '10:00']
            },
            'gimnasio': {
                '2025-02-15': [],
                '2025-02-16': [],
                '2025-02-17': [],
                '2025-02-18': [],
                '2025-02-19': []
            }
        };

        function initCalendar() {
            updateCalendar();
            
            document.getElementById('areaSeleccionada').addEventListener('change', function() {
                selectedArea = this.value;
                updateCalendar();
                hideSchedule();
            });
        }

        function updateCalendar() {
            const calendar = document.getElementById('calendar');
            const monthNames = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
                               'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
            
            document.getElementById('currentMonth').textContent = 
                `${monthNames[currentDate.getMonth()]} ${currentDate.getFullYear()}`;
            
            calendar.innerHTML = '';
            
            // Headers
            const dayHeaders = ['DOM', 'LUN', 'MAR', 'MIE', 'JUE', 'VIE', 'SÁB'];
            dayHeaders.forEach(day => {
                const dayElement = document.createElement('div');
                dayElement.className = 'calendar-day header';
                dayElement.textContent = day;
                calendar.appendChild(dayElement);
            });
            
            // Days
            const firstDay = new Date(currentDate.getFullYear(), currentDate.getMonth(), 1);
            const lastDay = new Date(currentDate.getFullYear(), currentDate.getMonth() + 1, 0);
            const startDate = new Date(firstDay);
            startDate.setDate(startDate.getDate() - firstDay.getDay());
            
            for (let i = 0; i < 42; i++) {
                const date = new Date(startDate);
                date.setDate(startDate.getDate() + i);
                
                const dayElement = document.createElement('div');
                dayElement.className = 'calendar-day';
                dayElement.textContent = date.getDate();
                
                const dateString = date.toISOString().split('T')[0];
                const isCurrentMonth = date.getMonth() === currentDate.getMonth();
                
                if (!isCurrentMonth) {
                    dayElement.classList.add('unavailable');
                } else if (selectedArea && availability[selectedArea]) {
                    const dayAvailability = availability[selectedArea][dateString];
                    if (dayAvailability === undefined || dayAvailability.length === 0) {
                        dayElement.classList.add('reserved');
                    } else if (dayAvailability.length > 0) {
                        dayElement.classList.add('available');
                    }
                    // Permitir hacer clic en cualquier día del mes actual para ver horarios
                    dayElement.onclick = () => selectDate(dateString, dayElement);
                } else {
                    dayElement.classList.add('unavailable');
                }
                
                calendar.appendChild(dayElement);
            }
        }

        function selectDate(dateString, element) {
            // Remove previous selection
            document.querySelectorAll('.calendar-day.selected').forEach(day => {
                day.classList.remove('selected');
            });
            
            // Add selection to clicked day
            element.classList.add('selected');
            selectedDate = dateString;
            
            showSchedule(dateString);
        }

        function showSchedule(dateString) {
            const container = document.getElementById('horariosContainer');
            const grid = document.getElementById('horariosGrid');
            
            if (!selectedArea || !availability[selectedArea]) {
                container.style.display = 'none';
                return;
            }
            
            // Mostrar todas las horas del día (8 AM a 8 PM)
            const allHours = ['08:00', '09:00', '10:00', '11:00', '12:00', '13:00', '14:00', '15:00', '16:00', '17:00', '18:00', '19:00', '20:00'];
            const availableTimes = availability[selectedArea][dateString] || [];
            
            grid.innerHTML = '';
            
            allHours.forEach(time => {
                const timeSlot = document.createElement('div');
                timeSlot.className = 'horario-slot';
                timeSlot.textContent = time;
                
                if (availableTimes.includes(time)) {
                    // Hora disponible
                    timeSlot.onclick = () => selectTime(time, timeSlot);
                } else {
                    // Hora no disponible
                    timeSlot.classList.add('unavailable');
                }
                
                grid.appendChild(timeSlot);
            });
            
            container.style.display = 'block';
            updateReserveButton();
        }

        function selectTime(time, element) {
            document.querySelectorAll('.horario-slot.selected').forEach(slot => {
                slot.classList.remove('selected');
            });
            
            element.classList.add('selected');
            selectedTime = time;
            updateReserveButton();
        }

        function hideSchedule() {
            document.getElementById('horariosContainer').style.display = 'none';
            selectedDate = null;
            selectedTime = null;
            updateReserveButton();
        }

        function updateReserveButton() {
            const btn = document.getElementById('reservarBtn');
            const form = document.getElementById('reservaForm');
            const formValid = form.checkValidity();
            
            btn.disabled = !(formValid && selectedDate && selectedTime && selectedArea);
        }

        function realizarReserva() {
            const nombre = document.getElementById('nombre').value;
            const documento = document.getElementById('documento').value;
            const apartamento = document.getElementById('apartamento').value;
            
            alert(`Reserva realizada exitosamente!\n\nDetalles:\nNombre: ${nombre}\nDocumento: ${documento}\nApartamento: ${apartamento}\nÁrea: ${selectedArea}\nFecha: ${selectedDate}\nHora: ${selectedTime}`);
            
            // Reset form
            document.getElementById('reservaForm').reset();
            hideSchedule();
            document.querySelectorAll('.calendar-day.selected').forEach(day => {
                day.classList.remove('selected');
            });
            selectedArea = null;
            selectedDate = null;
            selectedTime = null;
            updateReserveButton();
        }

        function toggleAreaDetails(areaId) {
            const details = document.getElementById(`details-${areaId}`);
            details.classList.toggle('active');
        }

        function previousMonth() {
            currentDate.setMonth(currentDate.getMonth() - 1);
            updateCalendar();
            hideSchedule();
        }

        function nextMonth() {
            currentDate.setMonth(currentDate.getMonth() + 1);
            updateCalendar();
            hideSchedule();
        }

        // Initialize calendar when page loads
        document.addEventListener('DOMContentLoaded', initCalendar);

        // Update reserve button when form changes
        document.getElementById('reservaForm').addEventListener('input', updateReserveButton);
    </script>
        <footer>
        <div class="buttons">
            <a href="#" class="round-button edit-button">
                <span>✎</span>
            </a>
            <a href="crear-zona-comun.php" class="round-button add-button">
                <span>+</span>
            </a>
        </div>
    </footer>

</body>

</html>

    
>>>>>>> b749a1aeeb17e11874b55fe17b55fa3d883dc79d
