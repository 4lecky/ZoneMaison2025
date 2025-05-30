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

</head>

<body>

    <!-- Registro de Visitas -->
    <main class="container mt-4">
        <h2>REGISTRO DE VISITAS</h2>
        <section class="form-card">
            <form id="formVisita" method="post" action="../controller/visitaController.php">
                <fieldset style="border: 1px solid #ccc; padding: 20px;">
                    <legend><strong>Datos Visita</strong></legend>

                    <input type="text" data-validate="number" name="torre" placeholder="Num. Torre Visitada" required>
                    <input type="text" data-validate="number" name="apto" placeholder="Num. Apto Visitado" required><br><br>
                    
                    <label Fecha de Entrada></label>
                    <input type="date" name="fechaEntrada" required>
                    <input type="date" name="fechaSalida" required>

                    <input type="time" name="horaInicio" time-valitime="time" required>
                    <input type="time" name="horaSalida" time-valitime="time" required><br><br>

                </fieldset>

                <button type="reset" id="btnLimpiar">Limpiar</button>
                <button type="submit" id="btnRegistrar" name="registrarFormVisi">Registrar Visitante</button>
            </form>

            <form id="formVisitante" method="post" action="../controller/visitanteController.php">
                <fieldset style="border: 1px solid #ccc; padding: 20px; margin-bottom: 20px;">
                    <legend><strong>Datos Visitante</strong></legend>

                    <input type="text" data-validate="text" name="nombre" placeholder="Nombre Completo"
                        required><br><br>

                    <select name="tipoDoc" required>
                        <option value="">Tipo Doc.</option>
                        <option value="CC">C.C</option>
                        <option value="TI">T.I</option>
                    </select>

                    <input type="text" data-validate="number" name="documento" placeholder="Número Documento"
                        required><br><br>

                    <input type="email" data-validate="email" name="email" placeholder="Email" required><br><br>

                    <input type="telefono" data-validate="number" name="telefono" placeholder="Número de Teléfono"
                        required><br>

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