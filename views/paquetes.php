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
  <title>ZONEMAISONS - vigilante</title>
  <link rel="stylesheet" href="../../assets/Css/global.css">
   <link rel="stylesheet" href="../assets/Css/ComunicaciondeNovedades/paquetess.css">
</head>
<body>

  <main>
    <section class="principal-page">
    <h2>Paquetería</h2>

<!-- <form action="procesarFormulario.php" method="POST" class="formulario-registro"> -->
<fieldset>
  <legend>Formulario de Paquetes</legend>
  <!-- Destinatario cc -->
  
     <label>Tipo de Documento</label>
        <select class="form-control" type="text"  name="tipo_doc" placeholder="Tipo Doc" required />
            <option selected="">Seleccione el Tipo de Documento</option>
            <option value="C.C.">C.C.</option>
            <option value="Otro">Otro</option>
         </select>

         <label>Número Documento</label>
       <input type="text" class="form-control" name="numero_doc" placeholder="Número Documento" required />
 
  <!-- Destinatario -->
     <label>Destinatario</label>
      <select class="form-control" type="text" name="filtrodestinatario" id="filtrodestinatario">
        <option selected="">Seleccione un Destinatario</option>
        <option value="Juan Perez">Juan Perez</option>
        <option value="Luis Rodriguez">Luis Rodriguez</option>
        <option value="Maria Lopez">Maria Lopez</option>
        <option value="Carlos Perez">Carlos Perez</option>
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
          <input type="text" class="form-control" placeholder="Descripción del paquete" name="descripcion" id="descripcion">
          
       <!--  Estado -->
        <label>Estado</label>
        <div class="cyberpunk-checkbox-group">
             <label class="cyberpunk-checkbox-label">
             <input type="checkbox" class="cyberpunk-checkbox">
             Pendiente
             </label>
             <label class="cyberpunk-checkbox-label">
             <input type="checkbox" class="cyberpunk-checkbox">
             Entregado
             </label>
        </label>
        </div>
  </fieldset>
          
      <!-- Botones -->
      <div style="display: flex; justify-content: center; gap: 10px;">
        <button class="Enviar">Enviar</button>
        <button class="Cancelar">Cancelar</button>
      </div>

  </main>  
  <script src="../assets/js/paquetes.js"></script>

  <?php require_once "./Layout/footer.php" ?>
</body>
</html>