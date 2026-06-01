<?php
require_once "../helpers/init.php";

$message = "";

// Get all groups for the form (used in both GET and POST)
$allGroups = $dbObject->query("SELECT * FROM `group` ORDER BY name ASC");

// 2. Define all statistic columns (ability removed here since it's in Base Info)
$statCols = [
    "cost", "health", "health-regen", "heal-and-shield-power", "armor", 
    "magic-resistance", "tenacity", "slow-resist", "attack-speed", "attack-damage", 
    "ability-power", "crit-chance", "crit-damage", "lethality", "magic-pen", 
    "life-steal", "omnivamp", "gold-generation", "ability-haste", "mana", 
    "mana-regen", "movement-speed", "movement-speed-percent", "armor-pen-percent", "magic-pen-percent"
];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    try {
        // --- Handle Image ---
        $imageName = null;
        if (!empty($_FILES['image_file']['name'])) {
            $imageName = time() . "_" . basename($_FILES["image_file"]["name"]); 
            $targetPath = "../images/" . $imageName;
            
            if (!move_uploaded_file($_FILES["image_file"]["tmp_name"], $targetPath)) {
                throw new Exception("Could not upload the image.");
            }
        }

        // --- CREATE ITEM (Base Info) ---
        $insertCols = ["`name`", "`image`", "`ability`"];
        $placeholders = ["?", "?", "?"];
        $values = [$_POST['name'], $imageName, $_POST['ability']];

        // Add stats dynamically if they are filled
        foreach ($statCols as $col) {
            if (isset($_POST[$col]) && $_POST[$col] !== "") {
                $insertCols[] = "`$col`";
                $placeholders[] = "?";
                $values[] = $_POST[$col];
            }
        }

        $sql = "INSERT INTO item (" . implode(", ", $insertCols) . ") VALUES (" . implode(", ", $placeholders) . ")";
        $newId = $dbObject->insertAndGetID($sql, $values);

        // --- CREATE CONNECTIONS TO GROUPS ---
        if (isset($_POST['selected_groups']) && is_array($_POST['selected_groups'])) {
            $count = 0;
            foreach ($_POST['selected_groups'] as $groupId) {
                if ($count < 22) {
                    // Using `item-ID` and `group-ID` according to your database structure
                    $dbObject->write("INSERT INTO `item-group` (`item-ID`, `group-ID`) VALUES (?, ?)", [$newId, $groupId]);
                    $count++;
                }
            }
        }

        $message = "Created item with ID: " . $newId;
    } catch (Exception $e) {
        $message = "Error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create New Item</title>
    <style>
        body { font-family: sans-serif; background: #010a13; color: #f0e6d2; padding: 20px; }
        .form-section { margin-bottom: 20px; border: 1px solid #c89b3c; padding: 15px; background: #1e2328; }
        .grid { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 10px; }
        textarea { background: #010a13; border: 1px solid #5b5a56; color: white; padding: 5px; width: 100%; box-sizing: border-box; }
        input { background: #010a13; border: 1px solid #5b5a56; color: white; padding: 5px; box-sizing: border-box; }
        .current-img { width: 64px; border: 1px solid #c89b3c; margin-bottom: 10px; }
        button { background: #c89b3c; color: black; padding: 10px 20px; border: none; cursor: pointer; font-weight: bold; }
        .status-msg { padding: 10px; margin-bottom: 20px; border: 1px solid; }
    </style>
</head>
<body>
    <a href="index.php" style="color: #00bcff;">← Back</a>
    <h1>Create New Item</h1>

    <?php if ($message): ?>
        <p style="color: #c89b3c; font-weight: bold;"><?php echo es($message); ?></p>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
        <div class="form-section">
            <h3>Base Info</h3>
            <label>Name:</label><br>
            <input type="text" name="name" required><br><br>
            
            <label>Ability:</label><br>
            <textarea name="ability" rows="4" placeholder="Describe the item's passive/active ability..."></textarea><br><br>

            <label>Image:</label><br>
            <input type="file" name="image_file" accept="image/*">
        </div>

        <div class="form-section">
            <h3>Select Item Groups</h3>
            <div style="height: 150px; overflow-y: auto; border: 1px solid #5b5a56; padding: 10px;">
                <?php foreach ($allGroups as $g): ?>
                    <label style="display: block; cursor: pointer;">
                        <?php echo es($g['name']); ?>
                        <input type="checkbox" name="selected_groups[]" value="<?php echo $g['ID']; ?>"> 
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
                        <input style="width: 100%;" type="text" name="<?php echo es($col); ?>">
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <button type="submit">CREATE ITEM</button>
    </form>
</body>
</html>