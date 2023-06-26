<?php

require __DIR__ . "/vendor/autoload.php";

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

if (isset($_POST) && isset($_POST["username"]) && isset($_POST["password"])) {
    $username = $_POST["username"];
    $password = $_POST["password"];

    if ($username == $_ENV["USERNAME"] && $password == $_ENV["PASSWORD"]) {
        session_start();
        $_SESSION["username"] = $username;
        $_SESSION["password"] = $password;
        $_SESSION["loggedin"] = true;
        header("Location: index.php");
    }
}

?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <link rel="icon" type="image/ico" href="./favicon.ico" />
    <link rel="stylesheet" href="styles.css" />
    <title>Connexion - PeaceAndCube</title>
</head>
<body>
    <h1>Se connecter</h1>

    <form action="login.php" method="post">
        <div>
            <label for="username">Nom d'utilisateur</label>
            <input type="text" id="username" name="username" placeholder="Nom d'utilisateur" required>
        </div>
        <div>
            <label for="password">Mot de passe</label>
            <input type="password" id="password" name="password" placeholder="Mot de passe" required>
        </div>
        <div>
            <input type="submit" value="Se connecter">
        </div>
    </form>

</body>
</html>
