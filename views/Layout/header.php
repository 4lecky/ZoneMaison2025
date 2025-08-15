<?php

$rol = $_SESSION['usuario']['rol'] ?? '';


?>


<div id="ColorHeader"> 
    <header>
        <div class="logo-ZM">
            <img class="Logo" src="../assets/img/LogoZM.png" alt="Imagen Logo">
        </div>

        <div class="Titulo">
            <img class="LogoT" src="../assets/img/tituloCentro.png" alt="Titulo/Zm">
        </div>

        <div class="contenedor_btn_header">

            <button class="btns_header" onclick="window.location.href='logout.php'"> <i class="ri-logout-box-r-line"></i> Cerrar sesión</button>
            <button class="btns_header" onclick="window.location.href='perfil_usuario.php'"><i class="ri-user-3-fill"></i> Mi cuenta </button>

        </div>

    </header>
    <nav class="nav_expanded" id="main_nav"> 
        <a href="./novedades.php" class="nav-link"> Notificaciones </a>
        <a href="./reserva2.php" class="nav-link">Reservas</a>
        <a href="./visitas.php" class="nav-link"> visitas </a>
        <a href="./parqueadero.php" class="nav-link"> Parqueaderos </a>
        <?php if (in_array($_SESSION['usuario']['rol'] ?? '', ['Administrador', 'Vigilante'], true)): ?>
            <a href="./crud.php" class="nav-link"> Usuarios </a>
        <?php endif;?>
        <a href="./pqrs.php" class="nav-link"> PQRS </a>


    </nav>
</div>
