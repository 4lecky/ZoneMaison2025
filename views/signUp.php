<?php
session_start();
$errorRegistro = $_SESSION['errorRegistro'] ?? false;
unset($_SESSION['errorRegistro']);


?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro</title>
    <link rel="stylesheet" href="../assets/Css/globals.css" />
    <link rel="stylesheet" href="../assets/Css/registro.css" />
    <!-- Libreria de iconos RemixIcon-->
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>

<body>


    <div class="container_derecho">


        <img src="../assets/img/conjuntos.webp" alt="Imagen" class="conjunto">
        <h3 class="texto_superior"> ¡Bienvenido! </h3>
        <i class="texto_superior2">Sera un gusto tenerte con nosotros</i>

    </div>

    
    <div class="container_izquierdo">

        <form method="post" action="/index.php?controller=Auth&action=registrar">
        <!-- <form class="formulario_ingresosRe" id="formulario_ingresosRe" method="post" action="/zonemaison2025/index.php?controller=Auth&action=registrar"> -->
            <h2>Registro usuario</h2>

            <?php if ($errorRegistro): ?>
                <div style="color: red; margin-top: 10px; text-align: center; margin-bottom: 10px;">
                    <?php echo $errorRegistro; ?>
                </div>
            <?php endif; ?>

            <div class="columnas">

                <div class="campo">
                    <label for="NombreUsuario" class="titulo_campo">
                        Nombres y apellidos
                    </label>
                    <input type="text" placeholder="Ingrese la información" class="campos" name="NombreUsuario" required>
                    <i class="ri-user-3-fill"></i>
                </div>

                <div class="campo">
                    <label for="NumeroCedula" class="titulo_campo">
                        Numero de documento
                    </label>
                    <i class="ri-hashtag"></i>
                    <input type="number" placeholder="Ingrese su de cedula" name="NumeroCedula" class="campos <?php echo $errorRegistro ? 'input-error-registro' : ''; ?>" required>
                </div>

            </div>

            <div class="columnas">

                <div class="campo">
                    <label for="TipoDocumento" class="titulo_campo">Tipo de documento</label>
                    <i class="ri-asterisk"></i>
                    <select class="campos_select" id="validationCustom04" name="TipoDocumento">

                        <option>Elija una opción</option>
                        <option> Cedula de ciudadania </option>
                        <option> Cedula de extranjeria </option>
                        <option> Pasaporte </option>
                        <option> Permiso especial de permanencia (PEP) </option>

                    </select>
                </div>

                <div class="campo">
                    <label for="NumeroTelefonico" class="titulo_campo">
                        Teléfono
                    </label>
                    <i class="ri-phone-fill"></i>
                    <input type="tel" placeholder="Ingrese su número telefónico" name="NumeroTelefonico" class="campos <?php echo $errorRegistro ? 'input-error-registro' : ''; ?>" required>
                </div>


            </div>


            <div class="columnas">

                <div class="campo">
                    <label for="Apartamento" class="titulo_campo">
                    Apartamento
                    </label>
                    <i class="ri-building-line"></i>
                    <input type="text" placeholder="  Ingrese el apartamento donde vive" class="campos" name="Apartamento" required>
                </div>

                <div class="campo">
                    <label for="Torre" class="titulo_campo">
                        Torre
                    </label>
                    <i class="ri-building-line"></i>
                    <input type="text" placeholder="Ingrese donde vive" class="campos" name="Torre" required>
                </div>

            </div>

            <div class="columnas">

                <div class="campo">

                    <label for="Parqueadero" class="titulo_campo">
                        Parqueadero
                    </label>
                    <i class="ri-car-fill"></i>
                    <input type="text" placeholder="Ingrese su parqueadero asignado" class="campos" name="Parqueadero" required>

                </div>

                <div class="campo">

                    <label for="Propiedades" class="titulo_campo">
                        Propiedades
                    </label>
                    <i class="ri-home-6-fill"></i>
                    <input type="text" placeholder="Ej: Apto 12 Torre 2 / Sin propiedades" class="campos" name="Propiedades" required>

                </div>

            </div>


            <div class="columnas">

                <div class="campo">
                    <label for="Email" class="titulo_campo">
                    Correo electronico
                    </label>
                    <i class="ri-mail-star-fill"></i>
                    <input type="email" placeholder="Defina su correo electronico" name="Email" class="campos <?php echo $errorRegistro ? 'input-error-registro' : ''; ?>" required>
                </div>


                <div class="campo">
                    <label for="Password" class="titulo_campo">
                     Contraseña
                    </label>
                    <i class="ri-lock-fill"></i>
                    <input type="password" placeholder="Defina una contraseña" class="campos" name="Password" required>
                </div>
            </div>


            <div class="texto_inf">

                <a href="./login.php" class="text_usu"> ¿Ya tiene una cuenta? De click aqui </a>

            </div>


            <div class="contenedor_btn">

                <button type="submit" id="btn_incio" name="registrar">Registrarse</button>

            </div>

        </form>

    </div>

    <!-- Font Awesome for icons -->
    <script src="https://kit.fontawesome.com/dbd1801b06.js" crossorigin="anonymous"></script>
    <!-- Custom JavaScript -->
    <script src="../assets/Js/singnUp.js"></script>

</body>

</html>