<?php
/**
 * Add Product
 * Hibiscus Efsya Katalog
 */
require_once __DIR__ . '/../config/config.php';
requireLogin();

$error = '';
$success = '';

// Get categories
$categories = db()->query("SELECT * FROM categories ORDER BY name")->fetchAll();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize($_POST['name'] ?? '');
    $slug = sanitize($_POST['slug'] ?? '');
    $description = $_POST['description'] ?? '';
    $price = (float)($_POST['price'] ?? 0);
    $category_id = !empty($_POST['category_id']) ? (int)$_POST['category_id'] : null;
    $status = sanitize($_POST['status'] ?? 'draft');
    $featured = isset($_POST['featured']) ? 1 : 0;
    
    // Generate slug if empty
    if (empty($slug)) {
        $slug = generateSlug($name);
    }
    
    // Validation
    if (empty($name)) {
        $error = 'Nama produk harus diisi';
    } else {
        // Check unique slug
        $stmt = db()->prepare("SELECT id FROM products WHERE slug = ?");
        $stmt->execute([$slug]);
        if ($stmt->fetch()) {
            $slug = $slug . '-' . time();
        }
        
        // Handle image upload
        $imageName = null;
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $allowed = ['image/jpeg', 'image/png', 'image/webp'];
            $fileType = $_FILES['image']['type'];
            
            if (!in_array($fileType, $allowed)) {
                $error = 'Format gambar tidak valid. Gunakan JPG, PNG, atau WebP.';
            } else {
                // Create upload directory if not exists
                if (!is_dir(UPLOAD_PATH)) {
                    mkdir(UPLOAD_PATH, 0755, true);
                }
                
                $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                $imageName = $slug . '-' . time() . '.' . $ext;
                $uploadFile = UPLOAD_PATH . $imageName;
                
                if (!move_uploaded_file($_FILES['image']['tmp_name'], $uploadFile)) {
                    $error = 'Gagal mengupload gambar';
                    $imageName = null;
                }
            }
        }
        
        if (empty($error)) {
            try {
                $stmt = db()->prepare("
                    INSERT INTO products (name, slug, description, price, category_id, image, status, featured)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)
                ");
                $stmt->execute([$name, $slug, $description, $price, $category_id, $imageName, $status, $featured]);
                
                header('Location: index.php?saved=1');
                exit();
            } catch (PDOException $e) {
                $error = 'Gagal menyimpan produk: ' . $e->getMessage();
            }
        }
    }
}

$pageTitle = 'Tambah Produk';
include __DIR__ . '/../includes/header.php';
?>

<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Tambah Produk</h1>
    <a href="index.php" class="btn btn-outline-secondary btn-sm">
        <i class="fas fa-arrow-left mr-1"></i> Kembali
    </a>
</div>

<?php if ($error): ?>
<div class="alert alert-danger">
    <i class="fas fa-exclamation-circle mr-2"></i><?php echo $error; ?>
</div>
<?php endif; ?>

<div class="row">
    <div class="col-lg-8">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Informasi Produk</h6>
            </div>
            <div class="card-body">
                <form method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                        <label>Nama Produk <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" 
                               value="<?php echo sanitize($_POST['name'] ?? ''); ?>" 
                               required
                               onkeyup="generateSlug(this.value)">
                    </div>
                    
                    <div class="form-group">
                        <label>Slug</label>
                        <input type="text" name="slug" id="slug" class="form-control" 
                               value="<?php echo sanitize($_POST['slug'] ?? ''); ?>"
                               placeholder="Auto-generate dari nama">
                        <small class="text-muted">URL friendly. Kosongkan untuk auto-generate.</small>
                    </div>
                    
                    <div class="form-group">
                        <label>Deskripsi</label>
                        <textarea name="description" class="form-control" rows="5"><?php echo $_POST['description'] ?? ''; ?></textarea>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Harga (Rp) <span class="text-danger">*</span></label>
                                <input type="number" name="price" class="form-control" 
                                       value="<?php echo $_POST['price'] ?? '0'; ?>" min="0" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Kategori</label>
                                <select name="category_id" class="form-control">
                                    <option value="">-- Pilih Kategori --</option>
                                    <?php foreach ($categories as $cat): ?>
                                    <option value="<?php echo $cat['id']; ?>"
                                            <?php echo (isset($_POST['category_id']) && $_POST['category_id'] == $cat['id']) ? 'selected' : ''; ?>>
                                        <?php echo sanitize($cat['name']); ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>Gambar Produk</label>
                        <div class="custom-file">
                            <input type="file" name="image" class="custom-file-input" id="imageInput" accept="image/*">
                            <label class="custom-file-label" for="imageInput">Pilih gambar...</label>
                        </div>
                        <small class="text-muted">Format: JPG, PNG, WebP. Maks 2MB.</small>
                        <div id="imagePreview" class="mt-3"></div>
                    </div>
                    
                    <hr>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Status</label>
                                <select name="status" class="form-control">
                                    <option value="draft" <?php echo (isset($_POST['status']) && $_POST['status'] === 'draft') ? 'selected' : ''; ?>>Draft</option>
                                    <option value="publish" <?php echo (isset($_POST['status']) && $_POST['status'] === 'publish') ? 'selected' : ''; ?>>Publish</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>&nbsp;</label>
                                <div class="custom-control custom-checkbox mt-2">
                                    <input type="checkbox" class="custom-control-input" id="featured" name="featured" 
                                           <?php echo isset($_POST['featured']) ? 'checked' : ''; ?>>
                                    <label class="custom-control-label" for="featured">Produk Unggulan</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save mr-2"></i>Simpan Produk
                    </button>
                    <a href="index.php" class="btn btn-outline-secondary ml-2">Batal</a>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Tips</h6>
            </div>
            <div class="card-body">
                <ul class="mb-0 pl-3">
                    <li class="mb-2">Nama produk sebaiknya deskriptif dan unik</li>
                    <li class="mb-2">Gunakan gambar berkualitas baik dengan rasio 1:1</li>
                    <li class="mb-2">Deskripsi yang detail membantu calon pembeli</li>
                    <li class="mb-2">Produk dengan status "Draft" tidak akan tampil di katalog</li>
                    <li>Produk unggulan akan ditampilkan di halaman utama</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<script>
function generateSlug(text) {
    var slug = text.toLowerCase()
        .replace(/[^a-z0-9\s-]/g, '')
        .replace(/[\s-]+/g, '-')
        .replace(/^-+|-+$/g, '');
    document.getElementById('slug').value = slug;
}

// Image preview
document.getElementById('imageInput').addEventListener('change', function(e) {
    var file = e.target.files[0];
    if (file) {
        document.querySelector('.custom-file-label').textContent = file.name;
        
        var reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('imagePreview').innerHTML = 
                '<img src="' + e.target.result + '" class="img-thumbnail" style="max-width: 200px;">';
        };
        reader.readAsDataURL(file);
    }
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>
