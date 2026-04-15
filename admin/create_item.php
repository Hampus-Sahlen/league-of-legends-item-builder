<?php
require_once "init.php";

// 1. Definiera dina kolumner (exkludera din auto_increment kolumn)
// Genom att lista dem här kan vi loopa ut både formulär och SQL-hantering
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
    "move-speed-percent" => "move speed percent",
    "armor-pen-percent" => "armor pen percent",
    "magic-pen-percent" => "magic pen percent",
    // ... fyll på med resterande kolumner upp till 29 stycken
];

$message = "";

// 2. Hantera POST-förfrågan
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $insertColumns = [];
    $placeholders = [];
    $values = [];

    foreach ($columns as $dbCol => $label) {
        if (isset($_POST[$dbCol])) {
            $insertColumns[] = $dbCol;
            $placeholders[] = "?";
            $values[] = $_POST[$dbCol];
        }
    }

    if (!empty($insertColumns)) {
        $sql = "INSERT INTO items (" . implode(", ", $insertColumns) . ") 
                VALUES (" . implode(", ", $placeholders) . ")";
        
        try {
            // Använder din metod från database.php
            $newId = $dbObject->insertAndGetID($sql, $values);
            $message = "Lyckades! Skapade rad med ID: " . $newId;
        } catch (Exception $e) {
            $message = "Fel: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="sv">
<head>
    <meta charset="UTF-8">
    <title>Skapa nytt objekt</title>
    <style>
        .form-group { margin-bottom: 15px; display: flex; flex-direction: column; width: 300px; }
        .message { padding: 10px; background: #e0f0e0; margin-bottom: 20px; border-radius: 4px; }
    </style>
</head>
<body>

    <h1>Lägg till i databasen</h1>

    <?php if ($message): ?>
        <div class="message"><?php echo es($message); ?></div>
    <?php endif; ?>

    <form method="POST">
        <?php foreach ($columns as $dbCol => $label): ?>
            <div class="form-group">
                <label for="<?php echo es($dbCol); ?>"><?php echo es($label); ?></label>
                <input type="text" name="<?php echo es($dbCol); ?>" id="<?php echo es($dbCol); ?>">
            </div>
        <?php endforeach; ?>

        <button type="submit">Spara i databasen</button>
    </form>

</body>
</html>