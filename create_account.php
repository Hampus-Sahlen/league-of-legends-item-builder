<?php
require_once "helpers/init.php";
if (isset($_SESSION["UUID"]) && isset($_SESSION["accessLevel"])) {
    redirect("mainpage.php");
}

$errorMessage = [];
if (isset($_POST["username"])) { // try to create the user
    $username = trim($_POST["username"]);
    $email = trim($_POST["email"]);
    $password = $_POST["password"];
    $passwordRepeat = $_POST["password_repeat"];
    
    if (!preg_match("/^[\wåäöÅÄÖ]{3,30}$/", $username)){ // check for a valid username
        $errorMessage[] = "Your username must have between 3 and 30 symbols. Allowed symbols: A to Z, numbers, ÅÄÖ and _";
    }
    if (!preg_match("/^[^@\s]+@[^.\s]+\.[^\s]+\s*$/", $email)){ // check for a valid email
        $errorMessage[] = "Invalid email adress";
    }
    else { // query only if valid email adress
        if (count($dbObject->query("SELECT * FROM `user` WHERE `email` = ?", [$email])) !== 0){ // check if email already exists
            $errorMessage[] = "Email already taken";
        }
    }
    if (!preg_match("/^[^\s]+$/", $password)){ // checks if password contains no spaces and at least 1 character
        $errorMessage[] = "You must have a password that does not contain spaces";
    }
    if ($password !== $passwordRepeat){
        $errorMessage[] = "Your passwords do not match";
    }

    if (count($errorMessage) === 0) { // if no problems were found
        // create account
        $passwordHASHED = password_hash($password, PASSWORD_BCRYPT); // 60 character HASH
        unset($password);
        unset($passwordCheck); // prevent accidental leaks

        $reply = $dbObject->write(
        "INSERT INTO `user` (`username`, `email`, `access-level`, `password`) VALUES (?, ?, 0, ?)",
        [$username, $email, $passwordHASHED]);
        if ($reply){
            $_SESSION["createdAccount"] = true;
            redirect("login.php");
        } else {
            $errorMessage[] = "An error occured while creating your account! E01P01";
        }
    }
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
        <?php foreach ($errorMessage as $error): ?>
        <p><?php echo $error ?></p>
        <?php endforeach ?>
    </header>
    <form method="post" target="_self">
        <fieldset>
            <legend></legend>
            username <input type="text" id="username" name="username" required>
            email <input type="text" id="email" name="email" required>
            password <input type="text" id="password" name="password" required>
            repeat password <input type="text" id="password_repeat" name="password_repeat" required>
            <input type="submit">
        </fieldset>
    </form>
    <a href="login.php">login</a>
</body>
</html>
