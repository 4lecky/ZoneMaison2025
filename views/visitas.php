<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}
require_once "./Layout/header.php"
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ZONEMAISONS - admin</title>
    <link rel="stylesheet" href="../assets/Css/globals.css" />
    <link rel="stylesheet" href="../assets/Css/Layout/header.css" />
    <link rel="stylesheet" href="../assets/Css/Layout/footer.css" />
    <link rel="stylesheet" href="../assets/Css/visitas/visitas.css" />
    <!-- Libreria de iconos RemixIcon-->
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    


    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-LN+7fdVzj6u52u30Kp6M/trliBMCMKTyK833zpbD+pXdCLuTusPj697FH4R/5mcr" crossorigin="anonymous">
</head>

<body>


    <!-- Registro de Visitas -->
    <main class="container mt-4">
        <div class="principal-page">
            <h2>REGISTRO DE VISITAS</h2>

            <!-- Botón Consultar Residente -->
        <div class="boton-residente">
            <button onclick="window.location.href='crud.php'">
                Consultar Residente
            </button>
        </div>

            <form method="POST" action="../controller/RegistrarVisitaController.php" id="formVisitante">
                <fieldset>
                    <legend>Datos del Visitante</legend>

                    <div class="input-group">
                        <div class="input-box" style="width: 100%;">
                            <label for="nombre">Nombre Completo del Visitante <span class="requerido">*</span></label>
                            <input type="text" class="form-control" name="nombre" id="nombre" placeholder="Nombre completo" required>
                        </div>
                    </div>

                    <div class="input-group">
                        <div class="input-box">
                            <label for="tipo_doc">Tipo de Documento <span class="requerido">*</span></label>
                            <select class="form-control" name="tipo_doc" id="tipo_doc" required>
                                <option selected disabled>Seleccione el Tipo de Documento</option>
                                <option value="C.C.">C.C.</option>
                                <option value="T.I.">T.I.</option>
                                <option value="C.E.">C.E.</option>
                            </select>
                        </div>

                        <div class="input-box">
                            <label for="numero_doc">Número de Documento <span class="requerido">*</span></label>
                            <input type="number" class="form-control" name="numero_doc" id="numero_doc" placeholder="Número Documento" required>
                        </div>
                    </div>

                    <div class="input-group">
                        <div class="input-box">
                            <label for="correo">Correo Electrónico <span class="requerido">*</span></label>
                            <input type="email" class="form-control" name="correo" id="email" placeholder="correo@ejemplo.com" required>
                        </div>

                        <div class="input-box">
                            <label for="telefono">Teléfono <span class="requerido">*</span></label>
                            <input type="number" class="form-control" name="telefono" id="telefono" placeholder="Número de teléfono" required>
                        </div>
                    </div>

                    <div class="input-group">
                        <div class="input-box" style="width: 100%;">
                            <label for="usuario">Cédula del Residente <span class="requerido">*</span></label>
                            <input type="number" class="form-control" name="usuario" id="usuario" placeholder="Ej: 1234567890" data-validate="telefono" min="1" required>
                        </div>
                    </div>

                </fieldset>

                <fieldset>
                    <legend>Datos de la Visita</legend>

                    <div class="input-group">
                        <div class="input-box">
                            <label for="fechaEntrada">Fecha de Entrada <span class="requerido">*</span></label>
                            <input type="date" class="form-control" name="fechaEntrada" id="fechaEntrada" data-validate="date" required>
                        </div>

                        <div class="input-box">
                            <label for="fechaSalida">Fecha de Salida <span class="requerido">*</span></label>
                            <input type="date" class="form-control" name="fechaSalida" id="fechaSalida" data-validate="date" required>
                        </div>
                    </div>

                    <div class="input-group">
                        <div class="input-box">
                            <label for="horaEntrada">Hora de Entrada <span class="requerido">*</span></label>
                            <input type="time" class="form-control" name="horaEntrada" id="horaEntrada" data-validate="hora" required>
                        </div>

                        <div class="input-box">
                            <label for="horaSalida">Hora de Salida <span class="requerido">*</span></label>
                            <input type="time" class="form-control" name="horaSalida" id="horaSalida" data-validate="hora" required>
                        </div>
                    </div>
                </fieldset>


                <!-- Botones -->
                <div class="input-group" style="justify-content: center;">

                    <!-- onclick="window.location.href='visita.php';" -->

                    <button type="submit" class="Enviar" id="btnRegistrar" name="registrarFormVisi">Registrar Visita</button>

                    <button type="reset" class="Cancelar" id="btnLimpiar">Limpiar</button>
                </div>
            </form>
        </div>

        <!-- Botones principales -->
        <div class="botones-container">

        <!-- Botón Consultar Visitas -->
        <div class="contenedor-boton-consulta" onclick="window.location.href='visita_crud.php'">
            <div class="boton consulta">
                Consultar Visitas
            </div>
        </div>

        <!-- Botón Registrar Vehículo -->
        <div class="contenedor-boton-vehiculo" onclick="window.location.href='parqueadero.php'">
            <div class="boton vehiculo">
                Registrar un Vehiculo
            </div>
        </div>

    </div>


           
    </main>

    <!-- Scripts -->
    <script src="../assets/js/visitas.js"></script>
    <?php
    require_once "./Layout/footer.php"
    ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js" integrity="sha384-ndDqU0Gzau9qJ1lfW4pNLlhNTkCfHzAVBReH9diLvGRem5+R9g2FzA8ZGN954O5Q" crossorigin="anonymous"></script>
</body>

</html>