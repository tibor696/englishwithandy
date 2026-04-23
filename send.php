<?php
require_once('email.php');

header('Content-Type: application/json');

if(isset($_POST['email'])){
    try {
        // Configuration
        $config = require 'config.php';

        $from = $_POST['email']; // Sender's email
        $name = $_POST['name'];

        // Message Body
        $message = $name . " píše:" . "\n\n" . $_POST['message'];

        $result = sendEmail($config['to_email'], $config['subject_contact'], $message, $from, $name);
        
        if ($result)
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
            "message" => "Nepodarilo sa odoslať email."
        ]);
    }
}

?>