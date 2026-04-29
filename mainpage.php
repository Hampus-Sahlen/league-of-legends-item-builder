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
            unset($item["ID"]);
            foreach ($item as $key => $val) { 
                if ($val !== null) {
                    echo "<h1>";
                    echo $key;
                    echo "</h1><p>";
                    echo $val;
                    echo "</p>";
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
        <div>
            <article>
                <img src="images/Blade_of_the_Ruined_King_item_HD.webp" alt="Blade of the Ruined King">
            </article>
            <article>
                <img src="images/Infinity_Edge_item_HD.webp" alt="Infinity Edge">
            </article>
        </div>
        <div>
            <article>
                <img src="images/Void_Staff_item_HD.webp" alt="Void Staff">
            </article>
        </div>
        <div>
            <p>Ability Power: 95</p>
            <p>Magic penetration: 40%</p>
        </div>
    </main>
</body>
</html>