<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= esc($title ?? 'Inventaris') ?> · Sistem Inventaris BNSP</title>

    <!-- Google Fonts: Plus Jakarta Sans & JetBrains Mono -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;500;600&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 & Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <!-- Custom Clean Modern Light Design Tokens -->
    <link href="/css/app.css" rel="stylesheet">
</head>
<body>
    <!-- Mobile Backdrop -->
    <div class="sidebar-backdrop" id="sidebarBackdrop"></div>

    <!-- Sidebar Navigasi -->
    <aside class="sidebar" id="appSidebar">
        <div class="sidebar-header">
            <div class="brand-icon">
                <i class="bi bi-box-seam-fill"></i>
            </div>
            <div>
                <div class="brand-title">Inventaris</div>
                <div class="brand-subtitle">Standar BNSP</div>
            </div>
        </div>

        <nav class="sidebar-nav">
            <p class="nav-section-title">Menu Utama</p>
            <a href="/dashboard" class="nav-link <?= service('router')->controllerName() === '\App\Controllers\DashboardController' ? 'active' : '' ?>">
                <i class="bi bi-grid-1x2-fill"></i>
                <span>Dashboard</span>
            </a>

            <p class="nav-section-title">Inventaris Barang</p>
            <a href="/barang" class="nav-link <?= service('router')->controllerName() === '\App\Controllers\BarangController' ? 'active' : '' ?>">
                <i class="bi bi-boxes"></i>
                <span>Katalog Barang</span>
            </a>
            <a href="/kategori" class="nav-link <?= service('router')->controllerName() === '\App\Controllers\KategoriController' ? 'active' : '' ?>">
                <i class="bi bi-tags-fill"></i>
                <span>Kategori</span>
            </a>
            <a href="/stok" class="nav-link <?= service('router')->controllerName() === '\App\Controllers\StokController' ? 'active' : '' ?>">
                <i class="bi bi-arrow-left-right"></i>
                <span>Keluar / Masuk Stok</span>
            </a>

            <?php if (session()->get('user_role') === 'admin'): ?>
            <p class="nav-section-title">Administrasi</p>
            <a href="/user" class="nav-link <?= service('router')->controllerName() === '\App\Controllers\UserController' ? 'active' : '' ?>">
                <i class="bi bi-shield-lock-fill"></i>
                <span>Hak Akses User</span>
            </a>
            <?php endif; ?>
        </nav>

        <div class="sidebar-footer">
            <div class="d-flex align-items-center gap-2">
                <span class="badge-dot pulse badge-soft-success" style="font-size:0.7rem;">Live</span>
                <span class="small text-muted" style="font-size:0.75rem;">CI4 v4.7.4</span>
            </div>
            <a href="/logout" class="btn btn-sm btn-link p-0 text-secondary" title="Keluar" onclick="return confirm('Keluar dari aplikasi?')">
                <i class="bi bi-box-arrow-right fs-6"></i>
            </a>
        </div>
    </aside>

    <!-- Main Content Area -->
    <div class="main-wrapper">
        <!-- Top Navbar -->
        <header class="topbar">
            <div class="d-flex align-items-center gap-3">
                <button class="btn btn-sm btn-light d-lg-none" id="btnToggleSidebar">
                    <i class="bi bi-list fs-5"></i>
                </button>
                <div class="topbar-title-wrap">
                    <h1 class="topbar-title h5 mb-0"><?= esc($title ?? 'Inventaris') ?></h1>
                </div>
            </div>

            <div class="d-flex align-items-center gap-3">
                <!-- User Profile Badge -->
                <div class="dropdown">
                    <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle gap-2 text-dark" data-bs-toggle="dropdown">
                        <div class="avatar-circle">
                            <?= strtoupper(substr(session()->get('user_nama') ?? 'U', 0, 1)) ?>
                        </div>
                        <div class="d-none d-md-block text-start">
                            <div class="fw-semibold small leading-tight"><?= esc(session()->get('user_nama')) ?></div>
                            <span class="badge badge-soft-primary" style="font-size:0.68rem;">
                                <?= ucfirst(esc(session()->get('user_role'))) ?>
                            </span>
                        </div>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 mt-2">
                        <li class="px-3 py-2 border-bottom">
                            <div class="small fw-bold"><?= esc(session()->get('user_nama')) ?></div>
                            <div class="text-muted small">@<?= esc(session()->get('user_name')) ?></div>
                        </li>
                        <li>
                            <a class="dropdown-item text-danger py-2" href="/logout" onclick="return confirm('Keluar dari aplikasi?')">
                                <i class="bi bi-box-arrow-right me-2"></i> Keluar
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </header>

        <!-- Main Body -->
        <main class="content-body">
            <!-- Flash Alerts -->
            <?php if (session()->getFlashdata('sukses')): ?>
                <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2 mb-4" role="alert">
                    <i class="bi bi-check-circle-fill fs-5"></i>
                    <div><?= esc(session()->getFlashdata('sukses')) ?></div>
                    <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center gap-2 mb-4" role="alert">
                    <i class="bi bi-exclamation-triangle-fill fs-5"></i>
                    <div><?= esc(session()->getFlashdata('error')) ?></div>
                    <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('errors')): ?>
                <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                    <div class="fw-bold mb-1"><i class="bi bi-exclamation-triangle-fill me-1"></i> Periksa kembali isian form:</div>
                    <ul class="mb-0 ps-3 small">
                        <?php foreach (session()->getFlashdata('errors') as $err): ?>
                            <li><?= esc($err) ?></li>
                        <?php endforeach; ?>
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?= $this->renderSection('content') ?>
        </main>
    </div>

    <!-- Toast Container -->
    <div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 1090;">
        <div id="liveToast" class="toast align-items-center border-0 shadow-lg" role="alert">
            <div class="d-flex">
                <div class="toast-body small fw-semibold" id="toastMessage"></div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="/js/app.js"></script>
    <?= $this->renderSection('scripts') ?>
</body>
</html>
