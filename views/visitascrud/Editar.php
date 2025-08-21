<?php


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modificar Usuario</title>
    <link rel="stylesheet" href="../assets/Css/globals.css" />
    <link rel="stylesheet" href="../../assets/css/visitas.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-LN+7fdVzj6u52u30Kp6M/trliBMCMKTyK833zpbD+pXdCLuTusPj697FH4R/5mcr" crossorigin="anonymous">


</head>

<body>
     
<div class="form-container">
    <h2>Datos de la Visita</h2>

    <form action="procesar_editar.php" method="POST">
        <fieldset>
            <legend>Información de la Visita</legend>

            <div class="form-row">
                <div class="form-group">
                    <label for="fecha_entrada">Fecha de Entrada</label>
                    <input type="date" id="fecha_entrada" name="fecha_entrada">
                </div>
                <div class="form-group">
                    <label for="fecha_salida">Fecha de Salida</label>
                    <input type="date" id="fecha_salida" name="fecha_salida">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="hora_entrada">Hora de Entrada</label>
                    <input type="time" id="hora_entrada" name="hora_entrada">
                </div>
                <div class="form-group">
                    <label for="hora_salida">Hora de Salida</label>
                    <input type="time" id="hora_salida" name="hora_salida">
                </div>
            </div>
        </fieldset>

        <div class="btn-container">
            <button type="submit" class="btn btn-confirmar">Confirmar</button>
            <a href="listado.php" class="btn btn-cancelar">Cancelar</a>
        </div>
    </form>
</div>
</body>
</html>