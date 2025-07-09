<?php

session_start();

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

//Load Composer's autoloader (created by composer, not included with PHPMailer)
require '../vendor/autoload.php';
$pdo = require __DIR__ . '/../config/db.php';


if (isset($_POST['send'])):
    if (!empty($_POST['email'])) {
        $Usuario = ConsultaUsuarioPorEmail($pdo, $_POST['email']);
        if (count($Usuario) > 0) {
            $token_ = bin2hex(random_bytes(32));

            if (updateUser($pdo, $token_, TIEMPO_VIDA, $Usuario[0]->usuario_cc)); {
                EnviarCorreoResetPassword(
                    $Usuario[0]->usu_correo,
                    $Usuario[0]->usu_nombre_completo,
                    $Usuario[0]->usuario_cc,
                    $token_
                );
                $_SESSION['response'] = 'Porfavor revise su correo electronico';
                $_SESSION['response_type'] = 'success';
            }
        } else {
            $_SESSION['response'] = 'Información no valida';
            $_SESSION['response_type'] = 'danger';
            // header("location:../view/reset_contrasenha.php?message=no_found");
        }
    } else {
        $_SESSION['response'] = 'Email incorrecto';
        $_SESSION['response_type'] = 'warning';
        // header("location:../view/reset_contrasenha.php?message=error");
    }
    header("location:../views/reset_contrasenha.php");
    exit();
endif;

if (isset($_POST['save'])):
    if (!empty($_POST['id'])) {


        $new_password = password_hash($_POST['password'], PASSWORD_BCRYPT);
        $id = $_POST['id'];


        $Usuario = ConsultaUsuarioPorId($pdo, $_POST['id']);
        if (count($Usuario) > 0) {

            updateUserID($pdo, $new_password, $id);

            $_SESSION['response'] = 'Contraseña cambiada exitosamente';
            $_SESSION['response_type'] = 'success';
            
        } else {
            // echo "no existe el usuario";
            $_SESSION['response'] = 'No existe usuario';
            $_SESSION['response_type'] = 'danger';
        }
    } else {
        // echo "Ingrese su correo electrónico";
        $_SESSION['response'] = 'Email incorrecto';
        $_SESSION['response_type'] = 'warning';
    }

    header("location:../views/login.php?message=success_password");
    exit();

endif;


// metodo que consulta usuario por email
function ConsultaUsuarioPorEmail($pdo, $email)
{
    try {
        //Consulta de sql
        $sql = "SELECT * FROM tbl_usuario WHERE usu_correo = :usu_correo";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(":usu_correo", $email);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_OBJ);
    } catch (\Throwable $th) {
        //throw $th;

        echo $th->getMessage();
    } finally {
        $stmt = null;
    }
}



//metodo que consulta usuario por id
function ConsultaUsuarioPorId($pdo, $id)
{

    try {
        //code...
        $sql = "SELECT * FROM tbl_usuario WHERE usuario_cc=:id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(":id", $id);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_OBJ);
    } catch (\Throwable $th) {
        //throw $th;

        echo $th->getMessage();
    } finally {
        $stmt = null;
    }
}


// actualizar usuario
function updateUser($pdo, $token, $tiempo_vida, $user_cc)
{

    try {
        $sql = "UPDATE tbl_usuario set request_password=:request_password, token_password=:token_password, expired_session=:expired_session WHERE usuario_cc =:usuario_cc";
        $stmt = $pdo->prepare($sql);
        $Valor = 1;
        $stmt->bindParam(":request_password", $Valor);
        $stmt->bindParam(":token_password", $token);
        $stmt->bindParam(":expired_session", $tiempo_vida);
        $stmt->bindParam(":usuario_cc", $user_cc);


        return $stmt->execute();
    } catch (\Throwable $th) {
        //throw $th;
        echo $th->getMessage();
    }
}


// actualizar usuario
function updateUserID($pdo, $new_password, $user_id)
{

    try {
        // password (sin :): es el nombre de la columna en la base de datos (en tu tabla usuarios).
        $sql = "UPDATE tbl_usuario set usu_password=:password WHERE usuario_cc=:usuario_cc";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(":password", $new_password);
        $stmt->bindParam(":usuario_cc", $user_id);

        $stmt->execute();
    } catch (\Throwable $th) {
        //throw $th;
        echo $th->getMessage();
    }
}

//envio de correos electronicos
function EnviarCorreoResetPassword($Correo, $NombreReceptor, $userid, $token_User)
{

    //Create an instance; passing `true` enables exceptions
    $mail = new PHPMailer(true);

    try {
        //Server REAL
        $mail->SMTPDebug = SMTP::DEBUG_SERVER;        //Enable verbose debug output
        $mail->isSMTP();                              //Send using SMTP
        $mail->Host       = HOST;                     //Set the SMTP server to send through
        $mail->SMTPAuth   = true;                     //Enable SMTP authentication
        $mail->Username   = USERNAME;                 //SMTP username
        $mail->Password   = PASSWORD;                 //SMTP password
        $mail->SMTPSecure = 'tls';                    //Enable implicit TLS encryption
        $mail->Port = 587;

        //Recipients
        $mail->setFrom('zonemaizon2025@gmail.com', 'ZoneMaisons');
        $mail->addAddress($Correo, $NombreReceptor);     //Add a recipient

        //Content
        $mail->isHTML(true);                                  //Set email format to HTML
        $mail->Subject = 'Reseteo de password';
        $mail->Body    = 'Usted a solicitado un reseteo de contraseña <b> 
        <a href="http://localhost/zonemaison2025/views/update_contrasenha.php?id=' .$userid . '&&token=' . $token_User . '">Cambiar Contraseña</a> </b>';


        $mail->send();
        echo 'Se ha enviado un correo con las instrucciones para recuperar contraseña';       
        } catch (Exception $e) {
            echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }

        //Pruebas con mailTrap
    //     $mail->SMTPDebug = SMTP::DEBUG_SERVER;
    //     $mail->isSMTP();
    //     $mail->Host       = HOST;
    //     $mail->SMTPAuth   = true;
    //     $mail->Username   = USERNAME;
    //     $mail->Password   = PASSWORD;
    //     $mail->SMTPSecure = SMTP_SECURE;
    //     $mail->Port = PORT;

    //     //Recipients
    //     $mail->setFrom('from@example.com', 'ZoneMaison');
    //     $mail->addAddress($Correo, $NombreReceptor);     //Add a recipient

    //     //Content
    //     $mail->isHTML(true);                                  //Set email format to HTML
    //     $mail->Subject = 'Reseteo de password';
    //     $mail->Body    = 'Usted a solicitado un reseteo de contraseña <b> 
    //         <a href="http://localhost/zonemaison2025/views/update_contrasenha.php?id=' . $userid . '&token=' . $token_User . '">Cambiar Contraseña</a> </b>';

    //     $mail->send();
    //     echo 'Se ha enviado un correo con las instrucciones para recuperar contraseña';
    // } catch (Exception $e) {
    //     echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    // }
}
// update_contraseña