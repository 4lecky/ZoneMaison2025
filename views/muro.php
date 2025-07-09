<?php
session_start();
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
          <label for="destinatario">Selecciona el rol destinatario</label>
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
            <option value="" selected disabled>Seleccione un Rol</option>
            <?php foreach ($roles as $role): ?>
              <option value="<?= htmlspecialchars($role['usu_rol']) ?>">
                <?= htmlspecialchars($role['usu_rol']) ?>
              </option>
            <?php endforeach; ?>
          </select>


          <!-- Asunto -->
          <label>Asunto</label>
          <input type="text" class="form-control" id="asunto" name="asunto" placeholder="Asunto" required />

          <!-- Fecha -->
          <label>Fecha</label>
          <input type="date" class="form-control" id="fecha" name="fecha" required />

          <!-- Hora -->
          <label>Hora</label>
          <input type="time" class="form-control" id="hora" name="hora" required />

          <!-- Imagen -->
          <div class="imagen">
            <label><i class="fas fa-images"></i> Imágenes</label>
            <div class="image-upload-container">
              <div class="image-upload-box">
                <input type="file" id="zone-images" name="zone-images" accept="image/*" class="hidden-upload" required>
                <label for="zone-images" class="upload-label">
                  <i class="fas fa-cloud-upload-alt"></i>
                  <span>Arrastra imágenes aquí o haz clic para seleccionar</span>
                  <span class="upload-hint">Máximo 5 imágenes (JPEG/PNG, 5MB max cada una)</span>
                </label>
              </div>
              <div id="image-preview" class="image-preview-grid">
                <!-- Preview JS -->
              </div>
            </div>
          </div>

          <!-- Descripción -->
          <label>Descripción</label>
          <textarea class="form-control" rows="10" placeholder="Descripción..." id="descripcion" name="descripcion" required>
Estimados residentes, Les informamos 
Para cualquier pregunta o inconveniente, por favor, contacten a la administración.

Agradecemos su comprensión y cooperación.
Atentamente, [Nombre del Responsable].
Administración del Conjunto Residencial [Nombre conjunto residencial].
        </textarea>

          <!-- Botones -->
          <div style="display: flex; justify-content: center; gap: 10px;">
            <button type="submit" class="enviar">Enviar</button>
            <button type="reset" class="cancelar">Cancelar</button>
          </div>
        </fieldset>
      </form>


    </section>
  </main>

  <?php require_once "./Layout/footer.php"; ?>

  <script src="../assets/Js/muro.js"></script>

</body>

</html>