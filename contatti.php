<?php
ini_set("auto_detect_line_endings", true);

require_once('./MieClassi/Utility.php');
require_once('head_menu_footer.php');

use MieClassi\Utility as UT;

$menu = "./json/menu.json";

$inviato = UT::richiestaHTTP("inviato");
$inviato = ($inviato == null || $inviato != 1) ? false : true;

if ($inviato) {
    $valido = 0;
    // RECUPERO DATI //

    $nome = UT::richiestaHTTP("nome");
    $cognome = UT::richiestaHTTP("cognome");
    $email = UT::richiestaHTTP("email");
    $argomento = UT::richiestaHTTP("argomento");
    $testo = UT::richiestaHTTP("testo");
    $clsErrore = ' class="errore" ';


    //VALIDO I DATI//

    if (($nome != "") && UT::controllaRangeStringa($nome, 0, 25)) {
        $clsErroreNome = "";
    } else {
        $valido++;
        $clsErroreNome = $clsErrore;
        $nome = "";
    }

    if (($cognome != "") && UT::controllaRangeStringa($cognome, 0, 25)) {
        $clsErroreCognome = "";
    } else {
        $valido++;
        $clsErroreCognome = $clsErrore;
        $cognome = "";
    }

    if (($email != "") && UT::controllaRangeStringa($email, 10, 100) && filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $clsErroreEmail = "";
    } else {
        $valido++;
        $clsErroreEmail = $clsErrore;
        $email = "";
    }

    if ($argomento != "") {
        $clsErroreArgomento = "";
    } else {
        $valido++;
        $clsErroreArgomento = $clsErrore;
        $argomento = "";
    }

    if (($testo != "") && UT::controllaRangeStringa($testo, 10, 500)) {
        $clsErroreTesto = "";
    } else {
        $valido++;
        $clsErroreTesto = $clsErrore;
        $testo = "";
    }

    $inviato = ($valido == 0) ? true : false;
} else {
    $nome = "";
    $cognome = "";
    $email = "";
    $argomento = "";
    $testo = "";

    $clsErroreNome = "";
    $clsErroreCognome = "";
    $clsErroreEmail = "";
    $clsErroreArgomento = "";
    $clsErroreTesto = "";
}


$arrCss = [];
$arrCss[] = "contatti.min.css";
$strHead = head('contatti', $arrCss); // richiama funzione head //
echo $strHead;
?>



<body>

    <header>

        <?php
        
        $strMenu = menu($menu); // richiama funzione menu //
        echo $strMenu;

        ?>

    </header>

    <main>
        <h1>I AM FRANCESCO SPINAZZOLA</h1>
 

    <!-- FORM DATI -->
    <?php
    if (!$inviato) {

    ?>
        <div class="contenitore-form-mappa">
            <div class="contatti">
                <h2>SE DESIDERI ESSERE CONTATTATO</h2>
                <form action="contatti.php?inviato=1" method="POST" novalidate>
                    <fieldset class="card">

                        <label for="nome" <?php echo $clsErroreNome; ?>>Nome <span>*</span></label>
                        <input type="text" id="nome" name="nome" placeholder="nome" maxlength="25" value="<?php echo $nome; ?>" />

                        <label for="cognome" <?php echo $clsErroreCognome; ?>>Cognome <span>*</span></label>
                        <input type="text" id="cognome" name="cognome" placeholder="cognome" maxlength="25" value="<?php echo $cognome; ?>" />

                        <label for="email" <?php echo $clsErroreEmail; ?>>E-mail <span>*</span></label>
                        <input type="email" id="email" name="email" placeholder="e-mail" maxlength="40" minlength="10" value="<?php echo $email; ?>" />

                        <label for="argomento" <?php echo $clsErroreArgomento; ?>>Argomento <span>*</span></label>
                        <select name="argomento" id="argomento">
                            <option value="" <?php echo ($argomento == "") ? 'selected' : ''; ?>>Seleziona argomento</option>
                            <option value="1" <?php echo ($argomento == 1) ? 'selected' : ''; ?>>Interessato</option>
                            <option value="2" <?php echo ($argomento == 2) ? 'selected' : ''; ?>>Dubbioso</option>
                        </select>

                        <label for="testo" <?php echo $clsErroreTesto; ?>>Testo <span>*</span></label>
                        <textarea id="testo" name="testo" placeholder="testo" maxlength="500"><?php echo $testo; ?></textarea>

                        <div><button type="reset" title="clicca per annulare">annulla</button>
                            <button type="submit" title="clicca per accedere">invia</button>
                        </div>
                    </fieldset>
                </form>
            </div>
        <?php
    } else {
        $argomento = ($argomento == 1) ? "Interessato" : "Dubbioso";
        $str = "<strong>Nome:</strong> %s<br>" .
            "<strong>Cognome:</strong>: %s<br>" .
            "<strong>E-Mail:</strong> %s<br>" .
            "<strong>Argomento:</strong> %s<br>" .
            "<strong>Testo:</strong><br>%s<br>";
        $str = sprintf($str, $nome, $cognome, $email, $argomento, $testo);
        echo '<div class="riepilogo">';
        echo "<h1>Grazie per averci contattato</h1>Ecco il riepilogo dei tuoi dati:<br><br>$str";


        $str = str_replace('<br>', chr(10), $str);

        $file = 'datiUtente.txt';

        $str = str_repeat("-", 30) . chr(10) . $str . chr(10) . str_repeat("-", 30) . chr(10);
        $rit = UT::scriviTesto($file, $str);

        if ($rit) {
            echo "<br>" . str_repeat("-", 30) . "<br>Modulo inviato correttamente<br>";
        } else {
            echo "<br>" . str_repeat("-", 30) . "<br>Problema nell'invio del modulo<br>";
        }
        echo "</div>";
    }
        ?>

        <div class="mappa">
            <h2>INDICAZIONE MAPPA</h2>
            <iframe
                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2821.463760144219!2d7.645665275037975!3d44.995203464610405!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x47881244e0d5897b%3A0x1b7ab3f8d62eb5eb!2sVia%20Giuseppe%20Giusti%2C%203%2C%2010042%20Nichelino%20TO!5e0!3m2!1sit!2sit!4v1716892363978!5m2!1sit!2sit"
                width="400" height="400" style="border:0;" allowfullscreen="" loading="lazy"
                referrerpolicy="no-referrer-when-downgrade"></iframe>
        </div>
        </div>

        <!-- DATI PERSONALI -->
        <div class="rigaFooter">
            <div class="dati">
                <ul>
                    <li>
                        <p>vienici a trovare in:</p>
                        <address>
                            Largo Giusti,3 <br> 10100 Nichelino (to)<br>Italia
                        </address>

                    </li>

                    <li>
                        <p>email - telefono:</p>
                        <address>
                            <ul>
                                <li>
                                    <a href="francescospinazzola084@gmail.com"
                                        title="scrivici una email">francescospinazzola084@gmail.com</a>
                                </li>
                                <li>
                                    <a href="tel:3402330981" title="telefonaci"> 3402330981</a>
                                </li>
                            </ul>
                        </address>
                    </li>

                    <li>
                        <p>seguici su:</p>
                        <address>
                            <ul>
                                <li>
                                    <a href="http://www.facebook.com" title="seguici su facebook">facebook</a>
                                </li>
                                <li>
                                    <a href="http://www.twitter.com" title="seguici su twitter">twitter</a>
                                </li>
                                <li>
                                    <a href="http://wwww.instagram.com" title="seguici su instagram">instagram</a>
                                </li>
                            </ul>
                        </address>
                    </li>
                </ul>
            </div>
        </div>
        </main>

        <!-- FOOTER -->


            <?php
            $strFooter =  footer(); // richiama la funzione footer //
            echo $strFooter;
            ?>

     




</body>

</html>