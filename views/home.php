<?php
session_start();
// if (!isset($_SESSION['user_id'])) {
//     header("Location: login.php");
//     exit();
// }

require_once "./Layout/header.php"
?>




<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    <link rel="stylesheet" href="../assets/Css/home.css"/>
    <link rel="stylesheet" href="../assets/Css/globals.css"/>

</head>
<body>

    <div class="container">
        <h1>Bienvenido a ZoneMaisons</h1>
        <p>Esta es la página de inicio de tu aplicación.</p>
        <p>¡Disfruta navegando!</p>
    </div>

    <?php require_once "./Layout/footer.php" ?>
</body>
</html>