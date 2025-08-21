<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}
$pdo = require_once "../config/db.php";
require_once "./Layout/header.php";

// Obtener los usuarios
$stmt = $pdo->query("SELECT usu_nombre_completo, usu_cedula FROM tbl_usuario");
$usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);


// // cedula
// $_SESSION['usuario'] = [
//     'cedula' => $usuario['cedula'],
//     'nombre' => $usuario['nombre'],
// ];

// if ($_SESSION['usuario']['cedula'] === 'vigilante') {
//     // El vigilante ve todas las publicaciones
//     $query = "SELECT * FROM tbl_paquetes ORDER BY paqu_FechaLlegada DESC, paqu_Hora DESC";
//     $stmt = $pdo->prepare($query);
//     $stmt->execute();
// } else {
//     // Los demás usuarios solo ven publicaciones asociadas a su cédula
//     $query = "SELECT * FROM tbl_paquetes 
//               WHERE paqu_usuario_cedula = :cedula 
//               ORDER BY paqu_FechaLlegada DESC, paqu_Hora DESC";
//     $stmt = $pdo->prepare($query);
//     $stmt->execute(['cedula' => $_SESSION['usuario']['cedula']]);
// }

?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>ZONEMAISONS - Paquetería</title>
  <link rel="stylesheet" href="../assets/Css/globals.css" />
  <link rel="stylesheet" href="../assets/Css/Layout/header.css" />
  <link rel="stylesheet" href="../assets/Css/Layout/footer.css" />
  <link rel="stylesheet" href="../assets/Css/ComunicaciondeNovedades/paquetess.css">
</head>

<body>
  <main>
    <section class="principal-page">
      <h2>Paquetería</h2>

      <form action="../controller/paquetesController.php" method="POST" enctype="multipart/form-data" class="paquetes">
        <fieldset>
          <legend>Formulario de Paquetes</legend>

          <label>Tipo de Documento</label>
          <select class="form-control" name="tipo_doc" id="tipo_doc" required>
            <option value="">Seleccione el Tipo de Documento</option>
            <option> Cedula de cidadania </option>
            <option> Cedula de extrangeria </option>
            <option> Pasaporte </option>
            <option> Permiso especial de permanencia (PEP) </option>
          </select>

          <label>Número Documento</label>
          <input type="text" class="form-control" name="numero_doc" id="numero_doc" placeholder="Número Documento" required />
          <input type="hidden" name="paqu_usuario_cedula" id="cedula_oculta">

          <label>Destinatario</label>
          <input type="text" name="paqu_Destinatario" id="paqu_Destinatario" class="form-control" placeholder="Nombre del destinatario" readonly required />

          <label>Asunto</label>
         <textarea class="form-control" rows="1" placeholder="asunto" id="asunto" name="asunto" required>
Ha llegado un paquete para usted.
          </textarea> 

          <label>Fecha</label>
          <input type="date" class="form-control" name="fecha" id="fecha" />

          <label>Hora</label>
          <input type="time" class="form-control" name="hora" id="hora" />

          <label></i> Imágenes</label>
          <input type="file" name="zone-images" accept="image/*" />

          <label>Descripción</label>
          <input type="text" class="form-control" name="descripcion" placeholder="Descripción del paquete" />

          <label>Estado</label>
          <select name="estado" id="estado" required>
            <option value="">Seleccione estado</option>
            <option value="Pendiente">Pendiente</option>
            <option value="Entregado">Entregado</option>
          </select>
        </fieldset>

        <div style="display: flex; justify-content: center; gap: 10px;">
          <button type="submit" class="Enviar">Enviar</button>
          <button type="reset" class="Cancelar">Cancelar</button>
        </div>
      </form>
    </section>
  </main>

  <script src="../assets/js/paquetes.js"></script>

  <?php require_once "./Layout/footer.php"; ?>
</body>
</html>