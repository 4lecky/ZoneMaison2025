<?php
require_once "./Layout/header.php"
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ZONEMAISONS - admin</title>
    <link rel="stylesheet" href="../assets/Css/visitas.css">
    <link rel="stylesheet" href="../assets/Css/globals.css" />
    <link rel="stylesheet" href="../assets/Css/Layout/header.css" />
    <link rel="stylesheet" href="../assets/Css/Layout/footer.css" />
    <link rel="stylesheet" href="../assets/Css/visitas-form.css" />
</head>

<body>

    <!-- Registro de Visitas -->
    <main class="container mt-4">
        <h2>REGISTRO DE VISITAS</h2>

        <section class="form-card">
            <form id="formVisita" method="post" action="../controller/visitaController.php">
                <fieldset>
                    <legend><strong>Datos Visita</strong></legend>

                    <label for="torre">Número Torre Visitada</label>
                    <input type="text" data-validate="number" name="torre" id="torre" required>

                    <label for="apto">Número Apto Visitado</label>
                    <input type="text" data-validate="number" name="apto" id="apto" required>

                    <label for="fechaEntrada">Fecha de Entrada</label>
                    <input type="date" name="fechaEntrada" id="fechaEntrada" required>

                    <label for="fechaSalida">Fecha de Salida</label>
                    <input type="date" name="fechaSalida" id="fechaSalida" required>

                    <label for="horaInicio">Hora de Ingreso</label>
                    <input type="time" name="horaInicio" id="horaInicio" required>

                    <label for="horaSalida">Hora de Salida</label>
                    <input type="time" name="horaSalida" id="horaSalida" required>
                </fieldset>

                <button type="reset" id="btnLimpiar">Limpiar</button>
                <button type="submit" id="btnRegistrar" name="registrarFormVisi">Registrar Visitante</button>
            </form>

            <form id="formVisitante" method="post" action="../controller/visitanteController.php">
                <fieldset>
                    <legend><strong>Datos Visitante</strong></legend>

                    <label for="nombre">Nombre Completo</label>
                    <input type="text" data-validate="text" name="nombre" id="nombre" required>

                    <label for="tipoDoc">Tipo Documento</label>
                    <select name="tipoDoc" id="tipoDoc" required>
                        <option value="">Tipo Doc.</option>
                        <option value="CC">C.C</option>
                        <option value="TI">T.I</option>
                    </select>

                    <label for="documento">Número Documento</label>
                    <input type="text" data-validate="number" name="documento" id="documento" required>

                    <label for="email">Email</label>
                    <input type="email" data-validate="email" name="email" id="email" required>

                    <label for="telefono">Número de Teléfono</label>
                    <input type="text" data-validate="number" name="telefono" id="telefono" required>
                </fieldset>

                <button type="reset" id="btnLimpiar">Limpiar</button>
                <button type="submit" id="btnRegistrar" name="registrarFormVisi">Registrar Visitante</button>
            </form>
        </section>

        <section class="table-card">
            <h3>Consulta Visitantes</h3>
            <div class="Consulta">
                <select class="filtro-visitas">
                    <option>Todos los visitantes</option>
                    <option>Visitas de hoy</option>
                    <option>Pendientes de salida</option>
                    <option>Completadas</option>
                </select>
            </div>

            <table>
                <thead>
                    <tr>
                        <th>Registro</th>
                        <th>Nombre</th>
                        <th>Fecha</th>
                        <th>Torre/Apto</th>
                    </tr>
                </thead>
            </table>

            <div class="empty-state">
                <i class="fa-solid fa-circle-info"></i> No hay visitas programadas próximamente
            </div>
            <button type="button" id="btnEditar">Editar Visita</button>
        </section>
    </main>

    <!-- Scripts -->
    <script src="../assets/Js/visitas.js"></script>
    <?php
    require_once "./Layout/footer.php"
    ?>
</body>

</html>
