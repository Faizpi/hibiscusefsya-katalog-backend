<?php
/**
 * Debug script to check product images
 */
require_once __DIR__ . '/config/config.php';

echo "=== Product Images Debug ===\n\n";

// Check products
$products = db()->query("SELECT id, name, image FROM products LIMIT 10")->fetchAll();

echo "Products in database:\n";
foreach ($products as $p) {
    echo "ID: {$p['id']}, Name: {$p['name']}\n";
    echo "  Image field: " . ($p['image'] ?: '(empty)') . "\n";

    if ($p['image']) {
        $imagePath = UPLOAD_PATH . $p['image'];
        $imageExists = file_exists($imagePath);
        echo "  Full path: {$imagePath}\n";
        echo "  File exists: " . ($imageExists ? 'YES' : 'NO') . "\n";
        echo "  URL: " . UPLOAD_URL . $p['image'] . "\n";
    }
    echo "\n";
}

echo "\n=== Inspirations (Articles) Debug ===\n\n";

$articles = db()->query("SELECT id, title, slug, image FROM inspirations")->fetchAll();

echo "Articles in database:\n";
foreach ($articles as $a) {
    echo "ID: {$a['id']}, Title: {$a['title']}\n";
    echo "  Slug: {$a['slug']}\n";
    echo "  Image field: " . ($a['image'] ?: '(empty)') . "\n";

    if ($a['image']) {
        $imagePath = UPLOAD_PATH_ARTICLES . $a['image'];
        $imageExists = file_exists($imagePath);
        echo "  Full path: {$imagePath}\n";
        echo "  File exists: " . ($imageExists ? 'YES' : 'NO') . "\n";
        echo "  URL: " . UPLOAD_URL_ARTICLES . $a['image'] . "\n";
    }
    echo "\n";
}

echo "\n=== Path Constants ===\n";
echo "UPLOAD_PATH: " . UPLOAD_PATH . "\n";
echo "UPLOAD_URL: " . UPLOAD_URL . "\n";
echo "UPLOAD_PATH_ARTICLES: " . UPLOAD_PATH_ARTICLES . "\n";
echo "UPLOAD_URL_ARTICLES: " . UPLOAD_URL_ARTICLES . "\n";

echo "\n=== Directory Contents ===\n";

echo "\nProducts folder (" . UPLOAD_PATH . "):\n";
if (is_dir(UPLOAD_PATH)) {
    $files = scandir(UPLOAD_PATH);
    foreach ($files as $f) {
        if ($f !== '.' && $f !== '..') {
            echo "  - {$f}\n";
        }
    }
} else {
    echo "  Directory does not exist!\n";
}

echo "\nArticles folder (" . UPLOAD_PATH_ARTICLES . "):\n";
if (is_dir(UPLOAD_PATH_ARTICLES)) {
    $files = scandir(UPLOAD_PATH_ARTICLES);
    foreach ($files as $f) {
        if ($f !== '.' && $f !== '..') {
            echo "  - {$f}\n";
        }
    }
} else {
    echo "  Directory does not exist!\n";
}
