<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

     <!-- Estilos -->
    <link rel="stylesheet" href="../assets/Css/globals.css"/>
    <link rel="stylesheet" href="../assets/Css/Layout/header.css"/>
    <link rel="stylesheet" href="../assets/Css/Layout/nav.css"/>
    <link rel="stylesheet" href="../assets/Css/Layout/footer.css"/>

    <!-- iconos -->
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
 
</head>
<body>

    <header >

            <div class="logo-ZM" >
                <img class="Logo" src="../assets/img/LogoZM.png" alt="Imagen Logo">
            </div>

            <div class="Titulo">
                <img class="LogoT" src="../assets/img/tituloCentro.png" alt="Titulo/Zm">
            </div>

            <button class="menu" id="menu_toggle">
    
                <i class="ri-arrow-down-s-line" id="icon_open" style="font-size: 50px; color:black;"></i>
                <i class="ri-arrow-up-s-line" id="icon_close"  style="display:none; font-size:50px; "></i>

            </button>

    </header>

    <!-- <main>      -->
            <nav class="nav_expanded" id="main_nav"> 

                <a href="./home.php" class="nav-link"> Inicio </a>
                <a href="./parqueadero" class="nav-link"> Parqueaderos </a>
                <a href="./reserva1.php" class="nav-link">Reservas</a>
                <a href="./pqrs.php" class="nav-link"> PQRS </a>
                <a href="./novedades.php" class="nav-link"> Notificaciones </a>
                <a href="./crud.php" class="nav-link"> Usuarios </a>
                <a href="./visitas.php" class="nav-link"> visitas </a>
            </nav>

    <!-- </main> -->

    
    <script src="https://kit.fontawesome.com/dbd1801b06.js" crossorigin="anonymous"></script>
    <script src="../../assets/Js/header.js"></script>
</body>
</html>
