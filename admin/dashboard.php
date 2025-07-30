<?php

// Avvia la sessione per gestire l'autenticazione dell'utente //
session_start();

// Importa le funzioni e componenti comuni del backend //
require_once("componenti_backend.php");
require_once("config.php");

// Controlla se l'utente è autenticato //
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit;
}

// Recupera i dati dell'utente dalla sessione
$user = $_SESSION['user'];

$errore = '';
$msg = '';
$modifica = false;
$clienteMod = null;

// GESTIONE CLIENTI //

// Aggiunta/Modifica cliente
if (isset($_POST['salva'])) {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    $modifica = $id > 0;

    if ($username !== '' && $email !== '' && ($password !== '' || $modifica)) {
        if ($modifica) {
            // Modifica cliente esistente
            if ($password !== '') {
                // Aggiorna anche la password
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $conn->prepare("UPDATE clienti SET username = ?, email = ?, password = ? WHERE id = ?");
                $stmt->bind_param("sssi", $username, $email, $hashedPassword, $id);
            } else {
                // Aggiorna solo username e email
                $stmt = $conn->prepare("UPDATE clienti SET username = ?, email = ? WHERE id = ?");
                $stmt->bind_param("ssi", $username, $email, $id);
            }
            
            if ($stmt->execute()) {
                $msg = "Cliente modificato con successo!";
            } else {
                $errore = "Errore nella modifica: " . htmlspecialchars($stmt->error);
            }
        } else {
            // Nuovo cliente
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO clienti (username, email, password) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $username, $email, $hashedPassword);
            
            if ($stmt->execute()) {
                $msg = "Cliente aggiunto con successo!";
            } else {
                $errore = "Errore nell'inserimento: " . htmlspecialchars($stmt->error);
            }
        }
        $stmt->close();
    } else {
        $errore = "Compila tutti i campi obbligatori.";
    }
}

// Eliminazione cliente
if (isset($_GET['del'])) {
    $id = (int)$_GET['del'];
    $stmt = $conn->prepare("DELETE FROM clienti WHERE id = ?");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        $msg = "Cliente eliminato con successo!";
    } else {
        $errore = "Errore nell'eliminazione: " . htmlspecialchars($stmt->error);
    }
    $stmt->close();
    
    // Redirect per evitare di riprocessare l'eliminazione
    header("Location: dashboard.php");
    exit;
}

// Caricamento cliente per modifica
if (isset($_GET['edit'])) {
    $id = (int)$_GET['edit'];
    $stmt = $conn->prepare("SELECT * FROM clienti WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $res = $stmt->get_result();
    $clienteMod = $res->fetch_assoc();
    $modifica = $clienteMod !== null;
    $stmt->close();
}

// Stampa l'header HTML della pagina backend con il titolo "DASHBOARD"
echo headBackend("DASHBOARD");
?>

<style>
    body {
        background-color: rgb(168, 204, 214);
        font-family: Arial, sans-serif;
        padding: 40px;
        color: #1c1c1b;
    }

    h2, h3 {
        color: #eaad61;
        margin-bottom: 20px;
    }

    p {
        margin-top: 10px;
        font-size: 18px;
    }

    .nav-links a {
        display: inline-block;
        margin: 10px 15px 10px 0;
        padding: 10px 20px;
        background-color: #e9f0e9;
        color: #eaad61;
        border: 1px solid #71600a;
        border-radius: 6px;
        text-decoration: none;
        transition: background-color 0.3s ease;
    }

    .nav-links a:hover {
        background-color: #ede913;
        color: #1c1c1b;
    }

    .gestione-clienti {
        background-color: #ffe4c4;
        padding: 25px;
        border: 2px solid #ce5c00;
        border-radius: 10px;
        margin: 30px 0;
        max-width: 800px;
    }

    .gestione-clienti form {
        margin-bottom: 25px;
    }

    .gestione-clienti form input[type="text"],
    .gestione-clienti form input[type="email"],
    .gestione-clienti form input[type="password"] {
        width: 100%;
        padding: 10px;
        margin: 8px 0;
        border: 1px solid #ccc;
        border-radius: 5px;
        font-size: 15px;
        box-sizing: border-box;
    }

    .gestione-clienti form button {
        padding: 10px 20px;
        background-color: #e9f0e9;
        color: #eaad61;
        border: 1px solid #71600a;
        border-radius: 6px;
        cursor: pointer;
        font-size: 15px;
        margin-right: 10px;
        transition: background-color 0.3s ease;
    }

    .gestione-clienti form button:hover {
        background-color: #ede913;
        color: #1c1c1b;
    }

    .gestione-clienti form a {
        padding: 10px 20px;
        background-color: #f8f9fa;
        color: #6c757d;
        border: 1px solid #6c757d;
        border-radius: 6px;
        text-decoration: none;
        display: inline-block;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
        background-color: #ffffff;
    }

    table, th, td {
        border: 1px solid #71600a;
    }

    th, td {
        padding: 12px;
        text-align: left;
    }

    th {
        background-color: #eaad61;
        color: #1c1c1b;
        font-weight: bold;
    }

    .errore {
        background-color: #f8d7da;
        color: #721c24;
        padding: 12px;
        border: 1px solid #f5c6cb;
        border-radius: 8px;
        margin: 15px 0;
        max-width: 800px;
    }

    .messaggio {
        background-color: #d4edda;
        color: #155724;
        padding: 12px;
        border: 1px solid #c3e6cb;
        border-radius: 8px;
        margin: 15px 0;
        max-width: 800px;
    }

    .logout-link {
        margin-top: 30px;
        padding-top: 20px;
        border-top: 1px solid #71600a;
    }
</style>
</head>

<body>
    <h2>Ciao <?= htmlspecialchars($user['username']) ?>!</h2>
    <p>Benvenuto nella dashboard amministrativa.</p>

    <!-- Link di navigazione principali -->
    <div class="nav-links">
        <a href="gestione_utenti.php">👥 Gestisci Utenti (Contatti)</a>
        <a href="gestione_lavori.php">💼 Gestione Lavori</a>
        <a href="gestione_categorie.php">📂 Gestione Categorie</a>
    </div>

    <!-- Gestione Clienti (Amministratori) -->
    <div class="gestione-clienti">
        <h3>Gestione Clienti (Amministratori)</h3>

        <!-- Mostra messaggi di errore o successo -->
        <?php if ($errore): ?>
            <div class="errore"><?= $errore ?></div>
        <?php endif; ?>

        <?php if ($msg): ?>
            <div class="messaggio"><?= $msg ?></div>
        <?php endif; ?>

        <!-- Form per aggiungere o modificare cliente -->
        <?= stampaFormCliente($clienteMod) ?>

        <!-- Tabella clienti registrati -->
        <h3>Clienti Registrati</h3>
        <?php
        $res = $conn->query("SELECT * FROM clienti ORDER BY id DESC");
        if ($res) {
            echo stampaTabellaClienti($res);
        } else {
            echo "<p>Errore nel caricamento clienti: " . htmlspecialchars($conn->error) . "</p>";
        }
        ?>
    </div>

    <!-- Link logout -->
    <div class="logout-link">
        <a href="logout.php">🚪 Esci dal Sistema</a>
    </div>

    <!-- Script JS per la validazione -->
    <?= validazioneJSGestioneCliente() ?>

    <!-- Alert per messaggi -->
    <?php if ($msg): ?>
        <script>
            window.addEventListener("DOMContentLoaded", function() {
                alert("<?= addslashes($msg) ?>");
            });
        </script>
    <?php endif; ?>

</body>
</html>