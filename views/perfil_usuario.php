    <?php
    session_start();

    if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
    }

    $id = $_SESSION['usuario']['id'] ?? '';


    require_once __DIR__ . "/Layout/header.php";

    ?>

    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Información de usario</title>
        <link rel="stylesheet" href="../assets/Css/globals.css" />
        <link rel="stylesheet" href="../assets/Css/Layout/header.css" />
        <link rel="stylesheet" href="../assets/Css/Perfil_usuario/usuPerfil.css">
        <!-- Libreria de iconos RemixIcon-->
        <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    </head>
    <body>



    <form action="" class="form_perfil_usuario">
        <h3 class='title_perfilUsu'> Perfil de usuario</h3>
            <fieldset class="fieldset_perfil_usuario">
                <legend class='legend_perfil_usuario' > Información del perfil </legend>

                <div class="columnas_perfilUsu">

                    <div class="inputs_perfilUsu">
                        <label for="validationCustom01" class="label_perfilUsu">Nombre completo</label>
                        <input type="text" class="form_control_perfilUsu" name="NombreCompleto" required>
                    </div>

                    <div class="inputs_perfilUsu">
                        <label for="validationCustom01" class="label_perfilUsu">Numero de documento</label>
                        <input type="number" class="form_control_perfilUsu" name="NumeroDocumento" required>
                    </div>

                </div>

                <div class="columnas_perfilUsu">

                    <div class="select_perfilUsu">
                        <label for="TipoDocumento" class="label_perfilUsu"> Tipo de documento </label>
                        <select class="campos_select_perfilUsu" id="validationCustom04" name="TipoDocumento">

                            <option>Elija una opción</option>
                            <option> Cedula de ciudadania </option>
                            <option> Cedula de extranjeria </option>
                            <option> Pasaporte </option>
                            <option> Permiso especial de permanencia (PEP) </option>

                        </select>
                    </div>

                    <div class="inputs_perfilUsu">
                        <label for="validationCustom01" class="label_perfilUsu">Numero telefonico</label>
                        <input type="number" class="form_control_perfilUsu" name="Telefono" required>
                    </div>

                </div>

                <div class="columnas_perfilUsu">

                    <div class="inputs_perfilUsu">
                        <label for="validationCustom01" class="label_perfilUsu"> Correo electronico</label>
                        <input type="email" class="form_control_perfilUsu" name="Correo" required>
                    </div>

                    <div class="inputs_perfilUsu">
                        <label for="validationCustom01" class="label_perfilUsu"> Torre </label>
                        <input type="text" class="form_control_perfilUsu" name="Torre" required>
                    </div>

                </div>

                <div class="columnas_perfilUsu">

                    <div class="inputs_perfilUsu">
                        <label for="validationCustom01" class="label_perfilUsu"> Apartamento</label>
                        <input type="text" class="form_control_perfilUsu" name="Apartamento" required>
                    </div>

                    <div class="inputs_perfilUsu">
                        <label for="validationCustom01" class="label_perfilUsu"> Propiedades </label>
                        <input type="text" class="form_control_perfilUsu" name="Apartamento" required>
                    </div>

                </div>

                
                <div class="container_bnt_perfilUsu">
                        <button type="button" class="btn_perfilUsu " name="btn-editar" value="ok"> Eliminar cuenta </button>
                        <button type="submit" class="btn_perfilUsu " name="btn-editar" value="ok"> Editar datos </button>
                        <button type="button" class="btn_perfilUsu " onclick="window.location.href='../views/novedades.php';"> Volver </button>
                </div>

            </fieldset>

    </form>



        
    </body>
    </html>