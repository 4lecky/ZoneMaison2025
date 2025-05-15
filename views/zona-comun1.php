<?php

require_once './Layout/header.php'
?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ZONEMAISONS - Salón Comunal</title>
    <link rel="stylesheet" href="../assets/css/areas-comunes/zona-comun1.css">
</head>
<body>
    <!-- <div class="header">
        <div class="logo-container">
            <img src="img/logo.jpg" alt="Logo" class="logo">
        </div>
        
        <div class="brand-container">
            <div class="brand-name">
                <span class="zone">ZONE</span><span class="maisons">MAISONS</span>
            </div>
            <div class="underline"></div>
        </div>
        
        <button class="menu-button">☰</button>
    </div>
    
    <nav class="nav-bar">
        <a href="../index.php" class="nav-item active">Inicio</a>
        <a href="./visitas.php" class="nav-item">Visitantes</a>
        <a href="./reserva1.php" class="nav-item">Reservas</a>
        <a href="./pqrs.php" class="nav-item">Pqrs</a>
    </nav> -->
    
    <main class="salon-comunal-page">
        <h2>Salón Comunal</h2>
        
        <div class="salon-gallery">
            <img src="../assets/img/salon-comunal-1.jpg" alt="Salon Comunal Vista 1" class="gallery-image">
            <img src="../assets/img/salon-comunal-2.jpg" alt="Salon Comunal Vista 2" class="gallery-image gallery-image-large">
            <img src="../assets/img/salon-comunal-3.jpg" alt="Salon Comunal Vista 3" class="gallery-image">
        </div>
        
        <div class="salon-description">
            <p>Este versátil espacio puede albergar desde reuniones comunitarias hasta celebraciones especiales. Con capacidad para 55 personas, el salón cuenta con un sistema de sonido integrado, aire acondicionado y calefacción para garantizar el confort durante todo el día. Su diseño incorpora elementos tradicionales con comodidades modernas.</p>
        </div>
        
        <h3>Disponibilidad</h3>
        
        <div class="availability-container">
            <div class="calendar-section">
                <div class="calendar-header">
                    <h4>FEBRERO</h4>
                    <h4>2025</h4>
                </div>
                
                <div class="calendar">
                    <div class="calendar-days">
                        <div class="day-header">DOM</div>
                        <div class="day-header">LUN</div>
                        <div class="day-header">MAR</div>
                        <div class="day-header">MIE</div>
                        <div class="day-header">JUE</div>
                        <div class="day-header">VIE</div>
                        <div class="day-header">SAB</div>
                    </div>
                    <div id="calendar-grid">
                        <!-- Calendar will be populated by JavaScript -->
                    </div>
                </div>
            </div>
            
            <div id="time-slots" class="time-slots">
                <h4>Horarios Disponibles</h4>
                <div class="time-slots-content">
                    <!-- Time slots will appear here when a date is selected -->
                    <p class="select-date-message">Seleccione una fecha en el calendario</p>
                </div>
            </div>
        </div>
        
        <div class="terms-and-form">
            <div class="terms">
                <h3>Términos y Condiciones</h3>
                <ol>
                    <li>Reserva con un mínimo de 48 horas de anticipación.</li>
                    <li>Duración máxima de uso: 5 horas.</li>
                    <li>El horario disponible es de 8:00 AM a 10:00 PM.</li>
                    <li>Responsabilidad del horario.</li>
                    <li>Depósito de limpieza reembolsable.</li>
                    <li>Cancelaciones con 24 horas de anticipación.</li>
                    <li>Prohibido fumar.</li>
                    <li>El volumen debe ser moderado después de las 9:00 PM.</li>
                    <li>Los invitados deben estar en la lista de acceso.</li>
                    <li>El usuario es responsable de cualquier daño.</li>
                    <li>Se requiere pago por adelantado.</li>
                    <li>La limpieza es responsabilidad del usuario.</li>
                </ol>
                <a href="#" class="download-button">Descargar PDF</a>
            </div>
            
            <div class="booking-form">
                <h3>Datos Personales</h3>
                <form id="booking-form">
                    <div class="form-group">
                        <label for="name">Nombre</label>
                        <input type="text" id="name" name="name" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="apartment">Apartamento</label>
                        <input type="text" id="apartment" name="apartment" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="document">Documento</label>
                        <input type="text" id="document" name="document" required>
                    </div>
                    
                    <button type="submit" class="reserve-button">RESERVAR</button>
                </form>
            </div>
        </div>
    </main>
    
    <script src="js/calendar.js"></script>
</body>
</html>