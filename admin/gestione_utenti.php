<?php
session_start();
require_once("config.php");
require_once("componenti_backend.php");

// Protezione accesso
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit;
}


// --- MESSAGGIO DI CONFERMA ---
$msg = "";
if (isset($_SESSION['msg'])) {
    $msg = $_SESSION['msg'];
    unset($_SESSION['msg']);
}


// Operazioni
$op = $_GET['op'] ?? '';
$idSel = isset($_GET['idSel']) ? (int)$_GET['idSel'] : null;
$utente = null;
$errore = "";

// AGGIUNTA
if ($op === 'ADD' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome']);
    $cognome = trim($_POST['cognome']);
    $email = trim($_POST['email']);
    $argomento = $_POST['argomento'];
    $testo = trim($_POST['testo']);

    if (strlen($nome) < 3 || strlen($nome) > 25) {
        $errore = "Il nome deve essere tra 3 e 25 caratteri.";
    } elseif (strlen($cognome) < 3 || strlen($cognome) > 25) {
        $errore = "Il cognome deve essere tra 3 e 25 caratteri.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) < 10 || strlen($email) > 100) {
        $errore = "Inserisci un'email valida (10-100 caratteri).";
    } elseif (empty($argomento)) {
        $errore = "Seleziona un argomento.";
    } elseif (strlen($testo) < 10 || strlen($testo) > 500) {
        $errore = "Il testo deve essere tra 10 e 500 caratteri.";
    } else {
        $stmt = $conn->prepare("INSERT INTO utenti (nome, cognome, email, argomento, testo) VALUES (?, ?, ?, ?, ?)");
        if (!$stmt) {
            $errore = "Errore nella preparazione della query: " . htmlspecialchars($conn->error);
        } else {
            $stmt->bind_param("sssss", $nome, $cognome, $email, $argomento, $testo);
            if (!$stmt->execute()) {
                $errore = "Errore nell'inserimento: " . htmlspecialchars($stmt->error);
            } else {
                $stmt->close();
                $_SESSION['msg'] = "Utente aggiunto con successo!";
                header("Location: gestione_utenti.php");
                exit;
            }
            $stmt->close();
        }
    }
}

// MODIFICA
if ($op === 'MOD' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)$_POST['id'];
    $nome = trim($_POST['nome']);
    $cognome = trim($_POST['cognome']);
    $email = trim($_POST['email']);
    $argomento = $_POST['argomento'];
    $testo = trim($_POST['testo']);

    if (strlen($nome) < 3 || strlen($nome) > 25) {
        $errore = "Il nome deve essere tra 3 e 25 caratteri.";
    } elseif (strlen($cognome) < 3 || strlen($cognome) > 25) {
        $errore = "Il cognome deve essere tra 3 e 25 caratteri.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) < 10 || strlen($email) > 100) {
        $errore = "Inserisci un'email valida (10-100 caratteri).";
    } elseif (empty($argomento)) {
        $errore = "Seleziona un argomento.";
    } elseif (strlen($testo) < 10 || strlen($testo) > 500) {
        $errore = "Il testo deve essere tra 10 e 500 caratteri.";
    } else {
        $stmt = $conn->prepare("UPDATE utenti SET nome=?, cognome=?, email=?, argomento=?, testo=? WHERE id=?");
        if (!$stmt) {
            $errore = "Errore nella preparazione della query: " . htmlspecialchars($conn->error);
        } else {
            $stmt->bind_param("sssssi", $nome, $cognome, $email, $argomento, $testo, $id);
            if (!$stmt->execute()) {
                $errore = "Errore nella modifica: " . htmlspecialchars($stmt->error);
            } else {
                $stmt->close();
                $_SESSION['msg'] = "Utente modificato con successo!";
                header("Location: gestione_utenti.php");
                exit;
            }
            $stmt->close();
        }
    }
}


// CANCELLAZIONE
if ($op === 'DEL' && $idSel) {
    $stmt = $conn->prepare("DELETE FROM utenti WHERE id = ?");
    if (!$stmt) {
        $errore = "Errore nella preparazione della query: " . htmlspecialchars($conn->error);
    } else {
        $stmt->bind_param("i", $idSel);
        if (!$stmt->execute()) {
            $errore = "Errore nell'eliminazione: " . htmlspecialchars($stmt->error);
        } else {
            $stmt->close();
            $_SESSION['msg'] = "Utente eliminato con successo!";
            header("Location: gestione_utenti.php");
            exit;
        }
        $stmt->close();
    }
}


// FORM DI MODIFICA
if ($op === 'FORM-MOD' && $idSel) {
    $stmt = $conn->prepare("SELECT * FROM utenti WHERE id = ?");
    if (!$stmt) {
        $errore = "Errore nella preparazione della query: " . htmlspecialchars($conn->error);
    } else {
        $stmt->bind_param("i", $idSel);
        if (!$stmt->execute()) {
            $errore = "Errore nel recupero dati: " . htmlspecialchars($stmt->error);
        } else {
            $res = $stmt->get_result();
            $utente = $res->fetch_assoc();
        }
        $stmt->close();
    }
}

echo headBackend("GESTIONE UTENTI");
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
    form input[type="email"],
    form textarea,
    form select {
        width: 100%;
        padding: 10px;
        margin-top: 5px;
        border: 1px solid #ccc;
        border-radius: 5px;
    }

    form textarea {
        min-height: 120px;
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
</style>

</head>

<body>
    <h2>Gestione Utenti</h2>

    <?php if (!empty($errore)): ?>
        <div class="errore"><?= $errore ?></div>
    <?php endif; ?>

    <?= stampaFormUtente($utente) ?>

    <h3>Elenco Utenti</h3>
    <?= stampaTabellaUtenti($conn) ?>

    <p><a href="dashboard.php">Torna alla Dashboard</a></p>

    <?= validazioneJSUtente() ?>

    <?php if (!empty($msg)): ?>
        <script>
            window.addEventListener("DOMContentLoaded", function() {
                alert("<?= addslashes($msg) ?>");
            });
        </script>
    <?php endif; ?>
</body>

</html>