<?php

ini_set("auto_detect_line_endings", true);

require_once('./MieClassi/Utility.php');
require_once('head_menu_footer.php');

use MieClassi\Utility as UT;

$menu = "./json/menu.json";

$file = "./json/work.json";
$lav = json_decode(UT::leggiTesto($file));

// $selezionato = UT::richiestaHTTP("selezionato");
$idWork = UT::richiestaHTTP("idWork");
$idWork = ($idWork == null) ? 1 : $idWork;

?>



<?php
head('Portfolio'); // richiama funzione head //
?>

<!-- CSS -->
<link href="./css/portfolio.min.css" rel="stylesheet">
<link href="./css/comune.min.css" rel="stylesheet">



<body>
    <header>

        <?php
        menu($menu); // richiama funzione menu //
        ?>

    </header>


    <main>
        <!-- LAVORO SELEZIONATO -->

        <h1>I AM FRANCESCO SPINAZZOLA</h1>
        <h2>LAVORO SELEZIONATO <?php echo $idWork; ?></h2>
        <div class="lavoro">
            <?php
            foreach ($lav as $lavoro) {
                if ($idWork == $lavoro->id) {
                    $tmp = '
                    <div>
                        <a href="work.php?idWork=%u"  >
                            <img class="immagine" src="./img/%s" alt="%s">
                        </a>
                        <p>%s</p>
                        <p>%s</p>
                        <p class="azienda">%s</p>
                    </div>';
                    printf($tmp, $lavoro->id, $lavoro->img, $lavoro->alt , $lavoro->description, $lavoro->data, $lavoro->azienda);
                }
            }
            ?>

        </div>

        <!-- ELENCO DEI LAVORI -->

        <h3>ALTRI LAVORI</h3>
        <div class="lavori">

            <?php
            lavori($file); // richiama la funzione dei lavori //
            ?>

        </div>
    </main>

    <!-- FOOTER -->

    <?php
    footer(); // richiama funzione il footer //
    ?>






</body>

</html>