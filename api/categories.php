<?php
/**
 * Public API - Categories
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
    // Get all categories with product count
    $stmt = db()->query("
        SELECT c.id, c.name, c.slug, c.description,
               COUNT(p.id) as product_count
        FROM categories c
        LEFT JOIN products p ON c.id = p.category_id AND p.status = 'publish'
        GROUP BY c.id
        HAVING product_count > 0
        ORDER BY c.name
    ");
    $categories = $stmt->fetchAll();

    jsonResponse(['data' => $categories]);
} catch (PDOException $e) {
    jsonResponse(['error' => 'Database error'], 500);
}
