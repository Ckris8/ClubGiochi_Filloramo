<?php
session_start();
?>
<link rel="stylesheet" href="../css/body.css">
<?php
if (isset($_SESSION['session_id'])) {
    require_once("../db/database.php");

    // Recupera le informazioni dell'utente dalla tabella Socio
    $username = $_SESSION['session_user'];
    $sql_profilo = "SELECT * FROM Socio WHERE username = ?";
    $stmt_profilo = $pdo->prepare($sql_profilo);
    $stmt_profilo->execute([$username]);
    $utente = $stmt_profilo->fetch(PDO::FETCH_ASSOC);

    if ($utente) {
        echo "<h1>Profilo Utente</h1>";
        echo "<p><strong>Username:</strong> " . htmlspecialchars($utente['username']) . "</p>";
        echo "<p><strong>Email:</strong> " . (!empty($utente['email']) ? htmlspecialchars($utente['email']) : "(Da inserire)") . "</p>";
        echo "<p><strong>Nome:</strong> " . (!empty($utente['nome']) ? htmlspecialchars($utente['nome']) : "(Da inserire)") . "</p>";
        echo "<p><strong>Cognome:</strong> " . (!empty($utente['cognome']) ? htmlspecialchars($utente['cognome']) : "(Da inserire)") . "</p>";
        echo "<p><strong>Anni:</strong> " . (!empty($utente['anni']) ? htmlspecialchars($utente['anni']) : "(Da inserire)") . "</p>";

        // Determina il ruolo dell'utente in base al livello
        $ruolo = "";
        switch ($utente['livello']) {
            case 0:
                $ruolo = "Ospite";
                break;
            case 1:
                $ruolo = "Regolare";
                break;
            case 3:
                $ruolo = "Donatore";
                break;
            default:
                if ($utente['livello'] > 7) {
                    $ruolo = "Amministratore";
                } else {
                    $ruolo = "Livello sconosciuto";
                }
                break;
        }

        echo "<p><strong>Ruolo:</strong> " . htmlspecialchars($ruolo) . "</p>";
        echo "<p><strong>Livello:</strong> " . htmlspecialchars($utente['livello']) . "</p>";
        echo "<p><strong>Quota:</strong> €" . htmlspecialchars($utente['Quota']) . "</p>";

        // Modulo per aggiornare le informazioni del profilo
        echo '<h2>Aggiorna le tue informazioni</h2>';
        echo '<form method="POST" action="profilo.php">';
        echo '<label for="email">Email:</label><br>';
        echo '<input type="email" name="email" id="email" value="' . (!empty($utente['email']) ? htmlspecialchars($utente['email']) : "") . '" required><br>';
        echo '<label for="nome">Nome:</label><br>';
        echo '<input type="text" name="nome" id="nome" value="' . (!empty($utente['nome']) ? htmlspecialchars($utente['nome']) : "") . '" required><br>';
        echo '<label for="cognome">Cognome:</label><br>';
        echo '<input type="text" name="cognome" id="cognome" value="' . (!empty($utente['cognome']) ? htmlspecialchars($utente['cognome']) : "") . '" required><br>';
        echo '<label for="anni">Anni:</label><br>';
        echo '<input type="number" name="anni" id="anni" value="' . (!empty($utente['anni']) ? htmlspecialchars($utente['anni']) : "") . '" required><br>';
        echo '<button type="submit">Aggiorna</button>';
        echo '</form>';

        // Aggiorna le informazioni nel database
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['delete_user'])) {
            $email = htmlspecialchars($_POST['email'], ENT_QUOTES, 'UTF-8');
            $nome = htmlspecialchars($_POST['nome'], ENT_QUOTES, 'UTF-8');
            $cognome = htmlspecialchars($_POST['cognome'], ENT_QUOTES, 'UTF-8');
            $anni = (int)$_POST['anni'];

            $sql_update = "UPDATE Socio SET email = ?, nome = ?, cognome = ?, anni = ? WHERE username = ?";
            $stmt_update = $pdo->prepare($sql_update);
            $stmt_update->execute([$email, $nome, $cognome, $anni, $username]);

            echo "<p>Informazioni aggiornate con successo!</p>";
            echo '<br><a href="profilo.php"><button>Ricarica Profilo</button></a>';
        }

        // Se l'utente è un amministratore (livello > 8), mostra la tabella degli utenti
        if ($utente['livello'] > 8) {
            echo "<h2>Gestione Utenti</h2>";

            // Elimina un utente se richiesto
            if (isset($_POST['delete_user'])) {
                $user_to_delete = htmlspecialchars($_POST['delete_user'], ENT_QUOTES, 'UTF-8');

                // Controlla il livello dell'utente da eliminare
                $sql_check_level = "SELECT livello FROM Socio WHERE username = ?";
                $stmt_check_level = $pdo->prepare($sql_check_level);
                $stmt_check_level->execute([$user_to_delete]);
                $user_to_delete_data = $stmt_check_level->fetch(PDO::FETCH_ASSOC);

                if ($user_to_delete_data && $user_to_delete_data['livello'] > 7) {
                    echo "<p>Errore: Non puoi eliminare utenti amministratore.</p>";
                } else {
                    $sql_delete = "DELETE FROM Socio WHERE username = ?";
                    $stmt_delete = $pdo->prepare($sql_delete);
                    $stmt_delete->execute([$user_to_delete]);
                    echo "<p>Utente '$user_to_delete' eliminato con successo.</p>";
                }
            }

            // Recupera tutti gli utenti registrati
            $sql_utenti = "SELECT username, email, livello, Quota FROM Socio";
            $stmt_utenti = $pdo->query($sql_utenti);

            if ($stmt_utenti->rowCount() > 0) {
                echo '<table border="1">';
                echo '<tr><th>Username</th><th>Email</th><th>Livello</th><th>Quota</th><th>Azione</th></tr>';
                while ($row = $stmt_utenti->fetch(PDO::FETCH_ASSOC)) {
                    echo '<tr>';
                    echo '<td>' . htmlspecialchars($row['username']) . '</td>';
                    echo '<td>' . (!empty($row['email']) ? htmlspecialchars($row['email']) : "(Da inserire)") . '</td>';
                    echo '<td>' . htmlspecialchars($row['livello']) . '</td>';
                    echo '<td>€' . htmlspecialchars($row['Quota']) . '</td>';
                    echo '<td>';
                    echo '<form method="POST" action="profilo.php" style="display:inline;">';
                    echo '<button type="submit" name="delete_user" value="' . htmlspecialchars($row['username']) . '">Elimina</button>';
                    echo '</form>';
                    echo '</td>';
                    echo '</tr>';
                }
                echo '</table>';
            } else {
                echo "<p>Nessun utente registrato.</p>";
            }
        }
    } else {
        echo "<p>Errore: Utente non trovato.</p>";
    }

    echo '<br><a href="dashboard.php"><button>Torna alla Dashboard</button></a>';
} else {
    printf("Effettua il %s per accedere all'area riservata", '<a href="login.html">login</a>');
}
?>