<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>ZONEMAISONS - admin</title>
  <link rel="stylesheet" href="../assets/Css/muro.css">
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
    <h2>Muro</h2>

    <div class="form-container">
      <!-- Destinatario -->
      <select class="form-select" id="filtrodestinatario">
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
      </div>
    </div>
  </main>

  <script src="../assets/js/muro.js"></script>
</body>
</html>
