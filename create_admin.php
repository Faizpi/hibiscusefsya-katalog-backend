<?php
require_once __DIR__ . '/config/database.php';

$db = db();

// Create password hash
$hash = password_hash('admin123', PASSWORD_DEFAULT);

// Delete existing admin if exists
$db->exec("DELETE FROM admins WHERE username = 'admin'");

// Insert new admin
$stmt = $db->prepare("INSERT INTO admins (username, email, password, full_name) VALUES (?, ?, ?, ?)");
$result = $stmt->execute(['admin', 'admin@hibiscusefsya.com', $hash, 'Administrator']);

if ($result) {
    echo "Admin berhasil dibuat!\n";
    echo "Username: admin\n";
    echo "Password: admin123\n";
} else {
    echo "Gagal membuat admin";
}
