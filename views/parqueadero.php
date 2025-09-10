<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}
require_once "./Layout/header.php"
?>


<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>ZoneMaisons - Registro Vehículos / Cobro de tarifas</title>
  <link rel="stylesheet" href="../assets/Css/globals.css" />
  <link rel="stylesheet" href="../assets/Css/Layout/header.css" />
  <link rel="stylesheet" href="../assets/Css/Layout/footer.css" />
  <link rel="stylesheet" href="../assets/Css/parqueadero.css" />
  <link rel="stylesheet" href="../assets/Css/mapaParqueadero.css" />
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
        <p class="texto-hover-parqueadero">
          Registra fácilmente los vehículos y sus propietarios. Llena el formulario, verifica los datos y guarda la información.
          ¡Ahorra tiempo y mantén un control seguro para toda la comunidad!
        </p>
        <button class="menu-button" onclick="mostrarFormulario('formularioRegistro')">REGISTRO DE VEHÍCULOS</button>
      </div>

      <!-- Segundo bloque:Parqueadero Propietario -->
      <div class="bloque">
        <img src="../assets/img/ConsultaParqueadero.png" alt="Icono Consulta Parqueadero" class="img_parqueadero" />
        <p class="texto-hover-parqueadero">
          Consulta fácilmente el estado actual de los parqueaderos. Verifica disponibilidad, ocupación y observaciones 
          relacionadas para una mejor organización y control, solo para propietarios.
        </p>
        <button class="menu-button" onclick="mostrarFormulario('formularioPropietario')">CONSULTA PARQUEADERO PROPIETARIOS</button>
      </div>

      <!-- Tercer bloque: Cobro Tarifas -->
      <div class="bloque">
        <img src="../assets/img/cobroTarifa.png" alt="Icono Cobro Tarifas" class="img_parqueadero" />
        <p class="texto-hover-parqueadero">
          Registra y calcula fácilmente el cobro por uso de parqueadero. Ingresa los datos necesarios y genera el valor correspondiente.
          ¡Agiliza el proceso y mantén un control claro y transparente para todos!
        </p>
        <button class="menu-button" onclick="mostrarFormulario('formularioCobro')">COBRO TARIFAS</button>
      </div>


      <!-- Botón Consultar Parqueaderos -->
      <!-- <div class="bloque" onclick="window.location.href='parqueadero_crud.php'">
          <div class="boton consulta">
              Consultar Parqueaderos
          </div>
      </div> -->

    </div>
  </aside>







<!-- Formulario Registro de Vehiculos -->
<main id="formularioRegistro" style="display: none;">
  <div class="formulario-container">
    <h2>REGISTRO DE VEHÍCULOS</h2>

    <form action="../controller/recibirDatosRegistroParqueadero.php" method="POST" class="formulario-registro">
      <fieldset>
        <legend>Datos del Vehículo y Propietario</legend>

        <div class="input-group">
          <div class="input-box">
            <label>Tipo Doc Propietario Vehículo*</label>
            <select name="parq_tipo_doc_vehi" required>
              <option value="">-- Selecciona un tipo --</option>
              <option value="CC">Cédula de Ciudadanía (CC)</option>
              <option value="CE">Cédula de Extranjería (CE)</option>
              <option value="PA">Pasaporte (PA)</option>
            </select>
          </div>

          <div class="input-box">
            <label>Número Documento Propietario Vehículo*</label>
            <input type="text" name="parq_num_doc_vehi" required />
          </div>
        </div>

        <div class="input-box">
          <label>Nombre Propietario Vehículo*</label>
          <input type="text" name="parq_nombre_propietario_vehi" required />
        </div>

        <div class="input-group">
          <div class="input-box">
            <label>Placa*</label>
            <input type="text" name="parq_vehi_placa" required />
          </div>

          <div class="input-box">
            <label>N° Parqueadero*</label>
            <input type="number" name="parq_numeroParqueadero" required />
          </div>
        </div>


        <div class="input-box">
          <label>Observaciones/Estado de Entrada*</label>
          <textarea name="parq_vehi_estadoIngreso" id="estado"></textarea>
        </div>


        <div class="input-group">
          <div class="input-box">
            <label>Fecha de Ingreso*
              <input type="date" name="parq_fecha_entrada" />
            </label>
          </div>
          <div class="input-box">
            <label>Fecha de Salida*
              <input type="date" name="parq_fecha_salida" />
            </label>
          </div>
        </div>


        <div class="input-box">
          <label>Hora de Ingreso*</label>
          <input type="time" name="parq_hora_entrada" />
        </div>




        <div class="form-buttons">
          <button type="reset">Limpiar</button>
          <button type="submit">Enviar Información</button>
        </div>

      </fieldset>
    </form>
  </div>
</main>




<!-- Formulario Propietarios Consulta de Parqueaderos--> 
<main id="formularioPropietario" style="display: none;">
  <div class="formulario-container">
    <h2>CONSULTA DE PARQUEADEROS PROPIETARIOS</h2>
    <form action="../controller/recibirDatosConsultaParqueadero.php" method="POST" class="formulario-Propietario">
      <fieldset>
        <legend>Formulario Parqueadero Propietario*</legend>

          <div class="input-group">
            <div class="input-box">
              <label for="tipo">Tipo de Vehículo*</label>
              <select id="tipo" name="consulParq_tipoVehiculo" required>
                <option value="">-- Selecciona un tipo --</option>
                <option value="Carro">Carro</option>
                <option value="Moto">Moto</option>
              </select>
            </div>

            <div class="input-box">
              <label for="placa">Placa*</label>
              <input type="text" id="placa" name="consulParq_placa" required>
            </div>
          </div>


          <div class="input-box">
            <label for="observaciones">Observaciones / Estado del Vehículo*</label>
            <textarea id="observaciones" name="consulParq_observaciones" required></textarea>
          </div>


          <h2>Mapa de Parqueaderos</h2>
          <div class="mapa-parq">
            <div id="parkingMap"></div>
          </div>

          <div class="input-box">
            <label>Número de Parqueadero*</label>
            <input type="number" name="consulParq_numeroParqueadero" required>
          </div>

          <div class="input-box">
            <label>Estado del Parqueadero*</label>
            <select name="consulParq_estado" required>
              <option value="">-- Seleccione el estado --</option>
              <option value="ocupado">Ocupado</option>
              <option value="disponible">Disponible</option>
              <option value="mantenimiento">En Mantenimiento</option>
            </select>
          </div>

      </fieldset>

      <div class="form-buttons">
        <button type="reset" class="limpiar">Limpiar</button>
        <button type="submit">Enviar Información</button>
      </div>
    </form>
  </div>
</main>

<div id="zonaConsultaFinal"></div>








<!-- Formulario Cobro de Tarifas -->
<main id="formularioCobro" style="display:none;">
  <div class="formulario-container">
    <h2>COBRO TARIFAS</h2>
    <form action="../controller/recibirDatosAlquiler.php" method="POST" class="formulario-Cobro">
      <fieldset>
        <legend>Datos del Residente y Vehículo</legend>

        <div class="input-box">
          <label for="num_recibo">Número de Recibo*</label>
          <input type="text" name="alqu_num_recibo" id="num_recibo" required>
        </div>

        <div class="input-group">
          <div class="input-box">
            <label for="tipo_doc">Tipo Doc*</label>
            <select name="alqu_tipo_doc_vehi" id="tipo_doc" required>
              <option value="">-- Selecciona un tipo --</option>
              <option value="CC">Cédula de Ciudadanía (CC)</option>
              <option value="TI">Tarjeta de Identidad (TI)</option>
              <option value="CE">Cédula de Extranjería (CE)</option>
            </select>
          </div>
          <div class="input-box">
            <label for="num_doc">Número Documento*</label>
            <input type="text" name="alqu_num_doc_vehi" id="num_doc" required>
          </div>
        </div>

        <div class="input-box">
          <label for="nombre_residente">Nombre Completo Residente/Propietario*</label>
          <input type="text" name="alqu_nombre_propietario" id="nombre_residente" required>
        </div>

        <div class="input-group">
          <div class="input-box">
            <label for="num_torre">Num. Torre*:</label>
            <input type="text" name="alqu_torre" id="num_torre" required>
          </div>
          <div class="input-box">
            <label for="num_apto">Num. Apto*</label>
            <input type="text" name="alqu_apartamento" id="num_apto" required>
          </div>
        </div>

        <div class="input-group">
          <div class="input-box">
            <label for="placa">Placa*</label>
            <input type="text" name="alqu_placa" id="placa" required>
          </div>
          <div class="input-box">
            <label for="num_parqueadero">Num. Parqueadero*</label>
            <input type="number" name="alqu_numeroParqueadero" id="num_parqueadero">
          </div>
        </div>

        <div class="input-box">
          <label for="observaciones">Observaciones / Estado de Salida*</label>
          <textarea name="alqu_estadoSalida" id="observaciones" rows="2" required></textarea>
        </div>

        <div class="input-group">
          <div class="input-box">
            <label for="fecha_ingreso">Fecha de Ingreso*</label>
            <input type="date" name="alqu_fecha_entrada" id="fecha_ingreso" required>
          </div>
          <div class="input-box">
            <label for="fecha_salida">Fecha de Salida*</label>
            <input type="date" name="alqu_fecha_salida" id="fecha_salida" required>
          </div>
        </div>

  
          <div class="input-box">
            <label for="hora_salida">Hora de Salida*</label>
            <input type="time" name="alqu_hora_salida" id="hora_salida" required>
          </div>


        <input type="hidden" name="costo" id="campoCosto">
        <!-- <input type="hidden" name="alqu_precio" id="campoCosto"> -->


      </fieldset>

      <div class="form-buttons">
        <button type="reset">Limpiar</button>
        <button type="submit" id="enviarBtn">Enviar Información</button>
      </div>
    </form>
  </div>
</main>

  <?php require_once "./Layout/footer.php" ?>


  <script src="../assets/Js/parqueadero.js"></script>
  <script src="../assets/Js/ticketparqueadero.js"></script>
  <script src="../assets/Js/mapaParqueadero.js"></script>

</body>
</html>
