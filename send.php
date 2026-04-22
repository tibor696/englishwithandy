<?php
require 'vendor/phpmailer/phpmailer/src/Exception.php';
require 'vendor/phpmailer/phpmailer/src/PHPMailer.php';
require 'vendor/phpmailer/phpmailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

header('Content-Type: application/json');

if(isset($_POST['email'])){
    // Configuration
    $config = require 'config.php';

    $from = $_POST['email']; // Sender's email
    $name = $_POST['name'];

    // Message Body
    $message = $name . " píše:" . "\n\n" . $_POST['message'];

    //Create an instance; passing `true` enables exceptions
    $mail = new PHPMailer(true);

    try {
        //Server settings
        $mail->CharSet = 'UTF-8';
        $mail->SMTPDebug = SMTP::DEBUG_OFF;                      //Enable verbose debug output
        $mail->isSMTP();                                            //Send using SMTP
        $mail->Host       = $config['smtp_host'];                     //Set the SMTP server to send through
        $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
        $mail->Username   = $config['smtp_user'];                     //SMTP username
        $mail->Password   = $config['smtp_pass'];                               //SMTP password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
        $mail->Port       = $config['smtp_port'];                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

        //Recipients
        $mail->setFrom($config['sender_email'], $config['sender_name']);
        $mail->addAddress($config['to_email']);                 //Add a recipient
        $mail->addReplyTo($from, $name);

        //Content
        $mail->isHTML(false);                                  //Set email format to HTML
        $mail->Subject = $config['subject'];
        $mail->Body    = $message;
        //$mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

        if ($mail->send())
        {
            echo json_encode([
                "success" => true,
                "message" => "Email bol úspešne odoslaný"
            ]);
        } else {
            echo json_encode([
                "success" => false,
                "message" => "Nepodarilo sa odoslať email"
            ]);
        }
    } catch (Exception $e) {
        echo json_encode([
            "success" => false,
            "message" => "Chyba: " . $mail->ErrorInfo
        ]);
    }
}

?>