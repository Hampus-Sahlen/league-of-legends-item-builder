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
        <?php while ($item = $items -> fetch_assoc()): 
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
        endwhile ?>
    </div>

    <header>
        <h1>Hampus Sahlen</h1>
        <a href="login.php?logout=true">Log out</a>
    </header>
    <main>
        <div id="itemStorage">
        </div>
        <br>
        <div id="itemInventory">
        </div>
        <div id="itemStats">
            <p>Ability Power: 95</p>
            <p>Magic penetration: 40%</p>
        </div>
    </main>
</body>
</html>