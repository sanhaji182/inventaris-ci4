<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
    <div>
        <h5 class="mb-0 fw-bold">Riwayat Pembelian</h5>
        <small class="text-muted">Setiap transaksi pembelian otomatis menghasilkan unit fisik baru</small>
    </div>
    <a href="/pembelian/form" class="btn btn-primary btn-sm"><i class="bi bi-plus-lg me-1"></i> Input Pembelian</a>
</div>

<div class="card mb-3 p-3">
    <form method="GET" action="/pembelian" class="row g-2 align-items-center">
        <div class="col-md-6">
            <input type="text" name="cari" class="form-control form-control-sm" placeholder="Cari no faktur atau supplier..." value="<?= esc($cari) ?>">
        </div>
        <div class="col-md-3 d-flex gap-2">
            <button type="submit" class="btn btn-sm btn-secondary w-100"><i class="bi bi-search me-1"></i> Filter</button>
            <?php if ($cari): ?>
                <a href="/pembelian" class="btn btn-sm btn-outline-secondary">Reset</a>
            <?php endif; ?>
        </div>
    </form>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover mb-0 align-middle">
            <thead class="table-light">
                <tr>
                    <th style="width:140px">No Faktur</th>
                    <th>Tanggal</th>
                    <th>Supplier</th>
                    <th>Sumber</th>
                    <th class="text-center">Jml Unit</th>
                    <th class="text-end">Total</th>
                    <th class="text-end">Sisa Utang</th>
                    <th class="text-center">Metode</th>
                    <th class="text-end" style="width:120px">Aksi</th>
                </tr>
            </thead>
            <tbody>
            <?php if (empty($pembelian)): ?>
                <tr><td colspan="9" class="text-center py-4 text-muted">Belum ada transaksi pembelian.</td></tr>
            <?php else: ?>
                <?php foreach ($pembelian as $p): ?>
                <tr>
                    <td><a class="fw-semibold" href="/pembelian/detail/<?= (int) $p['id'] ?>"><code><?= esc($p['no']) ?></code></a></td>
                    <td><?= esc($p['tanggal']) ?></td>
                    <td><?= esc($p['supplier_nama'] ?? '-') ?></td>
                    <td>
                        <span class="badge badge-soft-secondary"><?= esc($p['sumber']) ?></span>
                        <?php if ($p['url']): ?>
                            <a href="<?= esc($p['url']) ?>" target="_blank" class="small"><i class="bi bi-box-arrow-up-right"></i></a>
                        <?php endif; ?>
                    </td>
                    <td class="text-center"><span class="badge badge-soft-info"><?= (int) $p['jumlah_unit'] ?> unit</span></td>
                    <td class="text-end fw-semibold"><?= rupiah($p['total']) ?></td>
                    <td class="text-end">
                        <?php if ((float) $p['sisa'] > 0): ?>
                            <span class="badge badge-soft-danger"><?= rupiah($p['sisa']) ?></span>
                        <?php else: ?>
                            <span class="badge badge-soft-success">Lunas</span>
                        <?php endif; ?>
                    </td>
                    <td class="text-center"><span class="badge badge-soft-secondary"><?= ucfirst(esc($p['metode'])) ?></span></td>
                    <td class="text-end">
                        <a href="/pembelian/detail/<?= (int) $p['id'] ?>" class="btn btn-sm btn-outline-secondary"><i class="bi bi-eye"></i></a>
                        <form action="/pembelian/delete/<?= (int) $p['id'] ?>" method="POST" class="d-inline" onsubmit="return confirm('Batalkan pembelian ini? Unit yang belum terjual akan ikut terhapus.')">
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
