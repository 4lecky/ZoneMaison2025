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
    <link rel="stylesheet" href="../assets/Css/visitas.css" />
</head>

<body>

    <!-- Registro de Visitas -->
    <main class="container mt-4">
        <h2>REGISTRO DE VISITAS</h2>

        <div class="principal-page">
            
            <form method="POST" action="../controller/RegistrarVisitaController.php" id="formVisitante">
                <fieldset>
                    <legend>Datos del Visitante</legend>

                    <div class="input-group">
                        <div class="input-box">
                        <label for="tipo_doc">Tipo de Documento</label>
                        <select class="form-control" name="tipo_doc" id="tipo_doc" required>
                            <option selected disabled>Seleccione el Tipo de Documento</option>
                            <option value="C.C.">C.C.</option>
                            <option value="T.I.">T.I.</option>
                            <option value="C.E.">C.E.</option>
                        </select>
                        </div>

                        <div class="input-box">
                        <label for="numero_doc">Número de Documento</label>
                        <input type="text" class="form-control" name="numero_doc" id="numero_doc" placeholder="Número Documento" required>
                        </div>
                    </div>

                    <div class="input-group">
                        <div class="input-box">
                        <label for="nombre">Nombre del Visitante</label>
                        <input type="text" class="form-control" name="nombre" id="nombre" placeholder="Nombre completo" required>
                        </div>

                        <div class="input-box">
                        <label for="correo">Correo Electrónico</label>
                        <input type="email" class="form-control" name="correo" id="correo" placeholder="correo@ejemplo.com">
                        </div>
                    </div>

                    <div class="input-group">
                        <div class="input-box">
                        <label for="telefono">Teléfono</label>
                        <input type="text" class="form-control" name="telefono" id="telefono" placeholder="Número de teléfono">
                        </div>
                    </div>
                </fieldset>

                
                <fieldset>
                    <legend>Datos de la Visita</legend>

                    <div class="input-group">
                        <div class="input-box">
                        <label for="fechaEntrada">Fecha de Entrada</label>
                        <input type="date" class="form-control" name="fechaEntrada" id="fechaEntrada" data-validate="date" required>
                        </div>

                        <div class="input-box">
                        <label for="horaEntrada">Hora de Entrada</label>
                        <input type="time" class="form-control" name="horaEntrada" id="horaEntrada" data-validate="hora" required>
                        </div>
                    </div>

                    <div class="input-group">
                        <div class="input-box">
                        <label for="fechaSalida">Fecha de Salida</label>
                        <input type="date" class="form-control" name="fechaSalida" id="fechaSalida" data-validate="date" required>
                        </div>

                        <div class="input-box">
                        <label for="horaSalida">Hora de Salida</label>
                        <input type="time" class="form-control" name="horaSalida" id="horaSalida" data-validate="hora" required>
                        </div>
                    </div>

                    <div class="input-group">
                        <div class="input-box">
                        <label for="torreVisitada">Número de Torre</label>
                        <input type="number" class="form-control" name="torreVisitada" id="torreVisitada" placeholder="Ej: Torre 3" data-validate="telefono" min="1" required>
                        </div>

                        <div class="input-box">
                        <label for="aptoVisitado">Número de Apartamento</label>
                        <input type="number" class="form-control" name="aptoVisitado" id="aptoVisitado" placeholder="Ej: 302" data-validate="telefono" min="1" required>
                        </div>
                    </div>

                    <div class="input-group">
                        <div class="input-box">
                        <label for="usuario">Cédula del Residente</label>
                        <input type="number" class="form-control" name="usuario" id="usuario" placeholder="Ej: 1234567890" data-validate="telefono" min="1" required>
                        </div>
                    </div>
                </fieldset>

                <!-- Botones -->
                <div class="input-group" style="justify-content: center;">

                <!-- onclick="window.location.href='visita.php';" -->
 
                    <button type="submit" class="Enviar"  id="btnRegistrar" name="registrarFormVisi">Registrar Visita</button>
                    
                    <button type="reset" class="Cancelar" id="btnLimpiar">Limpiar</button>
                </div>
            </form> 
        </div>


        <section class="table-card">
        <h3>Consulta de Visitantes</h3>

        <div class="consulta-filtros">
            <label for="filtroVisitas">Filtrar:</label>
            <select class="filtro-visitas" id="filtroVisitas">
            <option value="todos">Todos los visitantes</option>
            <option value="hoy">Visitas de hoy</option>
            <option value="pendientes">Pendientes de salida</option>
            <option value="completadas">Completadas</option>
            </select>
        </div>

        <div class="tabla-responsive">
            <table class="tabla-visitas">
            <thead>
                <tr>
                <th>Registro</th>
                <th>Nombre</th>
                <th>Fecha</th>
                <th>Torre</th>
                <th>Apartamento</th>
                </tr>
            </thead>
            <tbody id="tablaCuerpo">
                <!-- Aquí se insertarán filas dinámicamente -->
            </tbody>
            </table>
        </div>

        <div class="empty-state" id="estadoVacio">
            <i class="fa-solid fa-circle-info"></i>
            No hay visitas programadas próximamente
        </div>

        <div class="acciones-tabla">
            <button type="button" id="btnEditar" class="btn-editar">Editar Visita</button>
        </div>
        </section>
    </main>

    <!-- Scripts -->
    <script src="../assets/Js/visitas.js"></script>
    <?php
    require_once "./Layout/footer.php"
    ?>
</body>

</html>
