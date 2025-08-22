    <?php
    //  

    if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
    }

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
        <link rel="stylesheet" href="../assets/Css/Layout/footer.css" />
        <link rel="stylesheet" href="../assets/Css/Perfil_usuario/usuPerfil.css">
        <!-- Bootstrap   -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
        <!-- Libreria de iconos RemixIcon-->
        <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    </head>
    <body>

    <section class = 'etiquetaPerfil'>

        <div class="container mt-2  ">
            <?php if (!empty($_SESSION['mensajesPerfil'])): ?>
                <div class="alert alert-<?= htmlspecialchars($_SESSION['mensajesPerfil']['tipo']) ?> fs-5 alert-dismissible fade show" role="alert">
                    <?= $_SESSION['mensajesPerfil']['texto'] /* contiene <br> a propósito */ ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php unset($_SESSION['mensajesPerfil']); ?>
            <?php endif; ?>
        </div>


        <form action="../controller/perfilUsuarioController.php" method='POST' class="form_perfil_usuario">
            <div class="containerTitulo">
                <h3 class='title_perfilUsu'> Perfil del usuario</h3>
            </div>

                <fieldset class="fieldset_perfil_usuario">
                    <legend class='legend_perfil_usuario' > Información del perfil </legend>

                    <div class="columnas_perfilUsu">

                        <div class="inputs_perfilUsu">
                            <label for="validationCustom01" class="label_perfilUsu">Nombre completo</label>
                            <input type="text" class="form_control_perfilUsu" name="NombreCompleto" value="<?= htmlspecialchars($user['usu_nombre_completo'] ?? '') ?>" required>
                        </div>

                        <div class="inputs_perfilUsu">
                            <label for="validationCustom01" class="label_perfilUsu">Numero de documento</label>
                            <input type="number" class="form_control_perfilUsu" name="NumeroDocumento" value="<?= htmlspecialchars($user['usu_cedula'] ?? '') ?>" required>
                        </div>

                    </div>

                    <div class="columnas_perfilUsu">

                        <div class="select_perfilUsu">
                            <label for="TipoDocumento" class="label_perfilUsu"> Tipo de documento </label>
                            <select class="campos_select_perfilUsu" id="validationCustom04" name="TipoDocumento">

                                <option>Elija una opción</option>
                                <option value="Cedula de ciudadania"  <?= ($user['usu_tipo_documento'] ?? '')==='Cedula de ciudadania' ? 'selected' : '' ?>> Cedula de ciudadania </option>
                                <option value="Cedula de extranjeria"  <?= ($user['usu_tipo_documento'] ?? '')==='Cedula de extranjeria' ? 'selected' : '' ?>>Cedula de extranjeria</option>
                                <option value="Pasaporte"  <?= ($user['usu_tipo_documento'] ?? '')==='Pasaporte' ? 'selected' : '' ?>> Pasaporte </option>
                                <option value="Permiso especial de permanencia (PEP)"  <?= ($user['usu_tipo_documento'] ?? '')==='Permiso especial de permanencia (PEP)' ? 'selected' : '' ?>> Permiso especial de permanencia (PEP) </option>

                            </select>
                        </div>

                        <div class="inputs_perfilUsu">
                            <label for="validationCustom01" class="label_perfilUsu">Numero telefonico</label>
                            <input type="number" class="form_control_perfilUsu" name="Telefono" value="<?= htmlspecialchars($user['usu_telefono'] ?? '') ?>" required>
                        </div>

                    </div>

                    <div class="columnas_perfilUsu">

                        <div class="inputs_perfilUsu">
                            <label for="validationCustom01" class="label_perfilUsu"> Correo electronico</label>
                            <input type="email" class="form_control_perfilUsu" name="Correo" value="<?= htmlspecialchars($user['usu_correo'] ?? '') ?>" required>
                        </div>

                        <div class="inputs_perfilUsu">
                            <label for="validationCustom01" class="label_perfilUsu"> Torre </label>
                            <input type="text" class="form_control_perfilUsu" name="Torre" value="<?= htmlspecialchars($user['usu_torre_residencia'] ?? '') ?>" required>
                        </div>

                    </div>

                    <div class="columnas_perfilUsu">

                        <div class="inputs_perfilUsu">
                            <label for="validationCustom01" class="label_perfilUsu"> Apartamento</label>
                            <input type="text" class="form_control_perfilUsu" name="Apartamento" value="<?= htmlspecialchars($user['usu_apartamento_residencia'] ?? '') ?>" required>
                        </div>

                        <div class="inputs_perfilUsu">
                            <label for="validationCustom01" class="label_perfilUsu"> Propiedades </label>
                            <input type="text" class="form_control_perfilUsu" name="Propiedades" value="<?= htmlspecialchars($user['usu_propiedades'] ?? '') ?>" required>
                        </div>

                    </div>

                    
                    <div class="container_bnt_perfilUsu">
                            <button type="submit" class="btn_perfilUsu " name="btn-eliminar"  onclick="return confirm('¿Seguro que deseas eliminar tu cuenta?');" value="ok"> Eliminar cuenta </button>
                            <button type="submit" class="btn_perfilUsu " name="btn-editar" value="ok"> Editar datos </button>
                            <button type="button" class="btn_perfilUsu " onclick="window.location.href='../views/novedades.php';"> Volver </button>
                    </div>

                </fieldset>
        </form>
    </section>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    </body>
</html>

<?php
    // var_dump('FLASH EN SESIÓN =>', $_SESSION['mensajesPerfil'] ?? null);
    // var_dump('FLASH EN VARIABLE =>', $flash ?? null);
    // var_dump('USER =>', $user ? 'ok' : 'null');
    require_once __DIR__ . "/Layout/footer.php";

?>
