<?php
require_once "../helpers/init.php";

// 1. Definiera dina kolumner
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

// 2. Kontrollera om formuläret har skickats via POST
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $insertColumns = [];
    $placeholders = [];
    $values = [];

    foreach ($columns as $dbCol => $label) {
        if (isset($_POST[$dbCol])) {
            $insertColumns[] = $dbCol;
            $placeholders[] = "?"; // Skapar ett frågetecken för varje kolumn
            
            // Trimma bort eventuella mellanslag och kolla om fältet är tomt
            $val = trim($_POST[$dbCol]);
            
            // Om fältet är tomt, skicka null - annars skicka värdet
            $values[] = ($val === "") ? null : $val;
        }
    }

    // 3. Bygg och kör SQL-frågan
    if (!empty($insertColumns)) {
        // Observera backticks runt kolumnnamnen och tabellnamnet "item"
        $sql = "INSERT INTO item (`" . implode("`, `", $insertColumns) . "`) 
                VALUES (" . implode(", ", $placeholders) . ")";
        
        try {
            // Använder din metod från database.php för att spara och hämta det nya ID:t
            $newId = $dbObject->insertAndGetID($sql, $values);
            $message = "Saved with ID: " . $newId;
        } catch (Exception $e) {
            $message = "Fel: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create Item</title>
    <style>
        .form-group { margin-bottom: 15px; display: flex; flex-direction: column; width: 300px; }
        .message { padding: 10px; background: #e0f0e0; margin-bottom: 20px; border-radius: 4px; }
    </style>
</head>
<body>

    <h1>Add new item to database (like a boss)</h1>

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

        <button type="submit">Save to database</button>
    </form>

</body>
</html>