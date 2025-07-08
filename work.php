<?php
ini_set("auto_detect_line_endings", true);

// Includi la configurazione del database e i componenti comuni (head, menu, footer) //
require_once('./MieClassi/Utility.php');
require_once('head_menu_footer.php');
require_once('admin/config.php'); // Connessione al DB

// Usa la classe Utility //
use MieClassi\Utility as UT;

// Percorso del file JSON per il menu //
$menu = "./json/menu.json";

// Recupera l'ID del lavoro selezionato dalla query string (GET o POST) //
$idWork = UT::richiestaHTTP("idWork");
// Se non è stato passato nessun idWork, imposta 1 come default //
$idWork = ($idWork == null) ? 1 : $idWork;

$arrCss = [];
$arrCss[] = "portfolio.min.css";

// Genera e stampa la sezione <head> della pagina //
echo head('work', $arrCss);
?>

<body>
    <header>
        <?php echo menu($menu); // Stampa il menu principale //
        ?>
    </header>

    <main>
        <!-- LAVORO SELEZIONATO -->
        <h1>I AM FRANCESCO SPINAZZOLA</h1>
        <h2>LAVORO SELEZIONATO <?php echo $idWork; ?></h2>

        <?php
        // Query per recuperare il lavoro specifico //
        $query = "SELECT * FROM lavori WHERE id = $idWork";
        $result = $conn->query($query);

        if ($result && $result->num_rows > 0) {
            // Se il lavoro esiste, mostra i dettagli //
            $lavoro = $result->fetch_assoc();
        ?>
            <div class="lavoro">
                <p>
                    <strong><?php echo nl2br(htmlspecialchars($lavoro['azienda'])); ?></strong><br><br>
                    <?php echo nl2br(htmlspecialchars($lavoro['description'])); ?><br><br>
                    <?php echo htmlspecialchars($lavoro['data']); ?>
                </p>
                <img class="immagine" src="./img/<?php echo htmlspecialchars($lavoro['img']); ?>" alt="<?php echo htmlspecialchars($lavoro['alt']); ?>">
            </div>
        <?php
        } else {
            // Se il lavoro non esiste, mostra un messaggio di errore //
            echo "<p>Lavoro non trovato.</p>";
        }
        ?>

        <!-- ELENCO DEI LAVORI -->
        <h3>ALTRI LAVORI</h3>
        <div class="lavori">
            <?php echo lavori($conn);  // Stampa l'elenco degli altri lavori //
            ?>
        </div>
    </main>

    <?php echo footer(); // Stampa il footer // 
    ?>
</body>

</html>