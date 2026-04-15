<?php
// ==========================================
// 1. DINA DATABASINSTÄLLNINGAR (Ändra här)
// ==========================================
$host       = 'localhost';
$dbname     = 'league_of_legends';
$user       = 'root';
$pass       = ''; 
$table_name = 'item';

// ==========================================
// 2. DINA KOLUMNER (Lägg till alla 30 här)
// ==========================================
// Skriv in exakt vad kolumnerna heter i databasen.
$columns = [
    'kolumn1', 
    'kolumn2', 
    'kolumn3',
    'kolumn4',
    // ... fortsätt listan upp till 30
];

$message = '';

// ==========================================
// 3. LOGIK FÖR ATT SPARA I DATABASEN
// ==========================================
try {
    // Skapa anslutning till databasen
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Om formuläret har skickats (Submit)
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        
        // Bygg SQL-frågan dynamiskt baserat på arrayen ovan
        // Resultat blir typ: INSERT INTO tabell (kolumn1, kolumn2) VALUES (:kolumn1, :kolumn2)
        $insert_columns = implode(', ', $columns);
        $placeholders   = implode(', ', array_map(function($col) { return ':' . $col; }, $columns));
        
        $sql = "INSERT INTO $table_name ($insert_columns) VALUES ($placeholders)";
        $stmt = $pdo->prepare($sql);

        // Hämta in datan från formuläret och koppla ihop med rätt kolumn
        $data = [];
        foreach ($columns as $col) {
            // Om fältet lämnas tomt sätter vi det till NULL (eller tom sträng om din databas kräver det)
            $data[':' . $col] = !empty($_POST[$col]) ? $_POST[$col] : null;
        }

        // Kör SQL-frågan och spara datan
        $stmt->execute($data);
        $message = "Datan har sparats framgångsrikt!";
    }
} catch(PDOException $e) {
    // Fångar upp eventuella databasfel
    $message = "Ett fel uppstod: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="sv">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lägg till data</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; background-color: #f4f4f9; }
        .container { max-width: 600px; background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .form-group { margin-bottom: 15px; }
        label { display: block; font-weight: bold; margin-bottom: 5px; text-transform: capitalize; }
        input[type="text"] { width: 100%; padding: 8px; box-sizing: border-box; border: 1px solid #ccc; border-radius: 4px; }
        button { padding: 10px 15px; background-color: #28a745; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 16px; }
        button:hover { background-color: #218838; }
        .message { padding: 10px; margin-bottom: 20px; border-radius: 4px; font-weight: bold; }
        .success { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .error { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
    </style>
</head>
<body>

<div class="container">
    <h2>Lägg till ny post</h2>

    <?php if ($message): ?>
        <div class="message <?php echo strpos($message, 'fel') !== false ? 'error' : 'success'; ?>">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>

    <form method="post" action="">
        <?php foreach ($columns as $col): ?>
            <div class="form-group">
                <label for="<?php echo htmlspecialchars($col); ?>">
                    <?php echo htmlspecialchars($col); ?>:
                </label>
                <input type="text" name="<?php echo htmlspecialchars($col); ?>" id="<?php echo htmlspecialchars($col); ?>">
            </div>
        <?php endforeach; ?>

        <button type="submit">Spara till databasen</button>
    </form>
</div>

</body>
</html>