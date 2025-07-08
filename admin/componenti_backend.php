<?php

// INTESTAZIONE PAGINA BACKEND//

/**
 * Restituisce l'header HTML per le pagine del backend.
 * @param string $title Titolo della pagina
 * @return string HTML dell'header
 */

function headBackend($title = "")
{
    return '<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>' . htmlspecialchars($title) . '</title>';
}


//  SEZIONE UTENTI (CONTATTI) //

/**
 * Stampa la tabella degli utenti/contatti dal database.
 * @param mysqli $conn Connessione al database
 * @return string HTML della tabella
 */

function stampaTabellaUtenti($conn)
{
    $html = '<table>
        <tr><th>ID</th><th>Nome</th><th>Cognome</th><th>Email</th><th>Argomento</th><th>Testo</th><th>Azioni</th></tr>';
    $res = $conn->query("SELECT * FROM utenti ORDER BY id DESC");
    if (!$res) {
        $html .= "<tr><td colspan='7'>Errore nella query: " . htmlspecialchars($conn->error) . "</td></tr></table>";
        return $html;
    }
    while ($r = $res->fetch_assoc()) {
        $html .= "<tr>
            <td>{$r['id']}</td>
            <td>" . htmlspecialchars($r['nome']) . "</td>
            <td>" . htmlspecialchars($r['cognome']) . "</td>
            <td>" . htmlspecialchars($r['email']) . "</td>
            <td>" . htmlspecialchars($r['argomento']) . "</td>
            <td>" . nl2br(htmlspecialchars($r['testo'])) . "</td>
            <td>
                <a href='?op=FORM-MOD&idSel={$r['id']}' title='Modifica'>✏️</a>
                <a href='?op=DEL&idSel={$r['id']}' title='Elimina' onclick=\"return confirm('Sei sicuro?')\">❌</a>
            </td>
        </tr>";
    }
    $html .= '</table>';
    return $html;
}

/**
 * Stampa il form per aggiungere o modificare un utente/contatto.
 * @param array|null $utente Dati dell'utente (null per nuovo)
 * @return string HTML del form
 */

function stampaFormUtente($utente = null)
{
    $isMod = $utente !== null;
    $action = $isMod ? '?op=MOD' : '?op=ADD';
    $btn = $isMod ? 'Salva modifiche' : 'Aggiungi';
    $id = $utente['id'] ?? '';
    $nome = htmlspecialchars($utente['nome'] ?? '');
    $cognome = htmlspecialchars($utente['cognome'] ?? '');
    $email = htmlspecialchars($utente['email'] ?? '');
    $argomento = $utente['argomento'] ?? '';
    $testo = htmlspecialchars($utente['testo'] ?? '');

    return "
    <h3>" . ($isMod ? 'Modifica Contatto' : 'Aggiungi Nuovo Contatto') . "</h3>
    <form method='POST' action='$action' id='formUtente'>
        " . ($isMod ? "<input type='hidden' name='id' value='$id'>" : "") . "
        <label for='nome'>Nome:</label><input type='text' id='nome' name='nome' value='$nome' required><br>
        <label for='cognome'>Cognome:</label><input type='text' id='cognome' name='cognome' value='$cognome' required><br>
        <label for='email'>Email:</label><input type='email' id='email' name='email' value='$email' required><br>
        <label for='argomento'>Argomento:</label>
        <select id='argomento' name='argomento' required>
            <option value=''>-- Seleziona --</option>
            <option value='Interessato' " . ($argomento === 'Interessato' ? 'selected' : '') . ">Interessato</option>
            <option value='Dubbioso' " . ($argomento === 'Dubbioso' ? 'selected' : '') . ">Dubbioso</option>
        </select><br>
        <label for='testo'>Testo:</label>
        <textarea id='testo' name='testo' rows='4' required>$testo</textarea><br>
        <button type='submit'>$btn</button>
    </form>";
}

/**
 * Restituisce lo script JS per la validazione lato client del form utente.
 * @return string Script JS
 */


function validazioneJSUtente()
{
    return '
<script>
document.addEventListener("DOMContentLoaded", function () {
    const form = document.getElementById("formUtente");
    if (!form) return;
    form.addEventListener("submit", function (e) {
        const nome = form.nome.value.trim();
        const cognome = form.cognome.value.trim();
        const email = form.email.value.trim();
        const argomento = form.argomento.value;
        const testo = form.testo.value.trim();

        if (nome.length < 3 || nome.length > 25) {
            alert("Il nome deve essere tra 3 e 25 caratteri.");
            e.preventDefault();
            return;
        }
        if (cognome.length < 3 || cognome.length > 25) {
            alert("Il cognome deve essere tra 3 e 25 caratteri.");
            e.preventDefault();
            return;
        }
        if (email.length < 10 || email.length > 100 || !/^[^\\s@]+@[^\\s@]+\\.[^\\s@]+$/.test(email)) {
            alert("Inserisci un\'email valida (10-100 caratteri).");
            e.preventDefault();
            return;
        }
        if (!argomento) {
            alert("Seleziona un argomento.");
            e.preventDefault();
            return;
        }
        if (testo.length < 10 || testo.length > 500) {
            alert("Il testo deve essere tra 10 e 500 caratteri.");
            e.preventDefault();
            return;
        }
    });
});
</script>
';
}

//  CLIENTI / LOGIN //


/**
 * Stampa il form per aggiungere o modificare un cliente (login).
 * @param array|null $cliente Dati del cliente (null per nuovo)
 * @return string HTML del form
 */

function stampaFormCliente($cliente = null)
{
    $isMod = $cliente !== null;
    $id = $cliente['id'] ?? '';
    $username = htmlspecialchars($cliente['username'] ?? '');
    $email = htmlspecialchars($cliente['email'] ?? '');
    $btn = $isMod ? 'Salva modifiche' : 'Aggiungi';
    $placeholder = $isMod ? 'Nuova Password (opzionale)' : 'Password';
    $required = $isMod ? '' : 'required';

    $html = '<form method="POST" onsubmit="return validaGestione();">
        <input type="hidden" name="id" value="' . $id . '">
        <input type="text" name="username" id="gestUsername" placeholder="Username" value="' . $username . '" required>
        <input type="email" name="email" id="gestEmail" placeholder="Email" value="' . $email . '" required>
        <input type="password" name="password" id="gestPassword" placeholder="' . $placeholder . '" ' . $required . '>
        <button type="submit" name="salva">' . $btn . '</button>';
    if ($isMod) {
        $html .= '<a href="login.php">Annulla</a>';
    }
    $html .= '</form>';
    return $html;
}

/**
 * Stampa la tabella dei clienti.
 * @param mysqli_result $res Risultato della query clienti
 * @return string HTML della tabella
 */

function stampaTabellaClienti($res)
{
    $html = '<table><thead><tr><th>ID</th><th>Username</th><th>Email</th><th>Azioni</th></tr></thead><tbody>';
    while ($r = $res->fetch_assoc()) {
        $html .= '<tr>
            <td>' . $r['id'] . '</td>
            <td>' . htmlspecialchars($r['username']) . '</td>
            <td>' . htmlspecialchars($r['email']) . '</td>
            <td>
                <a href="?edit=' . $r['id'] . '" title="Modifica">✏️</a>
                <a href="?del=' . $r['id'] . '"  title="Elimina" onclick="return confirm(\'Eliminare cliente?\');">❌</a>
            </td>
        </tr>';
    }
    $html .= '</tbody></table>';
    return $html;
}

/**
 * Script JS per la validazione lato client del login.
 * @return string Script JS
 */

function validazioneJSLogin()
{
    return '
<script>
function validaLogin() {
    const u = document.getElementById("loginUsername").value.trim();
    const e = document.getElementById("loginEmail").value.trim();
    const p = document.getElementById("loginPassword").value.trim();
    if (u.length < 5 || p.length < 5 || !e.includes("@")) {
        alert("Compila correttamente tutti i campi.");
        return false;
    }
    return true;
}
</script>';
}

/**
 * Script JS per la validazione lato client del form gestione cliente.
 * @return string Script JS
 */

function validazioneJSGestioneCliente()
{
    return '
<script>
function validaGestione() {
    const u = document.getElementById("gestUsername").value.trim();
    const e = document.getElementById("gestEmail").value.trim();
    const p = document.getElementById("gestPassword").value.trim();
    const editing = document.querySelector(\'input[name="id"]\').value !== "";
    if (u.length < 5) {
        alert("Username minimo 5 caratteri.");
        return false;
    }
    if (!e.includes("@")) {
        alert("Inserisci un\'email valida.");
        return false;
    }
    if (!editing && p.length < 5) {
        alert("Password obbligatoria (minimo 5 caratteri).");
        return false;
    }
    if (!editing && !/[A-Z]/.test(p)) {
        alert("La password deve contenere almeno una lettera maiuscola.");
        return false;
    }
    return true;
}
</script>';
}

//  CATEGORIE //

/**
 * Stampa il form per aggiungere o modificare una categoria.
 * @param array|null $categoria Dati della categoria (null per nuovo)
 * @return string HTML del form
 */

function stampaFormCategoria($categoria = null)
{
    $isMod = $categoria !== null;
    $action = $isMod ? '?op=MOD' : '?op=ADD';
    $id = $categoria['id'] ?? '';
    $nome = htmlspecialchars($categoria['nome'] ?? '');
    return "
    <h3>" . ($isMod ? 'Modifica Categoria' : 'Aggiungi Categoria') . "</h3>
    <form method='POST' action='$action' id='formCategoria'>
        " . ($isMod ? "<input type='hidden' name='id' value='$id'>" : '') . "
        <label for='nome'>Nome:</label>
        <input type='text' name='nome' id='nome' value='$nome' required><br>
        <button type='submit'>" . ($isMod ? 'Salva' : 'Aggiungi') . "</button>
    </form>";
}

/**
 * Stampa la tabella delle categorie.
 * @param mysqli $conn Connessione al database
 * @return string HTML della tabella
 */

function stampaTabellaCategorie($conn)
{
    $html = '<table><tr><th>ID</th><th>Nome</th><th>Azioni</th></tr>';
    $res = $conn->query("SELECT * FROM categorie ORDER BY id ASC");
    if (!$res) {
        $html .= "<tr><td colspan='3'>Errore nella query: " . htmlspecialchars($conn->error) . "</td></tr></table>";
        return $html;
    }
    while ($cat = $res->fetch_assoc()) {
        $html .= "<tr>
            <td>{$cat['id']}</td>
            <td>" . htmlspecialchars($cat['nome']) . "</td>
            <td>
                <a href='?op=FORM-MOD&idSel={$cat['id']}'  title='Modifica'>✏️</a>
                <a href='?op=DEL&idSel={$cat['id']}' title='Elimina' onclick=\"return confirm('Sei sicuro?')\">❌</a>
            </td>
        </tr>";
    }
    $html .= '</table>';
    return $html;
}

/**
 * Script JS per la validazione lato client del form categoria.
 * @return string Script JS
 */

function validazioneJSCategoria()
{
    return '
<script>
document.addEventListener("DOMContentLoaded", function () {
    const form = document.getElementById("formCategoria");
    if (!form) return;
    form.addEventListener("submit", function (e) {
        const nome = form.nome.value.trim();
        if (nome.length < 2) {
            alert("Il nome della categoria è obbligatorio (min 2 caratteri).");
            e.preventDefault();
        }
    });
});
</script>';
}

// LAVORI //

/**
 * Stampa il form per aggiungere o modificare un lavoro.
 * @param array $categorie Elenco categorie disponibili
 * @param array|null $lavoro Dati del lavoro (null per nuovo)
 * @return string HTML del form
 */

function stampaFormLavoro($categorie, $lavoro = null)
{
    $isMod = $lavoro !== null;
    $action = $isMod ? '?op=MOD' : '?op=ADD';
    $id = $lavoro['id'] ?? '';
    $titolo = htmlspecialchars($lavoro['titolo'] ?? '');
    $img = htmlspecialchars($lavoro['img'] ?? '');
    $descrizione = htmlspecialchars($lavoro['description'] ?? '');
    $data = htmlspecialchars($lavoro['data'] ?? '');
    $azienda = htmlspecialchars($lavoro['azienda'] ?? '');
    $categoria_id = $lavoro['categoria_id'] ?? '';

    $html = "<h3>" . ($isMod ? 'Modifica Lavoro' : 'Aggiungi Nuovo Lavoro') . "</h3>
    <form method='POST' action='$action' id='formLavoro'>
        " . ($isMod ? "<input type='hidden' name='id' value='$id'>" : '') . "
        <label for='titolo'>Titolo:</label><input type='text' name='titolo' id='titolo' value='$titolo' required><br>
        <label for='img'>Immagine:</label><input type='text' name='img' id='img' value='$img' required><br>
        <label for='description'>Descrizione:</label><textarea name='description' id='description' rows='3'>$descrizione</textarea><br>
        <label for='data'>Data:</label><input type='text' name='data' id='data' value='$data'><br>
        <label for='azienda'>Azienda:</label><input type='text' name='azienda' id='azienda' value='$azienda'><br>
        <label for='categoria_id'>Categoria:</label>
        <select name='categoria_id' id='categoria_id' required>
            <option value=''>-- Seleziona Categoria --</option>";
    foreach ($categorie as $c) {
        $sel = ($c['id'] == $categoria_id) ? 'selected' : '';
        $html .= "<option value='{$c['id']}' $sel>" . htmlspecialchars($c['nome']) . "</option>";
    }
    $html .= "</select><br>
        <button type='submit'>" . ($isMod ? 'Salva Modifiche' : 'Aggiungi') . "</button>
    </form>";
    return $html;
}

/**
 * Stampa la tabella dei lavori.
 * @param mysqli $conn Connessione al database
 * @return string HTML della tabella
 */

function stampaTabellaLavori($conn)
{
    $html = '<table>
        <thead><tr><th>ID</th><th>Titolo</th><th>Immagine</th><th>Descrizione</th><th>Data</th><th>Azienda</th><th>Categoria</th><th>Azioni</th></tr></thead><tbody>';
    $res = $conn->query("SELECT lavori.*, categorie.nome AS nome_categoria 
                         FROM lavori LEFT JOIN categorie ON lavori.categoria_id = categorie.id 
                         ORDER BY lavori.id ASC");
    if (!$res) {
        $html .= "<tr><td colspan='8'>Errore nella query: " . htmlspecialchars($conn->error) . "</td></tr></tbody></table>";
        return $html;
    }
    while ($row = $res->fetch_assoc()) {
        $html .= "<tr>
            <td>{$row['id']}</td>
            <td>" . htmlspecialchars($row['titolo']) . "</td>
            <td><img src=\"../img/{$row['img']}\" style='max-height:60px;'></td>
            <td>" . htmlspecialchars($row['description']) . "</td>
            <td>" . htmlspecialchars($row['data']) . "</td>
            <td>" . htmlspecialchars($row['azienda']) . "</td>
            <td>" . htmlspecialchars($row['nome_categoria'] ?? '') . "</td>
            <td>
                <a href='?op=FORM-MOD&idSel={$row['id']}' title='Modifica'>✏️</a>
                <a href='?op=DEL&idSel={$row['id']}' title='Elimina' onclick=\"return confirm('Sei sicuro?')\">❌</a>
            </td>
        </tr>";
    }
    $html .= '</tbody></table>';
    return $html;
}

/**
 * Script JS per la validazione lato client del form lavoro.
 * @return string Script JS
 */

function validazioneJSLavoro()
{
    return '
<script>
document.addEventListener("DOMContentLoaded", function () {
    const form = document.getElementById("formLavoro");
    if (!form) return;
    form.addEventListener("submit", function (e) {
        const titolo = form.titolo.value.trim();
        const img = form.img.value.trim();
        const descrizione = form.description.value.trim();
        const data = form.data.value.trim();
        const azienda = form.azienda.value.trim();
        const categoria = form.categoria_id.value;

        if (titolo.length < 3 || titolo.length > 100) {
            alert("Il titolo deve essere tra 3 e 100 caratteri.");
            e.preventDefault();
            return;
        }
        if (!/\.(jpg|jpeg|png|gif)$/i.test(img)) {
            alert("L\'immagine deve essere un file JPG, JPEG, PNG o GIF.");
            e.preventDefault();
            return;
        }
        if (descrizione.length < 10) {
            alert("La descrizione deve essere di almeno 10 caratteri.");
            e.preventDefault();
            return;
        }
        if (data !== "" && !/^\d{4}-\d{2}-\d{2}$/.test(data)) {
            alert("La data deve essere nel formato YYYY-MM-DD.");
            e.preventDefault();
            return;
        }
        if (azienda.length > 100) {
            alert("Il nome dell\'azienda può essere lungo massimo 100 caratteri.");
            e.preventDefault();
            return;
        }
        if (categoria === "" || parseInt(categoria) <= 0) {
            alert("Seleziona una categoria valida.");
            e.preventDefault();
            return;
        }
    });
});
</script>
';
}
