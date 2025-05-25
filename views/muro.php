<?php
session_start();
// if (!isset($_SESSION['user_id'])) {
//     header("Location: login.php");
//     exit();
// }

require_once "./Layout/header.php"
?>


<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>ZONEMAISONS - admin</title>
  <link rel="stylesheet" href="../../assets/Css/global.css">
  <link rel="stylesheet" href="../assets/Css/ComunicaciondeNovedades/muro.css">
</head>
<body>

  <main>
    <section class="principal-page">
    <h2>Muro</h2>

<!-- <form action="procesarFormulario.php" method="POST" class="formulario-registro"> -->
<fieldset>
  <legend>Formulario de Muro</legend>
  <!-- Destinatario -->
    <label>Destinatario</label>
      <select class="form-control" type="text" name="filtrodestinatario" id="filtrodestinatario">
        <option selected>Seleccione un Destinatario</option>
        <option value="p1">Residentes</option>
        <option value="p2">Administradores</option>
        <option value="p3">Vigilantes</option>
        <option value="p4">Todos</option>
      </select>

      <!-- Asunto -->
      <label>Asunto</label>
       <input type="text" class="form-control" placeholder="Asunto" id="Asunto"/>

      <!-- Fecha -->
      <label>Fecha</label>
       <input type="date" class="form-control" id="Fecha"/>

      <!-- Hora -->
      <label>Hora</label>
       <input type="time" class="form-control" id="Hora"/>

      <!-- Imagen -->
       <div class="imagen">
                    <label><i class="fas fa-images"></i> Imágenes</label>
                    <div class="image-upload-container">
                        <div class="image-upload-box">
                            <input type="file" id="zone-images" name="zone-images" accept="image/*" multiple class="hidden-upload">
                            <label for="zone-images" class="upload-label">
                                <i class="fas fa-cloud-upload-alt"></i>
                                <span>Arrastra imágenes aquí o haz clic para seleccionar</span>
                                <span class="upload-hint">Máximo 5 imágenes (JPEG/PNG, 5MB max cada una)</span>
                            </label>
                        </div>
                        <div id="image-preview" class="image-preview-grid">
                            <!-- Las imágenes seleccionadas aparecerán aquí -->
                        </div>
                    </div>
                </div>

      <!-- Descripción -->
          <label>Descripción</label>
      <textarea class="form-control" rows="10" placeholder="Descripción..." name="descripcion" id="descripcion">
Estimados residentes, Les informamos 
Para cualquier pregunta o inconveniente, por favor, contacten a la administración.

Agradecemos su comprensión y cooperación.
Atentamente, [Nombre del Responsable].
Administración del Conjunto Residencial [Nombre conjunto residencial].
          </textarea>
          </fieldset>

      <!-- Botones -->
      <div style="display: flex; justify-content: center; gap: 10px;">
        <button class="Enviar">Enviar</button>
        <button class="Cancelar">Cancelar</button>
      </div>
</fieldset>       
  </main>

    <?php require_once "./Layout/footer.php" ?>
</body>
</html>

  <script src="../assets/js/muro.js"></script>
</body>
</html>
