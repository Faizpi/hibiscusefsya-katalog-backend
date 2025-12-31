<?php
/**
 * Public API - Products
 * Hibiscus Efsya Katalog
 */
require_once __DIR__ . '/../config/config.php';

// Set CORS headers
setCorsHeaders();

// Only allow GET requests
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    jsonResponse(['error' => 'Method not allowed'], 405);
}

// Get parameters
$action = $_GET['action'] ?? 'list';
$id = $_GET['id'] ?? null;
$slug = $_GET['slug'] ?? null;
$category = $_GET['category'] ?? null;
$featured = isset($_GET['featured']) ? true : false;
$search = $_GET['search'] ?? null;
$page = max(1, (int) ($_GET['page'] ?? 1));
$limit = min(50, max(1, (int) ($_GET['limit'] ?? 12)));
$offset = ($page - 1) * $limit;

try {
    switch ($action) {
        case 'detail':
            // Get single product by ID or slug
            if ($id) {
                $stmt = db()->prepare("
                    SELECT p.*, c.name as category_name, c.slug as category_slug
                    FROM products p
                    LEFT JOIN categories c ON p.category_id = c.id
                    WHERE p.id = ? AND p.status = 'publish'
                ");
                $stmt->execute([$id]);
            } elseif ($slug) {
                $stmt = db()->prepare("
                    SELECT p.*, c.name as category_name, c.slug as category_slug
                    FROM products p
                    LEFT JOIN categories c ON p.category_id = c.id
                    WHERE p.slug = ? AND p.status = 'publish'
                ");
                $stmt->execute([$slug]);
            } else {
                jsonResponse(['error' => 'ID or slug required'], 400);
            }

            $product = $stmt->fetch();

            if (!$product) {
                jsonResponse(['error' => 'Product not found'], 404);
            }

            // Add image URL
            $product['image_url'] = $product['image'] ? UPLOAD_URL . $product['image'] : null;
            $product['price_formatted'] = formatRupiah($product['price']);

            // Get related products
            $relatedStmt = db()->prepare("
                SELECT id, name, slug, price, image, shopee_link, tokopedia_link
                FROM products
                WHERE category_id = ? AND id != ? AND status = 'publish'
                ORDER BY RAND()
                LIMIT 4
            ");
            $relatedStmt->execute([$product['category_id'], $product['id']]);
            $related = $relatedStmt->fetchAll();

            foreach ($related as &$item) {
                $item['image_url'] = $item['image'] ? UPLOAD_URL . $item['image'] : null;
                $item['price_formatted'] = formatRupiah($item['price']);
            }

            $product['related_products'] = $related;

            jsonResponse(['data' => $product]);
            break;

        case 'list':
        default:
            // Build query
            $where = ["p.status = 'publish'"];
            $params = [];

            if ($category) {
                $where[] = "c.slug = ?";
                $params[] = $category;
            }

            if ($featured) {
                $where[] = "p.featured = 1";
            }

            if ($search) {
                $where[] = "(p.name LIKE ? OR p.description LIKE ?)";
                $params[] = "%$search%";
                $params[] = "%$search%";
            }

            $whereClause = implode(' AND ', $where);

            // Get total count
            $countStmt = db()->prepare("
                SELECT COUNT(*) 
                FROM products p
                LEFT JOIN categories c ON p.category_id = c.id
                WHERE $whereClause
            ");
            $countStmt->execute($params);
            $total = $countStmt->fetchColumn();

            // Get products
            $params[] = $limit;
            $params[] = $offset;

            $stmt = db()->prepare("
                SELECT p.id, p.name, p.slug, p.description, p.price, p.image, 
                       p.shopee_link, p.tokopedia_link,
                       p.featured, p.created_at,
                       c.name as category_name, c.slug as category_slug
                FROM products p
                LEFT JOIN categories c ON p.category_id = c.id
                WHERE $whereClause
                ORDER BY p.featured DESC, p.created_at DESC
                LIMIT ? OFFSET ?
            ");
            $stmt->execute($params);
            $products = $stmt->fetchAll();

            // Add image URLs and formatted prices
            foreach ($products as &$product) {
                $product['image_url'] = $product['image'] ? UPLOAD_URL . $product['image'] : null;
                $product['price_formatted'] = formatRupiah($product['price']);
            }

            $totalPages = ceil($total / $limit);

            jsonResponse([
                'data' => $products,
                'meta' => [
                    'total' => (int) $total,
                    'page' => $page,
                    'limit' => $limit,
                    'total_pages' => $totalPages,
                    'has_next' => $page < $totalPages,
                    'has_prev' => $page > 1
                ]
            ]);
            break;
    }
} catch (PDOException $e) {
    jsonResponse(['error' => 'Database error'], 500);
}
