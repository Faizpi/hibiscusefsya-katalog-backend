-- =====================================================
-- Hibiscus Efsya Katalog - Database Schema
-- Kosmetik & Perawatan Tubuh M.B.K Indonesia
-- Import via phpMyAdmin
-- =====================================================

-- =====================================================
-- Table: admins (skip jika sudah ada)
-- =====================================================
CREATE TABLE IF NOT EXISTS admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =====================================================
-- Table: categories
-- =====================================================
CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) NOT NULL UNIQUE,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert categories (ignore if exists)
INSERT IGNORE INTO categories (id, name, slug, description) VALUES
(1, 'Deodorant Roll On', 'deodorant-roll-on', 'Deodorant roll on M.B.K untuk menjaga kesegaran tubuh dan mencegah bau badan. Tersedia varian untuk wanita dan pria.'),
(2, 'P.O. Powder', 'po-powder', 'Bedak tabur M.B.K yang efektif menyerap keringat dan menghilangkan bau badan. Terbuat dari tawas, talc, dan parfum berkualitas.'),
(3, 'Bedak Biang Keringat', 'bedak-biang-keringat', 'Bedak khusus untuk mengatasi dan mencegah biang keringat. Cocok untuk segala usia.'),
(4, 'Body Mist', 'body-mist', 'Pewangi tubuh segar dari M.B.K untuk memberikan aroma menyenangkan sepanjang hari.'),
(5, 'Body Lotion', 'body-lotion', 'Lotion pelembab kulit dari M.B.K untuk menjaga kelembaban dan kesehatan kulit.');

-- =====================================================
-- Table: products
-- =====================================================
CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(200) NOT NULL,
    slug VARCHAR(200) NOT NULL UNIQUE,
    description TEXT,
    price DECIMAL(12, 2) NOT NULL DEFAULT 0,
    category_id INT,
    image VARCHAR(255),
    status ENUM('publish', 'draft') DEFAULT 'draft',
    featured TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert products (ignore if exists)
INSERT IGNORE INTO products (id, name, slug, description, price, category_id, status, featured) VALUES
(1, 'MBK Deodorant Roll On Pink (Women)', 'mbk-deodorant-roll-on-pink', 'Deodorant roll on khusus wanita dengan warna pink yang feminin. Tahan lama hingga 24 jam, halal, dan aman untuk kulit sensitif.', 15000, 1, 'publish', 1),
(2, 'MBK Deodorant Roll On Purple (Women)', 'mbk-deodorant-roll-on-purple', 'Deodorant roll on wanita varian purple dengan aroma yang lembut dan tahan lama.', 15000, 1, 'publish', 1),
(3, 'MBK Deodorant Roll On Black (Men)', 'mbk-deodorant-roll-on-black', 'Deodorant roll on khusus pria dengan desain maskulin warna hitam.', 15000, 1, 'publish', 1),
(4, 'MBK Deodorant Roll On Blue (Men)', 'mbk-deodorant-roll-on-blue', 'Deodorant roll on pria varian biru dengan kesegaran ekstra.', 15000, 1, 'publish', 0),
(5, 'MBK P.O. Powder Silver Sachet', 'mbk-po-powder-silver-sachet', 'Bedak tabur M.B.K dalam kemasan sachet praktis. Efektif menyerap keringat berlebih.', 35000, 2, 'publish', 1),
(6, 'MBK P.O. Powder Putih Tin', 'mbk-po-powder-putih-tin', 'Bedak tabur M.B.K kemasan tin/kaleng yang ekonomis. Halal MUI.', 14000, 2, 'publish', 1),
(7, 'MBK P.O. Powder Putih Sachet', 'mbk-po-powder-putih-sachet', 'Bedak tabur putih M.B.K sachet dengan khasiat halal MUI.', 26000, 2, 'publish', 0),
(8, 'MBK P.O. Powder Silver Tin Anti Bau', 'mbk-po-powder-silver-tin-anti-bau', 'Varian silver dalam kemasan tin dengan formula anti bau badan.', 16000, 2, 'publish', 0),
(9, 'MBK Bedak Biang Keringat Biru Botol', 'mbk-bedak-biang-keringat-biru-botol', 'Bedak biang keringat M.B.K varian biru dalam kemasan botol.', 9000, 3, 'publish', 0),
(10, 'MBK Bedak Biang Keringat Hijau Botol', 'mbk-bedak-biang-keringat-hijau-botol', 'Bedak biang keringat varian hijau dengan aroma menthol.', 9000, 3, 'publish', 0),
(11, 'MBK Bedak Biang Keringat Hijau Tin', 'mbk-bedak-biang-keringat-hijau-tin', 'Bedak biang keringat kemasan tin dengan formula menthol.', 9000, 3, 'publish', 0),
(12, 'MBK Bedak Biang Keringat Biru Tin', 'mbk-bedak-biang-keringat-biru-tin', 'Bedak biang keringat varian biru kemasan tin.', 9000, 3, 'publish', 0),
(13, 'MBK Body Mist Fresh', 'mbk-body-mist-fresh', 'Body mist M.B.K dengan aroma segar yang tahan lama.', 25000, 4, 'publish', 0),
(14, 'MBK Eleven Body Lotion', 'mbk-eleven-body-lotion', 'Body lotion dari lini Eleven M.B.K untuk melembabkan kulit.', 35000, 5, 'publish', 0);

-- =====================================================
-- Table: inspirations
-- =====================================================
CREATE TABLE IF NOT EXISTS inspirations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    slug VARCHAR(200) NOT NULL UNIQUE,
    content TEXT,
    image VARCHAR(255),
    status ENUM('publish', 'draft') DEFAULT 'draft',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT IGNORE INTO inspirations (id, title, slug, content, status) VALUES
(1, 'Tips Mengatasi Bau Badan', 'tips-mengatasi-bau-badan', 'Bau badan disebabkan oleh bakteri yang berkembang di area lembab tubuh. Gunakan deodorant secara teratur dan bedak tabur untuk menyerap keringat berlebih.', 'publish'),
(2, 'Manfaat Bedak Tabur untuk Tubuh', 'manfaat-bedak-tabur', 'Bedak tabur M.B.K membantu menjaga kulit tetap kering, menyerap keringat, dan memberikan aroma harum sepanjang hari.', 'publish'),
(3, 'Cara Memilih Deodorant yang Tepat', 'cara-memilih-deodorant', 'Pilih deodorant yang sesuai dengan jenis kulit Anda. M.B.K menyediakan varian untuk wanita dan pria dengan formula yang aman dan halal.', 'publish');

-- =====================================================
-- Table: settings (untuk pengaturan website)
-- =====================================================
CREATE TABLE IF NOT EXISTS settings (
    setting_key VARCHAR(100) PRIMARY KEY,
    setting_value TEXT,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Default settings
INSERT IGNORE INTO settings (setting_key, setting_value) VALUES
('site_name', 'Hibiscus Efsya'),
('site_tagline', 'Part of M.B.K Indonesia'),
('site_description', 'Produk kecantikan dan perawatan tubuh berkualitas dari M.B.K Indonesia'),
('hero_title', 'Hibiscus Efsya'),
('hero_subtitle', 'Kecantikan Alami untuk Kesehatan Tubuh'),
('hero_description', 'Temukan rangkaian produk perawatan tubuh berkualitas dari M.B.K Indonesia. Deodorant, bedak, body mist, dan body lotion untuk kesegaran dan kesehatan kulit Anda setiap hari.'),
('about_title', 'Tentang Hibiscus Efsya'),
('about_content', 'Hibiscus Efsya adalah brand produk perawatan tubuh dibawah naungan M.B.K Indonesia. Kami berkomitmen menghadirkan produk-produk berkualitas yang aman dan efektif untuk menjaga kesehatan dan kesegaran tubuh Anda sehari-hari.'),
('contact_address', 'Jakarta, Indonesia'),
('contact_email', 'info@hibiscusefsya.com'),
('contact_phone', '+62 812 3456 7890'),
('contact_whatsapp', '6281234567890'),
('social_instagram', 'https://instagram.com/hibiscusefsya'),
('social_facebook', 'https://facebook.com/hibiscusefsya'),
('social_shopee', 'https://shopee.co.id/hibiscusefsya'),
('social_tokopedia', 'https://tokopedia.com/hibiscusefsya');
