<?php
require_once 'config/database.php';

try {
    $sql = "SHOW COLUMNS FROM settings LIKE 'favicon'";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    
    if ($stmt->rowCount() == 0) {
        $sql = "ALTER TABLE settings ADD COLUMN favicon VARCHAR(255) DEFAULT NULL AFTER logo";
        $pdo->exec($sql);
        echo "Column 'favicon' added successfully to 'settings' table.";
    } else {
        echo "Column 'favicon' already exists in 'settings' table.";
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
