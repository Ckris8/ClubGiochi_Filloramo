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
} else { //non c'è una sessione attiva
    printf("Effettua il %s per accedere all'area riservata", '<a href="../login.html">login</a>');
}

?>