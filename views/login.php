<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>

    <link rel="stylesheet" href="../assets/Css/globals.css" />
    <link rel="stylesheet" href="../assets/Css/login.css" />
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

            <?php if (!empty($mensaje)): ?>
                <div style="color: red;"><?php echo $mensaje; ?></div>
            <?php endif; ?>


            <i class="fa-solid fa-envelope"></i>
            <label for="username" class="titulo_campo">Email</label><br>
            <input type="text" placeholder="Correo electronico" name="Email" class="campos" required><br>

            <i class="fa-solid fa-lock"></i>
            <label for="password" class="titulo_campo">Contraseña</label><br>
            <div style="position: relative;">
                <input type="password" placeholder=" Contraseña" name="Password" class="campos" id="password-input" required>
                <button type="button" id="toggle-password" style="position: absolute; right: 10px; top: 40%; transform: translateY(-50%); background: none; border: none; cursor: pointer;">
                    <i class="fa-solid fa-eye" id="eye-icon"></i>
                </button>
            </div>

            <div class="contenedor_btn">

                <button type="submit" id="btn_incio" name="login">Inicio</button>

            </div>

            <div class="texto_recuperarC">

                <a href="./reset_contraseña.php" class="text_usu"> ¿Olvido su contraseña? </a> <br>
                <a href="./signUp.php" class="text_usu"> Registrarse </a>

            </div>

        </form>

    </div>

    <script src="../assets/Js/login.js"></script>
    <script src="https://kit.fontawesome.com/dbd1801b06.js" crossorigin="anonymous"></script>

</body>

</html>