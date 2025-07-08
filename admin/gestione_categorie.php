<?php
session_start();
require_once("config.php");
require_once("componenti_backend.php");

// Protezione: solo utenti loggati //
if (!isset($_SESSION['loggedin'])) {
    header("Location: login.php");
    exit;
}


// // MESSAGGIO DI CONFERMA //
// Recupera eventuale messaggio di conferma da mostrare all'utente (es: "Categoria aggiunta con successo!")//
$msg = "";
if (isset($_SESSION['msg'])) {
    $msg = $_SESSION['msg'];
    unset($_SESSION['msg']);
}

// Gestione variabili//
$op = $_GET['op'] ?? '';
$idSel = $_GET['idSel'] ?? '';
$errore = '';
$categoriaMod = null;

// OPERAZIONI POST: aggiunta o modifica categoria //
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? '';
    $nome = trim($_POST['nome']);

    // Validazione lato server //
    if ($nome === '' || strlen($nome) < 2) {
        $errore = "Il nome è obbligatorio e deve contenere almeno 2 caratteri.";
    } else {
        if ($op === 'ADD') {
            // Inserimento nuova categoria //
            $stmt = $conn->prepare("INSERT INTO categorie (nome) VALUES (?)");
            if (!$stmt) {
                $errore = "Errore nella preparazione della query: " . htmlspecialchars($conn->error);
            } else {
                $stmt->bind_param("s", $nome);
                if (!$stmt->execute()) {
                    $errore = "Errore nell'inserimento: " . htmlspecialchars($stmt->error);
                } else {
                    $stmt->close();
                    $_SESSION['msg'] = "Categoria aggiunta con successo!";
                    header("Location: gestione_categorie.php");
                    exit;
                }
                $stmt->close();
            }
        } elseif ($op === 'MOD' && $id !== '') {
            // Modifica categoria esistente //
            $stmt = $conn->prepare("UPDATE categorie SET nome=? WHERE id=?");
            if (!$stmt) {
                $errore = "Errore nella preparazione della query: " . htmlspecialchars($conn->error);
            } else {
                $stmt->bind_param("si", $nome, $id);
                if (!$stmt->execute()) {
                    $errore = "Errore nella modifica: " . htmlspecialchars($stmt->error);
                } else {
                    $stmt->close();
                    $_SESSION['msg'] = "Categoria modificata con successo!";
                    header("Location: gestione_categorie.php");
                    exit;
                }
                $stmt->close();
            }
        }
    }
}


// OPERAZIONI GET :  elimina o seleziona categoria per modifica //
if ($op === 'DEL' && $idSel !== '') {
    $id = (int)$idSel;
    if (!$conn->query("DELETE FROM categorie WHERE id = $id")) {
        $errore = "Errore nella cancellazione: " . htmlspecialchars($conn->error);
    } else {
        $_SESSION['msg'] = "Categoria eliminata con successo!";
        header("Location: gestione_categorie.php");
        exit;
    }
}

if ($op === 'FORM-MOD' && $idSel !== '') {
    $id = (int)$idSel;
    $res = $conn->query("SELECT * FROM categorie WHERE id = $id");
    if ($res && $res->num_rows === 1) {
        $categoriaMod = $res->fetch_assoc();
    } elseif (!$res) {
        $errore = "Errore nella selezione: " . htmlspecialchars($conn->error);
    }
}

// Stampa l'header HTML della pagina backend con il titolo "GESTIONE CATEGORIE"//
echo headBackend("GESTIONE CATEGORIE");
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
        margin-bottom: 15px;
    }

    form {
        background-color: #ffe4c4;
        padding: 20px;
        border: 2px solid #ce5c00;
        border-radius: 8px;
        max-width: 400px;
        margin-bottom: 30px;
    }

    form label {
        display: block;
        margin-top: 10px;
        color: #ce5c00;
        font-weight: bold;
    }

    form input[type="text"] {
        width: 100%;
        padding: 8px;
        margin-top: 5px;
        border: 1px solid #ccc;
        border-radius: 4px;
    }

    form button {
        margin-top: 15px;
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
        margin-right: 10px;
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
        max-width: 400px;
    }
</style>
</head>

<body>

    <h2>Gestione Categorie</h2>

    <?php if ($errore): ?>
        <p class="errore"><?= $errore ?></p>
    <?php endif; ?>

    <!-- Form per aggiungere o modificare una categoria -->
    <?php echo stampaFormCategoria($categoriaMod); ?>

    <hr>

    <!-- Elenco delle categorie già presenti -->
    <h3>Elenco categorie</h3>
    <?php echo stampaTabellaCategorie($conn); ?>

    <!-- Script JS per la validazione lato client del form categoria -->
    <?php echo validazioneJSCategoria(); ?>

    <!-- Link per tornare alla dashboard -->
    <p><a href="dashboard.php">⬅️ Torna alla Dashboard</a></p>

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