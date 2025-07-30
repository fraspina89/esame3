<?php

ini_set("auto_detect_line_endings", true);

// Includi le classi e i componenti necessari //
require_once("admin/config.php");
require_once("head_menu_footer.php");

// Percorso del file JSON per il menu //
$menu = "./json/menu.json";

$arrCss = [];
$arrCss[] = "portfolio.min.css";

// Genera e stampa la sezione <head> della pagina //
echo head('portfolio', $arrCss);

?>

<body>
    <header>
        <?php echo menu($menu); // Stampa il menu principale // 
        ?>
    </header>

    <main>
        <h1>I AM FRANCESCO SPINAZZOLA</h1>

        <!-- WORK -->
        <h2>LAVORO ATTUALE</h2>
        <div class="work">
            <p> Lorem ipsum dolor sit, amet consectetur adipisicing elit. Eveniet aliquam fugit exercitationem
                provident, suscipit modi eaque nesciunt non minima voluptatem laudantium, corrupti vel velit commodi
                quae quod ipsam maxime maiores..<br>
                Lorem ipsum dolor sit amet consectetur adipisicing elit. Veniam aperiam sint amet obcaecati minus
                nisi perferendis, quia in quas ducimus, provident non harum veritatis nesciunt, maiores fugiat a eos
                cum.</p>
            <img src="./img/lavoro.jpg" alt="lavoro" title="lavoro">
        </div>

        <!-- LAVORI -->
        <h3>ALTRI LAVORI</h3>
        <div class="lavori">
            <?php
            // Query per recuperare tutti i lavori dal database //
            $query = "SELECT * FROM lavori ORDER BY id ASC";
            $res = $conn->query($query);
            if ($res) {
                // Cicla su tutti i lavori e stampali //
                while ($lavoro = $res->fetch_assoc()) {
                    echo '<div>';
                    echo '<h2>' . htmlspecialchars($lavoro['titolo']) . '</h2>';
                    echo '<a href="work.php?idWork=' . $lavoro['id'] . '" title="' . htmlspecialchars($lavoro['titolo']) . '">';
                    echo '<img src="./img/' . htmlspecialchars($lavoro['img']) . '" alt="' . htmlspecialchars($lavoro['ALT']) . '">';
                    echo '</a>';
                    echo '</div>';
                }
            } else {
                // Messaggio se non ci sono lavori //
                echo "<p>Nessun lavoro trovato.</p>";
            }
            ?>
        </div>
    </main>

    <?php echo footer(); // Stampa il footer //
    ?>
</body>

</html>