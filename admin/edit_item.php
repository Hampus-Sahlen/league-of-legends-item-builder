<?php
require_once "../helpers/init.php";

$id = $_GET['id'] ?? die("No ID specified.");
$message = "";

// get current item data
try {
    $itemResult = $dbObject->query("SELECT * FROM item WHERE ID = ?", [$id]);
    if (empty($itemResult)) {
        die("Could not find the item in the database.");
    }
    $item = $itemResult[0];
} catch (Exception $e) {
    die("An error occurred while fetching the item: " . $e->getMessage());
}

// 2. Get all groups and the item's current connections
$allGroups = $dbObject->query("SELECT * FROM `group` ORDER BY name ASC");
// OBS: Changed to `group-ID` and `item-ID` with backticks
$currentLinks = $dbObject->query("SELECT `group-ID` FROM `item-group` WHERE `item-ID` = ?", [$id]);
$currentGroupIDs = array_column($currentLinks, 'group-ID');

// All statistic columns that can be edited
$statCols = ["cost", "health", "health-regen", "heal-and-shield-power", "armor", "magic-resistance", "tenacity", "slow-resist", "attack-speed", "attack-damage", "ability-power", "crit-chance", "crit-damage", "lethality", "magic-pen", "life-steal", "omnivamp", "gold-generation", "ability-haste", "mana", "mana-regen", "movement-speed", "movement-speed-percent", "armor-pen-percent", "magic-pen-percent"];

// 3. Handle POST (Save changes)
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    try {
        // --- Handle Image ---
        $imageName = $item['image']; 
        if (!empty($_FILES['image_file']['name'])) {
            $newFileName = time() . "_" . basename($_FILES["image_file"]["name"]);
            $targetPath = "../images/" . $newFileName; 
            
            if (move_uploaded_file($_FILES["image_file"]["tmp_name"], $targetPath)) {
                $imageName = $newFileName;
            } else {
                throw new Exception("Could not upload the image to the ../images/ folder.");
            }
        }

        // --- UPDATE ITEM ---
        $updates = ["`name` = ?", "`image` = ?", "`ability` = ?"];
        $params = [$_POST['name'], $imageName, $_POST['ability']];

        foreach ($statCols as $col) {
            $updates[] = "`$col` = ?";
            $val = trim($_POST[$col]);
            $params[] = ($val === "") ? null : $val;
        }

        $params[] = $id; 
        $dbObject->write("UPDATE item SET " . implode(", ", $updates) . " WHERE ID = ?", $params);

        // --- UPDATE GROUPS ---
        // Clear old connections
        $dbObject->write("DELETE FROM `item-group` WHERE `item-ID` = ?", [$id]);
        
        // Add new connections
        if (isset($_POST['selected_groups']) && is_array($_POST['selected_groups'])) {
            $selected = array_slice($_POST['selected_groups'], 0, 3);
            foreach ($selected as $groupId) {
                $dbObject->write("INSERT INTO `item-group` (`item-ID`, `group-ID`) VALUES (?, ?)", [$id, $groupId]);
            }
        }

        header("Location: edit_item.php?id=$id&status=success");
        exit;

    } catch (Exception $e) {
        $message = "Error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Item: <?php echo es($item['name']); ?></title>
    <style>
        body { font-family: sans-serif; background: #010a13; color: #f0e6d2; padding: 20px; }
        .form-section { margin-bottom: 20px; border: 1px solid #c89b3c; padding: 15px; background: #1e2328; }
        .grid { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 10px; }
        input, textarea { background: #010a13; border: 1px solid #5b5a56; color: white; padding: 5px; width: 100%; box-sizing: border-box; }
        .current-img { width: 64px; border: 1px solid #c89b3c; margin-bottom: 10px; }
        button { background: #c89b3c; color: black; padding: 10px 20px; border: none; cursor: pointer; font-weight: bold; }
        .status-msg { padding: 10px; margin-bottom: 20px; border: 1px solid; }
    </style>
</head>
<body>
    <a href="index.php" style="color: #00bcff;">← Back to the list</a>
    <h1>Edit Item: <?php echo es($item['name']); ?></h1>

    <?php if (isset($_GET['status'])): ?>
        <div class="status-msg" style="border-color: #00ff00; color: #00ff00;">Changes saved!</div>
    <?php endif; ?>

    <?php if ($message): ?>
        <div class="status-msg" style="border-color: #ff0000; color: #ff0000;"><?php echo es($message); ?></div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
        <div class="form-section">
            <h3>Base info</h3>
            <label>Name:</label><br>
            <input type="text" name="name" value="<?php echo es($item['name']); ?>" required><br><br>
            
            <label>Ability:</label><br>
            <textarea name="ability" rows="4"><?php echo es($item['ability'] ?? ''); ?></textarea><br><br>

            <label>Image:</label><br>
            <?php if ($item['image']): ?>
                <img src="../images/<?php echo es($item['image']); ?>" class="current-img"><br>
            <?php endif; ?>
            <input type="file" name="image_file" accept="image/*">
        </div>

        <div class="form-section">
            <h3>Groups (Select up to 3)</h3>
            <div style="height: 150px; overflow-y: auto; border: 1px solid #5b5a56; padding: 10px;">
                <?php foreach ($allGroups as $g): ?>
                    <?php $checked = in_array($g['ID'], $currentGroupIDs) ? "checked" : ""; ?>
                    <label style="display: block; cursor: pointer;">
                        <input type="checkbox" name="selected_groups[]" value="<?php echo $g['ID']; ?>" <?php echo $checked; ?>> 
                        <?php echo es($g['name']); ?>
                    </label>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="form-section">
            <h3>Stats</h3>
            <div class="grid">
                <?php foreach ($statCols as $col): ?>
                    <div>
                        <label style="font-size: 0.8em; color: #a19d94;"><?php echo es($col); ?>:</label><br>
                        <input type="text" name="<?php echo es($col); ?>" value="<?php echo es($item[$col] ?? ''); ?>">
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <button type="submit">SAVE CHANGES</button>
    </form>
</body>
</html>