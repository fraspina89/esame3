<?php

// Avvia la sessione per gestire l'autenticazione dell'utente //
session_start();

// Importa le funzioni e componenti comuni del backend //
require_once("componenti_backend.php");


// Controlla se l'utente è autenticato //
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit;
}

// Recupera i dati dell'utente dalla sessione
$user = $_SESSION['user'];

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

    h2 {
        color: #eaad61;
        margin-bottom: 10px;
    }

    p {
        margin-top: 10px;
        font-size: 18px;
    }

    a {
        display: inline-block;
        margin: 10px 0;
        padding: 10px 20px;
        background-color: #e9f0e9;
        color: #eaad61;
        border: 1px solid #71600a;
        border-radius: 6px;
        text-decoration: none;
        transition: background-color 0.3s ease;
    }

    a:hover {
        background-color: #ede913;
        color: #1c1c1b;
    }
</style>
</head>

<body>
    <h2>Ciao <?= htmlspecialchars($user['username']) ?>!</h2>
    <p>Sei nella dashboard protetta.</p>
    <a href="gestione_utenti.php">👥 Gestisci Utenti</a>
    <p><a href="gestione_lavori.php">Gestione Lavori</a></p>
    <a href="gestione_categorie.php">Gestione Categorie</a>
    <a href="logout.php">Esci</a>
</body>

</html>