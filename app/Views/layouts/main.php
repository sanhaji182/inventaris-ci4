<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= esc($title ?? 'Inventaris') ?> · Sistem Inventaris Unit & Stok</title>
    
    <!-- Fonts & Bootstrap 5 -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;500;600&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    
    <!-- Custom Modern Design System -->
    <link href="/css/app.css" rel="stylesheet">

    <!-- Anti-flicker theme init -->
    <script>
        (function() {
            const saved = localStorage.getItem('inventaris_theme');
            const theme = saved || (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
            if (theme === 'dark') {
                document.documentElement.setAttribute('data-theme', 'dark');
                document.documentElement.setAttribute('data-bs-theme', 'dark');
            } else {
                document.documentElement.setAttribute('data-bs-theme', 'light');
            }
        })();
    </script>
</head>
<body>
    <!-- Mobile Backdrop -->
    <div class="sidebar-backdrop" id="sidebarBackdrop"></div>

    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <div class="brand-icon">
                <i class="bi bi-box-seam"></i>
            </div>
            <div>
                <div class="brand-title">Inventaris</div>
                <div class="brand-subtitle">Tracking & Kasir</div>
            </div>
        </div>

        <nav class="sidebar-nav">
            <p class="nav-section-title">Ringkasan</p>
            <a class="nav-link <?= url_is('dashboard') || url_is('/') ? 'active' : '' ?>" href="/dashboard">
                <i class="bi bi-grid-1x2-fill"></i>
                <span>Dashboard</span>
            </a>

            <p class="nav-section-title">Aktivitas Toko</p>
            <a class="nav-link <?= url_is('penjualan*') ? 'active' : '' ?>" href="/penjualan">
                <i class="bi bi-receipt-cutoff"></i>
                <span>Kasir & Penjualan</span>
            </a>
            <a class="nav-link <?= url_is('pembelian*') ? 'active' : '' ?>" href="/pembelian">
                <i class="bi bi-bag-plus-fill"></i>
                <span>Pembelian Stok</span>
            </a>
            <a class="nav-link <?= url_is('utang*') ? 'active' : '' ?>" href="/utang">
                <i class="bi bi-wallet2"></i>
                <span>Utang & Cicilan</span>
            </a>

            <p class="nav-section-title">Inventaris Fisik</p>
            <a class="nav-link <?= url_is('unit*') ? 'active' : '' ?>" href="/unit">
                <i class="bi bi-phone-fill"></i>
                <span>Unit Fisik (IMEI)</span>
            </a>
            <a class="nav-link <?= url_is('barang*') ? 'active' : '' ?>" href="/barang">
                <i class="bi bi-boxes"></i>
                <span>Katalog Barang</span>
            </a>
            <a class="nav-link <?= url_is('kategori*') ? 'active' : '' ?>" href="/kategori">
                <i class="bi bi-tags-fill"></i>
                <span>Kategori</span>
            </a>
            <a class="nav-link <?= url_is('supplier*') ? 'active' : '' ?>" href="/supplier">
                <i class="bi bi-truck"></i>
                <span>Pemasok (Supplier)</span>
            </a>

            <p class="nav-section-title">Laporan Keuangan</p>
            <a class="nav-link <?= url_is('laporan*') ? 'active' : '' ?>" href="/laporan">
                <i class="bi bi-pie-chart-fill"></i>
                <span>Laba Rugi & Rekap</span>
            </a>

            <?php if (session('role') === 'admin'): ?>
            <p class="nav-section-title">Administrasi</p>
            <a class="nav-link <?= url_is('user*') ? 'active' : '' ?>" href="/user">
                <i class="bi bi-shield-lock-fill"></i>
                <span>Manajemen User</span>
            </a>
            <?php endif; ?>
        </nav>

        <div class="sidebar-footer">
            <div class="d-flex align-items-center gap-2">
                <span class="badge-dot pulse badge-soft-success" style="font-size:0.7rem;">Live</span>
                <span class="small text-muted" style="font-size:0.75rem;">CI4 v4.7.4</span>
            </div>
            <a href="https://github.com/sanhaji182/inventaris-ci4" target="_blank" class="text-muted" title="Source Code">
                <i class="bi bi-github"></i>
            </a>
        </div>
    </aside>

    <!-- Main Wrapper -->
    <div class="main-wrapper">
        <!-- Topbar -->
        <header class="topbar">
            <div class="d-flex align-items-center gap-3">
                <button class="btn btn-sm btn-outline-secondary d-lg-none" id="btnSidebar" type="button" aria-label="Toggle Sidebar">
                    <i class="bi bi-list fs-5"></i>
                </button>
                <h1 class="page-title h5 mb-0">
                    <?= esc($title ?? 'Sistem Inventaris') ?>
                </h1>
            </div>

            <div class="topbar-actions">
                <!-- Quick Search Input -->
                <div class="quick-search-box d-none d-md-block">
                    <i class="bi bi-search"></i>
                    <input type="text" id="globalQuickSearch" placeholder="Cari cepat... (Ctrl+K)" autocomplete="off">
                </div>

                <!-- Theme Toggle Button -->
                <button class="btn-theme-toggle" type="button" title="Ganti Mode Terang/Gelap" aria-label="Toggle Theme">
                    <i class="bi bi-moon-stars text-secondary"></i>
                </button>

                <!-- User Profile Pill -->
                <div class="dropdown">
                    <a class="user-menu-pill" href="#" data-bs-toggle="dropdown" aria-expanded="false">
                        <div class="avatar-initial">
                            <?= esc(strtoupper(substr(session('nama') ?? 'U', 0, 1))) ?>
                        </div>
                        <div class="d-none d-sm-block text-start pe-1">
                            <div class="fw-semibold small lh-1 mb-1 text-truncate" style="max-width:120px;">
                                <?= esc(session('nama')) ?>
                            </div>
                            <span class="badge badge-soft-primary px-1 py-0" style="font-size:0.65rem;">
                                <?= esc(ucfirst(session('role') ?? 'staff')) ?>
                            </span>
                        </div>
                        <i class="bi bi-chevron-down text-muted small ms-1"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0" style="border-radius: var(--radius-md); min-width: 190px;">
                        <li class="px-3 py-2 border-bottom">
                            <div class="fw-bold small"><?= esc(session('nama')) ?></div>
                            <div class="text-muted" style="font-size:0.75rem;">@<?= esc(session('username')) ?></div>
                        </li>
                        <li><a class="dropdown-item py-2 text-danger" href="/logout"><i class="bi bi-box-arrow-right me-2"></i>Keluar</a></li>
                    </ul>
                </div>
            </div>
        </header>

        <!-- Content Body -->
        <main class="content-body">
            <?= $this->renderSection('content') ?>
        </main>
    </div>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="/js/app.js"></script>

    <!-- Flash Notification via Toast -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            <?php if ($flash = session()->getFlashdata('sukses')): ?>
                window.showToast(<?= json_encode($flash) ?>, 'success');
            <?php endif; ?>
            <?php if ($flash = session()->getFlashdata('error')): ?>
                window.showToast(<?= json_encode($flash) ?>, 'error');
            <?php endif; ?>
        });
    </script>

    <?= $this->renderSection('scripts') ?>
</body>
</html>
