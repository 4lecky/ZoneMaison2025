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
  <link rel="stylesheet" href="../assets/Css/globals.css" />
  <link rel="stylesheet" href="../assets/Css/Layout/header.css" />
  <link rel="stylesheet" href="../assets/Css/Layout/footer.css" />
  <!-- iconos (RemixIcon+) -->
  <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
  
  <script src="../assets/js/pqrs.js" defer></script>
</head>

<body>


  <!-- TÍTULO PRINCIPAL (SE MANTIENE EL ESTILO ORIGINAL) -->
  <section class="titulo-principal">
    <h1>PETICIONES, QUEJAS Y RECLAMOS</h1>
  </section>

  <!-- CONTENIDO DEL CUERPO -->
  <div class="principal-page">
    <h2>Formulario PQRS</h2>
    <p class="form-subtitle">Por favor, diligencia la siguiente información para procesar tu solicitud.</p>
    <p class="campo-obligatorio">(*) Todos los campos son obligatorios</p>
  <form method="POST" action="../controller/pqrsController.php" enctype="multipart/form-data" id="formPQRS">
    <div id="mensaje-exito">
      Tu solicitud fue registrada correctamente. ¡Gracias por comunicarte con nosotros!
    </div>


    <!-- Información Personal -->
    <fieldset>
      <legend>Información Personal</legend>

      <div class="input-group">
        <div class="input-box">
          <label for="nombres">Nombres</label>
          <input type="text" name="nombres" id="nombres" class="form-control" placeholder="Nombres *">
        </div>
        <div class="input-box">
          <label for="apellidos">Apellidos</label>
          <input type="text" name="apellidos" id="apellidos" class="form-control" placeholder="Apellidos *">
        </div>
      </div>

      <div class="input-group">
        <div class="input-box">
          <label for="identificacion">Número de Identificación</label>
          <input type="text" name="identificacion" id="identificacion" class="form-control" placeholder="Número de identificación *">
        </div>
        <div class="input-box">
          <label for="email">Correo Electrónico</label>
          <input type="text" name="email" id="email" class="form-control" placeholder="Correo electrónico *">
        </div>
      </div>

      <div class="input-group">
        <div class="input-box">
          <label for="telefono">Teléfono de Contacto</label>
          <input type="tel" name="telefono" id="telefono" class="form-control" placeholder="Teléfono de contacto *">
        </div>
      </div>
    </fieldset>

    <!-- Detalles de la Solicitud -->
    <fieldset>
      <legend>Detalles de la Solicitud</legend>

      <div class="input-group">
        <div class="input-box">
          <label for="tipo_pqr">Tipo de Solicitud</label>
          <select name="tipo_pqr" id="tipo_pqr" class="form-control">
            <option value="" disabled selected>Selecciona el tipo de solicitud *</option>
            <option value="peticion">Petición</option>
            <option value="queja">Queja</option>
            <option value="reclamo">Reclamo</option>
            <option value="sugerencia">Sugerencia</option>
          </select>
        </div>
        <div class="input-box">
          <label for="asunto">Asunto</label>
          <input type="text" name="asunto" id="asunto" class="form-control" placeholder="Asunto *">
        </div>
      </div>

      <div class="input-group">
        <div class="input-box textarea-grande">
          <label for="mensaje">Descripción Detallada</label>
          <textarea name="mensaje" id="mensaje" class="form-control" placeholder="Describe tu solicitud con detalle *"></textarea>
        </div>
      </div>

      <div class="archivo-section">
        <label for="archivos">Documentos Anexos (opcional)</label>
        <input type="file" name="archivos" id="archivos" multiple>
      </div>

      <div class="medios-respuesta">
        <p>¿Cómo deseas recibir respuesta?</p>
        <div class="checkbox-opciones">
          <label class="checkbox-label">
            <input type="checkbox" name="respuesta[]" value="correo">
            <span>Correo electrónico</span>
          </label>
          <label class="checkbox-label">
            <input type="checkbox" name="respuesta[]" value="sms">
            <span>SMS</span>
          </label>
        </div>
      </div>
    </fieldset>

    <!-- Botón de envío -->
    <div class="boton-envio">
      <button type="submit">Enviar Solicitud</button>
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