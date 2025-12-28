<?php
/**
 * Public API - Homepage Data
 * Hibiscus Efsya Katalog
 */
require_once __DIR__ . '/../config/config.php';

// Set CORS headers
setCorsHeaders();

// Only allow GET requests
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    jsonResponse(['error' => 'Method not allowed'], 405);
}

try {
    // Get featured products
    $featuredStmt = db()->query("
        SELECT p.id, p.name, p.slug, p.description, p.price, p.image,
               c.name as category_name, c.slug as category_slug
        FROM products p
        LEFT JOIN categories c ON p.category_id = c.id
        WHERE p.status = 'publish' AND p.featured = 1
        ORDER BY p.created_at DESC
        LIMIT 6
    ");
    $featured = $featuredStmt->fetchAll();
    
    foreach ($featured as &$product) {
        $product['image_url'] = $product['image'] ? UPLOAD_URL . $product['image'] : null;
        $product['price_formatted'] = formatRupiah($product['price']);
    }
    
    // Get latest products
    $latestStmt = db()->query("
        SELECT p.id, p.name, p.slug, p.description, p.price, p.image,
               c.name as category_name, c.slug as category_slug
        FROM products p
        LEFT JOIN categories c ON p.category_id = c.id
        WHERE p.status = 'publish'
        ORDER BY p.created_at DESC
        LIMIT 8
    ");
    $latest = $latestStmt->fetchAll();
    
    foreach ($latest as &$product) {
        $product['image_url'] = $product['image'] ? UPLOAD_URL . $product['image'] : null;
        $product['price_formatted'] = formatRupiah($product['price']);
    }
    
    // Get categories with product count
    $categoriesStmt = db()->query("
        SELECT c.id, c.name, c.slug, c.description,
               COUNT(p.id) as product_count
        FROM categories c
        LEFT JOIN products p ON c.id = p.category_id AND p.status = 'publish'
        GROUP BY c.id
        HAVING product_count > 0
        ORDER BY c.name
    ");
    $categories = $categoriesStmt->fetchAll();
    
    // Get inspirations
    $inspirationsStmt = db()->query("
        SELECT id, title, slug, content, image
        FROM inspirations
        WHERE status = 'publish'
        ORDER BY created_at DESC
        LIMIT 3
    ");
    $inspirations = $inspirationsStmt->fetchAll();
    
    // Stats
    $totalProducts = db()->query("SELECT COUNT(*) FROM products WHERE status = 'publish'")->fetchColumn();
    
    // Get settings for hero, about, contact
    $settingsStmt = db()->query("SELECT setting_key, setting_value FROM settings");
    $settingsRaw = $settingsStmt->fetchAll();
    $settings = [];
    foreach ($settingsRaw as $row) {
        $settings[$row['setting_key']] = $row['setting_value'];
    }
    
    // Get hero images from settings or use featured product images
    $heroImages = [];
    if (!empty($settings['hero_images'])) {
        // Hero images stored as JSON array in settings
        $heroImages = json_decode($settings['hero_images'], true) ?: [];
    }
    
    // If no hero images set, use featured product images as fallback
    if (empty($heroImages) && !empty($featured)) {
        foreach ($featured as $prod) {
            if (!empty($prod['image_url'])) {
                $heroImages[] = $prod['image_url'];
            }
        }
    }
    
    jsonResponse([
        'data' => [
            'featured_products' => $featured,
            'latest_products' => $latest,
            'categories' => $categories,
            'inspirations' => $inspirations,
            'stats' => [
                'total_products' => (int)$totalProducts,
                'total_categories' => count($categories)
            ],
            'settings' => $settings,
            'hero_images' => $heroImages
        ]
    ]);
} catch (PDOException $e) {
    jsonResponse(['error' => 'Database error'], 500);
}
