<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h5 class="mb-0 fw-bold">Manajemen Pengguna</h5>
        <small class="text-muted">Kelola akun dan hak akses sistem (Role: Admin / Pembeli / Staf)</small>
    </div>
    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalUser" onclick="resetUserForm()">
        <i class="bi bi-person-plus me-1"></i> Tambah Pengguna
    </button>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover mb-0 align-middle">
            <thead class="table-light">
                <tr>
                    <th>Username</th>
                    <th>Nama Lengkap</th>
                    <th>Peran (Role)</th>
                    <th>Status</th>
                    <th>Tgl Dibuat</th>
                    <th class="text-end" style="width:120px">Aksi</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($users as $u): ?>
                <tr>
                    <td><code><?= esc($u['username']) ?></code></td>
                    <td class="fw-semibold"><?= esc($u['nama']) ?></td>
                    <td>
                        <span class="badge <?= $u['role'] === 'admin' ? 'badge-soft-danger' : ($u['role'] === 'pembeli' ? 'badge-soft-info' : 'badge-soft-secondary') ?>">
                            <?= strtoupper(esc($u['role'])) ?>
                        </span>
                    </td>
                    <td>
                        <span class="badge <?= $u['status'] === 'aktif' ? 'badge-soft-success' : 'badge-soft-warning' ?>">
                            <?= ucfirst(esc($u['status'])) ?>
                        </span>
                    </td>
                    <td class="small text-muted"><?= esc($u['created_at'] ?? '-') ?></td>
                    <td class="text-end">
                        <button class="btn btn-sm btn-outline-secondary" onclick='editUser(<?= json_encode($u) ?>)'><i class="bi bi-pencil"></i></button>
                        <?php if ((int) $u['id'] !== (int) session('user_id')): ?>
                        <form action="/user/delete/<?= (int) $u['id'] ?>" method="POST" class="d-inline" onsubmit="return confirm('Hapus pengguna ini?')">
                            <?= csrf_field() ?>
                            <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                        </form>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Form User -->
<div class="modal fade" id="modalUser" tabindex="-1">
    <div class="modal-dialog">
        <form class="modal-content" method="POST" action="/user/save">
            <?= csrf_field() ?>
            <input type="hidden" name="id" id="userId">
            <div class="modal-header">
                <h6 class="modal-title fw-bold" id="userModalTitle">Tambah Pengguna</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Username</label>
                    <input type="text" name="username" id="userUsername" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Nama Lengkap</label>
                    <input type="text" name="nama" id="userNama" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Password</label>
                    <input type="password" name="password" id="userPass" class="form-control" placeholder="Kosongkan bila tidak diubah">
                    <small class="text-muted" id="userPassHelp">Wajib untuk pengguna baru</small>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Peran (Role)</label>
                    <select name="role" id="userRole" class="form-select">
                        <option value="staf">Staf (Gudang/Operasional)</option>
                        <option value="pembeli">Pembeli (Kasir/Penjualan)</option>
                        <option value="admin">Administrator (Akses Penuh)</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Status</label>
                    <select name="status" id="userStatus" class="form-select">
                        <option value="aktif">Aktif</option>
                        <option value="nonaktif">Non-aktif</option>
                    </select>
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
function resetUserForm() {
    document.getElementById('userModalTitle').innerText = 'Tambah Pengguna';
    document.getElementById('userId').value = '';
    document.getElementById('userUsername').value = '';
    document.getElementById('userNama').value = '';
    document.getElementById('userPass').value = '';
    document.getElementById('userPass').required = true;
    document.getElementById('userPassHelp').innerText = 'Wajib untuk pengguna baru';
    document.getElementById('userRole').value = 'staf';
    document.getElementById('userStatus').value = 'aktif';
}

function editUser(u) {
    document.getElementById('userModalTitle').innerText = 'Edit Pengguna: ' + u.username;
    document.getElementById('userId').value = u.id;
    document.getElementById('userUsername').value = u.username;
    document.getElementById('userNama').value = u.nama;
    document.getElementById('userPass').value = '';
    document.getElementById('userPass').required = false;
    document.getElementById('userPassHelp').innerText = 'Kosongkan jika password tidak ingin diubah';
    document.getElementById('userRole').value = u.role;
    document.getElementById('userStatus').value = u.status;
    new bootstrap.Modal(document.getElementById('modalUser')).show();
}
</script>
<?= $this->endSection() ?>
