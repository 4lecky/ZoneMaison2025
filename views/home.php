<?php
session_start();
// if (!isset($_SESSION['user_id'])) {
//     header("Location: login.php");
//     exit();
// }

require_once "./Layout/header.php";


?>




<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    <!-- iconos (RemixIcon+) -->
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/Css/home.css" />
    <link rel="stylesheet" href="../assets/Css/globals.css" />
    <link rel="stylesheet" href="../assets/Css/globals.css" />
    <link rel="stylesheet" href="../assets/Css/Layout/header.css" />
    <link rel="stylesheet" href="../assets/Css/Layout/footer.css" />

</head>

<body>

    <div class="container_home">
        <h1>Bienvenido a ZoneMaisons</h1>
        <p>Esta es la página de inicio de tu aplicación.</p>
        <p>¡Disfruta navegando!</p>
    </div>

    <?php

    require_once "./Layout/footer.php"

    ?>

    <script src="../assets/Js/header.js"></script>
</body>

</html>