<?php
/**
 * Add Article
 * Hibiscus Efsya Katalog
 */
require_once __DIR__ . '/../config/config.php';
requireLogin();

$errors = [];
$article = [
    'title' => '',
    'slug' => '',
    'content' => '',
    'status' => 'draft'
];

// Handle form submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $article['title'] = trim($_POST['title'] ?? '');
    $article['slug'] = trim($_POST['slug'] ?? '');
    $article['content'] = trim($_POST['content'] ?? '');
    $article['status'] = $_POST['status'] ?? 'draft';

    // Validation
    if (empty($article['title'])) {
        $errors[] = 'Judul artikel wajib diisi';
    }

    // Auto generate slug if empty
    if (empty($article['slug'])) {
        $article['slug'] = createSlug($article['title']);
    }

    // Check slug uniqueness
    $stmt = db()->prepare("SELECT id FROM inspirations WHERE slug = ?");
    $stmt->execute([$article['slug']]);
    if ($stmt->fetch()) {
        $errors[] = 'Slug sudah digunakan';
    }

    // Handle image upload
    $imageName = null;
    if (!empty($_FILES['image']['name'])) {
        $uploadDir = UPLOAD_PATH_ARTICLES;
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $maxSize = 5 * 1024 * 1024; // 5MB

        if (!in_array($_FILES['image']['type'], $allowedTypes)) {
            $errors[] = 'Format gambar tidak valid. Gunakan JPG, PNG, GIF, atau WebP';
        } elseif ($_FILES['image']['size'] > $maxSize) {
            $errors[] = 'Ukuran gambar maksimal 5MB';
        } else {
            $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $imageName = 'article_' . time() . '_' . uniqid() . '.' . $ext;
            
            if (!move_uploaded_file($_FILES['image']['tmp_name'], $uploadDir . $imageName)) {
                $errors[] = 'Gagal mengupload gambar';
                $imageName = null;
            }
        }
    }

    // Save to database
    if (empty($errors)) {
        $stmt = db()->prepare("
            INSERT INTO inspirations (title, slug, content, image, status)
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $article['title'],
            $article['slug'],
            $article['content'],
            $imageName,
            $article['status']
        ]);

        header('Location: index.php?saved=1');
        exit();
    }
}

$pageTitle = 'Tambah Artikel';
include __DIR__ . '/../includes/header.php';
?>

<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Tambah Artikel</h1>
    <a href="index.php" class="btn btn-secondary btn-sm">
        <i class="fas fa-arrow-left fa-sm mr-1"></i> Kembali
    </a>
</div>

<?php if (!empty($errors)): ?>
    <div class="alert alert-danger">
        <ul class="mb-0">
            <?php foreach ($errors as $error): ?>
                <li><?php echo $error; ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<div class="card shadow mb-4">
    <div class="card-body">
        <form method="POST" enctype="multipart/form-data">
            <div class="row">
                <div class="col-md-8">
                    <div class="form-group">
                        <label for="title">Judul Artikel <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="title" name="title"
                            value="<?php echo sanitize($article['title']); ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="slug">Slug URL</label>
                        <input type="text" class="form-control" id="slug" name="slug"
                            value="<?php echo sanitize($article['slug']); ?>"
                            placeholder="auto-generate dari judul">
                        <small class="form-text text-muted">Biarkan kosong untuk generate otomatis</small>
                    </div>

                    <div class="form-group">
                        <label for="content">Konten Artikel</label>
                        <textarea class="form-control" id="content" name="content" rows="10"><?php echo sanitize($article['content']); ?></textarea>
                        <small class="form-text text-muted">Tulis konten lengkap artikel di sini</small>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        <label for="image">Gambar Artikel</label>
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" id="image" name="image" accept="image/*">
                            <label class="custom-file-label" for="image">Pilih gambar...</label>
                        </div>
                        <small class="form-text text-muted">Format: JPG, PNG, GIF, WebP. Maks 5MB</small>
                        <div id="imagePreview" class="mt-2"></div>
                    </div>

                    <div class="form-group">
                        <label for="status">Status</label>
                        <select class="form-control" id="status" name="status">
                            <option value="draft" <?php echo $article['status'] === 'draft' ? 'selected' : ''; ?>>Draft</option>
                            <option value="publish" <?php echo $article['status'] === 'publish' ? 'selected' : ''; ?>>Published</option>
                        </select>
                    </div>
                </div>
            </div>

            <hr>

            <div class="d-flex justify-content-end">
                <a href="index.php" class="btn btn-secondary mr-2">Batal</a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save mr-1"></i> Simpan Artikel
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// Auto generate slug from title
document.getElementById('title').addEventListener('blur', function() {
    const slugField = document.getElementById('slug');
    if (slugField.value === '') {
        slugField.value = this.value
            .toLowerCase()
            .replace(/[^a-z0-9\s-]/g, '')
            .replace(/\s+/g, '-')
            .replace(/-+/g, '-')
            .trim();
    }
});

// Image preview
document.getElementById('image').addEventListener('change', function(e) {
    const preview = document.getElementById('imagePreview');
    const file = e.target.files[0];
    
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.innerHTML = '<img src="' + e.target.result + '" class="img-fluid rounded" style="max-height: 200px;">';
        };
        reader.readAsDataURL(file);
        
        // Update label
        e.target.nextElementSibling.textContent = file.name;
    }
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>
