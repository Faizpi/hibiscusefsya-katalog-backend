<?php
/**
 * Products List
 * Hibiscus Efsya Katalog
 */
require_once __DIR__ . '/../config/config.php';
requireLogin();

// Handle delete
if (isset($_POST['delete_id'])) {
    $id = (int)$_POST['delete_id'];
    
    // Get image filename first
    $stmt = db()->prepare("SELECT image FROM products WHERE id = ?");
    $stmt->execute([$id]);
    $product = $stmt->fetch();
    
    // Delete from database
    $stmt = db()->prepare("DELETE FROM products WHERE id = ?");
    $stmt->execute([$id]);
    
    // Delete image file if exists
    if ($product && $product['image'] && file_exists(UPLOAD_PATH . $product['image'])) {
        unlink(UPLOAD_PATH . $product['image']);
    }
    
    header('Location: index.php?deleted=1');
    exit();
}

// Get all products
$products = db()->query("
    SELECT p.*, c.name as category_name 
    FROM products p 
    LEFT JOIN categories c ON p.category_id = c.id 
    ORDER BY p.created_at DESC
")->fetchAll();

$pageTitle = 'Daftar Produk';
include __DIR__ . '/../includes/header.php';
?>

<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Daftar Produk</h1>
    <a href="add.php" class="btn btn-primary btn-sm shadow-sm">
        <i class="fas fa-plus fa-sm text-white-50 mr-1"></i> Tambah Produk
    </a>
</div>

<?php if (isset($_GET['deleted'])): ?>
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <i class="fas fa-check-circle mr-2"></i>Produk berhasil dihapus
    <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
</div>
<?php endif; ?>

<?php if (isset($_GET['saved'])): ?>
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <i class="fas fa-check-circle mr-2"></i>Produk berhasil disimpan
    <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
</div>
<?php endif; ?>

<!-- Products Table -->
<div class="card shadow mb-4">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover" id="dataTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>Gambar</th>
                        <th>Nama Produk</th>
                        <th>Kategori</th>
                        <th>Harga</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($products as $product): ?>
                    <tr>
                        <td>
                            <?php if ($product['image']): ?>
                            <img src="<?php echo UPLOAD_URL . $product['image']; ?>" 
                                 alt="<?php echo sanitize($product['name']); ?>"
                                 class="rounded" style="width: 50px; height: 50px; object-fit: cover;">
                            <?php else: ?>
                            <div class="rounded bg-light d-flex align-items-center justify-content-center" 
                                 style="width: 50px; height: 50px;">
                                <i class="fas fa-image text-gray-400"></i>
                            </div>
                            <?php endif; ?>
                        </td>
                        <td>
                            <strong><?php echo sanitize($product['name']); ?></strong>
                            <?php if ($product['featured']): ?>
                            <span class="badge badge-info ml-1">Featured</span>
                            <?php endif; ?>
                            <br>
                            <small class="text-muted"><?php echo sanitize($product['slug']); ?></small>
                        </td>
                        <td>
                            <span class="badge badge-light"><?php echo $product['category_name'] ?? 'Uncategorized'; ?></span>
                        </td>
                        <td><?php echo formatRupiah($product['price']); ?></td>
                        <td>
                            <?php if ($product['status'] === 'publish'): ?>
                            <span class="badge badge-success">Publish</span>
                            <?php else: ?>
                            <span class="badge badge-warning">Draft</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="edit.php?id=<?php echo $product['id']; ?>" class="btn btn-sm btn-outline-primary" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form method="POST" style="display: inline;" onsubmit="return confirmDelete(this)">
                                <input type="hidden" name="delete_id" value="<?php echo $product['id']; ?>">
                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
