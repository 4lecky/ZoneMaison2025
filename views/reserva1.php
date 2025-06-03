<?php

require_once './Layout/header.php'
?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ZONEMAISONS - Áreas Comunes</title>
    <link rel="stylesheet" href="../assets/css/areas-comunes/reserva1.css">
    <link rel="stylesheet" href="../assets/Css/globals.css" />
    <link rel="stylesheet" href="../assets/Css/Layout/header.css" />
    <link rel="stylesheet" href="../assets/Css/Layout/footer.css" />

</head>
<body>
    <!-- <div class="header">
        <div class="logo-container">
            <img src="../assets/img/LogoZM.jpg" alt="Logo" class="logo">
        </div>
        
        <div class="brand-container">
            <div class="brand-name">
                <span class="zone">ZONE</span><span class="maisons">MAISONS</span>
            </div>
            <div class="underline"></div>
        </div>
        
        <button class="menu-button">☰</button>
    </div>
    
    <nav class="nav-bar">
        <a href="../index.php" class="nav-item active">Inicio</a>
        <a href="./visitas.php" class="nav-item">Visitantes</a>
        <a href="./reserva1.php" class="nav-item">Reservas</a>
        <a href="./pqrs.php" class="nav-item">Pqrs</a>
    </nav> -->
    
    <main>
        <h2>Áreas Comunes</h2>
        
        <div class="areas-container">
            <div class="area-card">
                <a href="zona-comun1.php">
                    <img src="../assets/img/salon-comunal.jpg" alt="Salon Comunal">
                    <h3>Salón Comunal</h3>
                </a>
            </div>
            
            <div class="area-card">
                <a href="zona-comun1.php">
                    <img src="../assets/img/piscina.jpg" alt="Piscina">
                    <h3>Piscina</h3>
                </a>
            </div>
            
            <div class="area-card">
                <a href="zona-comun1.php">
                    <img src="../assets/img/gimnasio.jpg" alt="Gimnasio">
                    <h3>Gimnasio</h3>
                </a>
            </div>
        </div>
    </main>
    
    <footer>
        <div class="buttons">
            <a href="#" class="round-button edit-button">
                <span>✎</span>
            </a>
            <a href="crear-zona-comun.php" class="round-button add-button">
                <span>+</span>
            </a>
        </div>
    </footer>
     <?php

        //require_once './Layout/footer.php'
    ?> 
</body>
</html>