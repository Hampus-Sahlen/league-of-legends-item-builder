<?php
require_once "helpers/init.php";

$items = $dbObject -> query_nofetch(
    "SELECT *
    FROM `item`
");

$groups_tmp = $dbObject -> query(
    "SELECT *
    FROM `group`
    JOIN `item-group` ON `group-ID` = `ID`
");

// debugPrint($groups_tmp);

$groups = [];
foreach ($groups_tmp as $group) {
    // debugPrint($group);
    if (empty($groups[$group["item-ID"]])) {
        $groups[$group["item-ID"]] = [];
    }
    $groups[$group["item-ID"]][] = $group["name"];
}


$userInfo = null;
if (!empty($_SESSION["UUID"])) {
    $userInfo = $dbObject -> query(
        "SELECT *
        FROM `user`
        WHERE `UUID` = ?",
        [$_SESSION["UUID"]]
    )[0];
}

$itemArray = [];
while ($item = $items -> fetch_assoc()) {
    foreach ($item as $stat => $val){ // remove stats that are not set or are 0
        if (empty($val)){
            unset($item[$stat]);
        }
    }

    $item["groups"] = [];
    if (!empty($groups[$item["ID"]])) { // add on groups
        foreach ($groups[$item["ID"]] as $group) {
            $item["groups"][] = $group;
        }
    }

    if (!empty($item["ability"])) {
        $abilityClump = $item["ability"];
        $item["ability"] = [];
        if (str_contains($abilityClump, 'Unique – ') && str_contains($abilityClump, ': ')) {
            foreach (explode('Unique – ', $abilityClump) as $ability) {
                if (!empty($ability)){
                    $temp = explode(': ', $ability);
                    $item["ability"][$temp[0]] = $temp[1];
                }
            }
        } else {
            $item["ability"]["Ability"] = $abilityClump;
        }

    } else {
        $item["ability"] = [];
    }

    $itemArray[] = $item;
}

// debugPrint($itemArray)



?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>League Item Builder</title>
    <link rel="stylesheet" href="style/mainpage.css">
    <script>
        const items = <?php echo json_encode($itemArray, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) /* imports all items as a json */ ?>;
    </script>
    <script src="script/item_builder.js" defer></script>
    <noscript>
        <code style="background-color: red;">
            This page requires javascript to be enabled
        </code>
    </noscript>
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

    <main id="builder-container">
        <div id="storage-container" class="panel storage">
            <p style="text-align:center;margin-bottom:10px;">Press and hold to drag the items</p>
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

        <div id="hover-stats-container" class="panel stats hover-container">
            <h2 id="hoverStatsTitle"></h2>
            <div id="hoverStats" class="stats-list"></div>
        </div>
        
        <div id="hover-ability-container" class="panel stats hover-container">
            <h2 id="hoverAbilityTitle"></h2>
            <div id="hoverAbility" class="stats-list"></div>
        </div>
    </main>
</body>
</html>