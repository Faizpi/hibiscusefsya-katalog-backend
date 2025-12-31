-- =====================================================
-- Hibiscus Efsya Katalog - Database Schema
-- Kosmetik & Perawatan Tubuh M.B.K Indonesia
-- =====================================================

CREATE DATABASE IF NOT EXISTS u983003565_api;
USE u983003565_api;

-- =====================================================
-- Table: admins
-- =====================================================
DROP TABLE IF EXISTS inspirations;
DROP TABLE IF EXISTS products;
DROP TABLE IF EXISTS categories;
DROP TABLE IF EXISTS admins;

CREATE TABLE IF NOT EXISTS admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert default admin (password: admin123)
INSERT INTO admins (username, email, password, full_name) VALUES
('admin', 'admin@hibiscusefsya.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrator');

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

-- Insert categories for M.B.K Products
INSERT INTO categories (name, slug, description) VALUES
('Deodorant Roll On', 'deodorant-roll-on', 'Deodorant roll on M.B.K untuk menjaga kesegaran tubuh dan mencegah bau badan. Tersedia varian untuk wanita dan pria.'),
('P.O. Powder', 'po-powder', 'Bedak tabur M.B.K yang efektif menyerap keringat dan menghilangkan bau badan. Terbuat dari tawas, talc, dan parfum berkualitas.'),
('Bedak Biang Keringat', 'bedak-biang-keringat', 'Bedak khusus untuk mengatasi dan mencegah biang keringat. Cocok untuk segala usia.'),
('Body Mist', 'body-mist', 'Pewangi tubuh segar dari M.B.K untuk memberikan aroma menyenangkan sepanjang hari.'),
('Body Lotion', 'body-lotion', 'Lotion pelembab kulit dari M.B.K untuk menjaga kelembaban dan kesehatan kulit.');

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
    shopee_link VARCHAR(500),
    tokopedia_link VARCHAR(500),
    status ENUM('publish', 'draft') DEFAULT 'draft',
    featured TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert M.B.K Products - Deodorant Roll On
INSERT INTO products (name, slug, description, price, category_id, status, featured) VALUES
('MBK Deodorant Roll On Pink (Women)', 'mbk-deodorant-roll-on-pink', 'Deodorant roll on khusus wanita dengan warna pink yang feminin. Tahan lama hingga 24 jam, halal, dan aman untuk kulit sensitif. Efektif mencegah bau badan dan menjaga ketiak tetap kering.', 15000, 1, 'publish', 1),
('MBK Deodorant Roll On Purple (Women)', 'mbk-deodorant-roll-on-purple', 'Deodorant roll on wanita varian purple dengan aroma yang lembut dan tahan lama. Formulasi halal yang aman digunakan sehari-hari.', 15000, 1, 'publish', 1),
('MBK Deodorant Roll On Black (Men)', 'mbk-deodorant-roll-on-black', 'Deodorant roll on khusus pria dengan desain maskulin warna hitam. Perlindungan maksimal dari bau badan dan keringat berlebih.', 15000, 1, 'publish', 1),
('MBK Deodorant Roll On Blue (Men)', 'mbk-deodorant-roll-on-blue', 'Deodorant roll on pria varian biru dengan kesegaran ekstra. Cocok untuk pria aktif yang membutuhkan perlindungan sepanjang hari.', 15000, 1, 'publish', 0);

-- Insert M.B.K Products - P.O. Powder
INSERT INTO products (name, slug, description, price, category_id, status, featured) VALUES
('MBK P.O. Powder Silver Sachet', 'mbk-po-powder-silver-sachet', 'Bedak tabur M.B.K dalam kemasan sachet praktis. Efektif menyerap keringat berlebih dan menghilangkan bau badan tidak sedap. Terbuat dari tawas, talc, dan parfum berkualitas.', 35000, 2, 'publish', 1),
('MBK P.O. Powder Putih Tin', 'mbk-po-powder-putih-tin', 'Bedak tabur M.B.K kemasan tin/kaleng yang ekonomis. Halal MUI dan aman digunakan untuk seluruh anggota keluarga. Menjaga kulit tetap kering dan nyaman.', 14000, 2, 'publish', 1),
('MBK P.O. Powder Putih Sachet', 'mbk-po-powder-putih-sachet', 'Bedak tabur putih M.B.K sachet dengan khasiat halal MUI. Memberikan aroma menyenangkan dan menjaga tubuh tetap segar sepanjang hari.', 26000, 2, 'publish', 0),
('MBK P.O. Powder Silver Tin Anti Bau', 'mbk-po-powder-silver-tin-anti-bau', 'Varian silver dalam kemasan tin dengan formula anti bau badan yang lebih kuat. Cocok untuk aktivitas berat dan cuaca panas.', 16000, 2, 'publish', 0);

-- Insert M.B.K Products - Bedak Biang Keringat
INSERT INTO products (name, slug, description, price, category_id, status, featured) VALUES
('MBK Bedak Biang Keringat Biru Botol', 'mbk-bedak-biang-keringat-biru-botol', 'Bedak biang keringat M.B.K varian biru dalam kemasan botol. Efektif mengatasi dan mencegah biang keringat, memberikan sensasi dingin dan nyaman.', 9000, 3, 'publish', 0),
('MBK Bedak Biang Keringat Hijau Botol', 'mbk-bedak-biang-keringat-hijau-botol', 'Bedak biang keringat varian hijau dengan aroma menthol yang menyegarkan. Cocok untuk bayi dan dewasa.', 9000, 3, 'publish', 0),
('MBK Bedak Biang Keringat Hijau Tin', 'mbk-bedak-biang-keringat-hijau-tin', 'Bedak biang keringat kemasan tin dengan formula menthol. Praktis dibawa bepergian.', 9000, 3, 'publish', 0),
('MBK Bedak Biang Keringat Biru Tin', 'mbk-bedak-biang-keringat-biru-tin', 'Bedak biang keringat varian biru kemasan tin. Cepat menyerap dan memberikan efek dingin.', 9000, 3, 'publish', 0);

-- Insert M.B.K Products - Body Mist & Lotion
INSERT INTO products (name, slug, description, price, category_id, status, featured) VALUES
('MBK Body Mist Fresh', 'mbk-body-mist-fresh', 'Body mist M.B.K dengan aroma segar yang tahan lama. Semprot kapan saja untuk kesegaran instan.', 25000, 4, 'publish', 0),
('MBK Eleven Body Lotion', 'mbk-eleven-body-lotion', 'Body lotion dari lini Eleven M.B.K untuk melembabkan dan menutrisi kulit. Tekstur ringan dan cepat menyerap.', 35000, 5, 'publish', 0);

-- =====================================================
-- Table: inspirations (untuk section tips & artikel)
-- =====================================================
CREATE TABLE IF NOT EXISTS inspirations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    slug VARCHAR(200) NOT NULL UNIQUE,
    excerpt TEXT,
    content TEXT,
    image VARCHAR(255),
    status ENUM('publish', 'draft') DEFAULT 'draft',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert tips & inspirations
INSERT INTO inspirations (title, slug, excerpt, content, status) VALUES
('Tips Mengatasi Bau Badan', 'tips-mengatasi-bau-badan', 'Tips ampuh mengatasi bau badan agar tetap percaya diri sepanjang hari.', 'Bau badan disebabkan oleh bakteri yang berkembang di area lembab tubuh. Gunakan deodorant secara teratur dan bedak tabur untuk menyerap keringat berlebih.', 'publish'),
('Manfaat Bedak Tabur untuk Tubuh', 'manfaat-bedak-tabur', 'Kenali berbagai manfaat bedak tabur untuk menjaga kesehatan kulit Anda.', 'Bedak tabur M.B.K membantu menjaga kulit tetap kering, menyerap keringat, dan memberikan aroma harum sepanjang hari. Cocok digunakan setelah mandi.', 'publish'),
('Cara Memilih Deodorant yang Tepat', 'cara-memilih-deodorant', 'Panduan lengkap memilih deodorant sesuai kebutuhan dan jenis kulit.', 'Pilih deodorant yang sesuai dengan jenis kulit Anda. M.B.K menyediakan varian untuk wanita dan pria dengan formula yang aman dan halal.', 'publish');

