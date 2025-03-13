<?php

ini_set("auto_detect_line_endings", true);

require_once('./MieClassi/Utility.php');
require_once('head_menu_footer.php');

use MieClassi\Utility as UT;

$menu = "./json/menu.json";

$file = "./json/work.json";


$arrCss = [];
$arrCss[] = "portfolio.min.css";
$strHead = head('portfolio', $arrCss); // richiama funzione head //
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

            $strLavori = lavori($file);  // richiama la funzione lavori //
            echo $strLavori;

            ?>
        </div>
    </main>

    <!-- FOOTER -->

    <?php

    $strFooter =  footer(); // richiama la funzione footer //
    echo $strFooter;

    ?>






</body>

</html>