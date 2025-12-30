<?php
/**
 * Database Configuration
 * Hibiscus Efsya Katalog - Backend
 */

define('DB_HOST', 'localhost');
define('DB_NAME', 'u983003565_api');
define('DB_USER', 'u983003565_api');
define('DB_PASS', 'Giyats123');
define('DB_CHARSET', 'utf8mb4');

// API URL
define('API_URL', 'https://api.hibiscusefsya.com');
define('FRONTEND_URL', 'https://katalog.hibiscusefsya.com');

// Upload Path
define('UPLOAD_PATH', __DIR__ . '/../uploads/products/');
define('UPLOAD_URL', API_URL . '/uploads/products/');

// Session Configuration
define('SESSION_NAME', 'hibiscus_admin_session');
define('SESSION_LIFETIME', 3600); // 1 hour

class Database
{
    private static $instance = null;
    private $connection;

    private function __construct()
    {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];
            $this->connection = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            die("Database connection failed: " . $e->getMessage());
        }
    }

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection()
    {
        return $this->connection;
    }

    // Prevent cloning
    private function __clone()
    {
    }
}

// Helper function
function db()
{
    return Database::getInstance()->getConnection();
}
