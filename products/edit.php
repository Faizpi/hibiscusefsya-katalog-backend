<?php
/**
 * Edit Product
 * Hibiscus Efsya Katalog
 */
require_once __DIR__ . '/../config/config.php';
requireLogin();

$error = '';

// Get product ID
$id = (int)($_GET['id'] ?? 0);
if (!$id) {
    header('Location: index.php');
    exit();
}

// Get product
$stmt = db()->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$id]);
$product = $stmt->fetch();

if (!$product) {
    header('Location: index.php');
    exit();
}

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
        // Check unique slug (exclude current product)
        $stmt = db()->prepare("SELECT id FROM products WHERE slug = ? AND id != ?");
        $stmt->execute([$slug, $id]);
        if ($stmt->fetch()) {
            $slug = $slug . '-' . time();
        }
        
        // Handle image upload
        $imageName = $product['image']; // Keep existing image
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
                
                if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadFile)) {
                    // Delete old image
                    if ($product['image'] && file_exists(UPLOAD_PATH . $product['image'])) {
                        unlink(UPLOAD_PATH . $product['image']);
                    }
                } else {
                    $error = 'Gagal mengupload gambar';
                    $imageName = $product['image'];
                }
            }
        }
        
        // Handle delete image
        if (isset($_POST['delete_image']) && $_POST['delete_image'] === '1') {
            if ($product['image'] && file_exists(UPLOAD_PATH . $product['image'])) {
                unlink(UPLOAD_PATH . $product['image']);
            }
            $imageName = null;
        }
        
        if (empty($error)) {
            try {
                $stmt = db()->prepare("
                    UPDATE products 
                    SET name = ?, slug = ?, description = ?, price = ?, 
                        category_id = ?, image = ?, status = ?, featured = ?
                    WHERE id = ?
                ");
                $stmt->execute([$name, $slug, $description, $price, $category_id, $imageName, $status, $featured, $id]);
                
                header('Location: index.php?saved=1');
                exit();
            } catch (PDOException $e) {
                $error = 'Gagal menyimpan produk: ' . $e->getMessage();
            }
        }
    }
    
    // Update product data for form
    $product['name'] = $name;
    $product['slug'] = $slug;
    $product['description'] = $description;
    $product['price'] = $price;
    $product['category_id'] = $category_id;
    $product['status'] = $status;
    $product['featured'] = $featured;
}

$pageTitle = 'Edit Produk';
include __DIR__ . '/../includes/header.php';
?>

<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Edit Produk</h1>
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
                               value="<?php echo sanitize($product['name']); ?>" 
                               required
                               onkeyup="generateSlug(this.value)">
                    </div>
                    
                    <div class="form-group">
                        <label>Slug</label>
                        <input type="text" name="slug" id="slug" class="form-control" 
                               value="<?php echo sanitize($product['slug']); ?>">
                        <small class="text-muted">URL friendly. Kosongkan untuk auto-generate.</small>
                    </div>
                    
                    <div class="form-group">
                        <label>Deskripsi</label>
                        <textarea name="description" class="form-control" rows="5"><?php echo $product['description']; ?></textarea>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Harga (Rp) <span class="text-danger">*</span></label>
                                <input type="number" name="price" class="form-control" 
                                       value="<?php echo $product['price']; ?>" min="0" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Kategori</label>
                                <select name="category_id" class="form-control">
                                    <option value="">-- Pilih Kategori --</option>
                                    <?php foreach ($categories as $cat): ?>
                                    <option value="<?php echo $cat['id']; ?>"
                                            <?php echo ($product['category_id'] == $cat['id']) ? 'selected' : ''; ?>>
                                        <?php echo sanitize($cat['name']); ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>Gambar Produk</label>
                        
                        <?php if ($product['image']): ?>
                        <div class="mb-3" id="currentImage">
                            <img src="<?php echo UPLOAD_URL . $product['image']; ?>" 
                                 class="img-thumbnail" style="max-width: 200px;">
                            <br>
                            <button type="button" class="btn btn-sm btn-outline-danger mt-2" onclick="deleteImage()">
                                <i class="fas fa-trash mr-1"></i>Hapus Gambar
                            </button>
                            <input type="hidden" name="delete_image" id="deleteImageInput" value="0">
                        </div>
                        <?php endif; ?>
                        
                        <div class="custom-file">
                            <input type="file" name="image" class="custom-file-input" id="imageInput" accept="image/*">
                            <label class="custom-file-label" for="imageInput">
                                <?php echo $product['image'] ? 'Ganti gambar...' : 'Pilih gambar...'; ?>
                            </label>
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
                                    <option value="draft" <?php echo ($product['status'] === 'draft') ? 'selected' : ''; ?>>Draft</option>
                                    <option value="publish" <?php echo ($product['status'] === 'publish') ? 'selected' : ''; ?>>Publish</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>&nbsp;</label>
                                <div class="custom-control custom-checkbox mt-2">
                                    <input type="checkbox" class="custom-control-input" id="featured" name="featured" 
                                           <?php echo $product['featured'] ? 'checked' : ''; ?>>
                                    <label class="custom-control-label" for="featured">Produk Unggulan</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save mr-2"></i>Simpan Perubahan
                    </button>
                    <a href="index.php" class="btn btn-outline-secondary ml-2">Batal</a>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Info Produk</h6>
            </div>
            <div class="card-body">
                <p class="mb-2"><strong>ID:</strong> <?php echo $product['id']; ?></p>
                <p class="mb-2"><strong>Dibuat:</strong> <?php echo date('d M Y, H:i', strtotime($product['created_at'])); ?></p>
                <p class="mb-0"><strong>Diupdate:</strong> <?php echo date('d M Y, H:i', strtotime($product['updated_at'])); ?></p>
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

function deleteImage() {
    if (confirm('Hapus gambar ini?')) {
        document.getElementById('currentImage').style.display = 'none';
        document.getElementById('deleteImageInput').value = '1';
    }
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
