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

      <!-- Primer bloque:Parqueadero Propietario -->
      <div class="bloque">
        <img src="../assets/img/ConsultaParqueadero.png" alt="Icono Consulta Parqueadero" class="img_parqueadero" />
        <p class="texto-hover-parqueadero">
          Consulta fácilmente el estado actual de los parqueaderos. Verifica disponibilidad, ocupación y observaciones 
          relacionadas para una mejor organización y control, solo para propietarios.
        </p>
        <button class="menu-button" onclick="mostrarFormulario('formularioPropietario')">CONSULTA PARQUEADERO PROPIETARIOS</button>
      </div>

      <!-- Segundo bloque: Registro Vehículo -->
      <div class="bloque">
        <img src="../assets/img/RegistroVehiculo.png" alt="Icono Registro Vehículo" class="img_parqueadero" />
        <p class="texto-hover-parqueadero">
          Registra fácilmente los vehículos y sus propietarios. Llena el formulario, verifica los datos y guarda la información.
          ¡Ahorra tiempo y mantén un control seguro para toda la comunidad!
        </p>
        <button class="menu-button" onclick="mostrarFormulario('formularioRegistro')">REGISTRO DE VEHÍCULOS</button>
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

    </div>
  </aside>


<!-- Formulario Propietarios Consulta de Parqueaderos--> 
<main id="formularioPropietario" style="display: block;">
  <div class="formulario-container">
    <h2>CONSULTA DE PARQUEADEROS PROPIETARIOS</h2>
    <form action="../controller/recibirDatosConsultaParqueadero.php" method="POST" class="formulario-Propietario">
      <fieldset>
        <legend>Formulario Parqueadero Propietario</legend>

          <div class="input-group">
            <div class="input-box">
              <label for="tipo">Tipo de Vehículo</label>
              <select id="tipo" name="consulParq_tipoVehiculo" required>
                <option value="">-- Selecciona un tipo --</option>
                <option value="Carro">Carro</option>
                <option value="Moto">Moto</option>
              </select>
            </div>

            <div class="input-box">
              <label for="placa">Placa</label>
              <input type="text" id="placa" name="consulParq_placa" maxlength="7" required>
            </div>
          </div>


          <div class="input-box">
            <label for="observaciones">Observaciones / Estado del Vehículo</label>
            <textarea id="observaciones" name="consulParq_observaciones" required></textarea>
          </div>

          <div class="input-box">
            <label>Estado de Ingreso</label>
            <select class="listaDesplegable" name="consulParq_estadoIngreso" id="estadoIngreso" required>
              <option value="">Seleccione el Estado de Ingreso del Vehiculo</option>
              <option value="buenas_Condiciones">Buenas Condiciones</option>
              <option value="malas_Condiciones">Malas Condiciones</option>
            </select>
          </div>

          <h2>Mapa de Parqueaderos</h2>
          <div id="parkingMap"></div>

          <div class="input-box">
            <label>Número de Parqueadero</label>
            <input type="number" name="consulParq_numeroParqueadero" required>
          </div>

          <div class="input-box">
            <label>Estado</label>
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








<!-- Formulario Registro de Vehiculos -->
<main id="formularioRegistro" style="display: block;">
  <div class="formulario-container">
    <h2>REGISTRO DE VEHÍCULOS</h2>

    <form action="../controller/recibirDatosRegistroParqueadero.php" method="POST" class="formulario-registro">
      <fieldset>
        <legend>Datos del Vehículo y Propietario</legend>

        <div class="input-group">
          <div class="input-box">
            <label>Tipo Doc Propietario Vehículo</label>
            <select name="parq_tipo_doc_vehi" required>
              <option value="">-- Selecciona un tipo --</option>
              <option value="CC">Cédula de Ciudadanía (CC)</option>
              <option value="CE">Cédula de Extranjería (CE)</option>
              <option value="PA">Pasaporte (PA)</option>
            </select>
          </div>

          <div class="input-box">
            <label>Número Documento Propietario Vehículo</label>
            <input type="text" name="parq_num_doc_vehi" pattern="\d{6,20}" required />
          </div>
        </div>

        <div class="input-box">
          <label>Nombre Propietario Vehículo</label>
          <input type="text" name="parq_nombre_propietario_vehi" required />
        </div>

        <div class="input-group">
          <div class="input-box">
            <label>Placa</label>
            <input type="text" name="parq_vehi_placa" maxlength="7" pattern="[A-Z0-9]{6}" style="text-transform:uppercase" required />


          </div>
          <div class="input-box">
            <label>N° Parqueadero</label>
            <select name="parq_consulParq_numeroParqueadero">
              <option value="">-- (opcional) Selecciona puesto --</option>
              <?php foreach ($puestos as $p): ?>
                <option value="<?= htmlspecialchars($p['consulParq_numeroParqueadero']) ?>">
                  <?= htmlspecialchars($p['consulParq_numeroParqueadero']) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>

        <div class="input-box">
          <label>Estado de Ingreso</label>
          <select class="listaDesplegable" name="parq_vehi_estadoIngreso" id="estado" required>
            <option value="">Seleccione el Estado de Ingreso del Vehiculo</option>
            <option value="buenas_Condiciones">Buenas Condiciones</option>
            <option value="malas_Condiciones">Malas condiciones</option>
          </select>
        </div>


        <div class="input-group">
          <div class="input-box">
            <label>Fecha de Ingreso:
              <input type="date" name="fecha_ingreso" />
            </label>
          </div>
          <div class="input-box">
            <label>Fecha de Salida:
              <input type="date" name="fecha_salida" />
            </label>
          </div>
        </div>

        <!-- ======= LLAVES FORÁNEAS (autorrelleno) ======= -->
        <div class="input-group">
          <div class="input-box">
            <label>Usuario</label>
            <select name="parq_usuario_cedula">
              <option value="">-- (opcional) Selecciona usuario --</option>
              <?php foreach ($usuarios as $u): 
                $selected = (isset($_SESSION['usu_cedula']) && $_SESSION['usu_cedula']==$u['usu_cedula']) ? 'selected' : '';
              ?>
                <option value="<?= htmlspecialchars($u['usu_cedula']) ?>" <?= $selected ?>>
                  <?= htmlspecialchars($u['usu_cedula']) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="input-box">
            <label>Visita</label>
            <select name="parq_visita_id">
              <option value="">-- (opcional) Selecciona visita --</option>
              <?php foreach ($visitas as $v): ?>
                <option value="<?= htmlspecialchars($v['vis_id']) ?>">
                  <?= htmlspecialchars($v['vis_id']) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>

        <!-- ======= /LLAVES FORÁNEAS ======= -->


        <div class="input-box">
          <label>Observaciones/Estado</label>
          <textarea name="observaciones" id="observaciones" rows="2"></textarea>
        </div>


        <div class="form-buttons">
          <button type="reset">Limpiar</button>
          <button type="submit">Enviar Información</button>
        </div>

      </fieldset>
    </form>
  </div>
</main>




  <!-- Formulario Cobro de Tarifas -->
<div id="formularioCobro" style="display:block;" class="formulario-container">
  <h2>COBRO TARIFAS</h2>
  <form>
    <fieldset>
      <legend>Datos del Residente y Vehículo</legend>

      <div class="input-box">
        <label for="num_recibo">Número de Recibo:</label>
        <input type="text" name="num_recibo" id="num_recibo" required>
      </div>

      <div class="input-group">
        <div class="input-box">
          <label for="tipo_doc">Tipo Doc:</label>
          <select name="tipo_doc" id="tipo_doc" required>
            <option value="">-- Selecciona un tipo --</option>
            <option value="CC">Cédula de Ciudadanía (CC)</option>
            <option value="TI">Tarjeta de Identidad (TI)</option>
            <option value="CE">Cédula de Extranjería (CE)</option>
          </select>
        </div>
        <div class="input-box">
          <label for="num_doc">Número Documento:</label>
          <input type="text" name="num_doc" id="num_doc" required>
        </div>
      </div>

      <div class="input-box">
        <label for="nombre_residente">Nombre Completo Residente/Propietario:</label>
        <input type="text" name="nombre_residente" id="nombre_residente" required>
      </div>

      <div class="input-group">
        <div class="input-box">
          <label for="num_torre">Num. Torre:</label>
          <input type="text" name="num_torre" id="num_torre">
        </div>
        <div class="input-box">
          <label for="num_apto">Num. Apto:</label>
          <input type="text" name="num_apto" id="num_apto">
        </div>
      </div>

      <div class="input-group">
        <div class="input-box">
          <label for="placa">Placa:</label>
          <input type="text" name="placa" id="placa">
        </div>
        <div class="input-box">
          <label for="num_parqueadero">Num. Parqueadero:</label>
          <input type="text" name="num_parqueadero" id="num_parqueadero">
        </div>
      </div>

      <div class="input-box">
        <label for="observaciones">Observaciones / Estado de Salida:</label>
        <textarea name="observaciones" id="observaciones" rows="2"></textarea>
      </div>

      <div class="input-group">
        <div class="input-box">
          <label for="fecha_ingreso">Fecha de Ingreso:</label>
          <input type="date" name="fecha_ingreso" id="fecha_ingreso" required>
        </div>
        <div class="input-box">
          <label for="fecha_salida">Fecha de Salida:</label>
          <input type="date" name="fecha_salida" id="fecha_salida" required>
        </div>
      </div>

      <div class="input-group">
        <div class="input-box">
          <label for="hora_ingreso">Hora de Ingreso:</label>
          <input type="time" name="hora_ingreso" id="hora_ingreso" required>
        </div>
        <div class="input-box">
          <label for="hora_salida">Hora de Salida:</label>
          <input type="time" name="hora_salida" id="hora_salida" required>
        </div>
      </div>

      <input type="hidden" name="costo" id="campoCosto">

    </fieldset>

    <div class="form-buttons">
      <button type="reset">Limpiar</button>
      <button type="submit" id="enviarBtn">Enviar Información</button>
    </div>
  </form>
</div>

  <?php require_once "./Layout/footer.php" ?>


  <script src="../assets/Js/parqueadero.js"></script>
  <script src="../assets/Js/ticketparqueadero.js"></script>
  <script src="../assets/Js/mapaParqueadero.js"></script>

</body>
</html>
