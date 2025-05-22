<?php

//Conexion a la base de datos
$pdo = require ('../config/db.php');
session_start();
// Verificar si se proporcionó un ID (Si cc esta funcionando)
if (!isset($_GET['cc']) || empty($_GET['cc'])) {
    echo "Error: No se proporcionó un ID de usuario válido.";
    exit;
}

$cc = $_GET['cc'];

try {

    $stmt = $pdo->prepare("SELECT * FROM tbl_usuario WHERE usuario_cc = :cc");
    $stmt->bindparam(':cc', $cc, PDO::PARAM_INT); // PARAM_INT para entero (int) ya que usuario_cc es int(10)
    $stmt->execute();

    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$usuario) {
    echo "No hay algun usuario con el id: $cc";
    exit;
    }
    $datos =(object)$usuario;
} catch (PDOException $mensaje) {
    echo "Error al ejecutar la consulta: ". $mensaje->getMessage();
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


    <form method="POST" action="../controller/ModificarUsuarioController.php">
        <h3> Modificar usuario </h3>

          <!-- Con esto guardamos el id del usuario y es enviado al controlador -->
         <input type="hidden" name="cc" value="<?= htmlspecialchars($_GET['cc']) ?>">

            <div class="col-md-4">
                <label for="validationCustom01" class="form-label">Correo Electronico</label>
                <input type="text" class="form-control" id="validationCustom01" name="correo" value="<?= $datos->usu_correo ?>" required >

            </div>

            <div class="col-md-4">
                <label for="validationCustom02" class="form-label">Torre de residencia</label>
                <input type="text" class="form-control" id="validationCustom02" name="torre" value="<?= $datos->usu_torre_residencia ?>" required >

            </div>

            <div class="col-md-4">
                <label for="validationCustomUsername" class="form-label">Apartamento de residencia</label>
                <div class="input-group has-validation">
                <input type="text" class="form-control" id="validationCustomUsername"  name="apartamento"  value="<?= $datos->usu_apartamento_residencia ?>" required >

            </div>

            <div class="col-md-10">

                <label for="validationCustom04" class="form-label">Estado</label>
                <select class="form-select" id="validationCustom04"  name="estado" >

                    <option value="<?= $datos->usu_estado ?>">Elija una opción</option>
                    <option> Activo </option>

                </select>

            </div>

            
            <div class="col-md-10">

                <label for="validationCustom04" class="form-label">Rol</label>
                <select class="form-select" id="validationCustom04" name="rol" >

                    <option value="<?=$datos->usu_rol?>">Elija una opción</option>
                    <option> Administrador </option>
                    <option> Residente </option>
                    <option> Propietario </option>
                    <option> Vigilante </option>

                </select>

            </div>

         
        <button type="submit" class="btn btn-primary" name="btn-confirmar" value="ok"> Confirmar </button>
        <button type="submit" class="btn btn-primary" name="btn-cancelar" value="ok"> Cancelar </button>


    </form>
    
</body>
</html>