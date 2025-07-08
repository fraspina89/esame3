<?php
ini_set("auto_detect_line_endings", true);

// Includi la configurazione del database e i componenti comuni (head, menu, footer)
require_once('./MieClassi/Utility.php');
require_once('head_menu_footer.php');
require_once("admin/config.php");

// Usa la classe Utility //
use MieClassi\Utility as UT;

// Percorso del menu JSON //
$menu = "./json/menu.json";

// Controlla se il form è stato inviato (tramite parametro GET o POST) //
$inviato = UT::richiestaHTTP("inviato") == 1;

// Inizializza variabili dei campi e delle classi errore //
$nome = $cognome = $email = $argomento = $testo = "";
$clsErroreNome = $clsErroreCognome = $clsErroreEmail = $clsErroreArgomento = $clsErroreTesto = "";

// Se il form è stato inviato, esegui la validazione lato server //
if ($inviato) {
    $valido = 0;

    // RECUPERO DATI //

    $clsErrore = ' class="errore" ';
    $nome = UT::richiestaHTTP("nome");
    $cognome = UT::richiestaHTTP("cognome");
    $email = UT::richiestaHTTP("email");
    $argomento = UT::richiestaHTTP("argomento");
    $testo = UT::richiestaHTTP("testo");



    // Validazione lato server //
    if (UT::controllaRangeStringa($nome, 3, 25)) $clsErroreNome = "";
    else {
        $valido++;
        $clsErroreNome = $clsErrore;
    }

    if (UT::controllaRangeStringa($cognome, 3, 25)) $clsErroreCognome = "";
    else {
        $valido++;
        $clsErroreCognome = $clsErrore;
    }

    if (UT::controllaRangeStringa($email, 10, 100) && filter_var($email, FILTER_VALIDATE_EMAIL)) $clsErroreEmail = "";
    else {
        $valido++;
        $clsErroreEmail = $clsErrore;
    }

    if (!empty($argomento)) $clsErroreArgomento = "";
    else {
        $valido++;
        $clsErroreArgomento = $clsErrore;
    }

    if (UT::controllaRangeStringa($testo, 3, 500)) $clsErroreTesto = "";
    else {
        $valido++;
        $clsErroreTesto = $clsErrore;
    }

    // Se tutti i campi sono validi, inserisci i dati nel database //
    if ($valido == 0) {
        $stmt = $conn->prepare("INSERT INTO utenti (nome, cognome, email, argomento, testo) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $nome, $cognome, $email, $argomento, $testo);
        $stmt->execute();
        $stmt->close();
    }

    $inviato = ($valido == 0);
}


$arrCss = ["contatti.min.css"];
echo head('contatti', $arrCss);
?>

<body>
    <header>
        <?php echo menu($menu); ?>
    </header>

    <main>
        <h1>I AM FRANCESCO SPINAZZOLA</h1>

        <div class="contenitore-form-mappa">
            <?php if (!$inviato): ?>
                <!-- FORM CONTATTI -->
                <div class="contatti">
                    <h2>SE DESIDERI ESSERE CONTATTATO</h2>
                    <form action="contatti.php?inviato=1" method="POST">
                        <fieldset class="card">
                            <!-- Ogni label mostra la classe errore se il campo non è valido -->
                            <label for="nome" <?= $clsErroreNome ?>>Nome <span>*</span></label>
                            <input type="text" id="nome" name="nome" maxlength="25" value="<?= htmlspecialchars($nome) ?>">

                            <label for="cognome" <?= $clsErroreCognome ?>>Cognome <span>*</span></label>
                            <input type="text" id="cognome" name="cognome" maxlength="25" value="<?= htmlspecialchars($cognome) ?>">

                            <label for="email" <?= $clsErroreEmail ?>>Email <span>*</span></label>
                            <input type="email" id="email" name="email" maxlength="100" minlength="10" value="<?= htmlspecialchars($email) ?>">

                            <label for="argomento" <?= $clsErroreArgomento ?>>Argomento <span>*</span></label>
                            <select name="argomento" id="argomento">
                                <option value="" <?= $argomento == "" ? 'selected' : '' ?>>Seleziona argomento</option>
                                <option value="Interessato" <?= $argomento == "Interessato" ? 'selected' : '' ?>>Interessato</option>
                                <option value="Dubbioso" <?= $argomento == "Dubbioso" ? 'selected' : '' ?>>Dubbioso</option>
                            </select>

                            <label for="testo" <?= $clsErroreTesto ?>>Testo <span>*</span></label>
                            <textarea id="testo" name="testo" maxlength="500"><?= htmlspecialchars($testo) ?></textarea>

                            <div>
                                <button type="reset">Annulla</button>
                                <button type="submit">Invia</button>
                            </div>
                        </fieldset>
                    </form>
                </div>
            <?php else: ?>
                <!-- RIEPILOGO DATI INVIATI -->
                <div class="riepilogo">
                    <h2>Grazie per averci contattato</h2>
                    <p><strong>Nome:</strong> <?= htmlspecialchars($nome) ?></p>
                    <p><strong>Cognome:</strong> <?= htmlspecialchars($cognome) ?></p>
                    <p><strong>Email:</strong> <?= htmlspecialchars($email) ?></p>
                    <p><strong>Argomento:</strong> <?= htmlspecialchars($argomento) ?></p>
                    <p><strong>Testo:</strong><br><?= nl2br(htmlspecialchars($testo)) ?></p>
                    <p style="color:green;"><strong>I dati sono stati salvati nel database correttamente.</strong></p>
                </div>
            <?php endif; ?>

            <!-- MAPPA -->
            <div class="mappa">
                <h2>INDICAZIONE MAPPA</h2>
                <iframe
                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2821.463760144219!2d7.645665275037975!3d44.995203464610405!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x47881244e0d5897b%3A0x1b7ab3f8d62eb5eb!2sVia%20Giuseppe%20Giusti%2C%203%2C%2010042%20Nichelino%20TO!5e0!3m2!1sit!2sit!4v1716892363978!5m2!1sit!2sit"
                    width="400" height="400" style="border:0;" allowfullscreen="" loading="lazy"
                    referrerpolicy="no-referrer-when-downgrade"></iframe>
            </div>
        </div> <!-- chiude .contenitore-form-mappa -->

        <?php if (!$inviato): ?>
            <div class="rigaFooter">
                <div class="dati">
                    <ul>
                        <li>
                            <p>vienici a trovare in:</p>
                            <address>Largo Giusti,3<br>10100 Nichelino (TO)<br>Italia</address>
                        </li>
                        <li>
                            <p>email - telefono:</p>
                            <ul>
                                <li><a href="mailto:francescospinazzola084@gmail.com">francescospinazzola084@gmail.com</a></li>
                                <li><a href="tel:3402330981">3402330981</a></li>
                            </ul>
                        </li>
                        <li>
                            <p>seguici su:</p>
                            <ul>
                                <li><a href="http://facebook.com">facebook</a></li>
                                <li><a href="http://twitter.com">twitter</a></li>
                                <li><a href="http://instagram.com">instagram</a></li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </div>
        <?php endif; ?>
    </main>

    <?php echo footer();  // Stampa il footer //
    ?>

    <!-- Validazione lato client con JS -->
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const form = document.querySelector("form");
            if (!form) return;

            form.addEventListener("submit", function(e) {
                let valido = true;
                const campi = [{
                        id: "nome",
                        min: 3,
                        max: 25
                    },
                    {
                        id: "cognome",
                        min: 3,
                        max: 25
                    },
                    {
                        id: "email",
                        min: 10,
                        max: 100,
                        email: true
                    },
                    {
                        id: "argomento",
                        select: true
                    },
                    {
                        id: "testo",
                        min: 3,
                        max: 500
                    }
                ];

                for (let campo of campi) {
                    const input = document.getElementById(campo.id);
                    const label = document.querySelector(`label[for="${campo.id}"]`);
                    const value = input.value.trim();
                    let errore = false;

                    // Rimuovi errore precedente sulla label //
                    if (label) label.classList.remove("errore");

                    if (campo.select && value === "") {
                        errore = true;
                    } else if (campo.email) {
                        const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                        errore = !re.test(value);
                    } else {
                        errore = value.length < campo.min || value.length > campo.max;
                    }

                    if (errore) {
                        if (label) label.classList.add("errore");
                        valido = false;
                    }
                }

                if (!valido) {
                    e.preventDefault();
                    alert("Compila correttamente tutti i campi obbligatori.");
                }
            });
        });
    </script>

    <!-- Alert di conferma dopo invio riuscito -->
    <?php if ($inviato): ?>
        <script>
            window.addEventListener("DOMContentLoaded", function() {
                alert("Grazie per averci contattato! I tuoi dati sono stati salvati correttamente.");
            });
        </script>
    <?php endif; ?>
</body>

</html>