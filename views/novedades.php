<<<<<<< HEAD
  <?php
  require_once "./Layout/header.php"
  ?>

<?php
session_start();
require_once "../config/db.php"; // si estás en views/
?>


<!DOCTYPE html>
<html lang="en">
=======
 <?php
session_start();
require_once "../config/db.php";
require_once "./Layout/header.php";
?>

<?php
$mensaje = $_GET['success'] ?? '';
if ($mensaje): ?>
    <div class="alert alert-success"><?= htmlspecialchars($mensaje) ?></div>
<?php endif; ?>

<!DOCTYPE html>
<html lang="es">
>>>>>>> b749a1aeeb17e11874b55fe17b55fa3d883dc79d

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>ZONEMAISONS - Notificaciones</title>
  <link rel="stylesheet" href="../assets/Css/globals.css" />
  <link rel="stylesheet" href="../assets/Css/Layout/header.css" />
  <link rel="stylesheet" href="../assets/Css/Layout/footer.css" />
  <link rel="stylesheet" href="../assets/Css/ComunicaciondeNovedades/novedades.css" />
</head>

<body>

<?php
<<<<<<< HEAD
$stmt = $pdo->query("SELECT * FROM tbl_muro ORDER BY muro_Fecha DESC, muro_Hora DESC");
$mensajes = $stmt->fetchAll();
?>


=======
try {
    $stmt = $pdo->query("SELECT * FROM tbl_muro ORDER BY muro_Fecha DESC, muro_Hora DESC");
    $mensajes = $stmt->fetchAll();
} catch (PDOException $e) {
    $mensajes = [];
    error_log("Error al obtener mensajes del muro: " . $e->getMessage());
}
?>

>>>>>>> b749a1aeeb17e11874b55fe17b55fa3d883dc79d
  <main>
    <section class="principal-page">
      <h2>Bienvenido a ZONEMAISONS</h2>

      <!-- Mantente al tanto -->
      <div class="code-loader">
        <span>Mantente al tanto!!!</span>
      </div>

<<<<<<< HEAD
      <h3>Todo lo que pasa en tu comunidad, en un solo lugar.
      </h3>
    </section>

    <section class="second-page">
      <!-- Muro -->
      <div class="muro">
        <a href="muro.php" class="round-button add-button">
              <span>+</span>
          </a>
        <h2>Muro</h2>
        <!-- Tarjetas 1 -->
  <?php if (count($mensajes) > 0): ?>
    <?php foreach ($mensajes as $muro): ?>
      <div class="tarjeta">
        <div class="tarjeta-interna">
          <img src="../<?= htmlspecialchars($muro['muro_image']) ?>" alt="Imagen del muro">
          <div class="contenido">
            <div class="Asunto"><?= htmlspecialchars($muro['muro_Asunto']) ?>
          <section class="hora"><?=htmlspecialchars($muro['muro_Hora'])?></section>
          <section class="fecha"><?=htmlspecialchars($muro['muro_Fecha'])?> </section>
</div>
            <div class="Descripcion">
            <p class="texto-muro"><?= nl2br(htmlspecialchars($muro['muro_Descripcion'])) ?></p>
            <div style="display: flex; justify-content: right; gap: 10px;">
              <button class="animated-button btn-vermas">
                <svg viewBox="0 0 24 24" class="arr-2">
                  <path d="M16.1716 10.9999L10.8076 5.63589L12.2218 4.22168L20 11.9999L12.2218 19.778L10.8076 18.3638L16.1716 12.9999H4V10.9999H16.1716Z" />
                </svg>
                <span class="text">Ver más</span>
                <span class="circle"></span>
                <svg viewBox="0 0 24 24" class="arr-1">
                  <path d="M16.1716 10.9999L10.8076 5.63589L12.2218 4.22168L20 11.9999L12.2218 19.778L10.8076 18.3638L16.1716 12.9999H4V10.9999H16.1716Z" />
                </svg>
              </button>
              <a href="muro.php" class="round-button edit-button"><span>✎</span></a>
            </div>
          </div>
         </div>
        </div>
       
       </div>
      </div>
    <?php endforeach; ?>
  <?php else: ?>
    <p>No hay publicaciones en el muro aún.</p>
  <?php endif; ?>
</div>

        <!-- Tarjeta 2
        <div class="tarjeta">
          <div class="tarjeta-interna">
            <img src="../assets/img/pay.png" alt="Pago de cuotas">
            <div class="contenido">
              <div class="Asunto">Recordatorio de Pago de Cuotas</div>
              <div class="Descripcion">Estimados residentes, este es un recordatorio de que la fecha límite...</div>

              <div style="display: flex; justify-content: right; gap: 10px;">

                Botón moderno -->
                <!-- <button class="animated-button">
                  <svg viewBox="0 0 24 24" class="arr-2">
                    <path d="M16.1716 10.9999L10.8076 5.63589L12.2218 4.22168L20 11.9999L12.2218 19.778L10.8076 18.3638L16.1716 12.9999H4V10.9999H16.1716Z" />
                  </svg>
                  <span class="text">Ver más</span>
                  <span class="circle"></span>
                  <svg viewBox="0 0 24 24" class="arr-1">
                    <path d="M16.1716 10.9999L10.8076 5.63589L12.2218 4.22168L20 11.9999L12.2218 19.778L10.8076 18.3638L16.1716 12.9999H4V10.9999H16.1716Z" />
                  </svg>
                </button>
                <a href="muro.php" class="round-button edit-button"><span>✎</span></a>
              </div>
            </div>
          </div>
        </div>
      </div> -->

      <!-- Paquetería -->
      <section class="paqueteria">
        <h2>Paquetería</h2>
=======
      <h3>Todo lo que pasa en tu comunidad, en un solo lugar.</h3>
    </section>

    <section class="second-page">
   <!-- Muro -->
<div class="muro">
  <div class="muro-header">
    <h2>Muro</h2>
    <a href="muro.php" class="round-button add-button">
      <span>+</span>
    </a>
  </div>
        
        <?php if (count($mensajes) > 0): ?>
          <?php foreach ($mensajes as $muro): ?>
            <div class="tarjeta">
              <div class="tarjeta-interna">
                <?php if (!empty($muro['muro_image'])): ?>
                  <img src="../<?= htmlspecialchars($muro['muro_image']) ?>" alt="Imagen del muro">
                <?php endif; ?>
                <div class="contenido">
                  <div class="Asunto">
                    <?= htmlspecialchars($muro['muro_Asunto']) ?>
                    <section class="hora"><?= htmlspecialchars($muro['muro_Hora']) ?></section>
                    <section class="fecha"><?= htmlspecialchars($muro['muro_Fecha']) ?></section>
                  </div>
                  <div class="Descripcion">
                    <p class="texto-muro"><?= nl2br(htmlspecialchars($muro['muro_Descripcion'])) ?></p>
                    <div style="display: flex; justify-content: right; gap: 10px;">
                      <button class="animated-button btn-vermas">
                        <svg viewBox="0 0 24 24" class="arr-2">
                          <path d="M16.1716 10.9999L10.8076 5.63589L12.2218 4.22168L20 11.9999L12.2218 19.778L10.8076 18.3638L16.1716 12.9999H4V10.9999H16.1716Z" />
                        </svg>
                        <span class="text">Ver más</span>
                        <span class="circle"></span>
                        <svg viewBox="0 0 24 24" class="arr-1">
                          <path d="M16.1716 10.9999L10.8076 5.63589L12.2218 4.22168L20 11.9999L12.2218 19.778L10.8076 18.3638L16.1716 12.9999H4V10.9999H16.1716Z" />
                        </svg>
                      </button>
                    <a href="editar_publicacion.php?id=<?= $muro['muro_Id'] ?>" class="round-button edit-button"><span>✎</span></a>                    </div>
                  </div>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        <?php else: ?>
          <p>No hay publicaciones en el muro aún.</p>
        <?php endif; ?>
      </div>

      <!-- Paquetería -->
      <section class="paqueteria">
        <div class="paqueteria-header">
        <h2>Paquetería</h2>
        <a href="paquetes.php" class="round-button add-button">
          <span>+</span>
        </a>
        </div>
>>>>>>> b749a1aeeb17e11874b55fe17b55fa3d883dc79d

        <div class="subtitulo">Recibido</div>

        <div class="tarjeta">
          <div class="tarjeta-interna">
            <div class="paquete-icono">📦</div>
            <div class="contenido">
              <div class="Asunto">Paquete grande de SHEIN</div>
              <div class="Descripcion">Caja de cartón</div>
            </div>
          </div>
        </div>

<<<<<<< HEAD
      <div class="subtitulo">Pendiente</div>
      
=======
        <div class="subtitulo">Pendiente</div>
        
>>>>>>> b749a1aeeb17e11874b55fe17b55fa3d883dc79d
        <div class="tarjeta">
          <div class="tarjeta-interna">
            <div class="paquete-icono">📦</div>
            <div class="contenido">
              <div class="Asunto">Paquete mediano de SHEIN</div>
              <div class="Descripcion">Bolsa Blanca</div>
            </div>
          </div>
        </div>
      </section>
    </section>
<<<<<<< HEAD
</main>
    <script src="../assets/Js/novedades.js"></script>

    <?php 
    require_once "./Layout/footer.php"
    ?>
=======
  </main>
  
  <script src="../assets/Js/novedades.js"></script>

  <?php 
  require_once "./Layout/footer.php";
  ?>
>>>>>>> b749a1aeeb17e11874b55fe17b55fa3d883dc79d

</body>

</html>