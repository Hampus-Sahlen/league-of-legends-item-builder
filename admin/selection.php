<?php
require_once "../helpers/init.php";

$items = $dbObject->query("SELECT ID, name, cost, image FROM item ORDER BY name ASC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>League of Legends Items</title>
    <style>
        body { font-family: sans-serif; background: #010a13; color: #f0e6d2; padding: 40px; }
        .item-card { border: 1px solid #c89b3c; padding: 15px; margin: 10px; display: inline-block; width: 200px; text-align: center; background: #1e2328; }
        .item-image { width: 64px; height: 64px; display: block; margin: 0 auto 10px; border: 1px solid #5b5a56; }
        .btn-edit { color: #00bcff; text-decoration: none; font-size: 0.8em; border: 1px solid #00bcff; padding: 2px 5px; }
        .btn-create { background: #c89b3c; color: black; padding: 10px 20px; text-decoration: none; font-weight: bold; }
    </style>
</head>
<body>

    <h1>Item Library</h1>
    <a href="create_item.php" class="btn-create">ADD NEW ITEM</a>
    <br><br>

    <div class="container">
        <?php foreach ($items as $item): ?>
            <div class="item-card">
                <?php if ($item['image']): ?>
                    <img src="../images/<?php echo es($item['image']); ?>" class="item-image">
                <?php endif; ?>
                <strong><?php echo es($item['name']); ?></strong><br>
                <small><?php echo es($item['cost']); ?> gold</small><br><br>
                <a href="edit_item.php?id=<?php echo $item['ID']; ?>" class="btn-edit">EDIT</a>
            </div>
        <?php endforeach; ?>
    </div>

</body>
</html>