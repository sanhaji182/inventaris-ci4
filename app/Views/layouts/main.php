<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= esc($title ?? 'Inventaris') ?> · Sistem Inventaris</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        :root {
            --sidebar-w: 240px;
            --topbar-h: 56px;
            --bg-body: #f5f6fa;
            --bg-sidebar: #1e293b;
        }
        body { background: var(--bg-body); font-size: 0.925rem; }
        .sidebar {
            position: fixed; top: 0; left: 0; bottom: 0;
            width: var(--sidebar-w); background: var(--bg-sidebar);
            color: #cbd5e1; z-index: 1030; overflow-y: auto;
            transition: margin-left .25s;
        }
        .sidebar .brand {
            display: flex; align-items: center; gap: .6rem;
            padding: 1rem 1.25rem; color: #fff; font-weight: 600;
            border-bottom: 1px solid rgba(255,255,255,.08);
        }
        .sidebar .nav-link {
            color: #cbd5e1; padding: .55rem 1.25rem; display: flex;
            align-items: center; gap: .65rem; border-radius: 0;
        }
        .sidebar .nav-link:hover { background: rgba(255,255,255,.06); color: #fff; }
        .sidebar .nav-link.active { background: #3b82f6; color: #fff; }
        .sidebar .section-label {
            font-size: .68rem; text-transform: uppercase; letter-spacing: .08em;
            color: #64748b; padding: 1rem 1.25rem .35rem; margin: 0;
        }
        .main {
            margin-left: var(--sidebar-w);
            min-height: 100vh;
        }
        .topbar {
            height: var(--topbar-h); background: #fff;
            border-bottom: 1px solid #e2e8f0;
            display: flex; align-items: center; justify-content: space-between;
            padding: 0 1.25rem; position: sticky; top: 0; z-index: 1020;
        }
        .content { padding: 1.25rem; }
        @media (max-width: 991.98px) {
            .sidebar { margin-left: calc(-1 * var(--sidebar-w)); }
            .sidebar.show { margin-left: 0; }
            .main { margin-left: 0; }
        }
        .card { border: none; box-shadow: 0 1px 3px rgba(0,0,0,.07); border-radius: .6rem; }
        .table > :not(caption) > * > * { padding: .6rem .75rem; }
        .badge-soft-success { background:#dcfce7; color:#166534; }
        .badge-soft-danger  { background:#fee2e2; color:#991b1b; }
        .badge-soft-warning { background:#fef3c7; color:#92400e; }
        .badge-soft-info    { background:#dbeafe; color:#1e40af; }
        .badge-soft-secondary { background:#e2e8f0; color:#334155; }
        .stat-card .label { color:#64748b; font-size:.8rem; }
        .stat-card .value { font-size:1.5rem; font-weight:700; }
        .stat-card .icon {
            width:42px;height:42px;border-radius:.55rem;display:flex;
            align-items:center;justify-content:center;font-size:1.2rem;
        }
    </style>
</head>
<body>
    <nav class="sidebar" id="sidebar">
        <div class="brand"><i class="bi bi-box-seam"></i> Inventaris</div>
        <ul class="nav flex-column pb-3">
            <p class="section-label">Utama</p>
            <li><a class="nav-link <?= url_is('dashboard') || url_is('/') ? 'active' : '' ?>" href="/dashboard"><i class="bi bi-speedometer2"></i> Dashboard</a></li>

            <p class="section-label">Transaksi</p>
            <li><a class="nav-link <?= url_is('pembelian*') ? 'active' : '' ?>" href="/pembelian"><i class="bi bi-cart-plus"></i> Pembelian</a></li>
            <li><a class="nav-link <?= url_is('penjualan*') ? 'active' : '' ?>" href="/penjualan"><i class="bi bi-cash-coin"></i> Penjualan</a></li>
            <li><a class="nav-link <?= url_is('utang*') ? 'active' : '' ?>" href="/utang"><i class="bi bi-credit-card-2-front"></i> Utang</a></li>

            <p class="section-label">Master</p>
            <li><a class="nav-link <?= url_is('unit*') ? 'active' : '' ?>" href="/unit"><i class="bi bi-phone"></i> Unit Stok</a></li>
            <li><a class="nav-link <?= url_is('barang*') ? 'active' : '' ?>" href="/barang"><i class="bi bi-collection"></i> Barang</a></li>
            <li><a class="nav-link <?= url_is('kategori*') ? 'active' : '' ?>" href="/kategori"><i class="bi bi-tags"></i> Kategori</a></li>
            <li><a class="nav-link <?= url_is('supplier*') ? 'active' : '' ?>" href="/supplier"><i class="bi bi-truck"></i> Supplier</a></li>

            <p class="section-label">Laporan</p>
            <li><a class="nav-link <?= url_is('laporan*') ? 'active' : '' ?>" href="/laporan"><i class="bi bi-graph-up"></i> Laporan</a></li>

            <?php if (session('role') === 'admin'): ?>
            <p class="section-label">Admin</p>
            <li><a class="nav-link <?= url_is('user*') ? 'active' : '' ?>" href="/user"><i class="bi bi-people"></i> Pengguna</a></li>
            <?php endif; ?>
        </ul>
    </nav>

    <div class="main">
        <div class="topbar">
            <button class="btn btn-sm btn-outline-secondary d-lg-none" id="btnSidebar"><i class="bi bi-list"></i></button>
            <div class="d-none d-md-block fw-semibold"><?= esc($title ?? '') ?></div>
            <div class="dropdown">
                <a class="d-flex align-items-center gap-2 text-decoration-none" href="#" data-bs-toggle="dropdown">
                    <span class="avatar bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width:34px;height:34px">
                        <?= esc(strtoupper(substr(session('nama') ?? 'U', 0, 1))) ?>
                    </span>
                    <div class="d-none d-md-block">
                        <div class="fw-semibold small"><?= esc(session('nama')) ?></div>
                        <div class="text-muted" style="font-size:.72rem"><?= esc(session('role')) ?></div>
                    </div>
                </a>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="/logout"><i class="bi bi-box-arrow-right me-2"></i>Keluar</a></li>
                </ul>
                <noscript><a class="btn btn-sm btn-link" href="/logout">Keluar</a></noscript>
            </div>
        </div>

        <div class="content">
            <?php if ($flash = session()->getFlashdata('sukses')): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?= esc($flash) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            <?php if ($flash = session()->getFlashdata('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?= esc($flash) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?= $this->renderSection('content') ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('btnSidebar')?.addEventListener('click', () => {
            document.getElementById('sidebar').classList.toggle('show');
        });
    </script>
    <?= $this->renderSection('scripts') ?>
</body>
</html>
