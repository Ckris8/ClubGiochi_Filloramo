<?php
session_start();
?>
<link rel="stylesheet" href="../index.css">
<?php
if (isset($_SESSION['session_id'])) {
    // Connessione al database
    require_once("db/database.php");

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

            $sql_insert = "INSERT INTO Incontro (ListaPartecipanti, ListaVincitori, Data_incontro) VALUES (?, ?, ?)";
            $stmt_insert = $pdo->prepare($sql_insert);
            $stmt_insert->execute([$listaPartecipanti, $listaVincitori, $data_incontro]);

            echo "Incontro registrato con successo.";
            echo '<br><a href="auth/dashboard.php"><button>Torna home</button></a>';
        }
    } else {
        // Recupera i soci dalla tabella Soci
        $sql_soci = "SELECT id, username FROM Socio";
        $stmt_soci = $pdo->query($sql_soci);

        if ($stmt_soci->rowCount() > 0) {
            echo '<h2>Registra un incontro</h2>';
            echo '<form method="POST" action="incontro.php">';
            echo '<label>Data dell\'incontro:</label> <input type="date" name="data_incontro" required><br><br>';

            echo '<label>Seleziona i partecipanti:</label><br>';
            while ($row = $stmt_soci->fetch(PDO::FETCH_ASSOC)) {
                echo '<input type="checkbox" name="partecipanti[]" value="' . htmlspecialchars($row['username']) . '"> ' . htmlspecialchars($row['username']) . '<br>';
            }

            echo '<br><label>Seleziona i vincitori:</label><br>';
            $stmt_soci->execute(); // Re-esegui la query per resettare il puntatore
            while ($row = $stmt_soci->fetch(PDO::FETCH_ASSOC)) {
                echo '<input type="checkbox" name="vincitori[]" value="' . htmlspecialchars($row['username']) . '"> ' . htmlspecialchars($row['username']) . '<br>';
            }

            echo '<br><button type="submit">Registra Incontro</button>';
            echo '</form>';
        } else {
            echo "Nessun socio trovato.";
        }

        // Recupera gli ultimi incontri registrati
        echo '<h2>Ultimi incontri registrati</h2>';
        $sql_incontri = "SELECT ListaPartecipanti, ListaVincitori, Data_incontro FROM Incontro ORDER BY Data_incontro DESC LIMIT 10";
        $stmt_incontri = $pdo->query($sql_incontri);

        if ($stmt_incontri->rowCount() > 0) {
            echo '<table border="1">';
            echo '<tr><th>Partecipanti</th><th>Vincitori</th><th>Data</th></tr>';
            while ($row = $stmt_incontri->fetch(PDO::FETCH_ASSOC)) {
                echo '<tr>';
                echo '<td>' . htmlspecialchars($row['ListaPartecipanti']) . '</td>';
                echo '<td>' . htmlspecialchars($row['ListaVincitori']) . '</td>';
                echo '<td>' . htmlspecialchars($row['Data_incontro']) . '</td>';
                echo '</tr>';
            }
            echo '</table>';
        } else {
            echo "Nessun incontro registrato.";
        }
    }
} else {
    printf("Effettua il %s per accedere all'area riservata", '<a href="login.html">login</a>');
}
?>