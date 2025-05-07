<?php
session_start();

if (isset($_SESSION['session_id'])) {
    // Connessione al database
    require_once("../db/database.php");

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $nome = htmlspecialchars($_POST['nome'], ENT_QUOTES, 'UTF-8');
        $Ngiocatori = (int)$_POST['Ngiocatori'];
        $DataAcquisto_Donazione = htmlspecialchars($_POST['DataAcquisto_Donazione'], ENT_QUOTES, 'UTF-8');

        // Controlla se il gioco esiste già
        $sql_check = "SELECT copieDisp FROM Gioco WHERE nome = ?";
        $stmt_check = $conn->prepare($sql_check);
        $stmt_check->bind_param("s", $nome);
        $stmt_check->execute();
        $result_check = $stmt_check->get_result();

        if ($result_check->num_rows > 0) {
            // Il gioco esiste già, aggiorna le copie
            $row = $result_check->fetch_assoc();
            $copieDisp = $row['copieDisp'];

            if ($copieDisp >= 3) {
                echo "Errore: Non è possibile avere più di 3 copie dello stesso gioco.";
            } else {
                $copieDisp++;
                $sql_update = "UPDATE Gioco SET copieDisp = ? WHERE nome = ?";
                $stmt_update = $conn->prepare($sql_update);
                $stmt_update->bind_param("is", $copieDisp, $nome);
                if ($stmt_update->execute()) {
                    echo "Copia aggiunta con successo.";
                } else {
                    echo "Errore durante l'aggiornamento delle copie.";
                }
            }
        } else {
            // Il gioco non esiste, inseriscilo
            $copieDisp = 1;
            $sql_insert = "INSERT INTO Gioco (nome, Ngiocatori, DataAcquisto_Donazione, copieDisp) VALUES (?, ?, ?, ?)";
            $stmt_insert = $conn->prepare($sql_insert);
            $stmt_insert->bind_param("sisi", $nome, $Ngiocatori, $DataAcquisto_Donazione, $copieDisp);
            if ($stmt_insert->execute()) {
                echo "Gioco inserito con successo.";
            } else {
                echo "Errore durante l'inserimento del gioco.";
            }
        }
    } else {
        // Mostra il modulo per l'inserimento
        echo '<h2>Inserisci un nuovo gioco</h2>';
        echo '<form method="POST" action="inserisci_gioco.php">';
        echo 'Nome: <input type="text" name="nome" required><br>';
        echo 'Numero Giocatori: <input type="number" name="Ngiocatori" required><br>';
        echo 'Data Acquisto/Donazione: <input type="date" name="DataAcquisto_Donazione" required><br>';
        echo '<button type="submit">Inserisci Gioco</button>';
        echo '</form>';
    }

    $conn->close();
} else {
    printf("Effettua il %s per accedere all'area riservata", '<a href="login.html">login</a>');
}
?>