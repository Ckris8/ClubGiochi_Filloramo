<?php
session_start();

if (isset($_SESSION['session_id'])) {
    // Connessione al database
    require_once("../db/database.php");

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Recupera i dati dal form
        $partecipanti = isset($_POST['partecipanti']) ? $_POST['partecipanti'] : [];
        $vincitori = isset($_POST['vincitori']) ? $_POST['vincitori'] : [];
        $data_incontro = htmlspecialchars($_POST['data_incontro'], ENT_QUOTES, 'UTF-8');

        if (empty($partecipanti)) {
            echo "Errore: Devi selezionare almeno un partecipante.";
        } elseif (empty($vincitori)) {
            echo "Errore: Devi selezionare almeno un vincitore.";
        } elseif (!array_intersect($vincitori, $partecipanti)) {
            echo "Errore: I vincitori devono essere tra i partecipanti.";
        } else {
            // Inserisci l'incontro nella tabella
            $listaPartecipanti = implode(", ", $partecipanti);
            $listaVincitori = implode(", ", $vincitori);

            $sql_insert = "INSERT INTO incontro (ListaPartecipanti, ListaVincitori, Data_incontro) VALUES (?, ?, ?)";
            $stmt_insert = $conn->prepare($sql_insert);
            $stmt_insert->bind_param("sss", $listaPartecipanti, $listaVincitori, $data_incontro);

            if ($stmt_insert->execute()) {
                echo "Incontro registrato con successo.";
            } else {
                echo "Errore durante la registrazione dell'incontro.";
            }
        }
    } else {
        // Recupera i soci dalla tabella Soci
        $sql_soci = "SELECT id, username FROM Soci";
        $result_soci = $conn->query($sql_soci);

        if ($result_soci->num_rows > 0) {
            echo '<h2>Registra un incontro</h2>';
            echo '<form method="POST" action="incontro.php">';
            echo '<label>Data dell\'incontro:</label> <input type="date" name="data_incontro" required><br><br>';

            echo '<label>Seleziona i partecipanti:</label><br>';
            while ($row = $result_soci->fetch_assoc()) {
                echo '<input type="checkbox" name="partecipanti[]" value="' . htmlspecialchars($row['username']) . '"> ' . htmlspecialchars($row['username']) . '<br>';
            }

            echo '<br><label>Seleziona i vincitori:</label><br>';
            $result_soci->data_seek(0); // Resetta il puntatore del risultato
            while ($row = $result_soci->fetch_assoc()) {
                echo '<input type="checkbox" name="vincitori[]" value="' . htmlspecialchars($row['username']) . '"> ' . htmlspecialchars($row['username']) . '<br>';
            }

            echo '<br><button type="submit">Registra Incontro</button>';
            echo '</form>';
        } else {
            echo "Nessun socio trovato.";
        }
    }

    $conn->close();
} else {
    printf("Effettua il %s per accedere all'area riservata", '<a href="login.html">login</a>');
}
?>