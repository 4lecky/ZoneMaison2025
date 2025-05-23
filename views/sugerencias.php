<?php

require_once './Layout/header.php'
?>


<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Sugerencias</title>
  <link rel="stylesheet" href="../assets/css/pqrs.css" />
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;700&display=swap" rel="stylesheet" />
  <script src="..assets/js/script.js" defer></script>
</head>
<body>

  <div class="sugerencias-container">
    <!-- Sección de bienvenida -->
    <header class="header">
      <h1>¿Tienes una sugerencia?</h1>
    </header>

    <!-- Imagen decorativa -->
    <div class="ilustracion">
      <img src="https://cdn-icons-png.flaticon.com/512/4140/4140037.png" alt="Ilustración sugerencias">
    </div>

    <!-- Frase motivadora -->
    <section class="frase-motivadora">
      <blockquote>
        “La mejora continua es mejor que la perfección postergada.” – Mark Twain
      </blockquote>
    </section>

    <!-- Formulario -->
    <section class="formulario-sugerencias">
      <h2>Formulario de sugerencias</h2>
      <form id="formularioSugerencias">
        <input type="text" name="nombre" placeholder="Tu nombre completo *" />
        <input type="text" name="email" placeholder="Correo electrónico *" />
        <input type="text" name="asunto" placeholder="Asunto de la sugerencia *" />
        <textarea name="mensaje" placeholder="Cuéntanos tu sugerencia con el mayor detalle posible *"></textarea>
        <button type="submit">Enviar sugerencia</button>
      </form>
    </section>
    


    <!-- Sección de testimonios -->
    <section class="testimonios">
      <h3>Lo que otros han dicho</h3>
      <div class="cards-testimonio">
        <div class="card">
          <p>“Gracias a sus sugerencias, implementamos mejoras reales en el servicio.”</p>
          <span>— Administrador General</span>
        </div>
        <div class="card">
          <p>“Sentí que realmente me escucharon. ¡Excelente atención!”</p>
          <span>— Usuario satisfecho</span>
        </div>
        <div class="card">
          <p>“El proceso es fácil, rápido y eficaz. Recomendado.”</p>
          <span>— Vecino del conjunto</span>
        </div>
      </div>
    </section>

<!-- SECCIÓN ANIMADA -->
<section class="animacion-valor">
    <h3>¡Estamos aquí para ayudarte! 🤝</h3>
    <div class="ondas"></div>
  </section>
  </div>
</body>

<?php

        require_once './Layout/footer.php'
    ?>
</html>



