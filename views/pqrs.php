<?php
require_once './Layout/header.php'
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

<div class="pqrs-intro">
  <h2>📋PQRS</h2>

  <p>
    El módulo de <strong>PQRS</strong> facilita la comunicación entre 
    <strong>residentes, propietarios, vigilantes</strong> y la 
    <strong>administración</strong>.
  </p>

  <p>
    Aquí puedes reportar fallas, temas de seguridad o dar sugerencias de forma rápida y centralizada.
  </p>

  <p>
    ✅ Haz clic en <strong>"Crear PQR"</strong>, completa el formulario y recibirás respuesta por <strong>correo electrónico</strong> al igual que en esta plataforma.
  </p>

  <p>
    🔍 Para consultar el estado o respuesta de tus solicitudes, entra en <strong>"Mis PQRS"</strong> y revisa su avance.
  </p>
</div>


    <!-- Fondo con mensaje y opciones -->
    <div class="fondo-container">
        <div class="mensaje-container">
    <div class="contenedor-limitado">

    <!-- Primera fila: Crear PQR -->
      <div class="fila-crear">
        <div class="opcion opcion-grande" onclick="location.href='crear_pqr.php'">
          <img src="../assets/img/crear_pqr.png" alt="Crear PQR">
          <p>Crear PQR</p>
        </div>
      </div>

      <!-- Segunda fila: Estado y Preguntas -->
      <div class="fila-secundaria">
        <div class="opcion" onclick="location.href='mis_pqrs.php'">
          <img src="../assets/img/estado_pqr.png" alt="Estado de mi PQR">
          <p>Mis PQRS</p>
        </div>

        <div class="opcion" onclick="location.href='#dudas'">
          <img src="../assets/img/preguntas.png" alt="Preguntas Frecuentes">
          <p>Preguntas Frecuentes</p>
        </div>
      </div>

    </div>
  </div>
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
                📌 ¿Puedo modificar o cancelar una PQR enviada? <span class="arrow">▼</span>
            </button>
            <div class="faq-answer">
                Sí, puedes modificar o cancelar tu PQR dentro de los primeros 20 minutos después de haberla registrado, siempre y cuando su estado aún sea "pendiente". Pasado ese tiempo o si ya está en proceso, no se permiten cambios.
            </div>
        </div>

        <div class="faq-item">
            <button class="faq-question">
                📌 ¿Cómo me notifican sobre la respuesta a mi PQR? <span class="arrow">▼</span>
            </button>
            <div class="faq-answer">
                Recibirás una notificación por correo electrónico y también podrás ver el estado actualizado desde la plataforma en tiempo real.
            </div>
        </div>
    </div>
    
</body>

<?php
    require_once './Layout/footer.php'
?>
</html>