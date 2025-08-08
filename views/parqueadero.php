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



  <!-- Formulario Registro de Vehiculos-->
  <main id="formularioRegistro" style="display: none;">
    <div class="formulario-container-registro">

      <h2>REGISTRO DE VEHÍCULOS</h2>
      <form action="../controller/recibirDatosRegistroParqueadero.php" method="POST" class="formulario-registro">



        <fieldset>

          <legend>Formulario Datos Personales Residente/Propietario</legend>
          <label>Email</label>
          <input type="email" name="email" placeholder="Email" required />

          <div class="input-group">
            <div class="input-box">
              <label>Tipo Doc</label>
              <select type="text" name="tipo_doc" placeholder="Tipo Doc" required>
                <option value="">-- Selecciona un tipo --</option>
                <option value="CC">Cédula de Ciudadanía (CC)</option>
                <option value="CE">Cédula de Extranjería (CE)</option>
                <option value="PA">Pasaporte (PA)</option>
              </select>
            </div>
            <div class="input-box">
              <label>Número Documento</label>
              <input type="text" name="numero_doc" placeholder="Número Documento" required />
            </div>
          </div>







          <label>Nombre Completo Residente/Propietario</label>
          <input type="text" name="nombre" placeholder="Nombre Completo" required />


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
          <legend>Datos del Vehículo y Propietario</legend>

          <div class="input-group">
            <div class="input-box">
              <label>Tipo Doc Propietario Vehículo</label>
              <select name="tipo_doc_vehiculo" required>
                <option value="">-- Selecciona un tipo --</option>
                <option value="CC">Cédula de Ciudadanía (CC)</option>
                <option value="CE">Cédula de Extranjería (CE)</option>
                <option value="PA">Pasaporte (PA)</option>
              </select>
            </div>
            <div class="input-box">
              <label>Número Documento</label>
              <input type="text" name="numero_doc_vehiculo" required />
            </div>
          </div>

          <label>Nombre Propietario Vehículo</label>
          <input type="text" name="nombre_propietario_vehiculo" required />

          <div class="input-group">
            <div class="input-box">
              <label>Placa</label>
              <input type="text" name="placa" required />
            </div>
            <div class="input-box">
              <label>Num. Parqueadero</label>
              <input type="text" name="parqueadero" required />
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



          <!-- Llaves foráneas -->
          <input type="hidden" name="usuario_cedula" value="<?php echo $_SESSION['usu_cedula'] ?? ''; ?>">
          <input type="hidden" name="visita_id" value="<!-- valor dinámico visita -->">
        </fieldset>

        <div class="acciones">
          <button type="reset">Limpiar</button>
          <button type="submit">Enviar Información</button>
        </div>
      </form>
    </div>
  </main>


  <!-- Formulario Cobro de Tarifas -->
  <main id="formularioCobro" style="display: none;">
    <div class="formulario-container-cobro">

      <h2>COBRO TARIFAS</h2>

      <form method="POST" action="../controller/recibirDatosAlquiler.php">

        <!-- Campo oculto para el costo calculado -->
        <input type="hidden" name="costo" id="campoCosto">

        <label>Número de Recibo</label>
        <input type="text" name="numRecibo" required>


      
        <div class="input-group">
          <div class="input-box">
            <label>Tipo Doc</label>
            <select type="text" name="tipo_doc" placeholder="Tipo Doc" required>
              <option value="">-- Selecciona un tipo --</option>
              <option value="CC">Cédula de Ciudadanía (CC)</option>
              <option value="CE">Cédula de Extranjería (CE)</option>
              <option value="PA">Pasaporte (PA)</option>
            </select>
          </div>
          <div class="input-box">
            <label>Número Documento</label>
            <input type="text" name="num_doc" placeholder="Número Documento" required>
          </div>
        </div>





        <label>Nombre Completo Residente/Propietario</label>
        <input type="text" name="nombre_residente" placeholder="Nombre completo residente/propietario" required>


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
            <input type="text" name="placa" required />
          </div>
          <div class="input-box">
            <label>Num. Parqueadero</label>
            <input type="text" name="parqueadero" required />
          </div>
        </div>

        <label>Observaciones / Estado de Salida</label>
        <textarea name="observaciones" required></textarea>

        <div class="input-group">
          <div class="input-box">
            <label>Fecha de Ingreso</label>
            <input type="date" name="fecha_ingreso" required>
          </div>
          <div class="input-box">
            <label>Fecha de Salida</label>
            <input type="date" name="fecha_salida" required>
          </div>
        </div>

        <div class="input-group">
          <div class="input-box">
            <label>Hora de Ingreso</label>
            <input type="time" name="hora_ingreso" required>
          </div>
          <div class="input-box">
            <label>Hora de Salida</label>
            <input type="time" name="hora_salida" required>
          </div>
        </div>

        <!-- Llaves foráneas -->
        <input type="hidden" name="usuario_cedula" value="<?php echo $_SESSION['usu_cedula'] ?? ''; ?>">
        <input type="hidden" name="visita_id" value="<!-- valor dinámico visita -->">

        <div class="acciones">
          <button type="button" id="enviarBtn">Enviar Información</button>
          <button type="reset">Limpiar</button>
        </div>

      </form>
    </div>
  </main>




<!-- Formulario Propietarios Consulta de Parqueaderos--> 
<main id="formularioPropietario" style="display: none;">
  <div class="formulario-container-Propietario">
    <h2>CONSULTA DE PARQUEADEROS PROPIETARIOS</h2>
    <form action="#" method="POST" class="formulario-Propietario">
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
        <label>Observaciones / Estado de Salida</label>
        <textarea name="observaciones" required></textarea>
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

</body>
</html>
