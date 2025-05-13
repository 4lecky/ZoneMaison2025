<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>ZONEMAISONS - vigilante</title>
   <link rel="stylesheet" href="../assets/Css/paquetes.css">
</head>
<body>

  <header>
    <div class="logo-container">
      <img src="../assets/img/logoZM.png" alt="Logo de ZONEMAISONS" class="logo" />
      <div class="title-container">
        <h1>ZONEMAISONS</h1>
        <div class="underline"></div>
      </div>
    </div>
    <nav class="menu-button">
      <div class="lines">&#9776;</div>
    </nav>
  </header>

  <nav class="main-nav">
    <ul>
      <li><a href="index.html">Inicio</a></li>
      <li><a href="index.html" class="active">Notificaciones</a></li>
      <li><a href="#">Reservas</a></li>
      <li><a href="#">Pqrs</a></li>
    </ul>
  </nav>

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
</body>
</html>