<?php

session_start();


?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Emai de recuperación</title>
    <!-- Css -->
    <link rel="stylesheet" href="../assets/Css/Cambio_contraseña/reset_contraseña.css">
    <link rel="stylesheet" href="../assets/Css/globals.css">
    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>

<body id="body_reset">


    <div class="fondo_reset">

    <img src="../assets/img/conjuntos.webp" alt="" class="img_fondo_reset">


    </div>

    <div class="container_form_reset">

        <h4>Recuperación de contraseña</h4>

        <form action="">

            <div class="col-md-25">
                <label for="validationCustom01" class="form-label" placeholder="Correo electronico">Correo Electronico</label>
                <input type="text" class="form-control" id="validationCustom01" name="correo" placeholder="Ingrese su correo electronico" required>
            </div>

            <div class="container-btn-reset">

                <button type="submit" class="btn btn-form-reset btn-primary" name="enviar_email"> Enviar </button>
                <button type="reset" class="btn btn-form-reset btn-primary"> Cancelar </button>

            </div>



        </form>

    </div>


</body>

</html>