<?php
require_once __DIR__ . '/../config/config.php';

// Check if logged in
if (!isLoggedIn()) {
    header('Location: /login.php');
    exit;
}

// Create settings table if not exists
$db = db();
$db->exec("CREATE TABLE IF NOT EXISTS settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) NOT NULL UNIQUE,
    setting_value TEXT,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

// Handle form submission
$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Handle hero images upload
        $heroImages = [];
        // existing_hero_images is already an array from form (name="existing_hero_images[]")
        $existingHeroImages = $_POST['existing_hero_images'] ?? [];
        
        // Keep existing images that weren't removed
        foreach ($existingHeroImages as $img) {
            if (!empty($img)) {
                $heroImages[] = $img;
            }
        }
        
        // Upload new hero images
        if (!empty($_FILES['hero_images']['name'][0])) {
            // Use absolute path to main uploads folder (not relative)
            $uploadDir = '/home/u983003565/domains/hibiscusefsya.com/public_html/api/uploads/hero/';
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            
            foreach ($_FILES['hero_images']['tmp_name'] as $key => $tmpName) {
                if (!empty($tmpName) && is_uploaded_file($tmpName)) {
                    $originalName = $_FILES['hero_images']['name'][$key];
                    $ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
                    
                    if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                        $filename = 'hero_' . time() . '_' . $key . '.' . $ext;
                        $filepath = $uploadDir . $filename;
                        
                        if (move_uploaded_file($tmpName, $filepath)) {
                            // Use API_URL directly for hero images path
                            $heroImages[] = API_URL . '/uploads/hero/' . $filename;
                        } else {
                            error_log("Failed to move uploaded file: $tmpName to $filepath");
                        }
                    } else {
                        error_log("Invalid extension: $ext for file: $originalName");
                    }
                } else {
                    error_log("Upload check failed - tmpName: $tmpName, is_uploaded: " . (is_uploaded_file($tmpName) ? 'yes' : 'no'));
                }
            }
        }
        
        $settings = [
            'site_name' => $_POST['site_name'] ?? 'Hibiscus Efsya',
            'site_tagline' => $_POST['site_tagline'] ?? 'part of M.B.K Indonesia',
            'site_description' => $_POST['site_description'] ?? '',
            'hero_title' => $_POST['hero_title'] ?? 'Hibiscus Efsya',
            'hero_subtitle' => $_POST['hero_subtitle'] ?? 'part of M.B.K Indonesia',
            'hero_description' => $_POST['hero_description'] ?? '',
            'hero_images' => json_encode($heroImages),
            'about_title' => $_POST['about_title'] ?? 'Tentang Kami',
            'about_content' => $_POST['about_content'] ?? '',
            'contact_address' => $_POST['contact_address'] ?? '',
            'contact_email' => $_POST['contact_email'] ?? '',
            'contact_phone' => $_POST['contact_phone'] ?? '',
            'contact_whatsapp' => $_POST['contact_whatsapp'] ?? '',
            'social_instagram' => $_POST['social_instagram'] ?? '',
            'social_facebook' => $_POST['social_facebook'] ?? '',
            'social_shopee' => $_POST['social_shopee'] ?? '',
            'social_tokopedia' => $_POST['social_tokopedia'] ?? '',
        ];

        $stmt = $db->prepare("INSERT INTO settings (setting_key, setting_value) VALUES (:key, :value) 
                              ON DUPLICATE KEY UPDATE setting_value = :value2");
        
        foreach ($settings as $key => $value) {
            $stmt->execute([
                ':key' => $key,
                ':value' => $value,
                ':value2' => $value
            ]);
        }

        $message = 'Pengaturan berhasil disimpan!';
        $messageType = 'success';
    } catch (Exception $e) {
        $message = 'Gagal menyimpan: ' . $e->getMessage();
        $messageType = 'danger';
    }
}

// Get current settings
$settings = [];
$result = $db->query("SELECT setting_key, setting_value FROM settings");
while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
    $settings[$row['setting_key']] = $row['setting_value'];
}

// Default values
$defaults = [
    'site_name' => 'Hibiscus Efsya',
    'site_tagline' => 'part of M.B.K Indonesia',
    'site_description' => 'Produk perawatan tubuh berkualitas dari M.B.K Indonesia',
    'hero_title' => 'Hibiscus Efsya',
    'hero_subtitle' => 'part of M.B.K Indonesia',
    'hero_description' => 'Produk perawatan tubuh berkualitas untuk menjaga kesegaran dan kebersihan Anda. Deodorant dan bedak tabur yang efektif mengatasi bau badan.',
    'about_title' => 'Tentang Kami',
    'about_content' => 'M.B.K Indonesia adalah produsen produk kosmetik perawatan tubuh yang telah dipercaya masyarakat Indonesia. Produk-produk kami seperti Deodorant Roll On dan P.O. Powder (Bedak Tabur) telah terbukti efektif menjaga kesegaran tubuh dan mencegah bau badan.

Semua produk M.B.K telah bersertifikat Halal MUI dan aman digunakan untuk seluruh anggota keluarga. Dengan bahan-bahan berkualitas seperti tawas, talc, dan parfum pilihan, produk kami memberikan perlindungan maksimal dengan harga terjangkau.',
    'contact_address' => 'Jakarta, Indonesia',
    'contact_email' => 'hello@hibiscusefsya.com',
    'contact_phone' => '+62 812 3456 7890',
    'contact_whatsapp' => '6281234567890',
    'social_instagram' => 'https://instagram.com/mbkindonesia',
    'social_facebook' => '',
    'social_shopee' => 'https://shopee.co.id/mbkofficialaccount',
    'social_tokopedia' => '',
    'hero_images' => '[]',
];

foreach ($defaults as $key => $value) {
    if (!isset($settings[$key])) {
        $settings[$key] = $value;
    }
}

$pageTitle = 'Pengaturan Website';
require_once '../includes/header.php';
?>

<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Pengaturan Website</h1>
</div>

<?php if ($message): ?>
<div class="alert alert-<?= $messageType ?> alert-dismissible fade show" role="alert">
    <?= $message ?>
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
<?php endif; ?>

<form method="POST" action="" enctype="multipart/form-data">
    <div class="row">
        <!-- Informasi Umum -->
        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-cog mr-2"></i>Informasi Umum
                    </h6>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label for="site_name">Nama Website</label>
                        <input type="text" class="form-control" id="site_name" name="site_name" 
                               value="<?= htmlspecialchars($settings['site_name']) ?>">
                    </div>
                    <div class="form-group">
                        <label for="site_tagline">Tagline</label>
                        <input type="text" class="form-control" id="site_tagline" name="site_tagline" 
                               value="<?= htmlspecialchars($settings['site_tagline']) ?>">
                    </div>
                    <div class="form-group">
                        <label for="site_description">Deskripsi Singkat</label>
                        <textarea class="form-control" id="site_description" name="site_description" rows="3"><?= htmlspecialchars($settings['site_description']) ?></textarea>
                    </div>
                </div>
            </div>
        </div>

        <!-- Hero Section -->
        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-home mr-2"></i>Hero Section (Beranda)
                    </h6>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label for="hero_title">Judul Hero</label>
                        <input type="text" class="form-control" id="hero_title" name="hero_title" 
                               value="<?= htmlspecialchars($settings['hero_title']) ?>">
                    </div>
                    <div class="form-group">
                        <label for="hero_subtitle">Subtitle Hero (teks berjalan)</label>
                        <input type="text" class="form-control" id="hero_subtitle" name="hero_subtitle" 
                               value="<?= htmlspecialchars($settings['hero_subtitle']) ?>">
                    </div>
                    <div class="form-group">
                        <label for="hero_description">Deskripsi Hero</label>
                        <textarea class="form-control" id="hero_description" name="hero_description" rows="4"><?= htmlspecialchars($settings['hero_description']) ?></textarea>
                    </div>
                    
                    <!-- Hero Images Upload -->
                    <div class="form-group">
                        <label>Gambar Hero (Card Stack)</label>
                        <small class="form-text text-muted mb-2">Upload gambar produk untuk ditampilkan di card stack hero. Bisa upload banyak gambar.</small>
                        
                        <?php 
                        $heroImagesArr = json_decode($settings['hero_images'] ?? '[]', true) ?: [];
                        ?>
                        
                        <!-- Current Images -->
                        <div class="row mb-3" id="hero-images-preview">
                            <?php foreach ($heroImagesArr as $idx => $imgUrl): ?>
                            <div class="col-md-3 col-sm-4 col-6 mb-2 hero-image-item">
                                <div class="position-relative" style="border: 1px solid #ddd; border-radius: 5px; overflow: hidden;">
                                    <img src="<?= htmlspecialchars($imgUrl) ?>" class="img-fluid" style="width: 100%; height: 120px; object-fit: cover;">
                                    <button type="button" class="btn btn-danger btn-sm position-absolute" style="top: 5px; right: 5px; padding: 2px 6px;" onclick="removeHeroImage(this)">
                                        <i class="fas fa-times"></i>
                                    </button>
                                    <input type="hidden" name="existing_hero_images[]" value="<?= htmlspecialchars($imgUrl) ?>">
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        
                        <!-- Upload New -->
                        <div class="custom-file mb-2">
                            <input type="file" class="custom-file-input" id="hero_images" name="hero_images[]" multiple accept="image/*">
                            <label class="custom-file-label" for="hero_images">Pilih gambar baru...</label>
                        </div>
                        <small class="form-text text-muted">Format: JPG, PNG, GIF, WEBP. Bisa pilih beberapa file sekaligus. Gambar akan ditampilkan sebagai card stack di hero section.</small>
                        
                        <!-- Preview new uploads -->
                        <div class="row mt-3" id="new-images-preview"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tentang Kami -->
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-info-circle mr-2"></i>Tentang Kami
                    </h6>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label for="about_title">Judul</label>
                        <input type="text" class="form-control" id="about_title" name="about_title" 
                               value="<?= htmlspecialchars($settings['about_title']) ?>">
                    </div>
                    <div class="form-group">
                        <label for="about_content">Konten Tentang Kami</label>
                        <textarea class="form-control" id="about_content" name="about_content" rows="8"><?= htmlspecialchars($settings['about_content']) ?></textarea>
                        <small class="form-text text-muted">Gunakan baris baru untuk paragraf berbeda</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Kontak -->
        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-address-card mr-2"></i>Informasi Kontak
                    </h6>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label for="contact_address">Alamat</label>
                        <input type="text" class="form-control" id="contact_address" name="contact_address" 
                               value="<?= htmlspecialchars($settings['contact_address']) ?>">
                    </div>
                    <div class="form-group">
                        <label for="contact_email">Email</label>
                        <input type="email" class="form-control" id="contact_email" name="contact_email" 
                               value="<?= htmlspecialchars($settings['contact_email']) ?>">
                    </div>
                    <div class="form-group">
                        <label for="contact_phone">Telepon</label>
                        <input type="text" class="form-control" id="contact_phone" name="contact_phone" 
                               value="<?= htmlspecialchars($settings['contact_phone']) ?>">
                    </div>
                    <div class="form-group">
                        <label for="contact_whatsapp">WhatsApp (tanpa + dan spasi)</label>
                        <input type="text" class="form-control" id="contact_whatsapp" name="contact_whatsapp" 
                               value="<?= htmlspecialchars($settings['contact_whatsapp']) ?>"
                               placeholder="6281234567890">
                    </div>
                </div>
            </div>
        </div>

        <!-- Social Media -->
        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-share-alt mr-2"></i>Media Sosial & Marketplace
                    </h6>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label for="social_instagram">Instagram URL</label>
                        <input type="url" class="form-control" id="social_instagram" name="social_instagram" 
                               value="<?= htmlspecialchars($settings['social_instagram']) ?>"
                               placeholder="https://instagram.com/username">
                    </div>
                    <div class="form-group">
                        <label for="social_facebook">Facebook URL</label>
                        <input type="url" class="form-control" id="social_facebook" name="social_facebook" 
                               value="<?= htmlspecialchars($settings['social_facebook']) ?>"
                               placeholder="https://facebook.com/page">
                    </div>
                    <div class="form-group">
                        <label for="social_shopee">Shopee URL</label>
                        <input type="url" class="form-control" id="social_shopee" name="social_shopee" 
                               value="<?= htmlspecialchars($settings['social_shopee']) ?>"
                               placeholder="https://shopee.co.id/shop">
                    </div>
                    <div class="form-group">
                        <label for="social_tokopedia">Tokopedia URL</label>
                        <input type="url" class="form-control" id="social_tokopedia" name="social_tokopedia" 
                               value="<?= htmlspecialchars($settings['social_tokopedia']) ?>"
                               placeholder="https://tokopedia.com/shop">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="mb-4">
        <button type="submit" class="btn btn-primary btn-lg">
            <i class="fas fa-save mr-2"></i>Simpan Pengaturan
        </button>
    </div>
</form>

<?php require_once '../includes/footer.php'; ?>

<script>
// Remove hero image (existing)
function removeHeroImage(btn) {
    const item = btn.closest('.hero-image-item');
    item.remove();
}

// Show selected file names and preview
document.getElementById('hero_images').addEventListener('change', function(e) {
    const files = Array.from(this.files);
    const label = this.nextElementSibling;
    const previewContainer = document.getElementById('new-images-preview');
    
    // Update label
    if (files.length > 0) {
        label.textContent = files.length + ' gambar dipilih: ' + files.map(f => f.name).join(', ');
    } else {
        label.textContent = 'Pilih gambar baru...';
    }
    
    // Show preview
    previewContainer.innerHTML = '';
    if (files.length > 0) {
        const title = document.createElement('div');
        title.className = 'col-12 mb-2';
        title.innerHTML = '<small class="text-info"><i class="fas fa-image mr-1"></i>Preview gambar yang akan diupload:</small>';
        previewContainer.appendChild(title);
        
        files.forEach((file, idx) => {
            const reader = new FileReader();
            reader.onload = function(e) {
                const col = document.createElement('div');
                col.className = 'col-md-3 col-sm-4 col-6 mb-2';
                col.innerHTML = `
                    <div style="border: 2px dashed #17a2b8; border-radius: 5px; overflow: hidden;">
                        <img src="${e.target.result}" class="img-fluid" style="width: 100%; height: 120px; object-fit: cover;">
                        <div class="text-center small text-muted py-1" style="background: #f8f9fa;">${file.name}</div>
                    </div>
                `;
                previewContainer.appendChild(col);
            };
            reader.readAsDataURL(file);
        });
    }
});
</script>
