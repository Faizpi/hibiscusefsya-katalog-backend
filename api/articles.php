<?php
/**
 * Public API - Articles (Inspirations)
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
$page = max(1, (int) ($_GET['page'] ?? 1));
$limit = min(50, max(1, (int) ($_GET['limit'] ?? 10)));
$offset = ($page - 1) * $limit;

try {
    switch ($action) {
        case 'detail':
            // Get single article by ID or slug
            if ($id) {
                $stmt = db()->prepare("
                    SELECT * FROM inspirations
                    WHERE id = ? AND status = 'publish'
                ");
                $stmt->execute([$id]);
            } elseif ($slug) {
                $stmt = db()->prepare("
                    SELECT * FROM inspirations
                    WHERE slug = ? AND status = 'publish'
                ");
                $stmt->execute([$slug]);
            } else {
                jsonResponse(['error' => 'ID or slug required'], 400);
            }

            $article = $stmt->fetch();

            if (!$article) {
                jsonResponse(['error' => 'Article not found'], 404);
            }

            // Add image URL
            $article['image_url'] = $article['image']
                ? UPLOAD_URL_ARTICLES . $article['image']
                : null;

            // Get related articles
            $relatedStmt = db()->prepare("
                SELECT id, title, slug, excerpt, content, image
                FROM inspirations
                WHERE id != ? AND status = 'publish'
                ORDER BY created_at DESC
                LIMIT 3
            ");
            $relatedStmt->execute([$article['id']]);
            $related = $relatedStmt->fetchAll();

            foreach ($related as &$item) {
                $item['image_url'] = $item['image']
                    ? UPLOAD_URL_ARTICLES . $item['image']
                    : null;
                // Use excerpt for preview if available
                $item['preview_text'] = !empty($item['excerpt'])
                    ? $item['excerpt']
                    : mb_substr(strip_tags($item['content']), 0, 100) . '...';
            }

            $article['related_articles'] = $related;

            jsonResponse(['data' => $article]);
            break;

        case 'list':
        default:
            // Get total count
            $countStmt = db()->query("SELECT COUNT(*) FROM inspirations WHERE status = 'publish'");
            $total = $countStmt->fetchColumn();

            // Get articles
            $stmt = db()->prepare("
                SELECT id, title, slug, excerpt, content, image, created_at
                FROM inspirations
                WHERE status = 'publish'
                ORDER BY created_at DESC
                LIMIT ? OFFSET ?
            ");
            $stmt->execute([$limit, $offset]);
            $articles = $stmt->fetchAll();

            // Add image URLs and truncate content
            foreach ($articles as &$article) {
                $article['image_url'] = $article['image']
                    ? UPLOAD_URL_ARTICLES . $article['image']
                    : null;
                // Use excerpt for preview if available
                $article['preview_text'] = !empty($article['excerpt'])
                    ? $article['excerpt']
                    : mb_substr(strip_tags($article['content']), 0, 150) . '...';
            }

            $totalPages = ceil($total / $limit);

            jsonResponse([
                'data' => $articles,
                'meta' => [
                    'total' => (int) $total,
                    'page' => $page,
                    'limit' => $limit,
                    'total_pages' => $totalPages,
                    'has_prev' => $page > 1,
                    'has_next' => $page < $totalPages
                ]
            ]);
            break;
    }
} catch (PDOException $e) {
    jsonResponse(['error' => 'Database error'], 500);
}
