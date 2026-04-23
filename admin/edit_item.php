<?php
require_once "init.php";

// 1. Definiera dina kolumner (Korrigerad movement-speed-percent)
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
$item = null;

// 2. Hämta ID från URL:en (t.ex. edit_item.php?id=5)
$id = $_GET['id'] ?? null;

if (!$id) {
    die("Ett ID krävs för att kunna redigera ett objekt.");
}

// 3. Hantera POST-förfrågan (när formuläret sparas)
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $updateColumns = [];
    $values = [];

    foreach ($columns as $dbCol => $label) {
        if (isset($_POST[$dbCol])) {
            $updateColumns[] = "`$dbCol` = ?";
            $values[] = $_POST[$dbCol];
        }
    }

    if (!empty($updateColumns)) {
        // Lägg till ID sist i values-arrayen för WHERE-satsen
        $values[] = $id;

        // Använder rätt tabellnamn "item" istället för "items"
        $sql = "UPDATE item SET " . implode(", ", $updateColumns) . " WHERE ID = ?";
        
        try {
            // Använder write() från din databas-klass för att köra UPDATE
            $dbObject->write($sql, $values); 
            $message = "Lyckades! Objektet uppdaterades.";
        } catch (Exception $e) {
            $message = "Fel vid uppdatering: " . $e->getMessage();
        }
    }
}

// 4. Hämta befintlig data för att förifylla formuläret
try {
    $sql = "SELECT * FROM item WHERE ID = ?";
    
    // Använder query() från din databasklass. 
    // Eftersom query() returnerar en array av rader, sparar vi den i $result.
    $result = $dbObject->query($sql, [$id]); 
    
    if (!empty($result)) {
        // Plocka ut den första raden (index 0) ur arrayen
        $item = $result[0];
    } else {
        die("Kunde inte hitta något objekt med ID: " . es($id));
    }
} catch (Exception $e) {
    die("Fel vid hämtning av data: " . $e->getMessage());
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Redigera objekt</title>
    <style>
        .form-group { margin-bottom: 15px; display: flex; flex-direction: column; width: 300px; }
        .message { padding: 10px; background: #e0f0e0; margin-bottom: 20px; border-radius: 4px; }
    </style>
</head>
<body>

    <h1>Redigera objekt #<?php echo es($id); ?></h1>

    <?php if ($message): ?>
        <div class="message"><?php echo es($message); ?></div>
    <?php endif; ?>

    <form method="POST" action="edit_item.php?id=<?php echo es($id); ?>">
        <?php foreach ($columns as $dbCol => $label): ?>
            <div class="form-group">
                <label for="<?php echo es($dbCol); ?>"><?php echo es($label); ?></label>
                <input type="text" 
                       name="<?php echo es($dbCol); ?>" 
                       id="<?php echo es($dbCol); ?>" 
                       value="<?php echo isset($item[$dbCol]) ? es($item[$dbCol]) : ''; ?>">
            </div>
        <?php endforeach; ?>

        <button type="submit">Uppdatera i databasen</button>
    </form>

</body>
</html>