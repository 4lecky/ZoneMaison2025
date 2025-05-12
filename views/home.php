<?php
session_start();
// if (!isset($_SESSION['user_id'])) {
//     header("Location: login.php");
//     exit();
// }
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="../assets/Css/home.css"/>
</head>
<body>


      <nav> 
        <a href="#" class="nav-link"> Inicio </a>
        <a href="#" class="nav-link"> Notificaciones </a>
        <a href="#" class="nav-link"> Parqueaderos </a>
        <a href="#" class="nav-link"> Reservas </a>
        <a href="./pqrs.php" class="nav-link"> PQRS </a>
        <a href="./visitas.php" class="nav-link"> Notificaciones </a>
        <a href="./crud.php" class="nav-link"> Usuarios </a>
      </nav>
    
</body>
</html>