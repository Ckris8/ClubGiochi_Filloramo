<?php
session_start();
?>
<link rel="stylesheet" href="css/body.css">
<?php
if (isset($_SESSION['session_id'])) {
    // Connessione al database
    require_once("db/database.php");

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $nome = htmlspecialchars($_POST['nome'], ENT_QUOTES, 'UTF-8');
        $Ngiocatori = (int)$_POST['Ngiocatori'];
        $DataAcquisto_Donazione = htmlspecialchars($_POST['DataAcquisto_Donazione'], ENT_QUOTES, 'UTF-8');

        // Controlla se il gioco esiste già
        $sql_check = "SELECT copieDisp FROM Gioco WHERE nome = ?";
        $stmt_check = $pdo->prepare($sql_check);
        $stmt_check->execute([$nome]);

        if ($stmt_check->rowCount() > 0) {
            // Il gioco esiste già, aggiorna il numero di copie disponibili
            $row = $stmt_check->fetch(PDO::FETCH_ASSOC);
            $copieDisp = $row['copieDisp'];



            if ($copieDisp >= 3) {
                echo "Errore: Non è possibile avere più di 3 copie dello stesso gioco.";
                echo '<br><a href="auth/dashboard.php"><button>Torna home</button></a>';
            } else {
                $copieDisp++;
                $sql_update = "UPDATE Gioco SET copieDisp = ? WHERE nome = ?";
                $stmt_update = $pdo->prepare($sql_update);
                $stmt_update->execute([$copieDisp, $nome]);
                echo "Copia aggiunta con successo.";
                echo '<br><a href="auth/dashboard.php"><button>Torna home</button></a>';
            }
        } else {
            // inserrimento del nuovo gioco
            $copieDisp = 1;
            $sql_insert = "INSERT INTO Gioco (nome, Ngiocatori, DataAcquisto_Donazione, copieDisp) VALUES (?, ?, ?, ?)";
            $stmt_insert = $pdo->prepare($sql_insert);
            $stmt_insert->execute([$nome, $Ngiocatori, $DataAcquisto_Donazione, $copieDisp]);
            echo "Gioco inserito con successo.";
        }

        // Controlla il livello dell'utente e aggiorna se necessario
        if ($_SESSION['livello'] == 0 || $_SESSION['livello'] == 1) {
            $sql_update_livello = "UPDATE Socio SET livello = 3 WHERE username = ?";
            $stmt_update_livello = $pdo->prepare($sql_update_livello);
            $stmt_update_livello->execute([$_SESSION['session_user']]);
            $_SESSION['livello'] = 3; // Aggiorna anche la sessione
            echo "<p>Congratulazioni! Sei passato al livello 3 grazie all'inserimento del gioco.</p>";
        }

        echo '<br><a href="auth/dashboard.php"><button>Torna home</button></a>';
    } else {
        // Mostra il modulo per l'inserimento
        echo '<h2>Inserisci un nuovo gioco</h2>';
        echo '<form method="POST" action="inserisci_gioco.php">';
        echo 'Nome gioco: <input type="text" name="nome" required><br>';
        echo 'Numero Giocatori: <input type="number" name="Ngiocatori" required><br>';
        echo 'Data Acquisto/Donazione: <input type="date" name="DataAcquisto_Donazione" required><br>';
        echo '<button type="submit">Inserisci Gioco</button>';
        echo '</form>';

        echo '<br><a href="auth/dashboard.php"><button>Torna home</button></a>';
    }
} else {
    printf("Effettua il %s per accedere all'area riservata", '<a href="login.html">login</a>');
}
?>