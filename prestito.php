<?php
session_start();

if (isset($_SESSION['session_id'])) {
    // Connessione al database
    require_once("../db/database.php");

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $nome = htmlspecialchars($_POST['nome'], ENT_QUOTES, 'UTF-8');

        // Controlla se il gioco esiste e ha almeno una copia disponibile
        $sql_check = "SELECT copieDisp FROM Gioco WHERE nome = ?";
        $stmt_check = $conn->prepare($sql_check);
        $stmt_check->bind_param("s", $nome);
        $stmt_check->execute();
        $result_check = $stmt_check->get_result();

        if ($result_check->num_rows > 0) {
            $row = $result_check->fetch_assoc();
            $copieDisp = $row['copieDisp'];

            if ($copieDisp > 0) {
                // Detrai una copia
                $copieDisp--;
                $sql_update = "UPDATE Gioco SET copieDisp = ? WHERE nome = ?";
                $stmt_update = $conn->prepare($sql_update);
                $stmt_update->bind_param("is", $copieDisp, $nome);
                if ($stmt_update->execute()) {
                    echo "Prestito effettuato con successo. Copie rimanenti: $copieDisp.";
                } else {
                    echo "Errore durante l'aggiornamento delle copie.";
                }
            } else {
                echo "Errore: Nessuna copia disponibile per il gioco richiesto.";
            }
        } else {
            echo "Errore: Il gioco richiesto non esiste.";
        }
    } else {
        // Mostra il modulo per richiedere un prestito
        echo '<h2>Richiedi un prestito</h2>';
        echo '<form method="POST" action="prestito.php">';
        echo 'Nome del gioco: <input type="text" name="nome" required><br>';
        echo '<button type="submit">Richiedi Prestito</button>';
        echo '</form>';
    }

    $conn->close();
} else {
    printf("Effettua il %s per accedere all'area riservata", '<a href="login.html">login</a>');
}
?>