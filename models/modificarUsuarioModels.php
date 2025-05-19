<?php

//Conexion a la base de datos
include('../config/db.php');

// Verificar si se proporcionó un ID (Si cc esta funcionando)
if (!isset($_GET['cc']) || empty($_GET['cc'])) {
    echo "Error: No se proporcionó un ID de usuario válido.";
    exit;
}

$cc = $_GET['cc'];

// Usar consultas preparadas para evitar inyección SQL
$stmt = $pdo->prepare("SELECT * FROM tbl_usuario WHERE usuario_cc = ?");
if (!$stmt) {
    echo "Error en la preparación de la consulta: " . $conexion->error;
    exit;
}

// $stmt->bindparam("i", $cc); // "i" para entero (int) ya que usuario_cc es int(10)
// $stmt->execute();
// $result = $stmt->get_result();

// Verificar si la consulta fue exitosa
// if (!$result) {
//     echo "Error al ejecutar la consulta: " . $stmt->error;
//     exit;
// }

// Verificar si se encontraron resultados
if ($result->num_rows === 0) {
    echo "No se encontró ningún usuario con el ID $cc";
    exit;
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modificar Usuario</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">


</head>
<body>

    <form method="POST" >
        <h3> Modificar usuario </h3>
        <input type="hidden" name="cc" value="<?= $_GET["cc"] ?>">

        <?php 
        

        include ("../controller/ModificarUsuario.php");
        while ($datos= $result-> fetch_object()) { ?>

            <div class="col-md-4">
                <label for="validationCustom01" class="form-label">Correo Electronico</label>
                <input type="text" class="form-control" id="validationCustom01" name="correo" value="<?= $datos->usu_correo ?>"  >

            </div>

            <div class="col-md-4">
                <label for="validationCustom02" class="form-label">Torre de residencia</label>
                <input type="text" class="form-control" id="validationCustom02" name="torre" value="<?= $datos->usu_torre_residencia ?>" >

            </div>

            <div class="col-md-4">
                <label for="validationCustomUsername" class="form-label">Apartamento de residencia</label>
                <div class="input-group has-validation">
                <input type="text" class="form-control" id="validationCustomUsername"  name="apartamento"  value="<?= $datos->usu_apartamento_residencia ?>" >

            </div>

            <div class="col-md-10">

                <label for="validationCustom04" class="form-label">Estado</label>
                <select class="form-select" id="validationCustom04"  name="estado"  >

                    <option value="<?= $datos->usu_estado ?>">Elija una opción</option>
                    <option> Activo </option>
                    <option> Innactivo </option>

                </select>

            </div>

            
            <div class="col-md-10">

                <label for="validationCustom04" class="form-label">Rol</label>
                <select class="form-select" id="validationCustom04" name="rol" >

                    <option value="<?=$datos->usu_rol_id?>">Elija una opción</option>
                    <option> 1</option>
                    <option> 2 </option>
                    <option> 3 </option>
                    <option> 4 </option>

                </select>

            </div>

        <?php }
        ?>
         
        <button type="submit" class="btn btn-primary" name="btn-confirmar" value="ok"> Confirmar </button>
        <button type="submit" class="btn btn-primary" name="btn-cancelar" value="ok"> Cancelar </button>


    </form>
    
</body>
</html>