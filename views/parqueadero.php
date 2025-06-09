
<?php 
require_once "./layout/header.php"
?>


<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>ZoneMaisons - Registro Vehículos/Cobro de tarifas</title>
  <link rel="stylesheet" href="../assets/Css/globals.css" />
  <link rel="stylesheet" href="../assets/Css/Layout/header.css" />
  <link rel="stylesheet" href="../assets/Css/Layout/footer.css" />
  <link rel="stylesheet" href="../assets/Css/parqueadero.css" />

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



    <!-- Formulario Registro de Vehiculos-->
    <main id="formularioRegistro" style="display: none;"> <!--javascript-->
      <div class="formulario-container-registro">

          <h2>REGISTRO DE VEHICULOS</h2>
          <form action="procesarFormulario.php" method="POST" class="formulario-registro">


            <fieldset>

              <legend>Formulario Datos Personales Residente/Propietario</legend>
              <label>Email</label>
              <input type="email" name="email" placeholder="Email" required />
              
              
              <label>Nombre Completo Residente/Propietario</label>
              <input type="text" name="nombre" placeholder="Nombre Completo" required />

                <div class="input-group">
                  <div class="input-box">
                    <label>Tipo Doc</label>
                    <input type="text" name="tipo_doc" placeholder="Tipo Doc" required />
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
                    <input type="text" name="tipo_doc_vehiculo" placeholder="Tipo Doc" required />
                  </div>
                  <div class="input-box">
                    <label>Número Documento</label>
                    <input type="text" name="numero_doc_vehciulo" placeholder="Número Documento" required />
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
              <button type="submit">Guardar Cambios</button>
              <button type="reset">Limpiar</button>
              <button type="submit">Enviar Información</button>
            </div>
          </form>
      </div>
    </main>
    
  
<!-- formulario cobro de Tarifas -->

      <main id="formularioCobro" style="display: none;">
        <div class="formulario-container-cobro">

            <h2>COBRO TARIFAS</h2>
        
          
              <form action="../controller/calculoParqueadero.php" method="POST">
                <fieldset>

                  <label>Numero de Recibo</label>
                  <input type="text" name="numRecibo" placeholder="Numero de Recibo" required>

                  <legend>Datos Personales Residente/Propietario</legend>
          
                  <label>Nombre Completo Residente/Propietario</label>
                  <input type="text" name="nombre_residente" placeholder="Nombre completo residente/propietario" required>
          

                  <div class="input-group">
                      <div class="input-box">
                          <label>Tipo Doc</label>
                          <input type="text" name="tipo_doc" placeholder="Tipo Doc" required>
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
          
                  <label>Num. Parqueadero</label>
                  <input type="text" name="num_parqueadero" placeholder="Num. Parqueadero" required>
          
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
                      <button type="submit" id="enviarBtn">Enviar Información</button>
                      <button type="reset">Limpiar</button>
                  </div>

                </fieldset>
              </form>
          </div>

      </main>


          

      <!-- Botón que lanza SweetAlert -->
      <!-- <button >Enviar Solicitud</button> -->

      <script>

        document.getElementById('enviarBtn').addEventListener('click', function () {
          Swal.fire({
            title: '¿Estás seguro?',
            text: "¿Deseas enviar la solicitud PQRS?",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí, enviar',
            cancelButtonText: 'Cancelar'
          }).then((result) => {
            if (result.isConfirmed) {
              Swal.fire(
                '¡Enviado!',
                'Tu PQRS fue registrada correctamente.',
                'success'
              );
            }
          });
        });

      </script>




<?php 
require_once "./Layout/footer.php"
?>


<script src="../assets/Js/parqueadero.js"></script>

</body>
</html>
