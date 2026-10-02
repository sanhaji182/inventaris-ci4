<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h5 class="mb-0 fw-bold">Kategori Barang</h5>
        <small class="text-muted">Kelola pengelompokan produk</small>
    </div>
    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalKategori" onclick="resetForm()">
        <i class="bi bi-plus-lg me-1"></i> Tambah Kategori
    </button>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover mb-0 align-middle">
            <thead class="table-light">
                <tr>
                    <th style="width:100px">Kode</th>
                    <th>Nama Kategori</th>
                    <th>Keterangan</th>
                    <th class="text-end" style="width:120px">Jml Barang</th>
                    <th class="text-end" style="width:120px">Aksi</th>
                </tr>
            </thead>
            <tbody>
            <?php if (empty($kategori)): ?>
                <tr><td colspan="5" class="text-center py-4 text-muted">Belum ada data kategori.</td></tr>
            <?php else: ?>
                <?php foreach ($kategori as $k): ?>
                <tr>
                    <td><code><?= esc($k['kode']) ?></code></td>
                    <td class="fw-semibold"><?= esc($k['nama']) ?></td>
                    <td class="text-muted small"><?= esc($k['keterangan'] ?? '-') ?></td>
                    <td class="text-end"><span class="badge badge-soft-info"><?= (int) $k['jumlah_barang'] ?></span></td>
                    <td class="text-end">
                        <button class="btn btn-sm btn-outline-secondary" onclick='editKategori(<?= json_encode($k) ?>)'><i class="bi bi-pencil"></i></button>
                        <form action="/kategori/delete/<?= (int) $k['id'] ?>" method="POST" class="d-inline" onsubmit="return confirm('Hapus kategori ini?')">
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

<!-- Modal Form Kategori -->
<div class="modal fade" id="modalKategori" tabindex="-1">
    <div class="modal-dialog">
        <form class="modal-content" method="POST" action="/kategori/save">
            <?= csrf_field() ?>
            <input type="hidden" name="id" id="katId">
            <div class="modal-header">
                <h6 class="modal-title fw-bold" id="modalTitle">Tambah Kategori</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Kode Kategori</label>
                    <input type="text" name="kode" id="katKode" class="form-control" required placeholder="Contoh: HP, LAPTOP">
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Nama Kategori</label>
                    <input type="text" name="nama" id="katNama" class="form-control" required placeholder="Contoh: Handphone">
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Keterangan (opsional)</label>
                    <textarea name="keterangan" id="katKet" class="form-control" rows="2"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-sm btn-primary">Simpan</button>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
function resetForm() {
    document.getElementById('modalTitle').innerText = 'Tambah Kategori';
    document.getElementById('katId').value = '';
    document.getElementById('katKode').value = '';
    document.getElementById('katNama').value = '';
    document.getElementById('katKet').value = '';
}
function editKategori(data) {
    document.getElementById('modalTitle').innerText = 'Edit Kategori';
    document.getElementById('katId').value = data.id;
    document.getElementById('katKode').value = data.kode;
    document.getElementById('katNama').value = data.nama;
    document.getElementById('katKet').value = data.keterangan || '';
    new bootstrap.Modal(document.getElementById('modalKategori')).show();
}
</script>
<?= $this->endSection() ?>
