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
   <link rel="stylesheet" href="../assets/Css/ComunicaciondeNovedades/paquetes.css">
</head>
<body>

  <main>
    <h2>Paqueteria</h2>

    <div class="form-container">
      <select class="form-select"  id="filtrodestinatario">
        <option selected="">Seleccione un Destinatario</option>
        <option value="Juan Perez">Juan Perez</option>
        <option value="Luis Rodriguez">Luis Rodriguez</option>
        <option value="Maria Lopez">Maria Lopez</option>
        <option value="Carlos Perez">Carlos Perez</option>
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
<input type="text" class="form-control" placeholder="Descripción..." id="Descripcion"/>

      <!-- Botones -->
      <div style="display: flex; justify-content: center; gap: 10px;">
        <button class="Enviar">Enviar</button>
        <button class="Cancelar">Cancelar</button>
      </div>
    </div>
  </main>  
  <script src="../assets/js/paquetes.js"></script>

  <?php require_once "./Layout/footer.php" ?>
</body>
</html>