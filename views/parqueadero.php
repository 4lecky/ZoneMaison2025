<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}
require_once "./layout/header.php"
?>


<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>ZoneMaisons - Registro Vehículos/Cobro de tarifas</title>
  <link rel="stylesheet" href="../assets/Css/globals.css" />
  <link rel="stylesheet" href="../assets/Css/Layout/header.css" />
  <link rel="stylesheet" href="../assets/Css/Layout/footer.css" />
  <link rel="stylesheet" href="../assets/Css/parqueadero.css" />
  <!-- Libreria de iconos RemixIcon-->
  <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">

  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

</head>

<body>

  <aside class="sidebar">
    <div class="contenedor-botones">

      <!-- Primer bloque: Registro Vehículo -->
      <div class="bloque">
        <img src="../assets/img/RegistroVehiculo.png" alt="Icono Registro Vehículo" class="img_parqueadero" />
        <p class="texto-hover-parqueadero">Registra fácilmente los vehículos y sus propietarios. Llena el formulario, verifica los datos y guarda la información. ¡Ahorra tiempo y mantén un control seguro para toda la comunidad!</p>
        <button class="menu-button" onclick="mostrarFormulario('formularioRegistro')">REGISTRO DE VEHÍCULOS</button>
      </div>

      <!-- Segundo bloque: Cobro Tarifas -->
      <div class="bloque">
        <img src="../assets/img/cobroTarifa.png" alt="Icono Cobro Tarifas" class="img_parqueadero" />
        <p class="texto-hover-parqueadero">Registra y calcula fácilmente el cobro por uso de parqueadero. Ingresa los datos necesarios y genera el valor correspondiente. ¡Agiliza el proceso y mantén un control claro y transparente para todos!</p>
        <button class="menu-button" onclick="mostrarFormulario('formularioCobro')">COBRO TARIFAS</button>
      </div>

    </div>
  </aside>



  <!-- Formulario Consulta Parqueadero -->
  <div class="contenedorConsulta">
    <main id="formularioConsulta" style="display: block;">
      <div class="formulario-container-consulta">

        <h2>CONSULTA DE PARQUEADERO</h2>

        <section class="table-card">
          <h3>Consulta de Parqueaderos</h3>

          <div class="consulta-filtros">
            <label for="filtroParqueadero">Filtrar:</label>
            <select class="filtro-parqueadero" id="filtroParqueadero">
              <option value="todos">Todos los registros</option>
              <option value="ocupados">Parqueaderos ocupados</option>
              <option value="disponibles">Parqueaderos disponibles</option>
              <option value="reservados">Reservados</option>
            </select>
          </div>

          <div class="tabla-responsive">
            <table class="tabla-parqueaderos">
              <thead>
                <tr>
                  <th>#</th>
                  <th>Placa</th>
                  <th>Propietario</th>
                  <th>Num. Parqueadero</th>
                  <th>Estado</th>
                </tr>
              </thead>
              <tbody id="tablaParqueaderoCuerpo">
                <!-- Aquí se insertarán filas dinámicamente -->
              </tbody>
            </table>
          </div>

          <div class="empty-state" id="estadoVacioParqueadero">
            <i class="fa-solid fa-circle-info"></i>
            No hay registros de parqueadero disponibles
          </div>

          <div class="acciones-tabla">
            <button type="button" id="btnEditarParqueadero" class="btn-editar">Editar Registro</button>
          </div>
        </section>

      </div>
    </main>

  </div>





  <!-- Formulario Registro de Vehiculos-->
  <main id="formularioRegistro" style="display: none;"> <!--javascript-->
    <div class="formulario-container-registro">

      <h2>REGISTRO DE VEHICULOS</h2>
      <form action="/ZoneMaison2025/controller/recibirDatosRegistroParqueadero.php" method="POST" class="formulario-registro">


        <fieldset>

          <legend>Formulario Datos Personales Residente/Propietario</legend>
          <label>Email</label>
          <input type="email" name="email" placeholder="Email" required />


          <label>Nombre Completo Residente/Propietario</label>
          <input type="text" name="nombre" placeholder="Nombre Completo" required />

          <div class="input-group">
            <div class="input-box">
              <label>Tipo Doc</label>
              <select type="text" name="tipo_doc" placeholder="Tipo Doc" required>
                <option value="">-- Selecciona un tipo --</option>
                <option value="CC">Cédula de Ciudadanía (CC)</option>
                <option value="TI">Tarjeta de Identidad (TI)</option>
                <option value="CE">Cédula de Extranjería (CE)</option>
                <option value="PA">Pasaporte (PA)</option>
              </select>
            </div>
            <div class="input-box">
              <label>Número Documento</label>
              <input type="text" name="numero_doc" placeholder="Número Documento" required />
            </div>
          </div>

          <div class="input-group">
            <div class="input-box">
              <label>Num. Torre</label>
              <input type="text" name="torre" placeholder="Num. Torre" required />
            </div>
            <div class="input-box">
              <label>Num. Apto</label>
              <input type="text" name="apto" placeholder="Num. Apto" required />
            </div>
          </div>

        </fieldset>

        <fieldset>
          <legend>Dirección Propietario Vehículo</legend>
          <label>Nombre Completo Propietario Vehículo</label>
          <input type="text" name="nombre_propietario_vehiculo" placeholder="Nombre Completo Propietario Vehículo" required />

          <div class="input-group">
            <div class="input-box">
              <label>Tipo Doc</label>
              <select type="text" name="tipo_doc_vehiculo" placeholder="Tipo Doc" required>
                <option value="">-- Selecciona un tipo --</option>
                <option value="CC">Cédula de Ciudadanía (CC)</option>
                <option value="TI">Tarjeta de Identidad (TI)</option>
                <option value="CE">Cédula de Extranjería (CE)</option>
                <option value="PA">Pasaporte (PA)</option>
              </select>
            </div>
            <div class="input-box">
              <label>Número Documento</label>
              <input type="text" name="numero_doc_vehiculo" placeholder="Número Documento" required />
            </div>
          </div>

          <div class="input-group">
            <div class="input-box">
              <label>Placa</label>
              <input type="text" name="placa" placeholder="Placa" required />
            </div>
            <div class="input-box">
              <label>Num. Parqueadero</label>
              <input type="text" name="parqueadero" placeholder="Num Parqueadero" required />
            </div>
          </div>


          <label>Estado de Ingreso</label>
          <select class="listaDesplegable" type="text" name="estado" id="estado" required>
            <option value="">Seleccione el Estado de Ingreso del Vehiculo</option>
            <option value="buenas_Condiciones">Buenas Condiciones</option>
            <option value="malas_condiciones">Malas condiciones</option>
          </select>

          <div class="input-group">
            <div class="input-box">
              <label>Fecha de Ingreso:
                <input type="date" name="fecha_ingreso" placeholder="dd/mm/aaaa" required />
              </label>
            </div>
            <div class="input-box">
              <label>Fecha de Salida:
                <input type="date" name="fecha_salida" placeholder="dd/mm/aaaa" required />
              </label>
            </div>
          </div>
        </fieldset>

        <div class="acciones">
          <button type="reset">Limpiar</button>
          <button type="submit">Enviar Información</button>
        </div>
      </form>
    </div>
  </main>


  <!-- formulario cobro de Tarifas -->

  <main id="formularioCobro" style="display: none;" method="POST">
    <div class="formulario-container-cobro">

      <h2>COBRO TARIFAS</h2>

      <form method="POST" action="#">

        <!-- <form action="../controller/recibirDatosAlquiler.php" method="POST" class="formulario-cobro"> -->
        <!-- Campo oculto para enviar el costo calculado -->
        <input type="hidden" name="costo" id="campoCosto">


        <label>Numero de Recibo</label>
        <input type="text" name="numRecibo" placeholder="Numero de Recibo" required>

        <legend>Datos Personales Residente/Propietario</legend>

        <label>Nombre Completo Residente/Propietario</label>
        <input type="text" name="nombre_residente" placeholder="Nombre completo residente/propietario" required>


        <div class="input-group">
          <div class="input-box">
            <label>Tipo Doc</label>
            <select type="text" name="tipo_doc" placeholder="Tipo Doc" required>
              <option value="">-- Selecciona un tipo --</option>
              <option value="CC">Cédula de Ciudadanía (CC)</option>
              <option value="TI">Tarjeta de Identidad (TI)</option>
              <option value="CE">Cédula de Extranjería (CE)</option>
              <option value="PA">Pasaporte (PA)</option>
            </select>
          </div>
          <div class="input-box">
            <label>Número Documento</label>
            <input type="text" name="num_doc" placeholder="Número Documento" required>
          </div>
        </div>


        <div class="input-group">
          <div class="input-box">
            <label>Num. Torre</label>
            <input type="text" name="torre" placeholder="Num. Torre" required>
          </div>
          <div class="input-box">
            <label>Num. Apto</label>
            <input type="text" name="apartamento" placeholder="Num. Apto" required>
          </div>
        </div>

        <div class="input-group">
          <div class="input-box">
            <label>Placa</label>
            <input type="text" name="placa" placeholder="Placa" required />
          </div>
          <div class="input-box">
            <label>Num. Parqueadero</label>
            <input type="text" name="parqueadero" placeholder="Num Parqueadero" required />
          </div>
        </div>

        <label>Estado de Salida/Observaciones</label>
        <textarea name="observaciones" class="auto-ajustable" placeholder="observaciones estado de salida" required></textarea>

        <div class="input-group">
          <div class="input-box">
            <label>Fecha de Ingreso</label>
            <input type="date" name="fecha_ingreso" placeholder="dd/mm/aaaa" required>
          </div>
          <div class="input-box">
            <label>Fecha de Salida</label>
            <input type="date" name="fecha_salida" placeholder="dd/mm/aaaa" required>
          </div>
        </div>

        <div class="input-group">
          <div class="input-box">
            <label>Hora de Ingreso</label>
            <input type="time" name="hora_ingreso" placeholder="hh:mm" required>
          </div>
          <div class="input-box">
            <label>Hora de Salida</label>
            <input type="time" name="hora_salida" placeholder="hh:mm" required>
          </div>
        </div>







        <div class="acciones">
          <button type="button" id="enviarBtn">Enviar Información</button>
          <button type="reset">Limpiar</button>
        </div>


        </fieldset>
      </form>
    </div>

  </main>




  <!-- Vinculo mensaje ticket sweetAlert2 -->

  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <!-- Contenedor donde se insertará el formulario de consulta -->
  <div id="zonaConsultaFinal"></div>


  <?php
  require_once "./Layout/footer.php"
  ?>


  <script src="../assets/Js/parqueadero.js"></script>
  <script src="../assets/Js/ticketparqueadero.js"></script>

</body>

</html>