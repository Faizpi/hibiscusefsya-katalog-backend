<?php
/**
 * Articles List
 * Hibiscus Efsya Katalog
 */
require_once __DIR__ . '/../config/config.php';
requireLogin();

// Handle delete
if (isset($_POST['delete_id'])) {
    $id = (int) $_POST['delete_id'];

    // Get image filename first
    $stmt = db()->prepare("SELECT image FROM inspirations WHERE id = ?");
    $stmt->execute([$id]);
    $article = $stmt->fetch();

    // Delete from database
    $stmt = db()->prepare("DELETE FROM inspirations WHERE id = ?");
    $stmt->execute([$id]);

    // Delete image file if exists
    if ($article && $article['image'] && file_exists(UPLOAD_PATH . 'articles/' . $article['image'])) {
        unlink(UPLOAD_PATH . 'articles/' . $article['image']);
    }

    header('Location: index.php?deleted=1');
    exit();
}

// Get all articles
$articles = db()->query("
    SELECT * FROM inspirations 
    ORDER BY created_at DESC
")->fetchAll();

$pageTitle = 'Daftar Artikel';
include __DIR__ . '/../includes/header.php';
?>

<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Tips & Artikel</h1>
    <a href="add.php" class="btn btn-primary btn-sm shadow-sm">
        <i class="fas fa-plus fa-sm text-white-50 mr-1"></i> Tambah Artikel
    </a>
</div>

<?php if (isset($_GET['deleted'])): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle mr-2"></i>Artikel berhasil dihapus
        <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
    </div>
<?php endif; ?>

<?php if (isset($_GET['saved'])): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle mr-2"></i>Artikel berhasil disimpan
        <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
    </div>
<?php endif; ?>

<!-- Articles Table -->
<div class="card shadow mb-4">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover" id="dataTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>Gambar</th>
                        <th>Judul</th>
                        <th>Status</th>
                        <th>Tanggal</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($articles as $article): ?>
                        <tr>
                            <td>
                                <?php if ($article['image']): ?>
                                    <img src="<?php echo UPLOAD_URL_ARTICLES . $article['image']; ?>"
                                        alt="<?php echo sanitize($article['title']); ?>" class="rounded"
                                        style="width: 80px; height: 50px; object-fit: cover;">
                                <?php else: ?>
                                    <div class="rounded bg-light d-flex align-items-center justify-content-center"
                                        style="width: 80px; height: 50px;">
                                        <i class="fas fa-image text-gray-400"></i>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <strong><?php echo sanitize($article['title']); ?></strong>
                                <br>
                                <small class="text-muted"><?php echo sanitize($article['slug']); ?></small>
                            </td>
                            <td>
                                <?php if ($article['status'] === 'publish'): ?>
                                    <span class="badge badge-success">Published</span>
                                <?php else: ?>
                                    <span class="badge badge-secondary">Draft</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php echo date('d M Y', strtotime($article['created_at'])); ?>
                            </td>
                            <td>
                                <a href="edit.php?id=<?php echo $article['id']; ?>" class="btn btn-sm btn-info"
                                    title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form method="POST" class="d-inline"
                                    onsubmit="return confirm('Yakin ingin menghapus artikel ini?')">
                                    <input type="hidden" name="delete_id" value="<?php echo $article['id']; ?>">
                                    <button type="submit" class="btn btn-sm btn-danger" title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($articles)): ?>
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">
                                <i class="fas fa-inbox fa-2x mb-2"></i>
                                <p class="mb-0">Belum ada artikel</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>