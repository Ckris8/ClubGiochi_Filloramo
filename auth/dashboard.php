<?php
session_start();

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
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        echo "<h2>Lista Giochi</h2>";
        echo "<table border='1'>";
        echo "<tr><th>Nome</th><th>Numero Giocatori</th><th>Data Acquisto/Donazione</th><th>Copie Disponibili</th></tr>";
        while ($row = $result->fetch_assoc()) {
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

    $conn->close();

    // Pulsante per aggiungere un nuovo gioco
    echo '<br><a href="../inserisci_gioco.php"><button>Aggiungi un nuovo gioco</button></a>';
     // Pulsante per aggiungere un prestito
    echo '<br><a href="../prestito.php"><button>Chiedi un prestito</button></a>';
     // Pulsante per aggiungere un incontro
     echo '<br><a href="../incontro.php"><button>Registra un incontro</button></a>';
} else { // Non c'è una sessione attiva
    printf("Effettua il %s per accedere all'area riservata", '<a href="../login.html">login</a>');
}
?>