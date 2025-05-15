<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ZONEMAISONS - Notificaciones</title>
  <link rel="stylesheet" href="../../assets/Css/globals.css">
  <link rel="stylesheet" href="../assets/Css/ComunicaciondeNovedades/novedades.css">
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
 
<main class="principal-page">

    <h2>Bienvenido a ZONEMAISONS</h2>

<div class="container-principal">   
    <!-- Mantente al tanto -->
    <div class="code-loader">
        <span>Mantente al tanto!!!</span>

    </div>
<br>
     <h4>Todo lo que pasa en tu comunidad, en un solo lugar.
    </h4>
</section>
</div>
<!--muro-->
  <div class="container-muro">
    <section class="muro">
        <h3>Muro</h3>
        <div class="tarjeta">
            <img src="../assets/img/areasverdes.png" alt="Mantenimiento de áreas verdes">
            <div class="contenido">
                <h4>Próximamente se iniciará el mantenimiento de las áreas verdes del conjunto</h4>
                <p>Estimados residentes, les informamos que el próximo 15 de septiembre...</p>

<!-- Botón moderno -->
    <button class="animated-button">
      <svg viewBox="0 0 24 24" class="arr-2" xmlns="http://www.w3.org/2000/svg">
        <path d="M16.1716 10.9999L10.8076 5.63589L12.2218 4.22168L20 11.9999L12.2218 19.778L10.8076 18.3638L16.1716 12.9999H4V10.9999H16.1716Z"/>
      </svg>
      <span class="text">Ver más</span>
      <span class="circle"></span>
      <svg viewBox="0 0 24 24" class="arr-1" xmlns="http://www.w3.org/2000/svg">
        <path d="M16.1716 10.9999L10.8076 5.63589L12.2218 4.22168L20 11.9999L12.2218 19.778L10.8076 18.3638L16.1716 12.9999H4V10.9999H16.1716Z"/>
      </svg>
    </button>

  </div>
</div>

        <div class="tarjeta">
            <img src="../assets/img/pay.png" alt="Pago de cuotas">
            <div class="contenido">
                <h4>Recordatorio de Pago de Cuotas</h4>
                <p>Estimados residentes, este es un recordatorio de que la fecha límite...</p>
                
          
    <!-- Botón moderno -->
    <button class="animated-button">
      <svg viewBox="0 0 24 24" class="arr-2" xmlns="http://www.w3.org/2000/svg">
        <path d="M16.1716 10.9999L10.8076 5.63589L12.2218 4.22168L20 11.9999L12.2218 19.778L10.8076 18.3638L16.1716 12.9999H4V10.9999H16.1716Z"/>
      </svg>
      <span class="text">Ver más</span>
      <span class="circle"></span>
      <svg viewBox="0 0 24 24" class="arr-1" xmlns="http://www.w3.org/2000/svg">
        <path d="M16.1716 10.9999L10.8076 5.63589L12.2218 4.22168L20 11.9999L12.2218 19.778L10.8076 18.3638L16.1716 12.9999H4V10.9999H16.1716Z"/>
      </svg>
    </button>

  </div>
</div>
</section>

    <!--paqueteria-->
    <div class="Paqueteria-container">
      <section class="paqueteria">
          <h3>Paquetería</h3>

    <div class="seccion">
      <div class="subtitulo">Recibido</div>
      <div class="paquete">
        <div class="paquete-icono">📦</div>
        <div class="paquete-info">
          <div class="paquete-titulo">Paquete grande de SHEIN</div>
          <div class="paquete-detalle">Caja de carton</div>
        </div>
      </div>
    </div>

    <div class="seccion">
      <div class="subtitulo">Pendiente</div>
      <div class="paquete">
        <div class="paquete-icono">📦</div>
        <div class="paquete-info">
          <div class="paquete-titulo">Paquete mediano de SHEIN</div>
          <div class="paquete-detalle">Bolsa Blanca</div>
        </div>
      </div>
    </div>
  </div>
</main>

<?php require_once "./Layout/footer.php" ?>

</body>
</html>
