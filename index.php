<?php
/**
 * Admin Dashboard
 * Hibiscus Efsya Katalog
 */
require_once __DIR__ . '/config/config.php';
requireLogin();

// Get statistics
$totalProducts = db()->query("SELECT COUNT(*) FROM products")->fetchColumn();
$publishedProducts = db()->query("SELECT COUNT(*) FROM products WHERE status = 'publish'")->fetchColumn();
$draftProducts = db()->query("SELECT COUNT(*) FROM products WHERE status = 'draft'")->fetchColumn();
$totalCategories = db()->query("SELECT COUNT(*) FROM categories")->fetchColumn();

// Get recent products
$recentProducts = db()->query("SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id ORDER BY p.created_at DESC LIMIT 5")->fetchAll();

$pageTitle = 'Dashboard';
include __DIR__ . '/includes/header.php';
?>

<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Dashboard</h1>
    <a href="products/add.php" class="btn btn-primary btn-sm shadow-sm">
        <i class="fas fa-plus fa-sm text-white-50 mr-1"></i> Tambah Produk
    </a>
</div>

<!-- Content Row -->
<div class="row">
    <!-- Total Products Card -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Total Produk</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $totalProducts; ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-box fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Published Products Card -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            Produk Publish</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $publishedProducts; ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Draft Products Card -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                            Produk Draft</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $draftProducts; ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-edit fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Categories Card -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                            Kategori</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $totalCategories; ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-tags fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Content Row -->
<div class="row">
    <!-- Recent Products -->
    <div class="col-lg-8">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Produk Terbaru</h6>
                <a href="products/index.php" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
            </div>
            <div class="card-body">
                <?php if (count($recentProducts) > 0): ?>
                <div class="table-responsive">
                    <table class="table table-borderless">
                        <thead>
                            <tr>
                                <th>Produk</th>
                                <th>Kategori</th>
                                <th>Harga</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recentProducts as $product): ?>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <?php if ($product['image']): ?>
                                        <img src="<?php echo UPLOAD_URL . $product['image']; ?>" 
                                             alt="<?php echo sanitize($product['name']); ?>"
                                             class="rounded mr-3" style="width: 40px; height: 40px; object-fit: cover;">
                                        <?php else: ?>
                                        <div class="rounded mr-3 bg-light d-flex align-items-center justify-content-center" 
                                             style="width: 40px; height: 40px;">
                                            <i class="fas fa-image text-gray-400"></i>
                                        </div>
                                        <?php endif; ?>
                                        <span class="font-weight-medium"><?php echo sanitize($product['name']); ?></span>
                                    </div>
                                </td>
                                <td><span class="badge badge-light"><?php echo $product['category_name'] ?? 'Uncategorized'; ?></span></td>
                                <td><?php echo formatRupiah($product['price']); ?></td>
                                <td>
                                    <?php if ($product['status'] === 'publish'): ?>
                                    <span class="badge badge-success">Publish</span>
                                    <?php else: ?>
                                    <span class="badge badge-warning">Draft</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <div class="text-center py-4">
                    <i class="fas fa-box-open fa-3x text-gray-300 mb-3"></i>
                    <p class="text-gray-500 mb-0">Belum ada produk</p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="col-lg-4">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Aksi Cepat</h6>
            </div>
            <div class="card-body">
                <a href="products/add.php" class="btn btn-primary btn-block mb-2">
                    <i class="fas fa-plus mr-2"></i>Tambah Produk Baru
                </a>
                <a href="products/index.php" class="btn btn-outline-primary btn-block mb-2">
                    <i class="fas fa-list mr-2"></i>Kelola Produk
                </a>
                <a href="categories/index.php" class="btn btn-outline-secondary btn-block mb-2">
                    <i class="fas fa-tags mr-2"></i>Kelola Kategori
                </a>
                <hr>
                <a href="<?php echo FRONTEND_URL; ?>" target="_blank" class="btn btn-outline-info btn-block">
                    <i class="fas fa-external-link-alt mr-2"></i>Lihat Katalog
                </a>
            </div>
        </div>
        
        <!-- Info Card -->
        <div class="card shadow mb-4 bg-gradient-primary text-white">
            <div class="card-body">
                <div class="text-white-50 small mb-2">Website Katalog</div>
                <div class="h5 mb-0 font-weight-bold">Hibiscus Efsya</div>
                <small class="text-white-50">part of M.B.K Indonesia</small>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
