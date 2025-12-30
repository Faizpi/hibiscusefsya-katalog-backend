<?php
/**
 * API Settings Endpoint
 * Hibiscus Efsya - M.B.K Indonesia
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once __DIR__ . '/../config/database.php';

// Create settings table if not exists
try {
    $db->exec("CREATE TABLE IF NOT EXISTS settings (
        setting_key VARCHAR(100) PRIMARY KEY,
        setting_value TEXT,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )");
} catch (PDOException $e) {
    // Table might already exist
}

// Get all settings
try {
    $stmt = $db->query("SELECT setting_key, setting_value FROM settings");
    $settings_raw = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

    // Organize settings into categories
    $settings = [
        'site' => [
            'name' => $settings_raw['site_name'] ?? 'Hibiscus Efsya',
            'tagline' => $settings_raw['site_tagline'] ?? 'Part of M.B.K Indonesia',
            'description' => $settings_raw['site_description'] ?? 'Produk kecantikan dan perawatan tubuh berkualitas dari M.B.K Indonesia'
        ],
        'hero' => [
            'title' => $settings_raw['hero_title'] ?? 'Hibiscus Efsya',
            'subtitle' => $settings_raw['hero_subtitle'] ?? 'Kecantikan Alami untuk Kesehatan Tubuh',
            'description' => $settings_raw['hero_description'] ?? 'Temukan rangkaian produk perawatan tubuh berkualitas dari M.B.K Indonesia. Deodorant, bedak, body mist, dan body lotion untuk kesegaran dan kesehatan kulit Anda setiap hari.'
        ],
        'about' => [
            'title' => $settings_raw['about_title'] ?? 'Tentang Hibiscus Efsya',
            'content' => $settings_raw['about_content'] ?? 'Hibiscus Efsya adalah brand produk perawatan tubuh dibawah naungan M.B.K Indonesia. Kami berkomitmen menghadirkan produk-produk berkualitas yang aman dan efektif untuk menjaga kesehatan dan kesegaran tubuh Anda sehari-hari.

Produk kami meliputi:
• Deodorant Roll On - Perlindungan sepanjang hari dari bau badan
• P.O. Powder - Bedak halus untuk kesegaran kulit  
• Bedak Biang Keringat - Mengatasi masalah keringat berlebih
• Body Mist - Aroma segar yang tahan lama
• Body Lotion - Melembabkan dan menutrisi kulit'
        ],
        'contact' => [
            'address' => $settings_raw['contact_address'] ?? 'Jakarta, Indonesia',
            'email' => $settings_raw['contact_email'] ?? 'info@hibiscusefsya.com',
            'phone' => $settings_raw['contact_phone'] ?? '+62 812 3456 7890',
            'whatsapp' => $settings_raw['contact_whatsapp'] ?? '6281234567890'
        ],
        'social' => [
            'instagram' => $settings_raw['social_instagram'] ?? 'https://instagram.com/hibiscusefsya',
            'facebook' => $settings_raw['social_facebook'] ?? 'https://facebook.com/hibiscusefsya',
            'shopee' => $settings_raw['social_shopee'] ?? 'https://shopee.co.id/hibiscusefsya',
            'tokopedia' => $settings_raw['social_tokopedia'] ?? 'https://tokopedia.com/hibiscusefsya'
        ]
    ];

    echo json_encode([
        'success' => true,
        'data' => $settings
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error fetching settings'
    ]);
}
