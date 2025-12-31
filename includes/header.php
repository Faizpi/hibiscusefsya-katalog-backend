<?php
/**
 * Admin Header Template
 * Hibiscus Efsya Katalog - SB Admin 2 Style
 */
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title><?php echo $pageTitle ?? 'Admin'; ?> - Hibiscus Efsya Admin</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">

    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- DataTables -->
    <link href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap4.min.css" rel="stylesheet">

    <!-- DataTables -->
    <link href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap4.min.css" rel="stylesheet">

    <!-- Custom styles -->
    <?php
    // Determine asset path relative to current script
    $assetPrefix = file_exists(__DIR__ . '/../assets/css/admin.css') ? 'assets/' : '../assets/';
    if (file_exists('assets/css/admin.css')) {
        $assetPrefix = 'assets/';
    } elseif (file_exists('../assets/css/admin.css')) {
        $assetPrefix = '../assets/';
    } else {
        $assetPrefix = '../../assets/'; // Fallback for deeper levels
    }

    // Version for cache busting
    $ver = time();
    ?>
    <link href="<?php echo $assetPrefix; ?>css/admin.css?v=<?php echo $ver; ?>" rel="stylesheet">
</head>

<body>
    <!-- Sidebar Overlay -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>
    
    <!-- Sidebar -->
    <nav class="sidebar" id="sidebar">
        <button class="sidebar-close" id="sidebarClose">
            <i class="fas fa-times"></i>
        </button>
        <div class="sidebar-brand">
            <div class="sidebar-brand-icon">
                <img src="<?php echo $assetPrefix; ?>img/logo.png" alt="Logo">
            </div>
            <div class="sidebar-brand-text">Hibiscus Efsya</div>
        </div>

        <div class="sidebar-nav">
            <div class="nav-item">
                <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'index.php' && strpos($_SERVER['PHP_SELF'], 'products') === false && strpos($_SERVER['PHP_SELF'], 'categories') === false && strpos($_SERVER['PHP_SELF'], 'settings') === false ? 'active' : ''; ?>"
                    href="/index.php">
                    <i class="fas fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>
            </div>

            <div class="sidebar-heading">Manajemen</div>

            <div class="nav-item">
                <a class="nav-link <?php echo strpos($_SERVER['PHP_SELF'], 'products') !== false ? 'active' : ''; ?>"
                    href="/products/">
                    <i class="fas fa-box"></i>
                    <span>Produk</span>
                </a>
            </div>

            <div class="nav-item">
                <a class="nav-link <?php echo strpos($_SERVER['PHP_SELF'], 'categories') !== false ? 'active' : ''; ?>"
                    href="/categories/">
                    <i class="fas fa-tags"></i>
                    <span>Kategori</span>
                </a>
            </div>

            <div class="nav-item">
                <a class="nav-link <?php echo strpos($_SERVER['PHP_SELF'], 'articles') !== false ? 'active' : ''; ?>"
                    href="/articles/">
                    <i class="fas fa-newspaper"></i>
                    <span>Tips & Artikel</span>
                </a>
            </div>

            <div class="nav-item">
                <a class="nav-link <?php echo strpos($_SERVER['PHP_SELF'], 'settings') !== false ? 'active' : ''; ?>"
                    href="/settings/">
                    <i class="fas fa-cog"></i>
                    <span>Pengaturan Website</span>
                </a>
            </div>

            <div class="sidebar-heading">Lainnya</div>

            <div class="nav-item">
                <a class="nav-link" href="<?php echo FRONTEND_URL; ?>" target="_blank">
                    <i class="fas fa-external-link-alt"></i>
                    <span>Lihat Katalog</span>
                </a>
            </div>

            <div class="nav-item">
                <a class="nav-link" href="/logout.php">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Logout</span>
                </a>
            </div>
        </div>
    </nav>

    <!-- Content Wrapper -->

    <div class="content-wrapper">
        <!-- Topbar -->
        <nav class="topbar">
            <button class="topbar-toggler" id="sidebarToggle">
                <i class="fas fa-bars"></i>
            </button>

            <div class="topbar-user">
                <div class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                        <div class="user-wrapper">
                            <div class="user-info">
                                <span
                                    class="user-name"><?php echo sanitize($_SESSION['admin_name'] ?? 'Admin'); ?></span>
                                <span class="user-role">Administrator</span>
                            </div>
                            <div class="user-avatar">
                                <?php echo strtoupper(substr($_SESSION['admin_name'] ?? 'A', 0, 1)); ?>
                            </div>
                        </div>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in">
                        <a class="dropdown-item" href="/logout.php">
                            <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                            Logout
                        </a>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Main Content -->
        <div class="main-content">