<?php

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
    $stmt = $conexion ->query(" update tbl_usuario set usu_correo='$correo', usu_torre_residencia='$torre', usu_apartamento_residencia=' $apartamento', usu_estado='$estado', usu_rol_id=$rol where usuario_cc=$cc ");
    
    if ($sql==1) {
       header("localhost/index-diseño.php");
       echo "<div class='alert alert-success' role='alert'> Cambio exitoso </div>";
    } else if ($sql==0) {
        echo "<div class='alert alert-warning'> No se realizaron cambios </div>";

    } else {
        echo "<div class='alert alert-danger'> Error al modificar </div>";
    }

    } else{ 
        echo "<div class='alert alert-warning'> Campos vacios</div>";
    }

}

?>

