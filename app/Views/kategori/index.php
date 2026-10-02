<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
    <div>
        <h4 class="mb-0 fw-bold">Kategori Inventaris</h4>
        <small class="text-muted">Kelompokkan barang agar inventaris tertata rapi dan mudah dicari</small>
    </div>
    <button class="btn btn-primary btn-sm" onclick="bukaModalKategori()">
        <i class="bi bi-plus-lg me-1"></i> Tambah Kategori
    </button>
</div>

<!-- Table Card -->
<div class="card p-3">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0 datatable" id="tabelKategori">
            <thead class="table-light">
                <tr>
                    <th style="width:70px">ID</th>
                    <th>Nama Kategori</th>
                    <th>Keterangan</th>
                    <th class="text-end" style="width:120px">Aksi</th>
                </tr>
            </thead>
            <tbody>
            <?php if (empty($kategori)): ?>
                <tr><td colspan="4" class="text-center py-4 text-muted">Belum ada kategori.</td></tr>
            <?php else: ?>
                <?php foreach ($kategori as $k): ?>
                <tr>
                    <td class="font-monospace text-muted"><?= (int) $k['id'] ?></td>
                    <td><strong class="text-primary"><?= esc($k['nama']) ?></strong></td>
                    <td><?= esc($k['keterangan'] ?: '-') ?></td>
                    <td class="text-end">
                        <div class="btn-group btn-group-sm">
                            <button class="btn btn-outline-secondary" onclick='editKategori(<?= json_encode($k) ?>)' title="Edit">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <?php if (session()->get('user_role') === 'admin'): ?>
                            <form action="/kategori/delete/<?= (int) $k['id'] ?>" method="POST" class="d-inline" onsubmit="return confirm('Hapus kategori ini?')">
                                <?= csrf_field() ?>
                                <button class="btn btn-outline-danger" title="Hapus"><i class="bi bi-trash"></i></button>
                            </form>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Kategori -->
<div class="modal fade" id="modalKategori" tabindex="-1">
    <div class="modal-dialog">
        <form class="modal-content" method="POST" action="/kategori/save" id="formKategori">
            <?= csrf_field() ?>
            <input type="hidden" name="id" id="katId" value="0">
            <div class="modal-header">
                <h6 class="modal-title fw-bold" id="modalKatTitle">Tambah Kategori Baru</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Nama Kategori <span class="text-danger">*</span></label>
                    <input type="text" name="nama" id="katNama" class="form-control" placeholder="Contoh: Smartphone & Tablet" required>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Keterangan Singkat</label>
                    <textarea name="keterangan" id="katKeterangan" class="form-control" rows="2" placeholder="Deskripsi kelompok barang..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-sm btn-primary">Simpan Kategori</button>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
function bukaModalKategori() {
    document.getElementById('katId').value = 0;
    document.getElementById('katNama').value = '';
    document.getElementById('katKeterangan').value = '';
    document.getElementById('modalKatTitle').innerText = 'Tambah Kategori Baru';
    new bootstrap.Modal(document.getElementById('modalKategori')).show();
}

function editKategori(k) {
    document.getElementById('katId').value = k.id;
    document.getElementById('katNama').value = k.nama;
    document.getElementById('katKeterangan').value = k.keterangan || '';
    document.getElementById('modalKatTitle').innerText = 'Edit Kategori: ' + k.nama;
    new bootstrap.Modal(document.getElementById('modalKategori')).show();
}
</script>
<?= $this->endSection() ?>
