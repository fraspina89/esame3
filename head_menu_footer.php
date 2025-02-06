<?php

ini_set("auto_detect_line_endings", true);

use MieClassi\Utility as UT;

?>

<!-- FUNZIONE HEAD -->
<?php
function head($title)
{
    echo '<!DOCTYPE html>
    <html lang="it">
    
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>' . $title . '</title>
    </head>';
}
?>


<body>

    <!-- FUNZIONE MENU -->
    <?php
    function menu($menu)
    {
        $arr = json_decode(UT::leggiTesto($menu));
        $selezionato = UT::richiestaHTTP("selezionato");
        $selezionato = ($selezionato == null) ? 1 : $selezionato;
        echo '<header>
            <nav class="menu">
                <input id="controllo" type="checkbox">
                <label class="label-controllo" for="controllo">
                    <span></span>
                </label>
                <a href="#" class="logo">MENU</a>
                <ul id="menu">';

        foreach ($arr as $link) {
            $n = $link->id;
            $classeSelezionato = ($n == $selezionato) ? ' class="selezionato"' : '';
            printf(
                '<li %s><a href="%s?selezionato=%u" title="%s" class="vociMenu">%s</a></li>',
                $classeSelezionato,
                $link->url,
                $link->id,
                $link->title,
                $link->nome
            );
        }

        echo '      </ul>
            </nav>
          </header>';
    }
    ?>
    <!-- FUNZIONE LAVORI -->
    <?php
    function lavori($file)
    {
        $lav = json_decode(UT::leggiTesto($file));
        $selezionato = UT::richiestaHTTP("selezionato");
        foreach ($lav as $lavoro) {
            $n = $lavoro->id;
            $tmp = '<div><h2>%s</h2><a href="work.php?idWork=%u&selezionato=%u" title="%s"><img src="./img/%s" alt="%s" ></a></div>';
            printf($tmp, $lavoro->titolo, $lavoro->id, $selezionato, $lavoro->title, $lavoro->img, $lavoro->alt);
        }
    }
    ?>




    <!-- FUNZIONE FOOTER -->
    <?php
    function footer()
    {
        echo '<footer>
            <div class="privacy">
                <ul>
                    <li>
                        <a href="#" title="leggi la cookies policy">cookies policy</a>
                    </li>
                    <li>
                        <a href="#" title="leggi la privacy policy">privacy policy</a>
                    </li>
                    <li>
                        <a href="#" title="leggi la privacy copyright">copyright</a>
                    </li>
                </ul>
            </div>
          </footer>';
    }
    ?>


</body>