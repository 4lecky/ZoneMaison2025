<?php


require_once "./Layout/header.php";

?>


<!DOCTYPE html>
<html lang="es">
  <head>
    <meta charset="utf-8" />
  
     <!-- <link rel="stylesheet" href="./assets/Css/crud/" /> -->
    <link rel="stylesheet" href="../assets/Css/globals.css" />
    <link rel="stylesheet" href="../assets/Css/crud/style.css" />
    <!-- Bootstrap   -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <!-- Iconos--> 
    <script src="https://kit.fontawesome.com/dbd1801b06.js" crossorigin="anonymous"></script>

  </head>
  <body>

    <main>

      <table class="table">
        <thead> <!-- Cabecera de la tabla-->
          <tr>
            <th scope="col">Cedula</th>
            <th scope="col">Nombres y apellidos</th>
            <th scope="col">Telefono</th>
            <th scope="col">Correo</th>
            <th scope="col">Torre</th>
            <th scope="col">Apartamento</th>
            <th scope="col">Estado</th>
            <th scope="col">Rol</th>
            <th></th>

          </tr>
        </thead> 
 
        <tbody> <!-- Cuerpo de la tabla -->
          <?php
          
          //Los '../' no son necesarios aqui ya que no se encuentra dentro de una carpeta
          require_once '../config/db.php';
          $stmt = $pdo -> query("SELECT * FROM `tbl_usuario`");

          while ($datos = $stmt ->fetch(PDO::FETCH_OBJ)){ ?>
            <tr>
              <td><?= $datos->usuario_cc ?></td>
              <td><?= $datos->usu_nombre_completo ?></td>
              <td><?= $datos->usu_telefono ?></td>
              <td><?= $datos->usu_correo ?></td>
              <td><?= $datos->usu_torre_residencia ?></td>
              <td><?= $datos->usu_apartamento_residencia ?></td>
              <td><?= $datos->usu_estado ?></td>
              <td><?= $datos->usu_rol ?></td> 
              <td>
                <a href="../models/modificarUsuarioModels.php?cc=<?= $datos->usuario_cc ?>" class="btn btn-small btn-warning"><i class="fa-solid fa-pen-to-square"></i></a>
                <a href="#" class="btn btn-small btn-danger"><i class="fa-solid fa-trash"></i></a>
              </td>
            </tr> 
          <?php  }
          ?>
        </tbody>
        
      </table>



    </main>

    <footer>


    </footer>

    
  <?php
  
    require_once "./Layout/footer.php"

  ?>

  </body>
</html>