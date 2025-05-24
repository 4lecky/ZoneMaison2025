<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ZONEMAISONS - Notificaciones</title>
    <link rel="stylesheet" href="../assets/css/globals.css" />
    <link rel="stylesheet" href="../assets/Css/ComunicaciondeNovedades/novedades.css" />
</head>
<body>

<?php
session_start();
// if (!isset($_SESSION['user_id'])) {
//     header("Location: login.php");
//     exit();
// }

require_once "./Layout/header.php"
?>
 
<main>
    <section class="principal-page">
      <h2>Bienvenido a ZONEMAISONS</h2>
  
       <!-- Mantente al tanto -->
      <div class="code-loader">
        <span>Mantente al tanto!!!</span>
      </div>

      <h3>Todo lo que pasa en tu comunidad, en un solo lugar.
      </h3>
    </section>

 <section class="second-page">
<!-- Muro -->
  <div class="muro">
    <h2>Muro</h2>

    <!-- Tarjeta 1 -->
    <div class="tarjeta">
      <div class="tarjeta-interna">
        <img src="../assets/img/areasverdes.png" alt="Mantenimiento de áreas verdes">
        <div class="contenido">
          <div class="Asunto">Próximamente se iniciará el mantenimiento de las áreas verdes del conjunto</div>
          <div class="Descripcion">Estimados residentes, les informamos que el próximo 15 de septiembre...</div>

          <div style="display: flex; justify-content: right; gap: 10px;">

            <!-- Botón moderno -->
            <button class="animated-button">
              <svg viewBox="0 0 24 24" class="arr-2">
                <path d="M16.1716 10.9999L10.8076 5.63589L12.2218 4.22168L20 11.9999L12.2218 19.778L10.8076 18.3638L16.1716 12.9999H4V10.9999H16.1716Z"/>
              </svg>
              <span class="text">Ver más</span>
              <span class="circle"></span>
              <svg viewBox="0 0 24 24" class="arr-1">
                <path d="M16.1716 10.9999L10.8076 5.63589L12.2218 4.22168L20 11.9999L12.2218 19.778L10.8076 18.3638L16.1716 12.9999H4V10.9999H16.1716Z"/>
              </svg>
            </button>
            <a href="muro.php" class="round-button edit-button"><span>✎</span></a>
          </div>
        </div>
      </div>
    </div>

    <!-- Tarjeta 2 -->
    <div class="tarjeta">
      <div class="tarjeta-interna">
        <img src="../assets/img/pay.png" alt="Pago de cuotas">
        <div class="contenido">
          <div class="Asunto">Recordatorio de Pago de Cuotas</div>
          <div class="Descripcion">Estimados residentes, este es un recordatorio de que la fecha límite...</div>

          <div style="display: flex; justify-content: right; gap: 10px;">

            <!-- Botón moderno -->
            <button class="animated-button">
              <svg viewBox="0 0 24 24" class="arr-2">
                <path d="M16.1716 10.9999L10.8076 5.63589L12.2218 4.22168L20 11.9999L12.2218 19.778L10.8076 18.3638L16.1716 12.9999H4V10.9999H16.1716Z"/>
              </svg>
              <span class="text">Ver más</span>
              <span class="circle"></span>
              <svg viewBox="0 0 24 24" class="arr-1">
                <path d="M16.1716 10.9999L10.8076 5.63589L12.2218 4.22168L20 11.9999L12.2218 19.778L10.8076 18.3638L16.1716 12.9999H4V10.9999H16.1716Z"/>
              </svg>
            </button>
            <a href="muro.php" class="round-button edit-button"><span>✎</span></a>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Paquetería -->
  <section class="paqueteria">
    <h2>Paquetería</h2>

    <div class="tarjeta">
      <div class="tarjeta-interna">
        <div class="paquete-icono">📦</div>
        <div class="contenido">
          <div class="subtitulo">Recibido</div>
          <div class="Asunto">Paquete grande de SHEIN</div>
          <div class="Descripcion">Caja de cartón</div>
        </div>
      </div>
    </div>

    <div class="tarjeta">
      <div class="tarjeta-interna">
        <div class="paquete-icono">📦</div>
        <div class="contenido">
          <div class="subtitulo">Pendiente</div>
          <div class="Asunto">Paquete mediano de SHEIN</div>
          <div class="Descripcion">Bolsa Blanca</div>
        </div>
      </div>
    </div>
  </section>
</section>
</main

<?php require_once "./Layout/footer.php" 
?>

</body>
</html>
