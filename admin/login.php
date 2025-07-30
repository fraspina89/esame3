<?php
session_start();
require_once('config.php');
require_once('componenti_backend.php');

$errore = '';

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

// Stampa l'header HTML della pagina backend con il titolo "LOGIN" //
echo headBackend("LOGIN");
?>

<style>
    body {
        background-color: rgb(168, 204, 214);
        font-family: Arial, sans-serif;
        padding: 40px;
        color: #1c1c1b;
        display: flex;
        flex-direction: column;
        align-items: center;
        min-height: 100vh;
        justify-content: center;
    }

    .login-container {
        background-color: #ffe4c4;
        padding: 30px;
        border: 2px solid #ce5c00;
        border-radius: 10px;
        max-width: 400px;
        width: 100%;
        text-align: center;
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }

    h2 {
        color: #eaad61;
        margin-bottom: 20px;
        font-size: 24px;
    }

    form input[type="text"],
    form input[type="email"],
    form input[type="password"] {
        width: 100%;
        padding: 12px;
        margin-bottom: 15px;
        border: 1px solid #ccc;
        border-radius: 5px;
        font-size: 15px;
        box-sizing: border-box;
    }

    form button {
        width: 100%;
        padding: 12px;
        background-color: #e9f0e9;
        color: #eaad61;
        border: 1px solid #71600a;
        border-radius: 6px;
        cursor: pointer;
        font-size: 16px;
        font-weight: bold;
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
        margin: 15px 0;
        text-align: center;
    }

    .login-info {
        margin-top: 20px;
        font-size: 14px;
        color: #666;
    }
</style>
</head>

<body>
    <div class="login-container">
        <h2>Accesso Amministratore</h2>
        
        <form method="POST" onsubmit="return validaLogin();">
            <input type="text" name="username" id="loginUsername" placeholder="Username" required>
            <input type="email" name="email" id="loginEmail" placeholder="Email" required>
            <input type="password" name="password" id="loginPassword" placeholder="Password" required>
            <button type="submit" name="login">Accedi</button>
        </form>

        <!-- Mostra eventuale messaggio di errore -->
        <?php if ($errore): ?>
            <div class="errore"><?= $errore ?></div>
        <?php endif; ?>

        <div class="login-info">
            <p>Accedi con le tue credenziali per gestire il sito</p>
        </div>
    </div>

    <!-- Script JS per la validazione lato client -->
    <?= validazioneJSLogin() ?>

</body>
</html>