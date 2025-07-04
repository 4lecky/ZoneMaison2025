<?php

session_start();

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <!-- Css -->
    <link rel="stylesheet" href="../assets/Css/globals.css" />
    <link rel="stylesheet" href="../assets/Css/login.css" />
     <!-- Libreria de iconos RemixIcon-->
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>

<body>

    <div class="container_derecho">

        <img src="../assets/img/conjuntos.webp" alt="Imagen" class="conjunto">
        <h3 class="texto_superior">¡Bienvenido de nuevo!</h3>
        <i class="texto_superior2">Es un gusto tenerlo aqui</i>

    </div>

    <div class="container_izquierdo">

        <!-- <img src="./assets/img/LogoZM.png" alt="" class="logo_zm"> -->

        <form class="formulario_ingreso" id="formulario_ingreso" method="post" action="../controller/AuthController.php">

            <h2>Inicie sesión aqui</h2>

            <div class="container_mensajes_update">

                <?php if (isset($_SESSION['response'])): ?>
                    <div class="alert alert-<?php echo $_SESSION['response_type'] ?? 'info'; ?> alert-dismissible fade show mt-3" role="alert">
                        <?php echo $_SESSION['response']; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    <?php unset($_SESSION['response'], $_SESSION['response_type']); ?>
                <?php endif; ?>

            </div>


            <div class="campos_login">

                <label for="username" class="titulo_campo">Email</label><br>
                <input type="text" placeholder="Correo electronico" name="Email" class="campo_login" required><br>
                <i class="ri-mail-star-fill" id="iconos_log"></i>
            </div>

            <div class="campos_login">

                <label for="password" class="titulo_campo">Contraseña</label><br>
                <div style="position: relative;">
                    <input type="password" placeholder=" Contraseña" name="Password" class="campo_login_p" id="password-input" required>
                    <button type="button" id="toggle-password" style="position: absolute; right: 10px; top: 40%; transform: translateY(-50%); background: none; border: none; cursor: pointer;">
                        <i class="ri-eye-off-fill" id="eye-icon" ></i>
                    </button>
                </div>

            </div>

            <div class="contenedor_btn">

                <button type="submit" id="btn_incio" name="login">Inicio</button>

            </div>

            <div class="texto_recuperarC">

                <a href="./reset_contrasenha.php" class="text_usu"> ¿Olvido su contraseña? </a> <br>
                <a href="./signUp.php" class="text_usu"> Registrarse </a>

            </div>

        </form>

    </div>

    <script src="../assets/Js/login.js"></script>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Font Awesome for icons -->
    <script src="https://kit.fontawesome.com/dbd1801b06.js" crossorigin="anonymous"></script>


</body>

</html>