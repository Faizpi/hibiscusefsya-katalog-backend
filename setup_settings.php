<?php
require_once __DIR__ . '/config/database.php';

echo "=== Creating Settings Table ===\n";

try {
    // Create settings table
    db()->exec("CREATE TABLE IF NOT EXISTS settings (
        setting_key VARCHAR(100) PRIMARY KEY,
        setting_value TEXT,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    
    echo "Settings table created!\n";
    
    // Insert default settings
    $defaults = [
        'site_name' => 'Hibiscus Efsya',
        'site_tagline' => 'Part of M.B.K Indonesia',
        'site_description' => 'Produk kecantikan dan perawatan tubuh berkualitas dari M.B.K Indonesia',
        'hero_title' => 'Hibiscus Efsya',
        'hero_subtitle' => 'Kecantikan Alami untuk Kesehatan Tubuh',
        'hero_description' => 'Temukan rangkaian produk perawatan tubuh berkualitas dari M.B.K Indonesia.',
        'about_title' => 'Tentang Hibiscus Efsya',
        'about_content' => 'Hibiscus Efsya adalah brand produk perawatan tubuh dibawah naungan M.B.K Indonesia.',
        'contact_address' => 'Jakarta, Indonesia',
        'contact_email' => 'info@hibiscusefsya.com',
        'contact_phone' => '+62 812 3456 7890',
        'contact_whatsapp' => '6281234567890',
        'social_instagram' => 'https://instagram.com/hibiscusefsya',
        'social_facebook' => 'https://facebook.com/hibiscusefsya',
        'social_shopee' => 'https://shopee.co.id/hibiscusefsya',
        'social_tokopedia' => 'https://tokopedia.com/hibiscusefsya'
    ];
    
    $stmt = db()->prepare("INSERT IGNORE INTO settings (setting_key, setting_value) VALUES (?, ?)");
    foreach ($defaults as $key => $value) {
        $stmt->execute([$key, $value]);
    }
    
    echo "Default settings inserted!\n";
    echo "\nDone! You can now access the admin panel.\n";
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
