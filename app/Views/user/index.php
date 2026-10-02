<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
    <div>
        <h4 class="mb-0 fw-bold">Manajemen Hak Akses Pengguna</h4>
        <small class="text-muted">Standar BNSP: Pemisahan peran hak akses sistem antara <strong>Admin</strong> dan <strong>Pengelola</strong></small>
    </div>
    <button class="btn btn-primary btn-sm" onclick="bukaModalUser()">
        <i class="bi bi-person-plus me-1"></i> Tambah Pengguna Baru
    </button>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-6">
        <div class="card p-3 border-start border-4 border-primary">
            <h6 class="fw-bold mb-1"><i class="bi bi-shield-check text-primary me-1"></i> Peran Admin</h6>
            <p class="text-muted small mb-0">Memiliki wewenang penuh terhadap seluruh data: kelola akun pengguna, edit & hapus barang, kelola kategori, dan rekap keuntungan.</p>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card p-3 border-start border-4 border-info">
            <h6 class="fw-bold mb-1"><i class="bi bi-box-seam text-info me-1"></i> Peran Pengelola</h6>
            <p class="text-muted small mb-0">Bertanggung jawab pada operasional harian: input data barang, perbarui stok fisik masuk/keluar, dan melihat katalog serta margin jual.</p>
        </div>
    </div>
</div>

<!-- Table Card -->
<div class="card p-3">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0 datatable" id="tabelUser">
            <thead class="table-light">
                <tr>
                    <th style="width:70px">ID</th>
                    <th>Nama Lengkap</th>
                    <th>Username</th>
                    <th class="text-center" style="width:120px">Hak Akses</th>
                    <th>Terdaftar Sejak</th>
                    <th class="text-end" style="width:120px">Aksi</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($users as $u): ?>
            <tr>
                <td class="font-monospace text-muted"><?= (int) $u['id'] ?></td>
                <td>
                    <div class="fw-bold"><?= esc($u['nama']) ?></div>
                    <?php if ($u['id'] === (int) session()->get('user_id')): ?>
                        <span class="badge bg-secondary" style="font-size:0.65rem;">Sedang Login</span>
                    <?php endif; ?>
                </td>
                <td><span class="font-monospace text-muted">@<?= esc($u['username']) ?></span></td>
                <td class="text-center">
                    <?php if ($u['role'] === 'admin'): ?>
                        <span class="badge badge-soft-primary"><i class="bi bi-shield-lock me-1"></i> Admin</span>
                    <?php else: ?>
                        <span class="badge badge-soft-info"><i class="bi bi-boxes me-1"></i> Pengelola</span>
                    <?php endif; ?>
                </td>
                <td><span class="small text-muted"><?= esc($u['created_at'] ?? '-') ?></span></td>
                <td class="text-end">
                    <div class="btn-group btn-group-sm">
                        <button class="btn btn-outline-secondary" onclick='editUser(<?= json_encode($u) ?>)' title="Edit">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <?php if ($u['id'] !== (int) session()->get('user_id')): ?>
                        <form action="/user/delete/<?= (int) $u['id'] ?>" method="POST" class="d-inline" onsubmit="return confirm('Hapus pengguna ini?')">
                            <?= csrf_field() ?>
                            <button class="btn btn-outline-danger" title="Hapus"><i class="bi bi-trash"></i></button>
                        </form>
                        <?php endif; ?>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal User -->
<div class="modal fade" id="modalUser" tabindex="-1">
    <div class="modal-dialog">
        <form class="modal-content" method="POST" action="/user/save" id="formUser">
            <?= csrf_field() ?>
            <input type="hidden" name="id" id="userId" value="0">
            <div class="modal-header">
                <h6 class="modal-title fw-bold" id="modalUserTitle">Tambah Pengguna Baru</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Nama Lengkap <span class="text-danger">*</span></label>
                    <input type="text" name="nama" id="userNama" class="form-control" required placeholder="Contoh: Budi Santoso">
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Username Login <span class="text-danger">*</span></label>
                    <input type="text" name="username" id="userUsername" class="form-control font-monospace" required placeholder="username_login">
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Hak Akses / Peran <span class="text-danger">*</span></label>
                    <select name="role" id="userRole" class="form-select" required>
                        <option value="pengelola">Pengelola (Staf Operasional)</option>
                        <option value="admin">Admin (Akses Penuh)</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold" id="labelPassword">Password <span class="text-danger">*</span></label>
                    <input type="password" name="password" id="userPassword" class="form-control" placeholder="Minimal 6 karakter">
                    <small class="text-muted" id="hintPassword" style="display:none;">Kosongkan password jika tidak ingin mengubahnya.</small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-sm btn-primary">Simpan Pengguna</button>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
function bukaModalUser() {
    document.getElementById('userId').value = 0;
    document.getElementById('userNama').value = '';
    document.getElementById('userUsername').value = '';
    document.getElementById('userUsername').removeAttribute('readonly');
    document.getElementById('userRole').value = 'pengelola';
    document.getElementById('userPassword').value = '';
    document.getElementById('userPassword').setAttribute('required', 'required');
    document.getElementById('labelPassword').innerHTML = 'Password <span class="text-danger">*</span>';
    document.getElementById('hintPassword').style.display = 'none';
    document.getElementById('modalUserTitle').innerText = 'Tambah Pengguna Baru';
    new bootstrap.Modal(document.getElementById('modalUser')).show();
}

function editUser(u) {
    document.getElementById('userId').value = u.id;
    document.getElementById('userNama').value = u.nama;
    document.getElementById('userUsername').value = u.username;
    document.getElementById('userRole').value = u.role;
    document.getElementById('userPassword').value = '';
    document.getElementById('userPassword').removeAttribute('required');
    document.getElementById('labelPassword').innerHTML = 'Password Baru (Opsional)';
    document.getElementById('hintPassword').style.display = 'block';
    document.getElementById('modalUserTitle').innerText = 'Edit Pengguna: ' + u.nama;
    new bootstrap.Modal(document.getElementById('modalUser')).show();
}
</script>
<?= $this->endSection() ?>
