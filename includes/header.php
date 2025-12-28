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
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- DataTables -->
    <link href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap4.min.css" rel="stylesheet">
    
    <!-- Custom styles -->
    <style>
        :root {
            --primary: #DC2626;
            --primary-dark: #B91C1C;
            --secondary: #991B1B;
            --dark: #1A1A1A;
            --light: #FFFFFF;
            --sidebar-bg: #1A1A1A;
        }
        
        * {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
        
        /* Sidebar */
        .sidebar {
            width: 250px;
            min-height: 100vh;
            background: var(--sidebar-bg);
            position: fixed;
            left: 0;
            top: 0;
            z-index: 100;
            transition: all 0.3s;
        }
        
        .sidebar.toggled {
            margin-left: -250px;
        }
        
        .sidebar-brand {
            padding: 1.5rem 1rem;
            text-align: center;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        
        .sidebar-brand h1 {
            color: white;
            font-size: 1.25rem;
            font-weight: 700;
            margin: 0;
        }
        
        .sidebar-brand small {
            color: rgba(255,255,255,0.5);
            font-size: 0.75rem;
        }
        
        .sidebar-nav {
            padding: 1rem 0;
        }
        
        .nav-item {
            padding: 0 1rem;
        }
        
        .nav-link {
            color: rgba(255,255,255,0.7) !important;
            padding: 0.75rem 1rem;
            border-radius: 8px;
            margin-bottom: 0.25rem;
            display: flex;
            align-items: center;
            transition: all 0.3s;
        }
        
        .nav-link:hover {
            color: white !important;
            background: rgba(255,255,255,0.1);
        }
        
        .nav-link.active {
            color: white !important;
            background: var(--primary);
        }
        
        .nav-link i {
            width: 20px;
            margin-right: 10px;
            text-align: center;
        }
        
        .sidebar-heading {
            color: rgba(255,255,255,0.3);
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.1rem;
            padding: 1rem 1rem 0.5rem;
        }
        
        /* Content Wrapper */
        .content-wrapper {
            margin-left: 250px;
            min-height: 100vh;
            background: var(--light);
            transition: all 0.3s;
        }
        
        .sidebar.toggled + .content-wrapper {
            margin-left: 0;
        }
        
        /* Top Navbar */
        .topbar {
            background: white;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            padding: 1rem 1.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 99;
        }
        
        .topbar-toggler {
            background: none;
            border: none;
            font-size: 1.25rem;
            color: var(--dark);
            cursor: pointer;
            padding: 0.5rem;
            border-radius: 8px;
        }
        
        .topbar-toggler:hover {
            background: var(--light);
        }
        
        .topbar-user {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        
        .topbar-user .dropdown-toggle {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: var(--dark);
            text-decoration: none;
            font-weight: 500;
        }
        
        .topbar-user .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: var(--primary);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
        }
        
        /* Main Content */
        .main-content {
            padding: 1.5rem;
        }
        
        /* Cards */
        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        
        .card-header {
            background: white;
            border-bottom: 1px solid #E5E7EB;
            border-radius: 12px 12px 0 0 !important;
            padding: 1rem 1.25rem;
        }
        
        /* Buttons */
        .btn-primary {
            background: var(--primary);
            border-color: var(--primary);
        }
        
        .btn-primary:hover {
            background: var(--primary-dark);
            border-color: var(--primary-dark);
        }
        
        .btn-outline-primary {
            color: var(--primary);
            border-color: var(--primary);
        }
        
        .btn-outline-primary:hover {
            background: var(--primary);
            border-color: var(--primary);
            color: white;
        }
        
        /* Border Left Cards */
        .border-left-primary {
            border-left: 4px solid var(--primary) !important;
        }
        
        .border-left-success {
            border-left: 4px solid #28a745 !important;
        }
        
        .border-left-warning {
            border-left: 4px solid #ffc107 !important;
        }
        
        .border-left-info {
            border-left: 4px solid #17a2b8 !important;
        }
        
        .text-primary {
            color: var(--primary) !important;
        }
        
        .bg-gradient-primary {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
        }
        
        /* Tables */
        .table th {
            border-top: none;
            font-weight: 600;
            color: var(--secondary);
            font-size: 0.85rem;
            text-transform: uppercase;
        }
        
        /* Page Title */
        .h3 {
            font-weight: 700;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                margin-left: -250px;
            }
            
            .sidebar.toggled {
                margin-left: 0;
            }
            
            .content-wrapper {
                margin-left: 0;
            }
            
            .sidebar.toggled + .content-wrapper {
                margin-left: 250px;
            }
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <nav class="sidebar" id="sidebar">
        <div class="sidebar-brand">
            <h1>🌺 Hibiscus Efsya</h1>
            <small>part of M.B.K Indonesia</small>
        </div>
        
        <div class="sidebar-nav">
            <div class="nav-item">
                <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'index.php' && strpos($_SERVER['PHP_SELF'], 'products') === false && strpos($_SERVER['PHP_SELF'], 'categories') === false && strpos($_SERVER['PHP_SELF'], 'settings') === false ? 'active' : ''; ?>" href="/index.php">
                    <i class="fas fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>
            </div>
            
            <div class="sidebar-heading">Manajemen</div>
            
            <div class="nav-item">
                <a class="nav-link <?php echo strpos($_SERVER['PHP_SELF'], 'products') !== false ? 'active' : ''; ?>" href="/products/">
                    <i class="fas fa-box"></i>
                    <span>Produk</span>
                </a>
            </div>
            
            <div class="nav-item">
                <a class="nav-link <?php echo strpos($_SERVER['PHP_SELF'], 'categories') !== false ? 'active' : ''; ?>" href="/categories/">
                    <i class="fas fa-tags"></i>
                    <span>Kategori</span>
                </a>
            </div>
            
            <div class="nav-item">
                <a class="nav-link <?php echo strpos($_SERVER['PHP_SELF'], 'settings') !== false ? 'active' : ''; ?>" href="/settings/">
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
                <a class="nav-link" href="logout.php">
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
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <div class="user-avatar">
                            <?php echo strtoupper(substr($_SESSION['admin_name'] ?? 'A', 0, 1)); ?>
                        </div>
                        <span class="d-none d-md-inline"><?php echo sanitize($_SESSION['admin_name'] ?? 'Admin'); ?></span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right">
                        <a class="dropdown-item" href="logout.php">
                            <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                            Logout
                        </a>
                    </div>
                </div>
            </div>
        </nav>
        
        <!-- Main Content -->
        <div class="main-content">
