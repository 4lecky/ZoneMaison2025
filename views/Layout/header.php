<?php

$rol = $_SESSION['usuario']['rol'] ?? '';


?>


<div id="ColorHeader"> 
    <header>
        <div class="logo-ZM">
            <img class="Logo" src="../assets/img/LogoZM.png" alt="Imagen Logo" onclick="window.location.href='../views/novedades.php'">
        </div>

        <div class="Titulo">
            <img class="LogoT" src="../assets/img/tituloCentro.png" alt="Titulo/Zm">
        </div>

        <div class="contenedor_btn_header">
            <button class="btns_header" onclick="window.location.href='../views/logout.php'">
                <i class="ri-logout-box-r-line"></i>
                <span class="btns_texto"> Cerrar sesión </span>
            </button>
            <button class="btns_header" onclick="window.location.href='../controller/perfilUsuarioController.php'">
                <i class="ri-user-3-fill"></i>
                <span class="btns_texto"> Mi cuenta </span>
            </button>
        </div>

    </header>
    <nav class="nav_expanded" id="main_nav"> 
        <a href="../views/novedades.php" class="nav-link"> Notificaciones </a>
        <a href="../views/reservas.php" class="nav-link"> Reservas </a>
        <?php if (in_array($_SESSION['usuario']['rol'] ?? '', ['Administrador', 'Vigilante'], true)): ?>
        <a href="../views/visitas.php" class="nav-link"> Visitas </a>
        <?php endif;?>
        <?php if (in_array($_SESSION['usuario']['rol'] ?? '', ['Administrador', 'Vigilante'], true)): ?>
        <a href="../views/parqueadero.php" class="nav-link"> Parqueaderos </a>
        <?php endif;?>
        <?php if (in_array($_SESSION['usuario']['rol'] ?? '', ['Administrador', 'Vigilante'], true)): ?>
        <a href="../views/crud.php" class="nav-link"> Usuarios </a>
        <?php endif;?>
        <?php if (in_array($_SESSION['usuario']['rol'] ?? '', ['Administrador'], true)): ?>
        <a href="../views/pqrs_admin.php" class="nav-link"> PQRS </a>
        <?php elseif (in_array($_SESSION['usuario']['rol'] ?? '', ['Residente', 'Propietario', 'Vigilante'], true)): ?>
        <a href="../views/pqrs.php" class="nav-link"> PQRS </a>
        <?php endif; ?>



    </nav>
</div>
