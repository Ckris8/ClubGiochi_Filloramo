<?php
session_start();
?>
<link rel="stylesheet" href="../index.css">
<?php

if (isset($_SESSION['session_id'])) {
    // Connessione al database
    require_once("db/database.php");

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $nome = htmlspecialchars($_POST['nome'], ENT_QUOTES, 'UTF-8');

        // Controlla se il gioco esiste e ha almeno una copia disponibile
        $sql_check = "SELECT copieDisp FROM Gioco WHERE nome = ?";
        $stmt_check = $pdo->prepare($sql_check);
        $stmt_check->execute([$nome]);

        if ($stmt_check->rowCount() > 0) {
            $row = $stmt_check->fetch(PDO::FETCH_ASSOC);
            $copieDisp = $row['copieDisp'];

            if ($copieDisp > 0) {
                // Detrai una copia
                $copieDisp--;
                $sql_update = "UPDATE Gioco SET copieDisp = ? WHERE nome = ?";
                $stmt_update = $pdo->prepare($sql_update);
                $stmt_update->execute([$copieDisp, $nome]);
                echo "Prestito effettuato con successo. Copie rimanenti: $copieDisp.";
                echo '<br><a href="auth/dashboard.php"><button>Torna home</button></a>';
            } else {
                echo "Errore: Nessuna copia disponibile per il gioco richiesto.";
                echo '<br><a href="auth/dashboard.php"><button>Torna home</button></a>';
            }
        } else {
            echo "Errore: Il gioco richiesto non esiste.";
            echo '<br><a href="auth/dashboard.php"><button>Torna home</button></a>';
        }
    } else {
        // Mostra il modulo per richiedere un prestito
        echo '<h2>Richiedi un prestito</h2>';
        echo '<form method="POST" action="prestito.php">';
        echo 'Nome del gioco: <input type="text" name="nome" required><br>';
        echo '<button type="submit">Richiedi Prestito</button>';
        echo '</form>';
    }
} else {
    printf("Effettua il %s per accedere all'area riservata", '<a href="login.html">login</a>');
}
?>