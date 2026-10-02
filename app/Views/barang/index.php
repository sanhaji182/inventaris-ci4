<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
    <div>
        <h5 class="mb-0 fw-bold">Katalog Barang</h5>
        <small class="text-muted">Master katalog produk (stok bersumber dari unit fisik)</small>
    </div>
    <a href="/barang/form" class="btn btn-primary btn-sm"><i class="bi bi-plus-lg me-1"></i> Tambah Barang</a>
</div>

<div class="card mb-3 p-3">
    <form method="GET" action="/barang" class="row g-2 align-items-center">
        <div class="col-md-5">
            <input type="text" name="cari" class="form-control form-control-sm" placeholder="Cari kode, nama, atau merek..." value="<?= esc($cari) ?>">
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
            <button type="submit" class="btn btn-sm btn-secondary w-100"><i class="bi bi-search me-1"></i> Filter</button>
            <?php if ($cari || $katPilih): ?>
                <a href="/barang" class="btn btn-sm btn-outline-secondary">Reset</a>
            <?php endif; ?>
        </div>
    </form>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover mb-0 align-middle">
            <thead class="table-light">
                <tr>
                    <th style="width:90px">Kode</th>
                    <th>Nama & Merek</th>
                    <th>Kategori</th>
                    <th class="text-end">Harga Jual Acuan</th>
                    <th class="text-center" style="width:110px">Tersedia</th>
                    <th class="text-center" style="width:90px">Terjual</th>
                    <th class="text-center" style="width:90px">Min</th>
                    <th class="text-end" style="width:140px">Aksi</th>
                </tr>
            </thead>
            <tbody>
            <?php if (empty($barang)): ?>
                <tr><td colspan="8" class="text-center py-4 text-muted">Belum ada barang di katalog.</td></tr>
            <?php else: ?>
                <?php foreach ($barang as $b): ?>
                <tr>
                    <td><code><?= esc($b['kode']) ?></code></td>
                    <td>
                        <div class="fw-semibold"><?= esc($b['nama']) ?></div>
                        <small class="text-muted"><?= esc($b['merek'] ?? '') ?> · <?= esc(ellipsize($b['spek'] ?? '', 40)) ?></small>
                    </td>
                    <td><span class="badge badge-soft-secondary"><?= esc($b['kategori_nama']) ?></span></td>
                    <td class="text-end fw-semibold"><?= rupiah($b['harga_jual']) ?></td>
                    <td class="text-center">
                        <?php if ((int) $b['stok_tersedia'] <= (int) $b['stok_min']): ?>
                            <span class="badge badge-soft-warning"><i class="bi bi-exclamation-triangle"></i> <?= (int) $b['stok_tersedia'] ?></span>
                        <?php else: ?>
                            <span class="badge badge-soft-success"><?= (int) $b['stok_tersedia'] ?></span>
                        <?php endif; ?>
                    </td>
                    <td class="text-center text-muted"><?= (int) $b['stok_terjual'] ?></td>
                    <td class="text-center text-muted small"><?= (int) $b['stok_min'] ?></td>
                    <td class="text-end">
                        <a href="/barang/riwayat/<?= (int) $b['id'] ?>" class="btn btn-sm btn-outline-info" title="Riwayat Unit"><i class="bi bi-clock-history"></i></a>
                        <a href="/barang/form/<?= (int) $b['id'] ?>" class="btn btn-sm btn-outline-secondary"><i class="bi bi-pencil"></i></a>
                        <form action="/barang/delete/<?= (int) $b['id'] ?>" method="POST" class="d-inline" onsubmit="return confirm('Hapus barang ini dari katalog?')">
                            <?= csrf_field() ?>
                            <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?= $this->endSection() ?>
