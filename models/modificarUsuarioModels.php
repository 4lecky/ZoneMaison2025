<?php

//Conexion a la base de datos
$pdo = require('../config/db.php');
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
    $datos = (object)$usuario;
} catch (PDOException $mensaje) {
    echo "Error al ejecutar la consulta: " . $mensaje->getMessage();
    exit;
}



?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modificar Usuario</title>
    <link rel="stylesheet" href="../assets/Css/crud/formCrud.css">
    <link rel="stylesheet" href="../assets/Css/globals.css" />

</head>

<body class='body_crud'>


    <form method="POST" action="../controller/ModificarUsuarioController.php" class="container_form_crud">
        <h3 class="h3_crud_form"> Aqui puede modificar su usuario</h3>
        <fieldset class='fieldset_crud'>
            <legend class='legend_crud' > Información de usuario </legend>
            <br>
            <!-- Con esto guardamos el id del usuario y es enviado al controlador -->
            <input type="hidden" name="cc" value="<?= htmlspecialchars($_GET['cc']) ?>">

            <div class="columnaCrud">

                <div class="inputs_crud_modificar">
                    <label for="validationCustom01" class="form-label">Correo Electronico</label>
                    <input type="text" class="form-control" id="validationCustom01" name="correo" value="<?= $datos->usu_correo ?>" required>

                </div>

                <div class="inputs_crud_modificar">
                    <label for="validationCustom02" class="form-label">Torre de residencia</label>
                    <input type="text" class="form-control" id="validationCustom02" name="torre" value="<?= $datos->usu_torre_residencia ?>" required>

                </div>

            </div>

            <div class="columnaCrud">

                <div class="inputs_crud_modificar">
                    <label for="validationCustomUsername" class="form-label">Apartamento de residencia</label>
                    <div class="input-group has-validation">
                        <input type="text" class="form-control" id="validationCustomUsername" name="apartamento" value="<?= $datos->usu_apartamento_residencia ?>" required>
                    </div>
                </div>

                <div class="inputs_crud_modificar">
                    <label for="validationCustomUsername" class="form-label">Parqueadero</label>
                    <div class="input-group has-validation">
                        <input type="text" class="form-control" id="validationCustomUsername" name="parqueadero" value="<?= $datos->usu_parqueadero_asignado ?>" required>
                    </div>
                </div>

            </div>

            <div class="columnaCrud">

                <div class="select_crud_modificar">

                        <label for="validationCustom04" class="form-label">Estado</label>
                        <select class="form-select" id="validationCustom04" name="estado">

                            <option value="<?= $datos->usu_estado ?>">Elija una opción</option>
                            <option value="Activo" <?= $datos->usu_estado  == 'Activo' ? 'selected' : '' ?>> Activo </option>
                            <option value="Inactivo" <?= $datos->usu_estado  == 'Inactivo' ? 'selected' : '' ?>> Inactivo </option>

                        </select>
                </div>

                <div class="select_crud_modificar">

                    <label for="validationCustom04" class="form-label">Rol</label>
                    <select class="form-select" id="validationCustom04" name="rol">

                        <option value="<?= $datos->usu_rol ?>">Elija una opción</option>
                        <option value="Administrador" <?= $datos->usu_rol  == 'Administrador' ? 'selected' : '' ?>> Administrador </option>
                        <option value="Residente" <?= $datos->usu_rol  == 'Residente' ? 'selected' : '' ?>> Residente </option>
                        <option value="Propietario" <?= $datos->usu_rol  == 'Propietario' ? 'selected' : '' ?>> Propietario </option>
                        <option value="Vigilante" <?= $datos->usu_rol  == 'Vigilante' ? 'selected' : '' ?>> Vigilante </option>

                    </select>

                </div>

            </div>

            
                <div class="select_crud_modificar">

                    <label for="validationCustom04" class="form-label">Pago de la administración</label>
                    <select class="form-select" id="validationCustom04" name="mora">

                        <option value="">Elija una opción</option>
                        <option value="1" <?= $datos->usu_mora  == 1 ? 'selected' : '' ?>> Pagado </option>
                        <option value="2" <?= $datos->usu_mora == 2 ? 'selected' : '' ?>> Pendiente </option>
                        <option value="3" <?= $datos->usu_mora == 3 ? 'selected' : '' ?>> No aplica </option>
                    </select>
                </div>

                <div class="container-btn-crud">
                    <button type="submit" class="btn btn-form-crud " name="btn-confirmar" value="ok"> Confirmar </button>
                    <button type="button" class="btn btn-form-crud " onclick="window.location.href='../views/crud.php';"> Cancelar </button>
                </div>

        </fieldset>




    </form>

</body>

</html>