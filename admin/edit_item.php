<?php
require_once "../helpers/init.php";
checkPermission(1, "../login.php"); // Only allow access to users with access level 1 (admin)
$columns = [
    "name" => "name",
    "cost"  => "cost",
    "item-group"        => "item group",
    "ability"     => "ability",
    "image"  => "image",
    "health" => "health",
    "health-regen" => "health regen",
    "heal-and-shield-power" => "heal and shield power",
    "armor" => "armor",
    "magic-resistance" => "magic resistance",
    "tenacity" => "tenacity",
    "slow-resist" => "slow resist",
    "attack-speed" => "attack speed",
    "attack-damage" => "attack damage",
    "ability-power" => "ability power",
    "crit-chance" => "crit chance",
    "crit-damage" => "crit damage",
    "lethality" => "lethality",
    "magic-pen" => "magic pen",
    "life-steal" => "life steal",
    "omnivamp" => "omnivamp",
    "gold-generation" => "gold generation",
    "ability-haste" => "ability haste",
    "mana" => "mana",
    "mana-regen" => "mana regen",
    "movement-speed" => "movement speed",
    "movement-speed-percent" => "movement speed percent",
    "armor-pen-percent" => "armor pen percent",
    "magic-pen-percent" => "magic pen percent",
];

$message = "";
$id = $_GET['id'] ?? null;

if (!$id) {
    die("No item ID specified.");
}

// Hantera sparande (UPDATE)
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $updateParts = [];
    $values = [];

    foreach ($columns as $dbCol => $label) {
        if (isset($_POST[$dbCol])) {
            $updateParts[] = "`$dbCol` = ?";
            $val = trim($_POST[$dbCol]);
            $values[] = ($val === "") ? null : $val;
        }
    }

    if (!empty($updateParts)) {
        $values[] = $id; 
        $sql = "UPDATE item SET " . implode(", ", $updateParts) . " WHERE ID = ?";
        
        try {
            $dbObject->write($sql, $values);
            $message = "The item has been updated successfully!";
        } catch (Exception $e) {
            $message = "Error: " . $e->getMessage();
        }
    }
}

// Get the current item data to pre-fill the form
try {
    $sql = "SELECT * FROM item WHERE ID = ?";
    $result = $dbObject->query($sql, [$id]);
    if (empty($result)) die("The item was not found.");
    $currentItem = $result[0];
} catch (Exception $e) {
    die("Error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Item</title>
    <link rel="stylesheet" href="../style.css"> <style>
        body { font-family: sans-serif; padding: 20px; background: #1a1a1a; color: white; }
        .form-group { margin-bottom: 10px; display: flex; flex-direction: column; width: 300px; }
        input { padding: 8px; background: #333; border: 1px solid #444; color: white; }
        .btn-save { background: #c89b3c; color: black; padding: 10px; border: none; cursor: pointer; font-weight: bold; margin-top: 10px; }
        .back-link { color: #00bcff; text-decoration: none; display: block; margin-bottom: 20px; }
    </style>
</head>
<body>

    <a href="selection.php" class="back-link">← Back to List</a>

    <h1>Edit: <?php echo es($currentItem['name']); ?></h1>

    <?php if ($message): ?>
        <p style="color: #00ff00;"><?php echo es($message); ?></p>
    <?php endif; ?>

    <form method="POST">
        <?php foreach ($columns as $dbCol => $label): ?>
            <div class="form-group">
                <label><?php echo es(ucfirst($label)); ?></label>
                <input type="text" name="<?php echo es($dbCol); ?>" value="<?php echo es($currentItem[$dbCol] ?? ''); ?>">
            </div>
        <?php endforeach; ?>
        <button type="submit" class="btn-save">Save Changes</button>
    </form>

</body>
</html>