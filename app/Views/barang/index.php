<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
    <div>
        <h4 class="mb-0 fw-bold">Katalog Model Barang</h4>
        <small class="text-muted">Master katalog produk (kuantitas stok dihitung otomatis dari unit fisik yang aktif)</small>
    </div>
    <a href="/barang/form" class="btn btn-primary btn-sm">
        <i class="bi bi-plus-lg me-1"></i> Tambah Model Barang
    </a>
</div>

<!-- Filter Card -->
<div class="card mb-3 p-3">
    <form method="GET" action="/barang" class="row g-2 align-items-center">
        <div class="col-md-5">
            <input type="text" name="cari" class="form-control form-control-sm" placeholder="Cari nama, merek, atau kode..." value="<?= esc($cari) ?>">
        </div>
        <div class="col-md-4">
            <select name="kategori" class="form-select form-select-sm">
                <option value="">Semua Kategori</option>
                <?php foreach ($kategori as $k): ?>
                    <option value="<?= (int) $k['id'] ?>" <?= $katPilih === (int) $k['id'] ? 'selected' : '' ?>>
                        <?= esc($k['nama']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-3 d-flex gap-2">
            <button type="submit" class="btn btn-sm btn-primary w-100"><i class="bi bi-funnel me-1"></i> Filter</button>
            <?php if ($cari || $katPilih): ?>
                <a href="/barang" class="btn btn-sm btn-outline-secondary">Reset</a>
            <?php endif; ?>
        </div>
    </form>
</div>

<!-- Table Card -->
<div class="card">
    <div class="table-filter-bar">
        <div class="d-flex align-items-center gap-2">
            <i class="bi bi-boxes text-primary fs-5"></i>
            <span class="fw-bold">Daftar Katalog Produk</span>
            <span class="badge badge-soft-primary ms-1" data-table-count="#tableBarang"><?= count($barang) ?> model</span>
        </div>
        <div class="table-search-input">
            <i class="bi bi-search"></i>
            <input type="text" placeholder="Filter cepat barang..." data-table-search="#tableBarang">
        </div>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0" id="tableBarang">
            <thead>
                <tr>
                    <th style="width:100px">Kode</th>
                    <th>Nama & Spesifikasi</th>
                    <th>Kategori</th>
                    <th class="text-end">Harga Acuan Jual</th>
                    <th class="text-center" style="width:110px">Stok Siap</th>
                    <th class="text-center" style="width:90px">Terjual</th>
                    <th class="text-center" style="width:90px">Batas Min</th>
                    <th class="text-end" style="width:140px">Aksi</th>
                </tr>
            </thead>
            <tbody>
            <?php if (empty($barang)): ?>
                <tr><td colspan="8" class="text-center py-4 text-muted">Belum ada barang di katalog.</td></tr>
            <?php else: ?>
                <?php foreach ($barang as $b): ?>
                <tr>
                    <td>
                        <span class="chip-imei" data-copy="<?= esc($b['kode']) ?>">
                            <i class="bi bi-tag"></i>
                            <?= esc($b['kode']) ?>
                        </span>
                    </td>
                    <td>
                        <div class="fw-bold"><?= esc($b['nama']) ?></div>
                        <div class="small text-muted"><?= esc($b['merek'] ?? '') ?> · <?= esc(ellipsize($b['spek'] ?? '', 45)) ?></div>
                    </td>
                    <td><span class="badge badge-soft-secondary"><?= esc($b['kategori_nama']) ?></span></td>
                    <td class="text-end font-monospace fw-semibold"><?= rupiah($b['harga_jual']) ?></td>
                    <td class="text-center">
                        <?php if ((int) $b['stok_tersedia'] <= (int) $b['stok_min']): ?>
                            <span class="badge-dot badge-soft-warning pulse">
                                <?= (int) $b['stok_tersedia'] ?> unit
                            </span>
                        <?php else: ?>
                            <span class="badge-dot badge-soft-success">
                                <?= (int) $b['stok_tersedia'] ?> unit
                            </span>
                        <?php endif; ?>
                    </td>
                    <td class="text-center font-monospace text-muted"><?= (int) $b['stok_terjual'] ?></td>
                    <td class="text-center text-muted small font-monospace"><?= (int) $b['stok_min'] ?></td>
                    <td class="text-end">
                        <div class="btn-group btn-group-sm">
                            <a href="/barang/riwayat/<?= (int) $b['id'] ?>" class="btn btn-outline-info" title="Riwayat Unit"><i class="bi bi-clock-history"></i></a>
                            <a href="/barang/form/<?= (int) $b['id'] ?>" class="btn btn-outline-secondary" title="Edit"><i class="bi bi-pencil"></i></a>
                            <form action="/barang/delete/<?= (int) $b['id'] ?>" method="POST" class="d-inline" onsubmit="return confirm('Hapus barang ini dari katalog?')">
                                <?= csrf_field() ?>
                                <button class="btn btn-outline-danger" title="Hapus"><i class="bi bi-trash"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?= $this->endSection() ?>
