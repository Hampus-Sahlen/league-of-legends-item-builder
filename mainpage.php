<?php
require_once "helpers/init.php";

$items = $dbObject -> query_nofetch(
    "SELECT * 
    FROM item
")


?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="style/mainpage.css">
    <script src="script/item_builder.js" defer></script>
</head>
<body>
    <div id="importDiv">
        <?php while ($item = $items -> fetch_assoc()) {
            echo "<div>";
            foreach ($item as $key => $val) { 
                if ($val !== null) {
                    echo "<div>";
                    echo "<h1>";
                    echo $key;
                    echo "</h1><p>";
                    echo $val;
                    echo "</p>";
                    echo "</div>";
                }
            }
            echo "</div>";
        } ?>
    </div>

    <header class="top-nav">
        <h1>Hampus Sahlen</h1>
        <a href="login.php?logout=true" class="logout-btn">Log out</a>
    </header>

    <main class="builder-container">

        <div id="storage-container" class="panel storage">
            <h2>Item Shop</h2>
            <div id="itemStorage" class="item-grid"></div>
        </div>

        <div id="inventory-container" class="panel inventory">
            <h2>Your Build</h2>
            <div id="itemInventory" class="inventory-grid">
                <div class="slot"></div>
                <div class="slot"></div>
                <div class="slot"></div>
                <div class="slot"></div>
                <div class="slot"></div>
                <div class="slot"></div>
            </div>
        </div>

        <div id="stats-container" class="panel stats">
            <h2>Total Stats</h2>
            <div id="itemStats" class="stats-list"></div>
        </div>

    </main>
</body>
</html>