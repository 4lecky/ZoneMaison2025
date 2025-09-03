<?php
session_start();
if (!isset($_SESSION['usuario'])) {
  header("Location: login.php");
  exit();
}
$pdo = require_once "../config/db.php";
require_once "./Layout/header.php";
?>

<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>ZONEMAISONS - admin</title>
  <link rel="stylesheet" href="../assets/Css/globals.css" />
  <link rel="stylesheet" href="../assets/Css/Layout/header.css" />
  <link rel="stylesheet" href="../assets/Css/Layout/footer.css" />
  <link rel="stylesheet" href="../assets/Css/ComunicaciondeNovedades/muro.css">
</head>

<body>

  <main>
    <section class="principal-page">
      <h2>Muro</h2>

      <!--  Aquí comienza el formulario -->
      <form action="../controller/muroController.php" method="POST" enctype="multipart/form-data" class="muro">
        <fieldset>
          <legend>Formulario de Muro</legend>

          <!-- Destinatario -->
          <label for="destinatario">Selecciona el rol destinatario<span class="asterisco">*</span></label>
          <?php
          // Los roles que queremos permitir
          $rolesPermitidos = ['Administrador', 'Residente', 'Propietario', 'Vigilante'];

          // Consulta para obtener roles activos
          $query = "SELECT DISTINCT usu_rol
                    FROM tbl_usuario 
                    WHERE usu_rol IN ('Administrador', 'Residente', 'Propietario', 'Vigilante') AND usu_estado = 'Activo'
                    ORDER BY usu_rol";

          $stmt = $pdo->prepare($query);
          $stmt->execute();
          $roles = $stmt->fetchAll(PDO::FETCH_ASSOC);
          ?>

          <select class="form-control" id="destinatario" name="destinatario" required>
            <option value="" selected disabled>Seleccione un Rol *</option>
            <?php foreach ($roles as $role): ?>
              <option value="<?= htmlspecialchars($role['usu_rol']) ?>">
                <?= htmlspecialchars($role['usu_rol']) ?>
              </option>
            <?php endforeach; ?>
          </select>

          <!-- Asunto -->
          <label>Asunto *</label>
          <input type="text" class="form-control" id="asunto" name="asunto" placeholder="Asunto" required />

          <!-- Campos para fecha y hora del evento -->
          <div class="fecha-hora-container" style="display: flex; gap: 15px; margin: 15px 0;">
            <div style="flex: 1;">
              <label for="fechaEvento">Fecha del evento<span class="asterisco">*</span></label>
              <input type="date" class="form-control" id="fechaEvento" name="fechaEvento" required>
            </div>
            <div style="flex: 1;">
              <label for="horaEvento">Hora del evento<span class="asterisco">*</span></label>
              <input type="time" class="form-control" id="horaEvento" name="horaEvento" required>
            </div>
          </div>

          <!-- Botón para insertar fecha y hora -->
          <button type="button" class="btn btn-primary" onclick="insertarFechaHora()" style="margin-bottom: 10px;" required>
            Insertar Fecha y Hora en el Texto *
          </button>

          <!-- Imagen -->
          <label></i>Adjuntar Imágenes<span class="asterisco">*</span></label>
          <input type="file" name="zone-images" accept="image/*" required />

          <!-- Descripción -->
          <label for="descripcion">Descripción<span class="asterisco">*</span></label>
          <textarea class="form-control" rows="10" placeholder="Descripción..." id="descripcion" name="descripcion" required>

Se llevara a cabo

Para cualquier pregunta o inconveniente, por favor, contacten a la administración.
Agradecemos su comprensión y cooperación.
Atentamente, [Nombre del Responsable].
Administración del Conjunto Residencial [Nombre conjunto residencial].</textarea>

          <!-- Botones -->
          <div style="display: flex; justify-content: center; gap: 10px; margin-top: 20px;">
            <button type="submit" class="enviar">Enviar</button>
            <button type="button" class="Cancelar" onclick="window.location.href='novedades.php';">Cancelar</button>
          </div>
        </fieldset>
      </form>

    </section>
  </main>

  <?php require_once "./Layout/footer.php"; ?>

  <script src="../assets/Js/muro.js"></script>

  <script>
    // Establecer fecha y hora actuales por defecto al cargar la página
    document.addEventListener('DOMContentLoaded', function() {
      const ahora = new Date();

      // Establecer fecha actual
      document.getElementById('fechaEvento').value = ahora.toISOString().split('T')[0];

      // Establecer hora actual
      document.getElementById('horaEvento').value = ahora.toTimeString().slice(0, 5);
    });

    function insertarFechaHora() {
      const fecha = document.getElementById('fechaEvento').value;
      const hora = document.getElementById('horaEvento').value;
      const textarea = document.getElementById('descripcion');

      if (!fecha || !hora) {
        alert('Por favor, selecciona una fecha y una hora para el evento.');
        return;
      }

      try {
        // Convertir fecha a formato legible en español
        const fechaObj = new Date(fecha + 'T00:00:00');
        const fechaTexto = fechaObj.toLocaleDateString('es-CO', {
          weekday: 'long',
          year: 'numeric',
          month: 'long',
          day: 'numeric'
        });

        // Convertir hora a formato 12 horas
        const [horas, minutos] = hora.split(':');
        const fechaHora = new Date();
        fechaHora.setHours(parseInt(horas), parseInt(minutos));
        const horaTexto = fechaHora.toLocaleTimeString('es-CO', {
          hour: 'numeric',
          minute: '2-digit',
          hour12: true
        });

        // Crear el texto con formato deseado
        const textoFechaHora = `Estimados residentes, Les informamos que el día ${fechaTexto} a las ${horaTexto} `;

        // Insertar al inicio del textarea
        textarea.value = textoFechaHora + textarea.value;

        // Posicionar cursor al final del texto insertado
        const nuevaPos = textoFechaHora.length;
        textarea.setSelectionRange(nuevaPos, nuevaPos);
        textarea.focus();

      } catch (error) {
        alert('Error al formatear la fecha y hora. Por favor, verifica que sean válidas.');
        console.error('Error:', error);
      }
    }

    function resetearFormulario() {
      // Restablecer el textarea al texto original
      const textarea = document.getElementById('descripcion');
      textarea.value = `Para cualquier pregunta o inconveniente, por favor, contacten a la administración.
Agradecemos su comprensión y cooperación.
Atentamente, [Nombre del Responsable].
Administración del Conjunto Residencial [Nombre conjunto residencial].`;

      // Restablecer fecha y hora a valores actuales
      const ahora = new Date();
      document.getElementById('fechaEvento').value = ahora.toISOString().split('T')[0];
      document.getElementById('horaEvento').value = ahora.toTimeString().slice(0, 5);
    }
  </script>

</body>

</html>