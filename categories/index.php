<?php
/**
 * Categories List
 * Hibiscus Efsya Katalog
 */
require_once __DIR__ . '/../config/config.php';
requireLogin();

// Handle delete
if (isset($_POST['delete_id'])) {
    $id = (int) $_POST['delete_id'];

    // Check if category has products
    $stmt = db()->prepare("SELECT COUNT(*) FROM products WHERE category_id = ?");
    $stmt->execute([$id]);
    $productCount = $stmt->fetchColumn();

    if ($productCount > 0) {
        $error = 'Tidak dapat menghapus kategori yang masih memiliki produk';
    } else {
        $stmt = db()->prepare("DELETE FROM categories WHERE id = ?");
        $stmt->execute([$id]);
        header('Location: index.php?deleted=1');
        exit();
    }
}

// Handle add/edit
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['name'])) {
    $id = (int) ($_POST['id'] ?? 0);
    $name = sanitize($_POST['name']);
    $slug = sanitize($_POST['slug'] ?? '');
    $description = sanitize($_POST['description'] ?? '');

    if (empty($slug)) {
        $slug = generateSlug($name);
    }

    if ($id) {
        // Update
        $stmt = db()->prepare("UPDATE categories SET name = ?, slug = ?, description = ? WHERE id = ?");
        $stmt->execute([$name, $slug, $description, $id]);
    } else {
        // Insert
        $stmt = db()->prepare("INSERT INTO categories (name, slug, description) VALUES (?, ?, ?)");
        $stmt->execute([$name, $slug, $description]);
    }

    header('Location: index.php?saved=1');
    exit();
}

// Get all categories with product count
$categories = db()->query("
    SELECT c.*, COUNT(p.id) as product_count 
    FROM categories c 
    LEFT JOIN products p ON c.id = p.category_id 
    GROUP BY c.id 
    ORDER BY c.name
")->fetchAll();

$pageTitle = 'Kategori';
include __DIR__ . '/../includes/header.php';
?>

<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Kategori</h1>
    <button class="btn btn-primary btn-sm shadow-sm" data-toggle="modal" data-target="#categoryModal">
        <i class="fas fa-plus fa-sm text-white-50 mr-1"></i> Tambah Kategori
    </button>
</div>

<?php if (isset($error)): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle mr-2"></i><?php echo $error; ?>
        <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
    </div>
<?php endif; ?>

<?php if (isset($_GET['deleted'])): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle mr-2"></i>Kategori berhasil dihapus
        <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
    </div>
<?php endif; ?>

<?php if (isset($_GET['saved'])): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle mr-2"></i>Kategori berhasil disimpan
        <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
    </div>
<?php endif; ?>

<!-- Categories Table -->
<div class="card shadow mb-4">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover" id="dataTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>Nama Kategori</th>
                        <th>Slug</th>
                        <th>Deskripsi</th>
                        <th>Jumlah Produk</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($categories as $category): ?>
                        <tr>
                            <td><strong><?php echo sanitize($category['name']); ?></strong></td>
                            <td><code><?php echo sanitize($category['slug']); ?></code></td>
                            <td><?php echo sanitize($category['description']) ?: '-'; ?></td>
                            <td><span class="badge badge-primary"><?php echo $category['product_count']; ?> produk</span>
                            </td>
                            <td>
                                <button type="button" class="btn btn-sm btn-outline-primary"
                                    onclick="editCategory(<?php echo htmlspecialchars(json_encode($category)); ?>)">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <?php if ($category['product_count'] == 0): ?>
                                    <form method="POST" style="display: inline;" onsubmit="return confirmDelete(this)">
                                        <input type="hidden" name="delete_id" value="<?php echo $category['id']; ?>">
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Category Modal -->
<div class="modal fade" id="categoryModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Tambah Kategori</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" id="categoryId">

                    <div class="form-group">
                        <label>Nama Kategori <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="categoryName" class="form-control" required
                            onkeyup="generateCategorySlug(this.value)">
                    </div>

                    <div class="form-group">
                        <label>Slug</label>
                        <input type="text" name="slug" id="categorySlug" class="form-control">
                        <small class="text-muted">URL friendly. Kosongkan untuk auto-generate.</small>
                    </div>

                    <div class="form-group">
                        <label>Deskripsi</label>
                        <textarea name="description" id="categoryDescription" class="form-control" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function generateCategorySlug(text) {
        var slug = text.toLowerCase()
            .replace(/[^a-z0-9\s-]/g, '')
            .replace(/[\s-]+/g, '-')
            .replace(/^-+|-+$/g, '');
        document.getElementById('categorySlug').value = slug;
    }

    function editCategory(category) {
        document.getElementById('modalTitle').textContent = 'Edit Kategori';
        document.getElementById('categoryId').value = category.id;
        document.getElementById('categoryName').value = category.name;
        document.getElementById('categorySlug').value = category.slug;
        document.getElementById('categoryDescription').value = category.description || '';
        $('#categoryModal').modal('show');
    }

    // Reset modal on close
    $('#categoryModal').on('hidden.bs.modal', function () {
        document.getElementById('modalTitle').textContent = 'Tambah Kategori';
        document.getElementById('categoryId').value = '';
        document.getElementById('categoryName').value = '';
        document.getElementById('categorySlug').value = '';
        document.getElementById('categoryDescription').value = '';
    });
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>