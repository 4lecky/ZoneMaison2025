<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro</title>
    <link rel="stylesheet" href="../assets/Css/globals.css" />
    <link rel="stylesheet" href="../assets/Css/registro.css"/>  
</head>
<body>


<div class="container_derecho">

    
        <img src="../assets/img/conjuntos.webp" alt="Imagen" class="conjunto">
        <h3 class="texto_superior"> ¡Bienvenido! </h3>
        <i class="texto_superior2">Sera un gusto tenerte con nosotros</i>

</div>

<div class="container_izquierdo">

    <form class="formulario_ingresosRe" id="formulario_ingresosRe" method="post" action="../controller/AuthController.php">

        <h2>Registro usuario</h2> 

        <div class="columnas">
            <div class="campo">
                <label for="NombreUsuario" class="titulo_campo">
                    <i class="fa-solid fa-user"></i> Nombres y apellidos
                </label>
                <input type="text" placeholder="Ingrese la información" class="campos"  name="NombreUsuario" required> 
            </div>

            <div class="campo">
                <label for="NumeroTelefonico" class="titulo_campo">
                    <i class="fa-solid fa-mobile-screen-button"></i> Teléfono
                </label>
                <input type="tel" placeholder="Ingrese su número telefónico" class="campos" name="NumeroTelefonico" required>
            </div>
        </div>

        <div class="columnas">
            <div class="campo">
                <label for="Apartamento" class="titulo_campo">
                    <i class="fa-solid fa-building-user"></i> Apartamento
                </label>
                <input type="text" placeholder="  Ingrese el apartamento donde vive" class="campos" name="Apartamento" required>
            </div>

            <div class="campo">
                <label for="Torre" class="titulo_campo">
                    <i class="fa-solid fa-building"></i>Torre
                </label>
                <input type="text" placeholder="Ingrese donde vive" class="campos" name="Torre" required>
            </div>
        </div>

        <div class="columnas">
            <div class="campo">

                <label for="Parqueadero" class="titulo_campo">
                   <i class="fa-solid fa-square-parking"></i> Parqueadero
                </label>
                <input type="text" placeholder="Ingrese su parqueadero asignado" class="campos" name="Parqueadero" required>

            </div>
            

            <div class="campo">

                <label for="Propiedades" class="titulo_campo">
                <i class="fa-solid fa-file-lines"></i> Propiedades
                </label>
                <input type="text" placeholder="Ingrese su propiedad" class="campos" name="Propiedades" required>

            </div>

        </div>

        <div class="columnas">

            <div class="campo">

            <label for="Rol" class="titulo_campo">Rol</label>
                <select class="campos"  id="validationCustom04" name="Rol" >

                    <option>Elija una opción</option>
                    <option> Administrador </option>
                    <option> Residente </option>
                    <option> Propietario </option>
                    <option> Vigilante </option>

                </select>
                
            </div>



            <div class="campo">
                <label for="Email" class="titulo_campo">
                    <i class="fa-solid fa-envelope"></i> Correo electronico
                </label>
                <input type="email" placeholder="Defina una contraseña" class="campos" name="Email" required>
            </div> 

        </div>

        
        <div class="columnas">
            
            <div class="campo">
                <label for="Password" class="titulo_campo">
                    <i class="fa-solid fa-lock"></i> Contraseña
                </label>
                <input type="password" placeholder="Defina una contraseña" class="campos" name="Password" required>
            </div>
        </div>


        <div class="texto_inf">

            <a href="./login.php" class="text_usu"> ¿Ya tiene una cuenta? De click aqui </a>

        </div>


        <div class="contenedor_btn">

            <button type="submit" id="btn_incio" name="registrar">Registrarse</button>
        
        </div>

    </form> 

</div>

<script src="../assets/Js/singnUp.js"></script>
<script src="https://kit.fontawesome.com/dbd1801b06.js" crossorigin="anonymous"></script>


</body>
</html>