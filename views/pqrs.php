<?php
session_start();
require_once './Layout/header.php'
?>

<?php if (isset($_GET['editado'])): ?>
  <div class="alerta-exito">¡Registro editado exitosamente!</div>
<?php elseif (isset($_GET['eliminado'])): ?>
  <div class="alerta-exito">¡Registro eliminado exitosamente!</div>
<?php elseif (isset($_GET['error']) && $_GET['error'] === 'eliminando'): ?>
  <div class="alerta-error">Ocurrió un error al intentar eliminar el registro.</div>
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
  <h2>📋 Módulo de PQRS</h2>

  <p>
    Nuestro sistema de PQRS (Peticiones, Quejas, Reclamos y Sugerencias) está diseñado para fortalecer la comunicación entre 
    <strong>residentes, propietarios, vigilantes</strong> y la 
    <strong>administración del conjunto</strong>.
  </p>

  <p>
    Desde este módulo puedes reportar cualquier situación que requiera atención: fallos en zonas comunes, reportes de seguridad, o sugerencias para mejorar la convivencia. Todo desde un mismo lugar, de forma rápida, ordenada y centralizada.
  </p>

  <p>
    ✅ Para hacerlo, simplemente haz clic en <strong>“Crear PQR”</strong>, llena el formulario con tu información y describe el caso. Podrás elegir cómo deseas recibir actualizaciones: por <strong>correo electrónico, mensaje SMS o ambos</strong>.
  </p>

  <p>
    🔍 Si ya enviaste una solicitud y deseas consultar su avance, haz clic en <strong>“Estado de mi PQR”</strong>. Solo necesitas tu número de cédula, y te mostraremos en qué etapa se encuentra tu PQR. Además, recibirás notificaciones en el medio que seleccionaste durante el registro.
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
        <div class="opcion" id="openModal">
          <img src="../assets/img/estado_pqr.png" alt="Estado de mi PQR">
          <p>Estado de mi PQR</p>
        </div>

        <div class="opcion" onclick="location.href='#dudas'">
          <img src="../assets/img/preguntas.png" alt="Preguntas Frecuentes">
          <p>Preguntas Frecuentes</p>
        </div>
      </div>

    </div>
  </div>
</div>




<!-- Modal para consultar PQR por cédula -->
<div id="modal" class="modal" style="display: none;">
    <div class="modal-content" style="width: 90%; max-height: 80vh; overflow-y: auto;">
        <span class="close">&times;</span>
        <h2>Consultar Estado de PQR</h2>
        <br>
        <form id="pqr-form">
            <label for="cedula">Número de cédula:</label>
            <input type="text" id="cedula" name="cedula" required>
            <br><br>
            <button type="submit" class="btn">Consultar</button>
        </form>

        <div id="resultado-pqr">
            <!-- Aquí se mostrará el resultado -->
        </div>
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
                📌 ¿Puedo modificar o cancelar una PQR enviada? <span class="arrow">▼</span>
            </button>
            <div class="faq-answer">
                Sí, puedes modificar o cancelar tu PQR dentro de los primeros 20 minutos después de haberla registrado, siempre y cuando su estado aún sea “pendiente”. Pasado ese tiempo o si ya está en proceso, no se permiten cambios.
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
    
    

</body>

<?php

    require_once './Layout/footer.php'
    ?>
</html>

