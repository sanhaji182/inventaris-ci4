<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h5 class="mb-0 fw-bold">Daftar Supplier</h5>
        <small class="text-muted">Pemasok dan status utang berjalan</small>
    </div>
    <a href="/supplier/form" class="btn btn-primary btn-sm"><i class="bi bi-plus-lg me-1"></i> Tambah Supplier</a>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover mb-0 align-middle">
            <thead class="table-light">
                <tr>
                    <th style="width:110px">Kode</th>
                    <th>Nama Supplier</th>
                    <th>Telepon</th>
                    <th>Alamat</th>
                    <th class="text-end">Sisa Utang</th>
                    <th class="text-end" style="width:120px">Aksi</th>
                </tr>
            </thead>
            <tbody>
            <?php if (empty($supplier)): ?>
                <tr><td colspan="6" class="text-center py-4 text-muted">Belum ada data supplier.</td></tr>
            <?php else: ?>
                <?php foreach ($supplier as $s): ?>
                <tr>
                    <td><code><?= esc($s['kode']) ?></code></td>
                    <td class="fw-semibold"><?= esc($s['nama']) ?></td>
                    <td><?= esc($s['telepon'] ?? '-') ?></td>
                    <td class="text-muted small"><?= esc($s['alamat'] ?? '-') ?></td>
                    <td class="text-end">
                        <?php if ((float) $s['utang_tersisa'] > 0): ?>
                            <span class="badge badge-soft-danger"><?= rupiah($s['utang_tersisa']) ?></span>
                        <?php else: ?>
                            <span class="text-muted small">Nihil</span>
                        <?php endif; ?>
                    </td>
                    <td class="text-end">
                        <a href="/supplier/form/<?= (int) $s['id'] ?>" class="btn btn-sm btn-outline-secondary"><i class="bi bi-pencil"></i></a>
                        <form action="/supplier/delete/<?= (int) $s['id'] ?>" method="POST" class="d-inline" onsubmit="return confirm('Hapus supplier ini?')">
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
