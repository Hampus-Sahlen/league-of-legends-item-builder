<?php
require_once "helpers/init.php";
if (isset($_SESSION["UUID"]) && isset($_SESSION["accessLevel"])) {
    redirect("mainpage.php");
}

$errorMessage = [];
if (isset($_POST["username"])) { // try to create the user
    $username = $_POST["username"];
    $email = $_POST["email"];
    $password = $_POST["password"];
    $passwordRepeat = $_POST["password_repeat"];
    
    if (!preg_match("/^[\wåäöÅÄÖ]{3,30}$/", $username)){
        $errorMessage[] = "Your username must have between 3 and 30 symbols. Allowed symbols: A to Z, ÅÄÖ and _";
    }
    // email regex: /^[^@\s]+@[^.\s]+\.[^\s]+\s*$/
    // check duplicate emails before adding
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="style/login.css">
</head>
<body>
    <header>
        error-ruta här
    </header>
    <form>
        <fieldset>
            <legend></legend>
            username <input type="text" id="username" name="username" pattern="/^[\wåäöÅÄÖ]{3,30}$/" required>
            email <input type="text" id="email" name="email" required>
            password <input type="text" id="password" name="password" required>
            repeat password <input type="text" id="password_repeat" name="password_repeat" required>
        </fieldset>
    </form>
</body>
</html>
