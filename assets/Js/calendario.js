class CalendarReservation {
    constructor() {
        this.currentMonth = 1; // Febrero (0-based)
        this.currentYear = 2025;
        this.selectedDate = null;
        this.selectedTimeSlot = null;
        this.bookedSlots = {}; // Almacenar reservas existentes
        
        this.init();
    }
    
    init() {
        this.generateCalendar();
        this.loadExistingReservations();
        this.setupFormHandler();
    }
    
    generateCalendar() {
        const calendarGrid = document.getElementById('calendar-grid');
        const firstDay = new Date(this.currentYear, this.currentMonth, 1);
        const lastDay = new Date(this.currentYear, this.currentMonth + 1, 0);
        const startDate = new Date(firstDay);
        startDate.setDate(startDate.getDate() - firstDay.getDay());
        
        calendarGrid.innerHTML = '';
        
        for (let i = 0; i < 42; i++) {
            const currentDate = new Date(startDate);
            currentDate.setDate(startDate.getDate() + i);
            
            const dayElement = document.createElement('div');
            dayElement.className = 'calendar-day';
            dayElement.textContent = currentDate.getDate();
            
            // Marcar días del mes anterior/siguiente
            if (currentDate.getMonth() !== this.currentMonth) {
                dayElement.classList.add('other-month');
            }
            
            // Marcar días pasados
            if (currentDate < new Date().setHours(0, 0, 0, 0)) {
                dayElement.classList.add('past-day');
            } else {
                dayElement.addEventListener('click', () => this.selectDate(currentDate));
            }
            
            // Marcar fechas con reservas
            const dateKey = this.formatDateKey(currentDate);
            if (this.bookedSlots[dateKey] && this.bookedSlots[dateKey].length > 0) {
                dayElement.classList.add('has-reservations');
            }
            
            calendarGrid.appendChild(dayElement);
        }
    }
    
    selectDate(date) {
        // Remover selección anterior
        document.querySelectorAll('.calendar-day.selected').forEach(day => {
            day.classList.remove('selected');
        });
        
        // Seleccionar nuevo día
        event.target.classList.add('selected');
        this.selectedDate = date;
        this.generateTimeSlots(date);
    }
    
    generateTimeSlots(date) {
        const timeSlotsContent = document.querySelector('.time-slots-content');
        const dateKey = this.formatDateKey(date);
        const bookedTimes = this.bookedSlots[dateKey] || [];
        
        // Horarios disponibles de 8:00 AM a 10:00 PM
        const timeSlots = [];
        for (let hour = 8; hour < 22; hour++) {
            timeSlots.push(`${hour}:00 - ${hour + 1}:00`);
        }
        
        timeSlotsContent.innerHTML = `
            <h4>Horarios para ${date.toLocaleDateString('es-ES')}</h4>
            <div class="slots-grid">
                ${timeSlots.map(slot => {
                    const isBooked = bookedTimes.includes(slot);
                    return `
                        <div class="time-slot ${isBooked ? 'booked' : 'available'}" 
                             data-time="${slot}" 
                             ${!isBooked ? 'onclick="calendar.selectTimeSlot(this)"' : ''}>
                            ${slot}
                            ${isBooked ? '<span class="booked-label">Ocupado</span>' : ''}
                        </div>
                    `;
                }).join('')}
            </div>
        `;
    }
    
    selectTimeSlot(element) {
        // Remover selección anterior
        document.querySelectorAll('.time-slot.selected').forEach(slot => {
            slot.classList.remove('selected');
        });
        
        // Seleccionar nuevo horario
        element.classList.add('selected');
        this.selectedTimeSlot = element.dataset.time;
    }
    
    formatDateKey(date) {
        return date.toISOString().split('T')[0];
    }
    
    async loadExistingReservations() {
        try {
            const response = await fetch('api/get_reservations.php');
            const reservations = await response.json();
            
            this.bookedSlots = {};
            reservations.forEach(reservation => {
                const dateKey = reservation.rese_fecha_inicio;
                if (!this.bookedSlots[dateKey]) {
                    this.bookedSlots[dateKey] = [];
                }
                this.bookedSlots[dateKey].push(
                    `${reservation.rese_hora_inicio.substring(0,5)} - ${reservation.rese_hora_fin.substring(0,5)}`
                );
            });
            
            this.generateCalendar(); // Regenerar para mostrar reservas
        } catch (error) {
            console.error('Error loading reservations:', error);
        }
    }
    
    setupFormHandler() {
        const form = document.getElementById('booking-form');
        form.addEventListener('submit', (e) => this.handleFormSubmit(e));
    }
    
    async handleFormSubmit(e) {
        e.preventDefault();
        
        if (!this.selectedDate || !this.selectedTimeSlot) {
            alert('Por favor seleccione una fecha y horario');
            return;
        }
        
        const formData = new FormData(e.target);
        const [startTime, endTime] = this.selectedTimeSlot.split(' - ');
        
        const reservationData = {
            nombre: formData.get('name'),
            apartamento: formData.get('apartment'),
            documento: formData.get('document'),
            fecha: this.formatDateKey(this.selectedDate),
            hora_inicio: startTime + ':00',
            hora_fin: endTime + ':00',
            area_id: 1 // ID del salón comunal
        };
        
        try {
            const response = await fetch('api/create_reservation.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(reservationData)
            });
            
            const result = await response.json();
            
            if (result.success) {
                alert('¡Reserva realizada exitosamente!');
                this.loadExistingReservations(); // Recargar reservas
                e.target.reset(); // Limpiar formulario
                this.selectedDate = null;
                this.selectedTimeSlot = null;
                document.querySelectorAll('.selected').forEach(el => el.classList.remove('selected'));
                document.querySelector('.time-slots-content').innerHTML = 
                    '<p class="select-date-message">Seleccione una fecha en el calendario</p>';
            } else {
                alert('Error al realizar la reserva: ' + result.message);
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Error al procesar la reserva');
        }
    }
}

// Inicializar el calendario cuando se carga la página
document.addEventListener('DOMContentLoaded', function() {
    window.calendar = new CalendarReservation();
});