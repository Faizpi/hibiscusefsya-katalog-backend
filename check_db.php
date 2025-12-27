<?php
require_once __DIR__ . '/config/database.php';

echo "=== Database Tables ===\n";
$tables = db()->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
print_r($tables);

if (empty($tables)) {
    echo "\n=== Importing Schema ===\n";
    
    // Create tables
    $sql = file_get_contents(__DIR__ . '/database/import_phpmyadmin.sql');
    
    // Split by semicolon and execute each statement
    $statements = array_filter(array_map('trim', explode(';', $sql)));
    
    foreach ($statements as $stmt) {
        if (!empty($stmt) && strpos($stmt, '--') !== 0) {
            try {
                db()->exec($stmt);
                echo "OK: " . substr($stmt, 0, 50) . "...\n";
            } catch (PDOException $e) {
                echo "Error: " . $e->getMessage() . "\n";
            }
        }
    }
    echo "\nSchema imported!\n";
} else {
    echo "\nTables already exist.\n";
}
