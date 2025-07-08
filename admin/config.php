<?php
// CONNESSIONE AMBIENTE LOCALE //

$ambienteLocale = ($_SERVER['SERVER_NAME'] === 'localhost');

// Credenziali locali
if ($ambienteLocale) {
    $host = 'localhost';
    $user = 'root';
    $pass = '';
    $db   = 'esame3';
} else {
    // Credenziali Aruba 
    $host = '31.11.39.224';
    $user = 'Sql1869495';
    $pass = 'Francesco89+';
    $db   = 'Sql1869495_1';
}

// Connessione
$conn = new mysqli($host, $user, $pass, $db);

// Controllo se la connessione ha avuto successo //
if ($conn->connect_error) {
    // messaggio di errore //
    die("Connessione fallita: " . $conn->connect_error);
}
