<?php
// Documentation: https://docs.google.com/document/d/1M4vFmgnGQlnSS8qmiZ4SVyzixE4QK-1tPX_lmDFmVyA/edit?tab=t.h6hbnaovoow8
require_once "helpers/init.php";
if (isset($_SESSION["UUID"]) && isset($_SESSION["accessLevel"]) && isset($_SESSION["username"])) { // if logged in, redirect to main page
    redirect("mainpage.php");
}

$errorMessage = [];
if (isset($_POST["username"])) { // try to create the user
    $username = trim($_POST["username"]);
    $email = trim($_POST["email"]);
    $password = $_POST["password"];
    $passwordRepeat = $_POST["password_repeat"];
    
    if (!preg_match("/^\w{3,30}$/", $username)){ // check for a valid username
        $errorMessage[] = "Your username must have between 3 and 30 symbols. Allowed symbols: A to Z, numbers and underscore";
    }
    if (!preg_match("/^[^@\s]+@[^.\s]+\.[^\s]+\s*$/", $email)){ // check for a valid email
        $errorMessage[] = "Invalid email adress";
    }
    else { // query only if valid email adress
        if (count($dbObject->query("SELECT * FROM `user` WHERE `email` = ?", [$email])) !== 0){ // check if email already exists
            $errorMessage[] = "Email already taken";
        }
    }
    if (!preg_match("/^.{8,}$/", $password)){ // checks if password contains at least 8 characters
        $errorMessage[] = "Your password must be at least 8 characters long";
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
            $errorMessage[] = "An unexpected error occured while creating your account! P01E01";
        }
    }
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Account</title>
    <link rel="stylesheet" href="style/auth.css">
</head>
<body>
    <header>
        <div id="error-message" class="error">
            <?php foreach ($errorMessage as $error): ?>
            <p><?php echo $error ?></p>
            <?php endforeach ?>
        </div>
        <!-- <div id="success-message" class="success"></div> -->
    </header>

    <main>
        <form target="_self" action="" method="POST">
            <fieldset>
                <legend>Create New Account</legend>

                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" required>
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                </div>

                <div class="form-group">
                    <label for="password_repeat">Repeat Password</label>
                    <input type="password" id="password_repeat" name="password_repeat" required>
                </div>

                <button type="submit">Create Account</button>
            </fieldset>

            <p>Already have an account? <a href="login.php">Log in here</a>.</p>
            <p><a href="mainpage.php">Back to main page</a></p>
        </form>
    </main>

</body>
</html>
