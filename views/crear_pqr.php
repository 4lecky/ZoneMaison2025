<?php

require_once './Layout/header.php'
?>



<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>CREAR PQR</title>
  <link rel="stylesheet" href="../assets/css/pqrs.css" />
  <script src="../assets/js/pqrs.js" defer></script>
</head>
<body>


  <!-- TÍTULO PRINCIPAL (SE MANTIENE EL ESTILO ORIGINAL) -->
<section class="titulo-principal">
  <h1>PETICIONES, QUEJAS Y RECLAMOS</h1>
</section>

<!-- CONTENIDO COMPLETO DEL CUERPO -->
<div class="formulario-container">
  <h2 class="form-title">Formulario PQRS</h2>
  <p class="form-subtitle">Por favor, diligencia la siguiente información para procesar tu solicitud.</p>
  <p class="campo-obligatorio">(*) Todos los campos son obligatorios</p>

  <form class="formulario-pqr" method="POST" action="../controller/pqrsController.php" enctype="multipart/form-data">
    <div class="campo-doble">
      <input type="text" name="nombres" placeholder="Nombres *">
    </div>
    <div class="campo-doble">
      <input type="text" name="apellidos" placeholder="Apellidos *">
    </div>
    <div class="campo-doble">
      <input type="text" name="identificacion" placeholder="Número de identificación *">
    </div>
    <div class="campo-doble">
      <input type="text" name="email" placeholder="Correo electrónico *">
    </div>
    <div class="campo-doble">
      <input type="tel" name="telefono" placeholder="Teléfono de contacto *">
    </div>

    <div class="campo-doble"> 
      <select name="tipo_pqr">
        <option value="">Selecciona el tipo de solicitud *</option>
        <option value="peticion">Petición</option>
        <option value="queja">Queja</option>
        <option value="reclamo">Reclamo</option>
        <option value="sugerencia">Sugerencia</option>
      </select>
    </div>
    <div class="campo-doble">
      <input type="text" name="asunto" placeholder="Asunto *">
    </div>
    <div class="campo-doble textarea-grande">
      <textarea name="mensaje" placeholder="Describe tu solicitud con detalle *"></textarea>
    </div>


    <div class="archivo-row">
      <label for="archivos">Documentos anexos (opcional)</label>
      <input type="file" id="archivos" name="archivos" multiple>
    </div>

    <div class="medios-respuesta">
      <p>¿Cómo deseas recibir respuesta?</p>
      <br>
      <div class="checkbox-opciones">
        <label class="checkbox-label">
          <span>Correo electrónico</span>
          <input type="checkbox" name="respuesta[]" value="correo" />
        </label>
    
        <label class="checkbox-label">
          <span>SMS</span>
          <input type="checkbox" name="respuesta[]" value="sms" />
        </label>
      </div>
    </div>
    <div class="boton-envio">
      <button type="submit">Enviar solicitud</button>
    </div>
  </form>
</div>


<!-- SECCIÓN DE TESTIMONIOS -->
<section class="testimonios">
  <h3>Lo que dicen nuestros usuarios</h3>
  <div class="testimonio">
    <p>"Me sorprendió la rapidez con la que respondieron mi solicitud. Excelente atención."</p>
    <span>– Laura G.</span>
  </div>
  <div class="testimonio">
    <p>"Muy útil este formulario. Me ayudó a expresar una sugerencia que fue escuchada."</p>
    <span>– Carlos M.</span>
  </div>
</section>

<!-- SECCIÓN ANIMADA -->
<section class="animacion-valor">
  <h3>¡Estamos aquí para ayudarte! 🤝</h3>
  <div class="ondas"></div>
</section>

</body>

<?php

    require_once './Layout/footer.php'
    ?>
</html>
