<?php

// Avvia la sessione per gestire l'autenticazione dell'utente //
session_start();
require_once("config.php");
require_once("componenti_backend.php");

// Protezione: solo utenti loggati //
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit;
}

// --- MESSAGGIO DI CONFERMA --- //
// Recupera eventuale messaggio di conferma da mostrare all'utente //
$msg = "";
if (isset($_SESSION['msg'])) {
    $msg = $_SESSION['msg'];
    unset($_SESSION['msg']);
}

$errore = "";

// Carica tutte le categorie disponibili dal database //
$categorie = [];
$resCat = $conn->query("SELECT * FROM categorie ORDER BY nome ASC");
if ($resCat) {
    while ($cat = $resCat->fetch_assoc()) {
        $categorie[] = $cat;
    }
} else {
    $errore = "Errore nel caricamento categorie: " . htmlspecialchars($conn->error);
}

// Gestione operazioni (aggiunta, modifica, eliminazione, caricamento per modifica) //
$op = $_GET['op'] ?? '';
$idSel = isset($_GET['idSel']) ? (int)$_GET['idSel'] : null;
$lavoro = null;

// --- AGGIUNTA ---
if ($op === 'ADD' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $titolo = trim($_POST['titolo']);
    $descrizione = trim($_POST['description']);
    $data = trim($_POST['data']);
    $azienda = trim($_POST['azienda']);
    $categoria_id = (int)$_POST['categoria_id'];

    // Validazione lato server //
    if (strlen($titolo) < 3 || strlen($titolo) > 100) {
        $errore = "Il titolo deve essere tra 3 e 100 caratteri.";
    } elseif (strlen($descrizione) < 10) {
        $errore = "La descrizione deve essere di almeno 10 caratteri.";
    } elseif ($data !== "" && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $data)) {
        $errore = "La data deve essere nel formato YYYY-MM-DD.";
    } elseif (strlen($azienda) > 100) {
        $errore = "Il nome dell'azienda può essere lungo massimo 100 caratteri.";
    } elseif ($categoria_id <= 0) {
        $errore = "Seleziona una categoria valida.";
    } else {
        // Gestione upload dell'immagine //
        if (!isset($_FILES['img']) || $_FILES['img']['error'] !== UPLOAD_ERR_OK) {
            $errore = "Seleziona un'immagine da caricare.";
        } else {
            $uploadResult = gestisciUploadImmagine($_FILES['img']);
            if (!$uploadResult['successo']) {
                $errore = $uploadResult['messaggio'];
            } else {
                $nomeFile = $uploadResult['nomeFile'];

                // Inserimento nel database con ALT automatico = "foto" //
                $stmt = $conn->prepare("INSERT INTO lavori (titolo, img, ALT, description, data, azienda, categoria_id) VALUES (?, ?, 'foto', ?, ?, ?, ?)");
                if (!$stmt) {
                    $errore = "Errore nella preparazione della query: " . htmlspecialchars($conn->error);
                } else {
                    $stmt->bind_param("sssssi", $titolo, $nomeFile, $descrizione, $data, $azienda, $categoria_id);
                    if (!$stmt->execute()) {
                        $errore = "Errore nell'inserimento: " . htmlspecialchars($stmt->error);
                        // Elimina il file caricato se l'inserimento fallisce //
                        if (file_exists('../img/' . $nomeFile)) {
                            unlink('../img/' . $nomeFile);
                        }
                    } else {
                        $stmt->close();
                        $_SESSION['msg'] = "Lavoro aggiunto con successo!";
                        header("Location: gestione_lavori.php");
                        exit;
                    }
                    $stmt->close();
                }
            }
        }
    }
}

// --- ELIMINAZIONE ---
if ($op === 'DEL' && $idSel) {
    // Prima recupera il nome del file immagine per eliminarlo //
    $stmt = $conn->prepare("SELECT img FROM lavori WHERE id = ?");
    if ($stmt) {
        $stmt->bind_param("i", $idSel);
        $stmt->execute();
        $result = $stmt->get_result();
        $lavoroData = $result->fetch_assoc();
        $stmt->close();

        // Elimina il record dal database //
        $stmt = $conn->prepare("DELETE FROM lavori WHERE id = ?");
        if (!$stmt) {
            $errore = "Errore nella preparazione della query: " . htmlspecialchars($conn->error);
        } else {
            $stmt->bind_param("i", $idSel);
            if (!$stmt->execute()) {
                $errore = "Errore nell'eliminazione: " . htmlspecialchars($stmt->error);
            } else {
                // Se l'eliminazione dal DB è riuscita, elimina anche il file //
                if ($lavoroData && !empty($lavoroData['img']) && file_exists('../img/' . $lavoroData['img'])) {
                    unlink('../img/' . $lavoroData['img']);
                }
                $stmt->close();
                $_SESSION['msg'] = "Lavoro eliminato con successo!";
                header("Location: gestione_lavori.php");
                exit;
            }
            $stmt->close();
        }
    } else {
        $errore = "Errore nella preparazione della query: " . htmlspecialchars($conn->error);
    }
}

// --- MODIFICA --- //
if ($op === 'MOD' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)$_POST['id'];
    $titolo = trim($_POST['titolo']);
    $descrizione = trim($_POST['description']);
    $data = trim($_POST['data']);
    $azienda = trim($_POST['azienda']);
    $categoria_id = (int)$_POST['categoria_id'];

    // Validazione lato server //
    if (strlen($titolo) < 3 || strlen($titolo) > 100) {
        $errore = "Il titolo deve essere tra 3 e 100 caratteri.";
    } elseif (strlen($descrizione) < 10) {
        $errore = "La descrizione deve essere di almeno 10 caratteri.";
    } elseif ($data !== "" && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $data)) {
        $errore = "La data deve essere nel formato YYYY-MM-DD.";
    } elseif (strlen($azienda) > 100) {
        $errore = "Il nome dell'azienda può essere lungo massimo 100 caratteri.";
    } elseif ($categoria_id <= 0) {
        $errore = "Seleziona una categoria valida.";
    } else {
        // Recupera l'immagine attuale per gestire l'upload //
        $stmt = $conn->prepare("SELECT img FROM lavori WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $lavoroAttuale = $result->fetch_assoc();
        $stmt->close();

        $vecchiaImmagine = $lavoroAttuale['img'] ?? '';

        // Gestione upload dell'immagine (opzionale in modifica) //
        $uploadResult = gestisciUploadImmagine($_FILES['img'] ?? [], $vecchiaImmagine);
        if (!$uploadResult['successo']) {
            $errore = $uploadResult['messaggio'];
        } else {
            $nomeFile = $uploadResult['nomeFile'];

            // Aggiornamento nel database con ALT automatico = "foto" //
            $stmt = $conn->prepare("UPDATE lavori SET titolo = ?, img = ?, ALT = 'foto', description = ?, data = ?, azienda = ?, categoria_id = ? WHERE id = ?");
            if (!$stmt) {
                $errore = "Errore nella preparazione della query: " . htmlspecialchars($conn->error);
            } else {
                $stmt->bind_param("ssssssi", $titolo, $nomeFile, $descrizione, $data, $azienda, $categoria_id, $id);
                if (!$stmt->execute()) {
                    $errore = "Errore nella modifica: " . htmlspecialchars($stmt->error);
                } else {
                    $stmt->close();
                    $_SESSION['msg'] = "Lavoro modificato con successo!";
                    header("Location: gestione_lavori.php");
                    exit;
                }
                $stmt->close();
            }
        }
    }
}

// --- CARICAMENTO PER MODIFICA (precompila il form con i dati del lavoro selezionato) --- //
if ($op === 'FORM-MOD' && $idSel) {
    $stmt = $conn->prepare("SELECT * FROM lavori WHERE id = ?");
    if (!$stmt) {
        $errore = "Errore nella preparazione della query: " . htmlspecialchars($conn->error);
    } else {
        $stmt->bind_param("i", $idSel);
        if (!$stmt->execute()) {
            $errore = "Errore nel recupero dati: " . htmlspecialchars($stmt->error);
        } else {
            $res = $stmt->get_result();
            $lavoro = $res->fetch_assoc();
        }
        $stmt->close();
    }
}

// Stampa l'header HTML della pagina backend con il titolo "GESTIONE LAVORI" //
echo headBackend("GESTIONE LAVORI");
?>

<style>
    body {
        background-color: rgb(168, 204, 214);
        font-family: Arial, sans-serif;
        padding: 40px;
        color: #1c1c1b;
    }

    h2,
    h3 {
        color: #eaad61;
        margin-bottom: 20px;
    }

    form {
        background-color: #ffe4c4;
        padding: 20px;
        border: 2px solid #ce5c00;
        border-radius: 10px;
        max-width: 600px;
        margin-bottom: 40px;
    }

    form label {
        display: block;
        margin-top: 12px;
        color: #ce5c00;
        font-weight: bold;
    }

    form input[type="text"],
    form input[type="file"],
    form input[type="date"],
    form textarea,
    form select {
        width: 100%;
        padding: 10px;
        margin-top: 5px;
        border: 1px solid #ccc;
        border-radius: 5px;
    }

    form button {
        margin-top: 20px;
        padding: 10px 20px;
        background-color: #e9f0e9;
        color: #eaad61;
        border: 1px solid #71600a;
        border-radius: 6px;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }

    form button:hover {
        background-color: #ede913;
        color: #1c1c1b;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
    }

    table,
    th,
    td {
        border: 1px solid #71600a;
    }

    th,
    td {
        padding: 12px;
        text-align: left;
        background-color: #ffffff;
    }

    th {
        background-color: #eaad61;
        color: #1c1c1b;
    }

    a {
        color: #eaad61;
        text-decoration: none;
    }

    a:hover {
        color: #ede913;
    }

    .errore {
        background-color: #f8d7da;
        color: #721c24;
        padding: 10px;
        border: 1px solid #f5c6cb;
        border-radius: 6px;
        margin-bottom: 15px;
        max-width: 600px;
    }

    .immagine-attuale {
        max-width: 150px;
        max-height: 100px;
        margin: 10px 0;
        border: 1px solid #ccc;
        border-radius: 5px;
    }
</style>

</head>

<body>
    <h2>Gestione Lavori</h2>

    <!-- Mostra eventuale messaggio di errore -->
    <?php if (!empty($errore)): ?>
        <div class="errore"><?= $errore ?></div>
    <?php endif; ?>

    <!-- Form per aggiungere o modificare un lavoro -->
    <?= stampaFormLavoro($categorie, $lavoro) ?>

    <!-- Elenco dei lavori già presenti -->
    <h3>Elenco Lavori</h3>
    <?= stampaTabellaLavori($conn) ?>

    <!-- Link per tornare alla dashboard -->
    <p><a href="dashboard.php">Torna alla Dashboard</a></p>

    <!-- Script JS per la validazione lato client del form lavoro -->
    <?= validazioneJSLavoro() ?>

    <!-- Mostra un alert JS se c'è un messaggio di conferma -->
    <?php if (!empty($msg)): ?>
        <script>
            window.addEventListener("DOMContentLoaded", function() {
                alert("<?= addslashes($msg) ?>");
            });
        </script>
    <?php endif; ?>
</body>

</html>