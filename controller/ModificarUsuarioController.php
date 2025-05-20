<?php
$pdo = require ('../config/db.php');

if (!empty($_POST['btn-confirmar'])) {

   
   if (!empty($_POST["correo"]) and !empty($_POST["torre"]) and !empty($_POST["apartamento"]) and !empty($_POST["estado"]) and !empty($_POST["rol"])) {

    #Variables para almacenar los datos
    $cc = $_POST["cc"];
    $correo = $_POST["correo"];
    $torre = $_POST["torre"];
    $apartamento = $_POST["apartamento"];
    $estado = $_POST["estado"];
    $rol = $_POST["rol"];
    
    #Conexión base de datos  	
    try {
            // Consulta preparada y segura
            $stmt = $pdo->prepare("UPDATE tbl_usuario 
                SET usu_correo = :correo,
                    usu_torre_residencia = :torre,
                    usu_apartamento_residencia = :apartamento,
                    usu_estado = :estado,
                    usu_rol = :rol
                WHERE usuario_cc = :cc");

            // Asignamos los valores a las variables creadar anteriormente
            $stmt->bindParam(':correo', $correo);
            $stmt->bindParam(':torre', $torre);
            $stmt->bindParam(':apartamento', $apartamento);
            $stmt->bindParam(':estado', $estado);
            $stmt->bindParam(':rol', $rol);
            $stmt->bindParam(':cc', $cc, PDO::PARAM_INT);

            if ($stmt->execute()) {
                 $_SESSION['mensaje'] = ['tipo' => 'success', 'texto' => '¡Usuario modificado con éxito!'];
                // Opcional: redirigir después de unos segundos
                header("Refresh:1; url=../views/crud.php");
                exit;
            } else {
                  $_SESSION['mensaje'] = ['tipo' => 'warning', 'texto' => 'No se realizaron cambios.'];
            }

        } catch (PDOException $e) {
             $_SESSION['mensaje'] = ['tipo' => 'danger', 'texto' => 'Error al modificar: ' . $e->getMessage()];
        }
    header("Location: ../views/crud.php");
        exit;

    } else {
       $_SESSION['mensaje'] = ['tipo' => 'warning', 'texto' => 'Todos los campos son obligatorios.'];
       header("Refresh:1; url=../models/modificarUsuarioModels.php");
    }

}

?>

