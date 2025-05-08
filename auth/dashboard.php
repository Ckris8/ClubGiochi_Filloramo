<?php
session_start();
?>
<link rel="stylesheet" href="../css/body.css">
<?php
if (isset($_SESSION['session_id'])) {
    $session_user = htmlspecialchars($_SESSION['session_user'], ENT_QUOTES, 'UTF-8');
    $session_id = htmlspecialchars($_SESSION['session_id']);
    $livello = $_SESSION['livello'];
    
    printf("Benvenuto %s, il tuo session ID è %s", $session_user, $session_id);
    echo "<br>";
    if ($livello>7) {
        printf("Hai i diritti di amministrazione, livello %d",$livello);
        echo "<br>";
    }
    printf("%s", '<br><a href="../index.html">Torna alla Homepage</a>');

    require_once("../db/database.php");

    // Query per ottenere i dati dalla tabella Gioco
    $sql = "SELECT nome, Ngiocatori, DataAcquisto_Donazione, copieDisp FROM Gioco";
    $stmt = $pdo->query($sql);

    if ($stmt->rowCount() > 0) {
        echo "<h2>Lista Giochi</h2>";
        echo "<table border='1'>";
        echo "<tr><th>Nome</th><th>Numero Giocatori</th><th>Data Acquisto/Donazione</th><th>Copie Disponibili</th></tr>";
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row['nome']) . "</td>";
            echo "<td>" . htmlspecialchars($row['Ngiocatori']) . "</td>";
            echo "<td>" . htmlspecialchars($row['DataAcquisto_Donazione']) . "</td>";
            echo "<td>" . htmlspecialchars($row['copieDisp']) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "Nessun gioco trovato.";
    }

    // Tabella degli ultimi incontri registrati
    echo "<h2>Ultimi Incontri Registrati</h2>";
    $sql_incontri = "SELECT ListaPartecipanti, ListaVincitori, Data_incontro FROM Incontro ORDER BY Data_incontro DESC LIMIT 10";
    $stmt_incontri = $pdo->query($sql_incontri);

    if ($stmt_incontri->rowCount() > 0) {
        echo "<table border='1'>";
        echo "<tr><th>Partecipanti</th><th>Vincitori</th><th>Data</th></tr>";
        while ($row = $stmt_incontri->fetch(PDO::FETCH_ASSOC)) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row['ListaPartecipanti']) . "</td>";
            echo "<td>" . htmlspecialchars($row['ListaVincitori']) . "</td>";
            echo "<td>" . htmlspecialchars($row['Data_incontro']) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "Nessun incontro registrato.";
    }

    // Pulsanti per altre azioni
    echo '<br><a href="../inserisci_gioco.php"><button>Aggiungi un nuovo gioco</button></a>';
// Pulsante per aggiungere un prestito
    echo '<br><a href="../prestito.php"><button>Chiedi un prestito</button></a>';
// Pulsante per aggiungere un incontro
    echo '<br><a href="../incontro.php"><button>Registra un incontro</button></a>';
    echo '<br><a href="../donazione.php"><button>Fai una donazione</button></a>';
} else { // Non c'è una sessione attiva
    printf("Effettua il %s per accedere all'area riservata", '<a href="../login.html">login</a>');
}
?>
