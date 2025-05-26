<?php
session_start();
require_once __DIR__ . "/Layout/header.php";
?>


<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="utf-8" />

  <!-- DataTable-->
  <link href="https://cdn.datatables.net/buttons/3.2.3/css/buttons.bootstrap5.min.css" rel="stylesheet" integrity="sha384-DJhypeLg79qWALC844KORuTtaJcH45J+36wNgzj4d1Kv1vt2PtRuV2eVmdkVmf/U" crossorigin="anonymous">
  <link href="https://cdn.datatables.net/2.3.1/css/dataTables.bootstrap5.min.css" rel="stylesheet" integrity="sha384-5hBbs6yhVjtqKk08rsxdk9xO80wJES15HnXHglWBQoj3cus3WT+qDJRpvs5rRP2c" crossorigin="anonymous">
  <!-- Bootstrap   -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <!-- Css despues del Bootstrap -->
  <link rel="stylesheet" href="../assets/Css/globals.css" />
  <link rel="stylesheet" href="../assets/Css/Layout/header.css" />
  <link rel="stylesheet" href="../assets/Css/Layout/footer.css" />
  <link rel="stylesheet" href="../assets/Css/crud/usuCrud.css">
  <link rel="stylesheet" href="../assets/Css/crud/tbl_crud.css">
  <!-- Libreria de iconos RemixIcon-->
  <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">

</head>

<body>

  <main>

    <h1>Lista de usuarios</h1>

    <div class="container mt-4">
      <!-- Mostrar mensaje -->
      <?php if (isset($_SESSION['mensaje'])): ?>
        <div class="alert alert-<?= $_SESSION['mensaje']['tipo']; ?> fs-5 alert-dismissible fade show" role="alert">
          <?= $_SESSION['mensaje']['texto']; ?>
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['mensaje']); ?>
      <?php endif; ?>
      <!-- Aquí iría el resto del contenido de la tabla de usuarios -->
    </div>

    <table id="usuarios" class="table table-striped table-bordered" cellspacing="0" width="100%">
      <thead>
        <tr>
          <th scope="col">Cedula</th>
          <th scope="col">Nombres y apellidos</th>
          <th scope="col">Telefono</th>
          <th scope="col">Correo</th>
          <th scope="col">Torre</th>
          <th scope="col">Apartamento</th>
          <th scope="col">Estado</th>
          <th scope="col">Rol</th>
          <th scope="col">Editar/Eliminar</th>
        </tr>
      </thead>

      <tbody>
        <?php
        require_once '../config/db.php';
        $stmt = $pdo->query("SELECT * FROM tbl_usuario ORDER BY CASE 
              WHEN usu_estado = 'Activo' THEN 1
              WHEN usu_estado = 'Inactivo' THEN 2
              ELSE 3
              END");

        while ($datos = $stmt->fetch(PDO::FETCH_OBJ)) {
          // $claseFila = ($datos->usu_estado === "Inactivo") ? 'usuario-inactivo' : '';  
        ?>
          <tr class="<?= ($datos->usu_estado === "Inactivo") ? 'usuario-inactivo' : 'usuario-activo' ?>">
            <td><?= $datos->usuario_cc ?></td>
            <td><?= $datos->usu_nombre_completo ?></td>
            <td><?= $datos->usu_telefono ?></td>
            <td><?= $datos->usu_correo ?></td>
            <td><?= $datos->usu_torre_residencia ?></td>
            <td><?= $datos->usu_apartamento_residencia ?></td>
            <td><?= $datos->usu_estado ?></td>
            <td><?= $datos->usu_rol ?></td>

            <td class="contenedorBotones">
              <a href="../models/modificarUsuarioModels.php?cc=<?= $datos->usuario_cc ?>" class="btn btn-small btn-warning"><i class="fa-solid fa-pen-to-square"></i></a>
              <a href="../controller/EliminarUsuarioController.php?cc=<?= $datos->usuario_cc ?>" class="btn btn-small btn-danger"><i class="fa-solid fa-trash"></i></a>
            </td>
          </tr>
        <?php  }
        ?>
      </tbody>
    </table>



  </main>




  <!-- jquery -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

  <!-- DataTable -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js" integrity="sha384-+mbV2IY1Zk/X1p/nWllGySJSUN8uMs+gUAN10Or95UBH0fpj6GfKgPmgC5EXieXG" crossorigin="anonymous"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js" integrity="sha384-VFQrHzqBh5qiJIU0uGU5CIW3+OWpdGGJM9LBnGbuIH2mkICcFZ7lPd/AAtI7SNf7" crossorigin="anonymous"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js" integrity="sha384-/RlQG9uf0M2vcTw3CX7fbqgbj/h8wKxw7C3zu9/GxcBPRKOEcESxaxufwRXqzq6n" crossorigin="anonymous"></script>
  <script src="https://cdn.datatables.net/2.3.1/js/dataTables.min.js" integrity="sha384-LiV1KhVIIiAY/+IrQtQib29gCaonfR5MgtWzPCTBVtEVJ7uYd0u8jFmf4xka4WVy" crossorigin="anonymous"></script>
  <script src="https://cdn.datatables.net/2.3.1/js/dataTables.bootstrap5.min.js" integrity="sha384-G85lmdZCo2WkHaZ8U1ZceHekzKcg37sFrs4St2+u/r2UtfvSDQmQrkMsEx4Cgv/W" crossorigin="anonymous"></script>
  <script src="https://cdn.datatables.net/buttons/3.2.3/js/dataTables.buttons.min.js" integrity="sha384-zlMvVlfnPFKXDpBlp4qbwVDBLGTxbedBY2ZetEqwXrfWm+DHPvVJ1ZX7xQIBn4bU" crossorigin="anonymous"></script>
  <script src="https://cdn.datatables.net/buttons/3.2.3/js/buttons.bootstrap5.min.js" integrity="sha384-BdedgzbgcQH1hGtNWLD56fSa7LYUCzyRMuDzgr5+9etd1/W7eT0kHDrsADMmx60k" crossorigin="anonymous"></script>
  <script src="https://cdn.datatables.net/buttons/3.2.3/js/buttons.colVis.min.js" integrity="sha384-v0wzF6NECWiQyIain/Wacl6wEYr6NDJRus6qpckumPIngNI9Zo0sDMon5lBh9Np1" crossorigin="anonymous"></script>
  <script src="https://cdn.datatables.net/buttons/3.2.3/js/buttons.html5.min.js" integrity="sha384-+E6fb8f66UPOVDHKlEc1cfguF7DOTQQ70LNUnlbtywZiyoyQWqtrMjfTnWyBlN/Y" crossorigin="anonymous"></script>
  <script src="https://cdn.datatables.net/buttons/3.2.3/js/buttons.print.min.js" integrity="sha384-FvTRywo5HrkPlBKFrm2tT8aKxIcI/VU819roC/K/8UrVwrl4XsF3RKRKiCAKWNly" crossorigin="anonymous"></script>

  <!-- Bootstrap -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

  <!-- Iconos-->
  <script src="https://kit.fontawesome.com/dbd1801b06.js" crossorigin="anonymous"></script>

  <!-- Links de JavaScript -->
  <script src="../assets/Js/crud/tableCrud.js"></script>
  <script src="../assets/Js/header.js"></script>


  <?php
  require_once __DIR__ . "./Layout/footer.php"
  ?>

</body>

</html>