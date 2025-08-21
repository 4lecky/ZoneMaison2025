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

      <!-- Segundo bloque: Cobro Tarifas -->
      <div class="bloque">
        <img src="../assets/img/cobroTarifa.png" alt="Icono Cobro Tarifas" class="img_parqueadero" />
        <p class="texto-hover-parqueadero">
          Registra y calcula fácilmente el cobro por uso de parqueadero. Ingresa los datos necesarios y genera el valor correspondiente.
          ¡Agiliza el proceso y mantén un control claro y transparente para todos!
        </p>
        <button class="menu-button" onclick="mostrarFormulario('formularioCobro')">COBRO TARIFAS</button>
      </div>

      <!-- Tercer bloque:Parqueadero Propietario -->
      <div class="bloque">
        <img src="../assets/img/ConsultaParqueadero.png" alt="Icono Consulta Parqueadero" class="img_parqueadero" />
        <p class="texto-hover-parqueadero">
          Consulta fácilmente el estado actual de los parqueaderos. Verifica disponibilidad, ocupación y observaciones 
          relacionadas para una mejor organización y control, solo para propietarios.
        </p>
        <button class="menu-button" onclick="mostrarFormulario('formularioPropietario')">CONSULTA PARQUEADERO PROPIETARIOS</button>
      </div>


    </div>
  </aside>


  <!-- Formulario Consulta de registro de vehiculo -->
  <!-- CRUD Consulta de Parqueaderos -->
  <div class="crud-container-consulta">
    <main id="crudConsultaParqueadero" style="display: block;">

        <h2>CONSULTA DE PARQUEADEROS</h2>

        <section class="table-card">
          <h3>Consulta de Parqueaderos</h3>

          <div class="input-group">
            <div class="input-box">
              <label>Filtrar:</label>
              <select name="filtro-parqueadero" required>
                <option value="todos">Todos los registros</option>
                <option value="ocupados">Parqueaderos ocupados</option>
                <option value="disponibles">Parqueaderos disponibles</option>
                <option value="reservados">Reservados</option>
              </select>
            </div>
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
    </main>
  </div>


  <!-- Formulario Registro de Vehiculos -->
  <main id="formularioRegistro" style="display: none;">
    <div class="formulario-container-registro">
      <h2>REGISTRO DE VEHÍCULOS</h2>

      <form action="../controller/recibirDatosRegistroParqueadero.php" method="POST" class="formulario-registro">
        <fieldset>
          <legend>Datos del Vehículo y Propietario (BD)</legend>

          <div class="input-group">
            <div class="input-box">
              <label>Tipo Doc Propietario Vehículo</label>
              <!-- BD: parq_tipo_doc_vehi -->
              <select name="parq_tipo_doc_vehi" required>
                <option value="">-- Selecciona un tipo --</option>
                <option value="CC">Cédula de Ciudadanía (CC)</option>
                <option value="CE">Cédula de Extranjería (CE)</option>
                <option value="PA">Pasaporte (PA)</option>
              </select>
            </div>
            <div class="input-box">
              <label>Número Documento Propietario Vehículo</label>
              <!-- BD: parq_num_doc_vehi -->
              <input type="text" name="parq_num_doc_vehi" required />
            </div>
          </div>

          <label>Nombre Propietario Vehículo</label>
          <!-- BD: parq_nombre_propietario_vehi -->
          <input type="text" name="parq_nombre_propietario_vehi" required />

          <div class="input-group">
            <div class="input-box">
              <label>Placa</label>
              <!-- BD: parq_vehi_placa -->
              <input type="text" name="parq_vehi_placa" maxlength="7" required />
            </div>
            <div class="input-box">
              <label>Num. Parqueadero (UI)</label>
              <!-- UI: no existe en tbl_parqueadero -->
              <input type="text" name="parqueadero" />
            </div>
          </div>

          <label>Estado de Ingreso</label>
          <!-- BD: parq_vehi_estadiIngreso -->
          <select class="listaDesplegable" name="parq_vehi_estadiIngreso" id="estado" required>
            <option value="">Seleccione el Estado de Ingreso del Vehiculo</option>
            <option value="buenas_Condiciones">Buenas Condiciones</option>
            <option value="malas_condiciones">Malas condiciones</option>
          </select>

          <!-- UI: fechas informativas, no se guardan en tbl_parqueadero -->
          <div class="input-group">
            <div class="input-box">
              <label>Fecha de Ingreso:
                <input type="date" name="fecha_ingreso" placeholder="dd/mm/aaaa" />
              </label>
            </div>
            <div class="input-box">
              <label>Fecha de Salida:
                <input type="date" name="fecha_salida" placeholder="dd/mm/aaaa" />
              </label>
            </div>
          </div>

          <!-- Llaves foráneas (BD) -->
          <input type="hidden" name="parq_usuario_cedula" value="<?php echo $_SESSION['usu_cedula'] ?? ''; ?>">
          <input type="hidden" name="parq_visita_id" value="">
          <div class="input-group">
            <div class="input-box">
              <label>Observaciones/Estado</label>
              <!-- BD: parq_vehi_alqu_id -->
                <textarea name="observaciones" id="observaciones" rows="2"></textarea>
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




  <!-- Formulario Cobro de Tarifas -->
<div id="formularioCobro" style="display:none;" class="formulario-container-cobro">
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
            <option value="CC">CC</option>
            <option value="TI">TI</option>
            <option value="CE">CE</option>
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

    <div class="acciones">
      <button type="submit" id="enviarBtn">Enviar Información</button>
      <button type="reset">Limpiar</button>
    </div>
  </form>
</div>




<!-- Formulario Propietarios Consulta de Parqueaderos--> 
<main id="formularioPropietario" style="display: none;">
  <div class="formulario-container-Propietario">
    <h2>CONSULTA DE PARQUEADEROS PROPIETARIOS</h2>
    <form action="../controllers/recibirDatosConsultaParqueadero.php" method="POST" class="formulario-Propietario">
      <fieldset>
        <legend>Formulario Parqueadero Propietario</legend>
        <div class="input-group">
          <div class="input-box">
            <label>Tipo de Vehículo</label>
            <select name="tipo_vehiculo" required>
              <option value="">-- Selecciona un tipo --</option>
              <option value="Carro">Carro</option>
              <option value="Moto">Moto</option>
            </select>
          </div>
          <div class="input-box">
            <label>Placa</label>
            <input type="text" name="placa" placeholder="Ej: ABC123" required />
          </div>
        </div>
        <label>Observaciones / Estado del Vehículo</label>
        <textarea name="observaciones" required></textarea>
        <div class="input-box">
          <label>Estado de Ingreso</label>
          <!-- BD: parq_vehi_estadiIngreso -->
          <select class="listaDesplegable" name="parq_vehi_estadiIngreso" id="estado" required>
            <option value="">Seleccione el Estado de Ingreso del Vehiculo</option>
            <option value="buenas_Condiciones">Buenas Condiciones</option>
            <option value="malas_condiciones">Malas condiciones</option>
          </select>
        </div>

          <h2>Mapa de Parqueaderos</h2>
          <div id="parkingMap"></div>

        <div class="input-box">
          <label>Estado</label>
          <select name="estado">
            <option value="">-- Seleccione el estado --</option>
            <option value="ocupado">Ocupado</option>
            <option value="disponible">Disponible</option>
            <option value="mantenimiento">En Mantenimiento</option>
          </select>
        </div>


      </fieldset>
      <div class="acciones">
        <button type="submit">Consultar</button>
        <button type="reset">Limpiar</button>
      </div>
    </form>
  </div>
</main>


  <div id="zonaConsultaFinal"></div>

  <?php require_once "./Layout/footer.php" ?>


  <script src="../assets/Js/parqueadero.js"></script>
  <script src="../assets/Js/ticketparqueadero.js"></script>
  <script src="../assets/Js/mapaParqueadero.js"></script>

</body>
</html>
