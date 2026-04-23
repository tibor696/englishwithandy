<?php
require 'vendor/phpmailer/phpmailer/src/Exception.php';
require 'vendor/phpmailer/phpmailer/src/PHPMailer.php';
require 'vendor/phpmailer/phpmailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

function sendEmail($email, $subject, $body, $isHtml, $replyToEmail = null, $replyToName = null, $bcc = null) {
    $result = true;

    try {
        // Configuration
        $config = require 'config.php';

        //Create an instance; passing `true` enables exceptions
        $mail = new PHPMailer(true);

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
        $mail->addAddress($email);                 //Add a recipient

        if(!is_null($bcc))
            $mail->addBCC($bcc);

        if(!is_null($replyToEmail)) {
            if(!is_null($replyToName))
                $mail->addReplyTo($replyToEmail, $replyToName);
            else
                $mail->addReplyTo($replyToEmail);
        }

        //Content
        $mail->isHTML($isHtml);                                  //Set email format to HTML
        $mail->Subject = $subject;
        $mail->Body    = $body;
        //$mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

        $result = $mail->send();
    } catch (Exception $e) {
        $result = false;
        //$mail->ErrorInfo
    }
    return $result;
}

?>