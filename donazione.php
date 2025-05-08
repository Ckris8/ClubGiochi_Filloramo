<?php
session_start();
?>
<link rel="stylesheet" href="css/body.css">
<?php
if (isset($_SESSION['session_id'])) {
    require_once("db/database.php");

    $session_user = htmlspecialchars($_SESSION['session_user'], ENT_QUOTES, 'UTF-8');
    $livello = $_SESSION['livello'];

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['donazione'])) {
        $donazione = floatval($_POST['donazione']);
        if ($donazione > 0) {
            // Aggiorna la quota dell'utente
            $sql_donazione = "UPDATE Socio SET Quota = Quota + ? WHERE username = ?";
            $stmt_donazione = $pdo->prepare($sql_donazione);
            $stmt_donazione->execute([$donazione, $session_user]);

            // Controlla se l'utente è di livello 0 e ha donato almeno 5 euro
            if ($livello == 0 && $donazione >= 5) {
                $sql_aggiorna_livello = "UPDATE Socio SET livello = 1 WHERE username = ?";
                $stmt_aggiorna_livello = $pdo->prepare($sql_aggiorna_livello);
                $stmt_aggiorna_livello->execute([$session_user]);
                echo "<p>Congratulazioni! Sei passato al livello 1 grazie alla tua donazione.</p>";
            }

            echo "<p>Grazie per la tua donazione di €" . htmlspecialchars($donazione) . "!</p>";
            echo '<br><a href="auth/dashboard.php"><button>Torna alla Dashboard</button></a>';
        } else {
            echo "<p>Errore: Inserisci un importo valido per la donazione.</p>";
        }
    } else {
        // Mostra il modulo per la donazione
        echo '<h2>Fai una Donazione</h2>';
        echo '<form method="POST" action="donazione.php">';
        echo '<label for="donazione">Inserisci l\'importo della donazione (€):</label>';
        echo '<input type="number" name="donazione" id="donazione" step="0.01" min="0" required>';
        echo '<button type="submit">Dona</button>';
        echo '</form>';
    }

    // Visualizza la quota rimanente dell'utente
    $sql_quota = "SELECT Quota FROM Socio WHERE username = ?";
    $stmt_quota = $pdo->prepare($sql_quota);
    $stmt_quota->execute([$session_user]);
    $quota = $stmt_quota->fetchColumn();

    echo "<h2>La tua quota attuale: €" . htmlspecialchars($quota) . "</h2>";

    // Se l'utente ha un livello maggiore di 7, mostra la quota di tutti gli utenti
    if ($livello > 7) {
        echo "<h2>Quote di tutti gli utenti registrati</h2>";
        $sql_tutte_quote = "SELECT username, Quota FROM Socio";
        $stmt_tutte_quote = $pdo->query($sql_tutte_quote);

        if ($stmt_tutte_quote->rowCount() > 0) {
            echo '<table border="1">';
            echo '<tr><th>Username</th><th>Quota</th></tr>';
            while ($row = $stmt_tutte_quote->fetch(PDO::FETCH_ASSOC)) {
                echo '<tr>';
                echo '<td>' . htmlspecialchars($row['username']) . '</td>';
                echo '<td>€' . htmlspecialchars($row['Quota']) . '</td>';
                echo '</tr>';
            }
            echo '</table>';
        } else {
            echo "<p>Nessun utente trovato.</p>";
        }
    }

    echo '<br><a href="auth/dashboard.php"><button>Torna alla Dashboard</button></a>';
} else {
    printf("Effettua il %s per accedere all'area riservata", '<a href="login.html">login</a>');
}
?>