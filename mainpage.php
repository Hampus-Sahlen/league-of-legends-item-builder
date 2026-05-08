<?php
require_once "helpers/init.php";

$items = $dbObject -> query_nofetch(
    "SELECT * 
    FROM item
");

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
    <title>League Item Builder</title>
    <link rel="stylesheet" href="style/mainpage.css">
    <script src="script/item_builder.js" defer></script>
</head>
<body>
    <div id="importDiv" style="display: none;">
    <?php while ($item = $items -> fetch_assoc()): ?>
        <div>
        <?php foreach ($item as $key => $val): if ($val !== null): ?>
            <div>
                <h1><?php echo $key; ?></h1>
                <p><?php echo $val; ?></p>
            </div>
        <?php endif; endforeach ?>
        </div>
    <?php endwhile ?>
    </div>

    <header class="top-nav">
        <?php if (!empty($userInfo)): ?>
        <h1><?php echo $userInfo["username"] ?></h1>
        <a href="login.php?logout=true" class="logout-btn">Log out</a>
        <?php if ($_SESSION["accessLevel"] === 1) {echo '<a href="./admin/" class="logout-btn">Admin page</a>';}?>
        <?php else: ?>
        <a href="login.php" class="logout-btn">Log in</a>
        <?php endif ?>
    </header>

    <main class="builder-container">

        <div id="storage-container" class="panel storage">
            <h2>Item Shop</h2>
            <div id="itemStorage" class="item-grid"></div>
        </div>

        <div id="inventory-container" class="panel inventory">
            <h2>Your Build</h2>
            <div id="itemInventory" class="inventory-grid">
            </div>
        </div>

        <div id="stats-container" class="panel stats">
            <h2>Total Stats</h2>
            <div id="itemStats" class="stats-list"></div>
        </div>

    </main>
</body>
</html>