<?php
require_once __DIR__ . '/config/config.php';

// Reset hero_images
$db = db();
$stmt = $db->prepare("UPDATE settings SET setting_value = '[]' WHERE setting_key = 'hero_images'");
$stmt->execute();

echo "hero_images reset to empty array. Now go to /settings and upload new images.";
