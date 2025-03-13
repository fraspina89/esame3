<?php

ini_set("auto_detect_line_endings", true);

require_once('./MieClassi/Utility.php');
require_once('head_menu_footer.php');

use MieClassi\Utility as UT;

$menu = "./json/menu.json";


$arrCss = [];
$arrCss[] = "index.min.css";
$strHead = head('index', $arrCss); // richiama funzione head //
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

        <!-- PRESENTAZIONE -->
        <div class="presentazione">

            <h1>CHI SONO</h1>
            <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Nulla dolor dicta officiis,
                quo nostrum ratione nemo. Enim ipsum error quasi.
                Cupiditate quia autem molestias nam quisquam consectetur pariatur dolore eaque!</p>
            <img src="./img/IO.jpg" alt="presentazione" title="presentazione">
        </div>

        <!-- STUDI -->
        <div class="studi">

            <!-- LAVORI -->
            <div class="lavori">
                <h2>STUDI E QUALIFICHE</h2>
                <img src="./img/progetti.jpg" alt="studi" title="studi">
                <p>Lorem ipsum dolor sit amet consectetur adipisicing elit.
                    Nostrum aperiam dolorem nihil officia voluptas cum aliquam illo doloribus
                    aspernatur non obcaecati dolores dicta culpa minus voluptatum, dolorum sed accusantium inventore?
                </p>
            </div>

            <!-- PROGETTI -->
            <div class="progetti">
                <h2>OBIETTIVI FUTURI</h2>
                <img src="./img/studi.jpg" alt="progetti" title="progetti">
                <p>Lorem, ipsum dolor sit amet consectetur adipisicing elit. Inventore amet, ipsum nesciunt
                    reprehenderit,
                    ea odio temporibus earum error incidunt ut expedita quo, sit aliquid quaerat quos provident tempore
                    tenetur aperiam?</p>
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