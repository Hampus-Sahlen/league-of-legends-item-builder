<?php
require_once "helpers/init.php";
$errorMessage = "";
$successMessage = "";

if (isset($_GET["logout"])) { if ($_GET["logout"] == "true") { // logout
    unset($_SESSION["UUID"]);
    unset($_SESSION["accessLevel"]);
    $_SESSION["logout"] = true;
    redirect("login.php");
}}

if (isset($_SESSION["logout"])) { if ($_SESSION["logout"]) { // if user has been logged out, display successful logout message
    $successMessage = "Logged out successfully";
}}

if (isset($_SESSION["UUID"]) && isset($_SESSION["accessLevel"])) { // if logged in, redirect to main page
    redirect("mainpage.php");
}

if (isset($_POST["email"])) { // try to login the user
    $email = $_POST["email"];
    $password = $_POST["password"];
    
    $user = $dbObject->query(
        "SELECT 
            `UUID`,
            `email`,
            `password`,
            `access-level`
        FROM `user`
        WHERE `email` = ?",
        [$email]
    );

    if (count($user) === 1) {
        // login the user
        if (password_verify($password, $user["password"])) {
            // user is verified and logged in
            $_SESSION["UUID"] = $user["UUID"];
            $_SESSION["accessLevel"] = $user["access-level"];
            redirect("test.php");
        }
        else {
            $errorMessage = "Email or password incorrect";
        }
    }
    elseif (count($user) === 0) {
        $errorMessage = "Email or password incorrect";
    } 
    elseif (count($user) > 1) {
        $errorMessage = "An unexpected error has occured! E0001";
    }
    else {
        $errorMessage = "An unexpected error has occured! E0002";
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
        <div>
            <?php $errorMessage ?>
        </div>
        <div>
            <?php $successMessage ?>
        </div>
    </header>
    <form>
        <fieldset>
            <legend></legend>
            email <input type="text" id="email" name="email">
            password <input type="password" id="password" name="password">
        </fieldset>
    </form>
</body>
</html>
