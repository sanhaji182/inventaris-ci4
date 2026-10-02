<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
    <div>
        <h5 class="mb-0 fw-bold">Riwayat Penjualan</h5>
        <small class="text-muted">Transaksi kasir & rekap laba kotor per penjualan</small>
    </div>
    <a href="/penjualan/form" class="btn btn-primary btn-sm"><i class="bi bi-cart-check me-1"></i> Buka Kasir / Jual Baru</a>
</div>

<div class="card mb-3 p-3">
    <form method="GET" action="/penjualan" class="row g-2 align-items-center">
        <div class="col-md-6">
            <input type="text" name="cari" class="form-control form-control-sm" placeholder="Cari no nota atau catatan..." value="<?= esc($cari) ?>">
        </div>
        <div class="col-md-3 d-flex gap-2">
            <button type="submit" class="btn btn-sm btn-secondary w-100"><i class="bi bi-search me-1"></i> Filter</button>
            <?php if ($cari): ?>
                <a href="/penjualan" class="btn btn-sm btn-outline-secondary">Reset</a>
            <?php endif; ?>
        </div>
    </form>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover mb-0 align-middle">
            <thead class="table-light">
                <tr>
                    <th style="width:140px">No Nota</th>
                    <th>Tanggal</th>
                    <th class="text-center">Jml Unit</th>
                    <th class="text-end">Total Penjualan</th>
                    <th class="text-end">Total Laba</th>
                    <th class="text-center">Metode</th>
                    <th>Kasir</th>
                    <th class="text-end" style="width:120px">Aksi</th>
                </tr>
            </thead>
            <tbody>
            <?php if (empty($penjualan)): ?>
                <tr><td colspan="8" class="text-center py-4 text-muted">Belum ada transaksi penjualan.</td></tr>
            <?php else: ?>
                <?php foreach ($penjualan as $p): ?>
                <tr>
                    <td><a class="fw-semibold" href="/penjualan/detail/<?= (int) $p['id'] ?>"><code><?= esc($p['no']) ?></code></a></td>
                    <td><?= esc($p['tanggal']) ?></td>
                    <td class="text-center"><span class="badge badge-soft-info"><?= (int) $p['jumlah_unit'] ?> unit</span></td>
                    <td class="text-end fw-semibold"><?= rupiah($p['total']) ?></td>
                    <td class="text-end fw-semibold <?= (float) $p['total_laba'] >= 0 ? 'text-success' : 'text-danger' ?>">
                        <?= rupiah($p['total_laba']) ?>
                    </td>
                    <td class="text-center"><span class="badge badge-soft-secondary"><?= strtoupper(esc($p['metode'])) ?></span></td>
                    <td class="small"><?= esc($p['user_nama'] ?? '-') ?></td>
                    <td class="text-end">
                        <a href="/penjualan/detail/<?= (int) $p['id'] ?>" class="btn btn-sm btn-outline-secondary"><i class="bi bi-eye"></i></a>
                        <form action="/penjualan/batal/<?= (int) $p['id'] ?>" method="POST" class="d-inline" onsubmit="return confirm('Batalkan penjualan ini? Unit akan dikembalikan menjadi TERSEDIA di stok.')">
                            <?= csrf_field() ?>
                            <button class="btn btn-sm btn-outline-danger" title="Batalkan & kembalikan unit"><i class="bi bi-arrow-counterclockwise"></i></button>
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
