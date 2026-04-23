<?php
require_once('email.php');

header('Content-Type: application/json');

if(isset($_POST['email'])){

    try {
        // Configuration
        $result = false;
        $config = require 'config.php';
        $string = file_get_contents('./testy/' . $config['test_file']);
        $test = json_decode($string);
        $from = $_POST['email']; // Sender's email

        if(!is_null($test)) {

            $score = 0;
            $max_score = count($test->otazky);
            for ($i = 0; $i <= $max_score - 1 ; $i++) {
                $q = 'q' . $i;
                if(isset($_POST[$q]) && $_POST[$q] == 1)
                    $score++;
            }

            $level = "A1";
            $message = "Začíname pekne od základov: istota vo vetách, otázkach a každodenných situáciách.";

            if ($score >= 4 && $score <= 6) {
                $level = "A2";
                $message = "Základy už máte, teraz sa oplatí rozšíriť slovnú zásobu a viac rozprávať.";
            }

            if ($score >= 7 && $score <= 9) {
                $level = "B1";
                $message = "Máte dobrý základ na samostatnejšiu komunikáciu. Spolu môžeme posilniť plynulosť a presnosť.";
            }

            if ($score >= 10) {
                $level = "B2+";
                $message = "Vaša úroveň je pokročilejšia. Hodiny môžu cieliť na prirodzenosť, nuansy a sebavedomý prejav.";
            }

            $body = "Orientačný výsledok: " . $level . " " .  $score . "/" . $max_score . " bodov. \n\n" . $message;
    
            // send mail to student
            $result = sendEmail($from, $config['subject_test'], $body, $config['to_email']);

            // send mail to teacher
            if($result)
                $result = sendEmail($config['to_email'], $config['subject_test'], $body, $from);
        }

        if ($result)
        {
            echo json_encode([
                "success" => true,
                "message" => "Email s vysledkom testu vam úspešne odoslaný"
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
            "message" => "Nepodarilo sa odoslať email"
        ]);
    }
}
?>