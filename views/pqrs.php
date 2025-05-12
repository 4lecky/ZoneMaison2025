<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PQRS ZONEMAISONS</title>
    <link rel="stylesheet" href="../assets/Css/pqrs.css">
    <script src="/PQRS/js/script.js" defer></script>
</head>
<body> 

    <!-- Header -->
    <header>
        <div class="logo-container">
            <img src="../assets/img/LogoZM.png" alt="ZONEMAISONS Logo" class="logo">
        </div>
        <h1 class="title">ZONEMAISONS</h1>
    </header>

    <!-- Navegación -->
    <nav class="main-nav">
        <ul>
            <li><a href="index.php">Inicio</a></li>
            <li><a href="#">Notificaciones</a></li>
            <li><a href="#">Reservas</a></li>
        </ul>
    </nav>

    <!-- Título -->
    <div class="titulo-container">
        ✨ Tu Opinión Construye: Hagamos de Nuestro Espacio un Mejor Lugar
    </div>
    
    <!-- Fondo con mensaje y opciones -->
    <div class="fondo-container">
        <div class="mensaje-container">    
            <div class="contenedor-limitado"> <!-- NUEVO CONTENEDOR -->
                <div class="opciones-container">
                    <div class="opcion" onclick="location.href='crear_pqr.php'">
                        <img src="../assets/img/crear_pqr.png" alt="Crear PQR">
                        <p>Crear PQR</p>
                    </div>
                    <div class="opcion" onclick="location.href='sugerencias.php'">
                        <img src="../assets/img/sugerencias.png" alt="Sugerencias">
                        <p>Sugerencias</p>
                    </div>
                    <div class="opcion" id="openModal">
                        <img src="../assets/img/estado_pqr.png" alt="Estado de mi PQR">
                        <p>Estado de mi PQR</p>
                    </div>

                    <a href="#dudas" class="opcion">
                        <img src="../assets/img/preguntas.png" alt="Preguntas Frecuentes">
                        <p>Preguntas Frecuentes</p>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div id="modal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Consultar Estado de PQR</h2>
            <br>
            <form id="pqr-form">
                <label for="pqr-id">Número de PQR:</label>
                <input type="text" id="pqr-id" name="pqr-id" required>
                <br><br>
                <button type="submit" class="btn">Consultar</button>
            </form>
        </div>
    </div>

    <div class="texto-container">
        Con nuestro sistema PQRS, puedes enviar peticiones, quejas, reclamos y sugerencias de manera rápida y sencilla. 
        ¡Tu opinión ayuda a mejorar nuestra gestión en pro a la comunidad !
    </div>

    <div class="inquietudes-container" id="dudas">
        <h2>¿Tienes inquietudes sobre tus PQR?</h2>
    
        <div class="faq-item">
            <button class="faq-question">
                📌 ¿Quién puede ver mis PQRS? <span class="arrow">▼</span>
            </button>
            <div class="faq-answer">
                Solo tú y los administradores autorizados pueden ver el contenido de tus PQRS.
            </div>
        </div>
    
        <div class="faq-item">
            <button class="faq-question">
                📌 ¿Cuánto tiempo tarda en resolverse una PQR? <span class="arrow">▼</span>
            </button>
            <div class="faq-answer">
                El tiempo puede variar, pero generalmente se responde en un plazo de 5 a 10 días hábiles.
            </div>
        </div>
    
        <div class="faq-item">
            <button class="faq-question">
                📌 ¿Puedo adjuntar documentos o imágenes a mi PQR? <span class="arrow">▼</span>
            </button>
            <div class="faq-answer">
                Sí, el sistema permite adjuntar archivos PDF, imágenes y documentos relacionados.
            </div>
        </div>
    
        <div class="faq-item">
            <button class="faq-question">
                📌 ¿Cómo me notifican sobre la respuesta a mi PQR? <span class="arrow">▼</span>
            </button>
            <div class="faq-answer">
                Recibirás una notificación por correo electrónico y también podrás ver el estado desde la plataforma.
            </div>
        </div>
    </div>
    

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-col">
                    <h3>ZoneMaisons</h3>
                    <p>Soluciones integrales para administración de propiedad horizontal, diseñadas para optimizar la gestión y mejorar la calidad de vida comunitaria.</p>
                    <div class="social-links">
                        <a href="#">📘</a>
                        <a href="#">📱</a>
                        <a href="#">📸</a>
                        <a href="#">🔗</a>
                    </div>
                </div>
                <div class="footer-col">
                    <h3>Enlaces</h3>
                    <ul>
                        <li><a href="#">Inicio</a></li>
                        <li><a href="#">Nosotros</a></li>
                        <li><a href="#">Funciones</a></li>
                        <li><a href="#">Precios</a></li>
                        <li><a href="#">Blog</a></li>
                    </ul>
                </div>
                <div class="footer-col">
                    <h3>Contacto</h3>
                    <ul>
                        <li>📍 Cra 32 #33-90, Soacha</li>
                        <li>📞 +57 302 593 9177</li>
                        <li>✉️ zonemaisons@gmail.com</li>
                    </ul>
                </div>
            </div>
            <div class="copyright">
                <p>&copy; 2025 ZoneMaisons. Todos los derechos reservados.</p>
            </div>
        </div>
    </footer>
    

</body>
</html>

