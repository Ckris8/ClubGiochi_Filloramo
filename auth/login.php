<?php
session_start();
require_once('../db/database.php');

if (isset($_SESSION['session_id'])) {
    header('Location: dashboard.php');
    exit;
}

if (isset($_POST['login'])) {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if (empty($username) || empty($password)) {
        $msg = 'Inserisci username e password %s';
    } else {
        $query = "
            SELECT username, password, livello
            FROM Socio
            WHERE username = :username
        ";
        
        $check = $pdo->prepare($query);
        $check->bindParam(':username', $username, PDO::PARAM_STR);
        $check->execute();
        
        $user = $check->fetch(PDO::FETCH_ASSOC);
        
        if (!$user || password_verify($password, $user['password']) === false) {
            $msg = 'Credenziali utente errate %s';
        } else {
            //session_regenerate_id();
            $_SESSION['session_id'] = session_id();
            $_SESSION['session_user'] = $user['username'];
            $_SESSION['livello'] = $user['livello'];
            
            header('Location: dashboard.php');
            exit;
        }
    }
}
?>
<link rel="stylesheet" href="../css/body.css">
<?php
// Mostra il modulo di login o eventuali messaggi
if (isset($msg)) {
    echo sprintf($msg, '<a href="login.php">Riprova</a>');
}
?>
<form method="POST" action="login.php">
    <label for="username">Username:</label>
    <input type="text" id="username" name="username" required>
    <br>
    <label for="password">Password:</label>
    <input type="password" id="password" name="password" required>
    <br>
    <button type="submit" name="login">Login</button>
</form>