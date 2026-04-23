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
        $name = $_POST['name']; 

        $test_results = '';

        if(!is_null($test)) {

            $score = 0;
            $max_score = count($test->otazky);

            $test_results = $test_results . '<ol>';

            for ($i = 0; $i <= $max_score - 1 ; $i++) {
                $q = 'q' . $i;

                $test_results = $test_results . '<li>' . $test->otazky[$i]->otazka;
                $test_results = $test_results . '<br/><strong>';

                if(isset($_POST[$q])) {

                    $answer = $test->otazky[$i]->odpovede[$_POST[$q]];

                    if($answer->spravna) {
                        $score++;
                        $test_results = $test_results . ' správne ('. $answer->odpoved . ')';
                    } else {
                        $test_results = $test_results . ' chyba ('. $answer->odpoved . ')';
                    }
                } else {
                    $test_results = $test_results . ' chýbajuca odpoveď';
                }
                
                $test_results = $test_results . '</strong>';
                $test_results = $test_results . '</li>';
            }

            $test_results = $test_results . '</ol>';

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

            $body_student = '<strong>Orientačný výsledok: ' . $level . '</strong><div>' .  $score . '/' . $max_score . ' bodov</div><br/><p>' . $message . '</p>';
    
            // send mail to student
            $result = sendEmail($from, $config['subject_test'], $body_student, true, $config['to_email']);

            // send mail to teacher
            if($result)
            {
                $body_teacher = 'Meno: '. $name . '<br/>';
                $body_teacher = $body_teacher . 'Email: '. $from . '<br/>';
                $body_teacher = $body_teacher . 'Výsledky: '. $test_results . '<br/>';
                $body_teacher = $body_teacher . '<br/>' . $body_student;

                $result = sendEmail($config['to_email'], $config['subject_test'], $body_teacher, true, $from, $name);
            }
        }

        if ($result)
        {
            echo json_encode([
                "success" => true,
                "message" => $body_student
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