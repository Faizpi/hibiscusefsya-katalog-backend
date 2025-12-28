<?php
require_once __DIR__ . '/config/config.php';

$uploadDir = __DIR__ . '/uploads/hero/';
echo "Upload Dir: " . $uploadDir . "<br>";
echo "Exists: " . (file_exists($uploadDir) ? "YES" : "NO") . "<br>";
echo "Writable: " . (is_writable($uploadDir) ? "YES" : "NO") . "<br>";
echo "Realpath: " . realpath($uploadDir) . "<br>";

// Test write
$testFile = $uploadDir . 'test_' . time() . '.txt';
$result = file_put_contents($testFile, 'test');
echo "Write test: " . ($result !== false ? "SUCCESS" : "FAILED") . "<br>";

if ($result !== false) {
    unlink($testFile);
    echo "File deleted<br>";
}
