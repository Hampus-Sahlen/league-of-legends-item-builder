<?php
// Documentation: https://docs.google.com/document/d/1M4vFmgnGQlnSS8qmiZ4SVyzixE4QK-1tPX_lmDFmVyA/edit?tab=t.0
require_once "helpers/init.php";
$errorMessage = "";
$successMessage = "";

if (isset($_GET["logout"])) { if ($_GET["logout"] == "true") { // logout
    unset($_SESSION["UUID"]);
    unset($_SESSION["accessLevel"]);
    unset($_SESSION["username"]);
    $_SESSION["logout"] = true;
    redirect("login.php");
}}

if (isset($_SESSION["logout"])) { if ($_SESSION["logout"]) { // if user has been logged out
    unset($_SESSION["logout"]);
    $successMessage = "Logged out successfully";
}}

if (isset($_SESSION["UUID"]) && isset($_SESSION["accessLevel"]) && isset($_SESSION["username"])) { // if logged in, redirect to main page
    redirect("mainpage.php");
}

if (isset($_SESSION["createdAccount"])) { if ($_SESSION["createdAccount"]) { // if user has created an account
    unset($_SESSION["createdAccount"]);
    $successMessage = "Account created successfully";
}}

if (isset($_POST["email"])) { // try to login the user
    $email = $_POST["email"];
    $password = $_POST["password"];
    $user = $dbObject->query(
        "SELECT 
            `UUID`,
            `username`,
            `email`,
            `password`,
            `access-level`
        FROM `user`
        WHERE `email` = ?",
        [$email]
    );

    if (count($user) === 1) {
        $user = $user[0];
        // login the user
        if (password_verify($password, $user["password"])) {
            // user is verified and logged in
            $_SESSION["UUID"] = $user["UUID"];
            $_SESSION["accessLevel"] = $user["access-level"];
            $_SESSION["username"] = $user["username"];
            redirect("mainpage.php");
        }
        else {
            $errorMessage = "Email or password incorrect";
        }
    }
    elseif (count($user) === 0) {
        $errorMessage = "Email or password incorrect";
    } 
    else {
        $errorMessage = "An unexpected error has occured! P00E01";
    }

}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log In</title>
    <link rel="stylesheet" href="style/auth.css">
</head>
<body>
    <header>
        <div id="error-message" class="error">
            <?php echo $errorMessage ?>
        </div>
        <div id="success-message" class="success">
            <?php echo $successMessage ?>
        </div>
    </header>

    <main>
        <form target="_self" action="" method="POST" >
            <fieldset>
                <legend>Account Login</legend>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                </div>

                <button type="submit">Log In</button>
            </fieldset>

            <p>Don't have an account? <a href="create_account.php">Create one here</a>.</p>
            <p><a href="mainpage.php">Back to main page</a></p>
        </form>
    </main>
</body>
</html>
