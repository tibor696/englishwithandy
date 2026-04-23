<?php
  $error = null;

  try {
    $config = require 'config.php';
    $string = file_get_contents('./testy/' . $config['test_file']);
    $test = json_decode($string);
  } catch (Exception $e) {
      $error = $e;
  }
?>

<!DOCTYPE html>
<html lang="sk">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Bezplatný test úrovne | Andrea Horčíková</title>
  <meta name="description" content="Bezplatný orientačný test úrovne angličtiny pre študentov Andrey Horčíkovej.">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Instrument+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="styles.css">

  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
  <main>
    <section class="section test-hero">
      <div class="shell test-shell">
        <a class="back-link" href="index.html">Späť na hlavnú stránku</a>
        <img class="brand-logo test-logo" src="logo1.png" alt="You alright?!">
        <h1>Bezplatný test úrovne</h1>
        <p class="lead">Vyplňte krátky orientačný test a zistite, odkiaľ sa spolu môžeme odraziť. Výsledok slúži ako prvý odhad, presnú úroveň doladíme na úvodnej konzultácii.</p>
      </div>
    </section>

    <section class="section test-section">
      <div class="shell test-shell">
        <form class="level-test" id="level-test" action="" method="post">
          <div class="test-card">

          <?php
            try {
              if(!is_null($test)) {
                echo('<h2>'. $test->nazov . '</h2>');
                echo('<p>Vyberte vždy jednu odpoveď.</p>');
              
                for ($i = 0; $i <= count($test->otazky) - 1 ; $i++) {
                  echo('<fieldset>');
                
                  echo('<legend>'. $i + 1 . '. ' . $test->otazky[$i]->otazka . '</legend>');
                  
                  for ($j = 0; $j <= count($test->otazky[$i]->odpovede) - 1; $j++) {
                    echo('<label><input type="radio" name="q' . $i . '" value="' . $j . '"');
                    
                    if($j == 0)
                       echo('required');
                    
                    echo('> ' . $test->otazky[$i]->odpovede[$j]->odpoved . '</label>');
                  }

                  echo('</fieldset>');
                }

              } else {
                echo("Chyba pri načítavaní testu.");
              }
            } catch (Exception $e) {
              $error = $e;
            }
          
            if(!is_null($error)) {
              echo ("Chyba: " + $error);
            }
          ?>

            <div class="form-field">
                <label for="contact-name">Meno</label>
                <input id="contact-name" name="name" type="text" autocomplete="name" required>
            </div>

            <div class="form-field">
                <label for="contact-email">Email</label>
                <input id="contact-email" name="email" type="email" autocomplete="email" required>
            </div>
          </div>

          <div class="test-actions">
            <button class="button primary" type="submit" id="submit">Vyhodnotiť test</button>
            <a class="button secondary" href="index.html#kalendar">Kontaktovať Andreu</a>
          </div>

          <div class="test-result" id="test-result" aria-live="polite"></div>
        </form>
      </div>
    </section>
  </main>

  <script>
    const form = document.getElementById('level-test');
    //const result = document.getElementById('test-result');
    const btn = document.getElementById('submit');

    form.addEventListener('submit', function(e) {
        e.preventDefault(); // zruší reload

        // zablokuj formulár
        btn.disabled = true;
        Swal.fire({
            title: 'Odosielam...',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        const formData = new FormData(this);

        fetch('test-results.php', {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {

            Swal.fire({
                    title: data.success ? 'Hotovo!' : 'Chyba!',
                    html: data.message,
                    icon: data.success ? 'success' : 'error'
                });

            if (data.success) {
                form.reset(); // vymaže všetky polia
            }
        })
        .catch((e) => {
            
            console.log(e);

            Swal.fire({
                    title: 'Chyba!',
                    text: 'Chyba pri odosielaní',
                    icon: 'error'
                });
        })
        .finally(() => {
            // odblokuj formulár
            btn.disabled = false;
        });
    });
  </script>
</body>
</html>
