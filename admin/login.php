<?php
session_start();
require_once('config.php');
require_once('componenti_backend.php');

$errore = '';
$modifica = false;
$clienteMod = ['id' => '', 'username' => '', 'email' => ''];

// --- MESSAGGIO DI CONFERMA --- //
$msg = "";
if (isset($_SESSION['msg'])) {
    $msg = $_SESSION['msg'];
    unset($_SESSION['msg']);
}

// LOGIN CLIENTE //
if (isset($_POST['login'])) {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if ($username !== '' && $email !== '' && $password !== '') {
        $stmt = $conn->prepare("SELECT * FROM clienti WHERE username = ? AND email = ?");
        if (!$stmt) {
            $errore = "Errore nella preparazione della query: " . htmlspecialchars($conn->error);
        } else {
            $stmt->bind_param("ss", $username, $email);
            if (!$stmt->execute()) {
                $errore = "Errore nell'esecuzione della query: " . htmlspecialchars($stmt->error);
            } else {
                $res = $stmt->get_result();
                if ($cliente = $res->fetch_assoc()) {
                    // Verifica la password hashata //
                    if (password_verify($password, $cliente['password'])) {
                        $_SESSION['loggedin'] = true;
                        $_SESSION['user'] = [
                            'id' => $cliente['id'],
                            'username' => $cliente['username'],
                            'email' => $cliente['email']
                        ];
                        $stmt->close();
                        header("Location: dashboard.php");
                        exit;
                    } else {
                        $errore = "Password errata.";
                    }
                } else {
                    $errore = "Credenziali non valide.";
                }
            }
            $stmt->close();
        }
    } else {
        $errore = "Compila tutti i campi di accesso.";
    }
}

// AGGIUNTA / MODIFICA CLIENTE //
if (isset($_POST['salva'])) {
    $id = $_POST['id'] ?? null;
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Validazione campi //
    if ($username === '' || $email === '' || ($id === '' && $password === '')) {
        $errore = "Compila tutti i campi.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errore = "Email non valida.";
    } elseif (strlen($username) < 5 || ($id === '' && strlen($password) < 5)) {
        $errore = "Username e password min 5 caratteri.";
    } elseif ($password !== '' && !preg_match('/[A-Z]/', $password)) {
        $errore = "La password deve contenere almeno una lettera maiuscola.";
    } else {
        if ($id) {
            // Modifica cliente //
            if ($password !== '') {
                $passwordHash = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $conn->prepare("UPDATE clienti SET username=?, email=?, password=? WHERE id=?");
                if (!$stmt) {
                    $errore = "Errore nella preparazione della query: " . htmlspecialchars($conn->error);
                } else {
                    $stmt->bind_param("sssi", $username, $email, $passwordHash, $id);
                    if (!$stmt->execute()) {
                        $errore = "Errore nella modifica: " . htmlspecialchars($stmt->error);
                    } else {
                        $stmt->close();
                        $_SESSION['msg'] = "Cliente modificato con successo!";
                        header("Location: login.php");
                        exit;
                    }
                    $stmt->close();
                }
            } else {
                $stmt = $conn->prepare("UPDATE clienti SET username=?, email=? WHERE id=?");
                if (!$stmt) {
                    $errore = "Errore nella preparazione della query: " . htmlspecialchars($conn->error);
                } else {
                    $stmt->bind_param("ssi", $username, $email, $id);
                    if (!$stmt->execute()) {
                        $errore = "Errore nella modifica: " . htmlspecialchars($stmt->error);
                    } else {
                        $stmt->close();
                        $_SESSION['msg'] = "Cliente modificato con successo!";
                        header("Location: login.php");
                        exit;
                    }
                    $stmt->close();
                }
            }
        } else {
            // Nuovo cliente //
            // Controllo se username o email già esistono
            $stmt = $conn->prepare("SELECT id FROM clienti WHERE username = ? OR email = ?");
            $stmt->bind_param("ss", $username, $email);
            $stmt->execute();
            $stmt->store_result();
            if ($stmt->num_rows > 0) {
                $errore = "Username o email già registrati.";
                $stmt->close();
            } else {
                $stmt->close();
                $passwordHash = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $conn->prepare("INSERT INTO clienti (username, email, password) VALUES (?, ?, ?)");
                if (!$stmt) {
                    $errore = "Errore nella preparazione della query: " . htmlspecialchars($conn->error);
                } else {
                    $stmt->bind_param("sss", $username, $email, $passwordHash);
                    if (!$stmt->execute()) {
                        $errore = "Errore nell'inserimento: " . htmlspecialchars($stmt->error);
                    } else {
                        $stmt->close();
                        $_SESSION['msg'] = "Cliente registrato con successo!";
                        header("Location: login.php");
                        exit;
                    }
                    $stmt->close();
                }
            }
        }
    }
}

// ELIMINA CLIENTE //
if (isset($_GET['del'])) {
    $id = (int)$_GET['del'];
    if (!$conn->query("DELETE FROM clienti WHERE id=$id")) {
        $errore = "Errore nell'eliminazione: " . htmlspecialchars($conn->error);
    } else {
        $_SESSION['msg'] = "Cliente eliminato con successo!";
        header("Location: login.php");
        exit;
    }
}

// CARICA CLIENTE PER MODIFICA //
if (isset($_GET['edit'])) {
    $id = (int)$_GET['edit'];
    $res = $conn->query("SELECT * FROM clienti WHERE id=$id");
    if ($res && $res->num_rows === 1) {
        $clienteMod = $res->fetch_assoc();
        $modifica = true;
    } elseif (!$res) {
        $errore = "Errore nel caricamento cliente: " . htmlspecialchars($conn->error);
    }
}

// Stampa l'header HTML della pagina backend con il titolo "LOGIN" //
echo headBackend("LOGIN");
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
        margin-top: 40px;
    }

    form {
        background-color: #ffe4c4;
        padding: 20px;
        border: 2px solid #ce5c00;
        border-radius: 10px;
        max-width: 600px;
        margin-bottom: 30px;
    }

    form input[type="text"],
    form input[type="email"],
    form input[type="password"] {
        width: 100%;
        padding: 10px;
        margin-top: 5px;
        margin-bottom: 15px;
        border: 1px solid #ccc;
        border-radius: 5px;
        font-size: 15px;
    }

    form button {
        padding: 10px 20px;
        background-color: #e9f0e9;
        color: #eaad61;
        border: 1px solid #71600a;
        border-radius: 6px;
        cursor: pointer;
        font-size: 16px;
        transition: background-color 0.3s ease;
    }

    form button:hover {
        background-color: #ede913;
        color: #1c1c1b;
    }

    .errore {
        background-color: #f8d7da;
        color: #721c24;
        padding: 12px;
        border: 1px solid #f5c6cb;
        border-radius: 8px;
        max-width: 600px;
        margin: 15px 0;
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
        background-color: #ffffff;
    }

    th {
        background-color: #eaad61;
        color: #1c1c1b;
        text-align: left;
    }

    a {
        color: #eaad61;
        text-decoration: none;
    }

    a:hover {
        color: #ede913;
    }
</style>
</head>

<body>

    <h2>Login Cliente</h2>
    <form method="POST" onsubmit="return validaLogin();">
        <input type="text" name="username" id="loginUsername" placeholder="Username" required>
        <input type="email" name="email" id="loginEmail" placeholder="Email" required>
        <input type="password" name="password" id="loginPassword" placeholder="Password" required>
        <button type="submit" name="login">Accedi</button>
    </form>

    <!-- Mostra eventuale messaggio di errore -->
    <?php if ($errore): ?>
        <p class="errore"><?= $errore ?></p>
    <?php endif; ?>

    <hr>

    <!-- Form per aggiungere o modificare un cliente -->
    <h2><?= $modifica ? 'Modifica Cliente' : 'Aggiungi Cliente' ?></h2>
    <?= stampaFormCliente($modifica ? $clienteMod : null) ?>

    <!-- Elenco dei clienti già registrati -->
    <h2>Clienti Registrati</h2>
    <?php
    $res = $conn->query("SELECT * FROM clienti ORDER BY id");
    if ($res) {
        echo stampaTabellaClienti($res);
    } else {
        echo "<p class='errore'>Errore nel caricamento clienti: " . htmlspecialchars($conn->error) . "</p>";
    }
    ?>

    <!-- Script JS per la validazione lato client -->
    <?= validazioneJSLogin() ?>
    <?= validazioneJSGestioneCliente() ?>

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