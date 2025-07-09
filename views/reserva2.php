<?php
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

</head>
<body>
    <div class="main-container">
        <!-- Sección de Reservas -->
        <div class="reservas-section">
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
        </div>

        <!-- Sección de Áreas Comunes -->
        <div class="areas-section">
            <h2 class="section-title">Áreas Comunes</h2>
            
            <div class="areas-grid">
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
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

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

    