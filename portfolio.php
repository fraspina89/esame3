<?php

ini_set("auto_detect_line_endings", true);

require_once('./MieClassi/Utility.php');
require_once('head_menu_footer.php');

use MieClassi\Utility as UT;

$menu = "./json/menu.json";

$file = "./json/work.json";
$lav = json_decode(UT::leggiTesto($file));
// $selezionato = UT::richiestaHTTP("selezionato");
// $selezionato = ($selezionato == null) ? 1 : $selezionato;

?>


<?php
head('Portfolio'); // richiama funzione head //
?>

<!-- CSS -->
<link href="css/portfolio.min.css" rel="stylesheet">
<link href="css/comune.min.css" rel="stylesheet">



<body>
    <header>

        <?php
        menu($menu); // richiama funzione menu //
        ?>

    </header>


    <main>

        <h1>I AM FRANCESCO SPINAZZOLA</h1>

        <!-- WORK -->

        <h2>LAVORO ATTUALE</h2>
        <div class="work">

            <p>
                Lorem ipsum dolor sit, amet consectetur adipisicing elit. Eveniet aliquam fugit exercitationem
                provident, suscipit modi eaque nesciunt non minima voluptatem laudantium, corrupti vel velit commodi
                quae quod ipsam maxime maiores..<br>
                Lorem ipsum dolor sit amet consectetur adipisicing elit. Veniam aperiam sint amet obcaecati minus
                nisi perferendis, quia in quas ducimus, provident non harum veritatis nesciunt, maiores fugiat a eos
                cum.

            </p>
            <img src="./img/lavoro.jpg" alt="lavoro" title="lavoro">
        </div>



        <!-- LAVORI  -->

        <h3>ALTRI LAVORI</h3>
        <div class="lavori">

            <?php
            foreach ($lav as $lavoro) {
                
                
                $tmp = '<div>
                <h2>%s</h2>
                    <a href="%s" title="%s">
                       <img src="./img/%s" alt="%s" > 
                         </a>
                         </div>';

                printf($tmp, $lavoro->titolo, $lavoro->url, $lavoro->title, $lavoro->img, $lavoro->alt);
            }

            ?>

        </div>
    </main>

    <!-- FOOTER -->

    <?php
    footer(); // richiama la funzione footer //
    ?>






</body>

</html>