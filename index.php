<?php
require_once "helpers/init.php";

$userInfo = null;
if (!empty($_SESSION["UUID"])) {
    $userInfo = $dbObject -> query(
        "SELECT *
        FROM `user`
        WHERE `UUID` = ?",
        [$_SESSION["UUID"]]
    )[0];
}




?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>League Item Builder - Home</title>
    <link rel="stylesheet" href="style/homepage.css">
</head>

<body>
    <header class="top-nav">
        <?php if (!empty($userInfo)): ?>
        <a href="login.php?logout=true" class="logout-btn">Log out</a>
        <?php if ($_SESSION["accessLevel"] === 1) {echo '<a href="./admin/" class="logout-btn">Admin page</a>';}?>
        <h1><?php echo es($userInfo["username"]) ?></h1>
        <?php else: ?>
        <a href="login.php" class="logout-btn">Log in</a>
        <?php endif ?>
    </header>

    <main class="home-container">
        <h2 class="main-title">League of Legends Item Builder</h2>

        <p class="sub-text">
            Plan your path to victory. Drag and drop items to test combinations and calculate your ultimate League of Legends build.
        </p>

        <a href="mainpage.php" class="cta-button">Click here to open builder</a>
    </main>
</body>

</html>