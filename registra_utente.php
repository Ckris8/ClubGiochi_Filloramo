<?php
session_start(); //ripristina o avvia una SESSIONe di navigazione già avviata
$livello = -1; //-1 == non loggato 
if (isset($_SESSION['session_id'])) {
    $session_user = htmlspecialchars($_SESSION['session_user'], ENT_QUOTES, 'UTF-8');
    $session_id = htmlspecialchars($_SESSION['session_id']);
    if (isset($_SESSION['livello'])==TRUE) { //se sono passato per il login
        $livello = $_SESSION['livello']; //allora trovo livello in $_SESSION e lo prelevo
    } else {
        //altrimenti lo imposto a -1 valore che usero per "utente non loggato" 
        $livello=-1; 
    }
    echo "<br>";
    switch ($livello) {
        case '-1':
            printf("Benvenuto Ospite, puoi registrarti per un account");
            break;
        case '0':
            printf("Benvenuto %s, il tuo session ID è %s puoi registrare un altro ospite", $session_user, $session_id);
            break;  
        case '8':
            printf("Benvenuto %s, il tuo session ID è %s e hai i poteri di amministrazione", $session_user, $session_id);
            break;
        case '9':
            printf("Benvenuto %s, il tuo session ID è %s e hai i poteri della supermucca", $session_user, $session_id);
            break;
        default:
            printf("Benvenuto %s, non dovresti essere qui", $session_user, $session_id);
            printf("Effettua il %s per accedere all'area riservata", '<a href="login.html">login</a>');
            die();
            break;
    }
    printf("%s", '<br><a href="auth/logout.php">logout</a><br><a href="index.html">homepage</a><br>');
} else { //non c'è una sessione attiva
    printf("Effettua il %s per accedere all'area riservata", '<a href="login.html">login</a>');
}

?>

<!DOCTYPE html>
<html>
    <head>
        <title>Registrazione</title>
        <link rel="stylesheet" href="login.css">
    </head>
    <body>
        <div>
            <form method="post" action="/auth/register.php">
                <h1>Registrazione</h1>
                <input type="text" id="username" placeholder="Username" name="username" maxlength="50" required>
                <input type="password" id="password" placeholder="Password" name="password" required>
                <?php    
                //logica per "stampare" o meno il campo select
                    if ($livello>7) {  //sono almeno SU stampo la scelta livello 
                        $scelte='<label for="livello">Livello utente</label>
                        <select name="livello" id="livello">
                        <option value=0>Ospite</option>
                        <option value=1>Utente base</option>
                        <option value=7>superuser</option>s
                        <option value=8>admin</option>
                        <option value=9>root</option>
                        </select>';
                    } else { 
                        $scelte=$scelte='<label for="livello">Livello utente</label>
                        <select name="livello" id="livello">
                        <option value=0>Ospite</option>
                        </select>';;
                    }
                    printf("%s",$scelte);
                ?>
                <button type="submit" name="register">Registrati</button>
            </form>
        </div>
    </body>
</html>
