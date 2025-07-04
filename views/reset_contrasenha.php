<?php

session_start();


?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email de recuperación</title>
    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <!-- Css -->
    <link rel="stylesheet" href="../assets/Css/Cambio_contraseña/reset_contraseña.css">
    <link rel="stylesheet" href="../assets/Css/globals.css">
    <!-- Libreria de iconos RemixIcon-->
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">

</head>

<body id="body_reset">


    <div class="fondo_reset">

        <img src="../assets/img/conjuntos.webp" alt="" class="img_fondo_reset">
        <h3 class="texto_superior">¿Olvidaste tu contraseña?</h3>
        <i class="texto_superior2">No te preocupes, aqui puedes recuperarla </i>
    </div>

    <div class="container_form_reset">

        <h4>Recuperación de contraseña</h4>

        <form method="POST" action="../controller/ResetPasswordController.php">

            <div class="container_mensajes_reset">
                <!-- Apartado para ver los mensajes custom-alert -->
                <?php if (isset($_SESSION['response'])): ?>
                    <div class="alert alert-<?php echo $_SESSION['response_type'] ?? 'info'; ?> fs-5 alert-dismissible fade show mt-3" role="alert">
                        <?php echo $_SESSION['response']; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    <?php unset($_SESSION['response'], $_SESSION['response_type']); ?>
                <?php endif; ?>

            </div>

            <div class="col-md-25 container-input-reset">
                <label for="email" class="form-label">Correo Electronico</label>
                <input type="email" class="form-control" id="email" name="email" placeholder="Ingrese su correo electronico" required>
                <i class="ri-mail-open-fill"></i>
            </div>

            <div class="container-btn-reset">

                <button class="btn btn-form-reset btn-primary" name="send"> Enviar </button>
                <button type="reset" class="btn btn-form-reset btn-primary" onclick="window.location.href='../views/login.php';"> Cancelar </button>

            </div>
        </form>

    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>