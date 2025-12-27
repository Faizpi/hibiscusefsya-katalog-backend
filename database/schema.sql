-- =====================================================
-- Hibiscus Efsya Katalog - Database Schema
-- =====================================================

CREATE DATABASE IF NOT EXISTS u983003565_api;
USE u983003565_api;

-- =====================================================
-- Table: admins
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

-- Insert default categories
INSERT INTO categories (name, slug, description) VALUES
('Bouquet', 'bouquet', 'Rangkaian bunga dalam bentuk bouquet'),
('Vas Bunga', 'vas-bunga', 'Rangkaian bunga dengan vas'),
('Dekorasi', 'dekorasi', 'Dekorasi bunga untuk acara'),
('Hampers', 'hampers', 'Paket hampers dengan bunga');

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

-- Insert sample products
INSERT INTO products (name, slug, description, price, category_id, status, featured) VALUES
('Rose Elegance Bouquet', 'rose-elegance-bouquet', 'Rangkaian mawar merah premium dengan sentuhan baby breath yang elegan. Cocok untuk hadiah romantis.', 350000, 1, 'publish', 1),
('Sunflower Joy', 'sunflower-joy', 'Bouquet bunga matahari cerah yang membawa kebahagiaan. Perfect untuk menyemangati orang tersayang.', 275000, 1, 'publish', 1),
('Lily White Dream', 'lily-white-dream', 'Rangkaian lily putih murni dengan aroma harum. Simbol kesucian dan keanggunan.', 400000, 1, 'publish', 0),
('Classic Vas Arrangement', 'classic-vas-arrangement', 'Rangkaian bunga campuran dalam vas keramik premium. Dekorasi sempurna untuk ruangan.', 500000, 2, 'publish', 1),
('Wedding Decoration Set', 'wedding-decoration-set', 'Paket dekorasi bunga untuk pernikahan. Termasuk centerpiece dan dekorasi meja.', 2500000, 3, 'publish', 0),
('Flower Hampers Gift', 'flower-hampers-gift', 'Hampers eksklusif dengan rangkaian bunga dan cokelat premium.', 750000, 4, 'publish', 1);

-- =====================================================
-- Table: inspirations (untuk section inspirasi)
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

-- Insert sample inspirations
INSERT INTO inspirations (title, slug, content, status) VALUES
('Tips Merawat Bunga Potong', 'tips-merawat-bunga-potong', 'Bunga potong bisa bertahan lebih lama dengan perawatan yang tepat. Ganti air setiap hari dan potong batang secara diagonal.', 'publish'),
('Makna Warna Bunga', 'makna-warna-bunga', 'Setiap warna bunga memiliki makna tersendiri. Merah melambangkan cinta, putih kesucian, dan kuning persahabatan.', 'publish'),
('Inspirasi Dekorasi Rumah', 'inspirasi-dekorasi-rumah', 'Hadirkan kesegaran dengan rangkaian bunga di sudut ruangan favorit Anda.', 'publish');
