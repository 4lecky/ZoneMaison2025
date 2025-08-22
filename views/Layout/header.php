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

            <button class="btns_header" onclick="window.location.href='../views/logout.php'"> <i class="ri-logout-box-r-line"></i> Cerrar sesión</button>
            <button class="btns_header" onclick="window.location.href='../controller/perfilUsuarioController.php'"><i class="ri-user-3-fill"></i> Mi cuenta </button>

        </div>

    </header>
    <nav class="nav_expanded" id="main_nav"> 
        <a href="../views/novedades.php" class="nav-link"> Notificaciones </a>
        <a href="../views/reserva2.php" class="nav-link">Reservas</a>
        <a href="../views/visitas.php" class="nav-link"> visitas </a>
        <a href="../views/parqueadero.php" class="nav-link"> Parqueaderos </a>
        <?php if (in_array($_SESSION['usuario']['rol'] ?? '', ['Administrador', 'Vigilante'], true)): ?>
            <a href="../views/crud.php" class="nav-link"> Usuarios </a>
        <?php endif;?>
        <a href="../views/pqrs.php" class="nav-link"> PQRS </a>


    </nav>
</div>
