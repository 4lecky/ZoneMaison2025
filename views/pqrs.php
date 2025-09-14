<?php
// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['usuario'])) {
    // Solo iniciar sesión si no existe
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    // Verificar nuevamente después de iniciar sesión
    if (!isset($_SESSION['usuario'])) {
        header("Location: login.php");
        exit();
    }
}

// Obtener el rol del usuario
$rol_usuario = $_SESSION['usuario']['rol'] ?? '';

// Verificar que el usuario tenga un rol válido para PQRS
$roles_con_acceso_pqrs = ['Residente', 'Propietario', 'Vigilante', 'Administrador'];
if (!in_array($rol_usuario, $roles_con_acceso_pqrs, true)) {
    $_SESSION['error_mensaje'] = 'No tienes permisos para acceder al módulo PQRS.';
    header("Location: novedades.php"); // Redirigir a página principal
    exit();
}

require_once './Layout/header.php';
?>

<?php if (isset($_GET['editado'])): ?>
  <div class="alerta-exito">¡Registro editado exitosamente!</div>
<?php elseif (isset($_GET['eliminado'])): ?>
  <div class="alerta-exito">¡Registro eliminado exitosamente!</div>
<?php elseif (isset($_GET['error']) && $_GET['error'] === 'eliminando'): ?>
  <div class="alerta-error">Ocurrió un error al intentar eliminar el registro.</div>
<?php elseif (isset($_GET['success']) && $_GET['success'] == '1'): ?>
  <div class="alerta-exito">
    <i class="ri-check-circle-fill"></i>
    ¡PQRS enviada exitosamente! 
    <?php if (isset($_GET['radicado'])): ?>
      Su número de radicado es: <strong><?= htmlspecialchars($_GET['radicado']) ?></strong>
    <?php endif; ?>
    <br>Puede hacer seguimiento desde 'Mis PQRS'.
  </div>
<?php endif; ?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PQRS ZONEMAISONS</title>
    <link rel="stylesheet" href="../assets/Css/pqrs.css">
    <link rel="stylesheet" href="../assets/Css/globals.css" />
    <link rel="stylesheet" href="../assets/Css/Layout/header.css" />
    <link rel="stylesheet" href="../assets/Css/Layout/footer.css" />

    <!-- Iconos -->
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">

    <!-- Font Awesome 6 (CDN) -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    <!-- DataTables y jQuery -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

    <!-- JS principal -->
    <script src="../assets/js/pqrs.js" defer></script>
</head>
<body>
    <!-- Encabezado principal -->
    <div class="encabezado-pqrs">
        <h1>
            <i class="ri-file-list-3-line"></i>
            Sistema de PQRS
        </h1>
        <p class="subtitulo">
            Envía y consulta tus peticiones, quejas, reclamos y sugerencias fácilmente
        </p>
    </div>

    <!-- Tarjetas informativas -->
    <div class="tarjetas-info">
        <div class="tarjeta-info">
            <div class="icono">
                <i class="ri-user-line"></i>
            </div>
            <h3>Para Residentes</h3>
            <p>Facilita la comunicación entre residentes, propietarios, vigilantes y la administración de manera rápida y centralizada.</p>
        </div>
        
        <div class="tarjeta-info">
            <div class="icono">
                <i class="ri-mail-send-line"></i>
            </div>
            <h3>Respuesta Garantizada</h3>
            <p>Recibirás respuesta por correo electrónico y podrás hacer seguimiento en tiempo real desde la plataforma.</p>
        </div>
        
        <div class="tarjeta-info">
            <div class="icono">
                <i class="ri-shield-check-line"></i>
            </div>
            <h3>Seguro y Privado</h3>
            <p>Solo tú y los administradores autorizados pueden ver el contenido de tus PQRS. Tu información está protegida.</p>
        </div>
    </div>

    <div class="contenedor-principal">
        <!-- Sección principal de crear PQR -->
        <div class="seccion-crear">
            <div class="cabecera">
                <h2>¿Necesitas reportar algo?</h2>
                <p>Crea tu PQRS de forma rápida y sencilla</p>
            </div>
            <div class="contenido">
                <a href="crear_pqr.php" class="boton-crear">
                    <i class="ri-add-circle-line"></i>
                    Crear Nueva PQR
                </a>
                <p style="margin-top: 15px; color: #666; font-size: 0.9rem;">
                    <i class="ri-time-line"></i>
                    Proceso rápido • Respuesta en 5-10 días hábiles
                </p>
            </div>
        </div>

        <!-- Opciones secundarias -->
        <div class="opciones-secundarias">
            <a href="mis_pqrs.php" class="opcion-card">
                <div class="imagen">
                    <img src="../assets/img/estado_pqr.png" alt="Estado de mi PQR">
                </div>
                <div class="contenido-card">
                    <h3>Mis PQRS</h3>
                    <p>Consulta el estado y respuesta de todas tus solicitudes enviadas</p>
                </div>
            </a>

            <a href="#faq-section" class="opcion-card">
                <div class="imagen">
                    <img src="../assets/img/preguntas.png" alt="Preguntas Frecuentes">
                </div>
                <div class="contenido-card">
                    <h3>Preguntas Frecuentes</h3>
                    <p>Resuelve tus dudas sobre el sistema de PQRS y sus procesos</p>
                </div>
            </a>
        </div>
    </div>

    <!-- Sección FAQ -->
    <div class="seccion-faq" id="faq-section">
        <div class="cabecera-faq">
            <h2>Preguntas Frecuentes</h2>
        </div>
        <div class="contenido-faq">
            <div class="faq-item">
                <button class="faq-question">
                    <span>
                        <i class="ri-eye-line icono-pregunta"></i>
                        ¿Quién puede ver mis PQRS?
                    </span>
                    <span class="arrow">▼</span>
                </button>
                <div class="faq-answer">
                    <p>Solo tú y los administradores autorizados pueden ver el contenido de tus PQRS. La información está protegida y es completamente confidencial.</p>
                </div>
            </div>

            <div class="faq-item">
                <button class="faq-question">
                    <span>
                        <i class="ri-time-line icono-pregunta"></i>
                        ¿Cuánto tiempo tarda en resolverse una PQR?
                    </span>
                    <span class="arrow">▼</span>
                </button>
                <div class="faq-answer">
                    <p>El tiempo puede variar según la complejidad, pero generalmente se responde en un plazo de 5 a 10 días hábiles.</p>
                </div>
            </div>

            <div class="faq-item">
                <button class="faq-question">
                    <span>
                        <i class="ri-attachment-line icono-pregunta"></i>
                        ¿Puedo adjuntar documentos o imágenes?
                    </span>
                    <span class="arrow">▼</span>
                </button>
                <div class="faq-answer">
                    <p>Sí, el sistema permite adjuntar archivos PDF, imágenes y documentos relacionados para complementar tu solicitud.</p>
                </div>
            </div>

            <div class="faq-item">
                <button class="faq-question">
                    <span>
                        <i class="ri-edit-line icono-pregunta"></i>
                        ¿Puedo modificar una PQR enviada?
                    </span>
                    <span class="arrow">▼</span>
                </button>
                <div class="faq-answer">
                    <p>Sí, puedes modificar o cancelar tu PQR dentro de los primeros 20 minutos después de haberla registrado, siempre que su estado sea "pendiente".</p>
                </div>
            </div>

            <div class="faq-item">
                <button class="faq-question">
                    <span>
                        <i class="ri-notification-line icono-pregunta"></i>
                        ¿Cómo me notifican sobre la respuesta?
                    </span>
                    <span class="arrow">▼</span>
                </button>
                <div class="faq-answer">
                    <p>Recibirás una notificación por correo electrónico y también podrás ver el estado actualizado desde la plataforma en tiempo real.</p>
                </div>
            </div>
        </div>
    </div>
</body>

<?php
    require_once './Layout/footer.php'
?>
</html>