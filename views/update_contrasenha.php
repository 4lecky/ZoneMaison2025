<?php
session_start();

$pdo = require_once '../config/db.php';

$tiempo = time();

$sql = "SELECT * FROM tbl_usuario WHERE usuario_cc=? and token_password=? and expired_session>?";

try {
  $stmt = $pdo->prepare($sql);
  $stmt->bindParam(1, $_GET['id']);
  $stmt->bindParam(2, $_GET['token']);
  $stmt->bindParam(3, $tiempo);

  $stmt->execute();

  $data = $stmt->fetchAll(PDO::FETCH_OBJ);
} catch (\Throwable $th) {
  echo $th->getMessage();
} finally {
  $stmt = null;
}

if (count($data) > 0):

?>

  <!DOCTYPE html>
  <html lang="en">

  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cambio de contraseña</title>
    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <!-- Css -->
    <link rel="stylesheet" href="../assets/Css/Cambio_contraseña/update_contraseña.css">
    <link rel="stylesheet" href="../assets/Css/globals.css">
    <!-- Libreria de iconos RemixIcon-->
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
  </head>

  <body id="body_update">

    <div class="fondo_reset">

      <img src="../assets/img/conjuntos.webp" alt="" class="img_fondo_reset">
      <h3 class="texto_superior">¿Olvidaste tu contraseña?</h3>
      <i class="texto_superior2">Restablecela aqui</i>

    </div>

    <div class="container_form_update">

      <h4>Cambio de contraseña</h4>

      <form method="POST" action="../controller/ResetPasswordController.php">

        <div class="col-md-25 container-input-update">
          <label for="password" class="form-label" placeholder="Correo electronico">Nueva contraseña</label>
          <input type="password" class="form-control_update" id="password" name="password" placeholder="Ingrese su nueva contraseña" required>
          <i class="ri-lock-password-fill"></i>
        </div>

        <div class="col-md-25 container-input-update">
          <label for="password" class="form-label">Confirmación de contraseña</label>
          <input type="password" class="form-control_update" id="password" name="new_password" placeholder="Confirmación de contraseña" required>
          <i class="ri-lock-password-fill"></i>
          <input type="hidden" name="id" value="<?php echo $_GET['id']; ?>">
        </div>

        <!-- Apartado para ver los mensajes -->
        <?php
        if (isset($_SESSION['error'])):
        ?>
          <div class="alert alert-danger">
            <?php echo  $_SESSION['error'] ?>
          </div>
        <?php
          unset($_SESSION['error']);
        endif;
        ?>

        <div class="container-btn-update">

          <button type="submit" class="btn btn-form-update btn-primary" name="save"> Enviar </button>
          <button type="reset" class="btn btn-form-update btn-primary" onclick="window.location.href='../views/login.php';"> Cancelar </button>


        </div>
      </form>

    </div>




    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

  </html>

<?php else:
  header("Location:Login.php");
endif; ?>