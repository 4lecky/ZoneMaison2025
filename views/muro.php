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
  <link rel="stylesheet" href="../assets/Css/ComunicaciondeNovedades/muro.css">
</head>
<body>

  <main class=first-container>
    <h2>Muro</h2>

      <!-- Destinatario -->
      <select class="form-control" id="filtrodestinatario">
        <option selected>Seleccione un Destinatario</option>
        <option value="p1">Residentes</option>
        <option value="p2">Administradores</option>
        <option value="p3">Vigilantes</option>
        <option value="p4">Todos</option>
      </select>

<!-- Asunto -->
<input type="text" class="form-control" placeholder="Asunto" id="Asunto"/>

      <!-- Fecha -->
      <input type="date" class="form-control" id="Fecha"/>

      <!-- Hora -->
      <input type="time" class="form-control" id="Hora"/>

      <!-- Imagen -->
      <div class="imagen">
        <label for="archivo" style="cursor: pointer;">
          <img src="https://img.icons8.com/ios-glyphs/30/image.png" alt="Icono Imagen" />
        </label>
        <input type="file" id="archivo" name="archivo" />
      </div>

<!-- Descripción -->
<textarea class="form-control" rows="10" placeholder="Descripción..." id="Descripcion">
    Estimados residentes, Les informamos 
    Para cualquier pregunta o inconveniente, por favor, contacten a la administración.
    
    Agradecemos su comprensión y cooperación.
    Atentamente,[Nombre del Responsable].
    Administración del Conjunto Residencial [Nombre conjunto residencial].
    </textarea>
      <!-- Botones -->
      <div style="display: flex; justify-content: center; gap: 10px;">
        <button class="Enviar">Enviar</button>
        <button class="Cancelar">Cancelar</button>
        
  </main>

    <?php require_once "./Layout/footer.php" ?>
</body>
</html>

  <script src="../assets/js/muro.js"></script>
</body>
</html>
