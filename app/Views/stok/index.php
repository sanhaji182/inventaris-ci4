<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
    <div>
        <h4 class="mb-0 fw-bold">Keluar / Masuk Stok & Realisasi Laba</h4>
        <small class="text-muted">Pencatatan barang keluar (terjual/rusak) dan barang masuk (kulak baru) serta akumulasi untung/rugi</small>
    </div>
    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalCatatStok">
        <i class="bi bi-plus-lg me-1"></i> Catat Perubahan Stok
    </button>
</div>

<!-- Table Card -->
<div class="card">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th style="width:110px">Tanggal</th>
                    <th>Nama Barang</th>
                    <th class="text-center" style="width:110px">Jenis</th>
                    <th class="text-center" style="width:90px">Jumlah</th>
                    <th class="text-end">Harga Transaksi</th>
                    <th class="text-end">Untung / Rugi Riil</th>
                    <th>Keterangan / Pembeli</th>
                    <th>Dicatat Oleh</th>
                </tr>
            </thead>
            <tbody>
            <?php if (empty($riwayat)): ?>
                <tr><td colspan="8" class="text-center py-4 text-muted">Belum ada riwayat aktivitas stok.</td></tr>
            <?php else: ?>
                <?php foreach ($riwayat as $r): ?>
                <tr>
                    <td><span class="small font-monospace text-muted"><?= esc($r['tanggal']) ?></span></td>
                    <td>
                        <div class="fw-bold small"><?= esc($r['nama_barang']) ?></div>
                        <span class="badge badge-soft-secondary font-monospace" style="font-size:0.68rem;"><?= esc($r['kode_barang']) ?></span>
                    </td>
                    <td class="text-center">
                        <?php if ($r['jenis'] === 'masuk'): ?>
                            <span class="badge badge-soft-info"><i class="bi bi-arrow-down-left"></i> Masuk</span>
                        <?php elseif ($r['jenis'] === 'keluar'): ?>
                            <span class="badge badge-soft-success"><i class="bi bi-arrow-up-right"></i> Keluar</span>
                        <?php else: ?>
                            <span class="badge badge-soft-secondary">Penyesuaian</span>
                        <?php endif; ?>
                    </td>
                    <td class="text-center font-monospace fw-bold"><?= (int) $r['jumlah'] ?></td>
                    <td class="text-end font-monospace"><?= rupiah($r['harga_transaksi']) ?></td>
                    <td class="text-end font-monospace fw-bold">
                        <?php if ($r['jenis'] === 'keluar'): ?>
                            <?php if ((float) $r['total_laba'] >= 0): ?>
                                <span class="text-success">+<?= rupiah($r['total_laba']) ?></span>
                            <?php else: ?>
                                <span class="text-danger"><?= rupiah($r['total_laba']) ?></span>
                            <?php endif; ?>
                        <?php else: ?>
                            <span class="text-muted">-</span>
                        <?php endif; ?>
                    </td>
                    <td><span class="small"><?= esc($r['keterangan'] ?: '-') ?></span></td>
                    <td><span class="badge badge-soft-primary"><?= esc($r['user_nama'] ?: 'Sistem') ?></span></td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Catat Stok -->
<div class="modal fade" id="modalCatatStok" tabindex="-1">
    <div class="modal-dialog">
        <form class="modal-content" method="POST" action="/stok/proses">
            <?= csrf_field() ?>
            <div class="modal-header">
                <h6 class="modal-title fw-bold"><i class="bi bi-arrow-left-right me-1 text-primary"></i> Catat Aktivitas Stok Barang</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Pilih Barang <span class="text-danger">*</span></label>
                    <select name="barang_id" id="modalBarangId" class="form-select" required onchange="updateBarangInfo()">
                        <option value="">-- Pilih Barang dari Inventaris --</option>
                        <?php foreach ($barangList as $b): ?>
                            <option value="<?= (int) $b['id'] ?>"
                                    data-stok="<?= (int) $b['stok'] ?>"
                                    data-beli="<?= (float) $b['harga_beli'] ?>"
                                    data-jual="<?= (float) $b['harga_jual'] ?>"
                                    data-satuan="<?= esc($b['satuan']) ?>">
                                <?= esc($b['kode_barang']) ?> · <?= esc($b['nama_barang']) ?> (Sisa: <?= (int) $b['stok'] ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-semibold">Jenis Aktivitas <span class="text-danger">*</span></label>
                    <select name="jenis" id="modalJenis" class="form-select" required onchange="toggleJenisForm()">
                        <option value="keluar">Barang Keluar / Terjual (Hitung Untung/Rugi)</option>
                        <option value="masuk">Barang Masuk / Tambah Kulakan</option>
                        <option value="penyesuaian">Penyesuaian Fisik (Opname)</option>
                    </select>
                </div>

                <div class="row g-2 mb-3">
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Jumlah <span class="text-danger">*</span></label>
                        <input type="number" name="jumlah" id="modalJumlah" class="form-control font-monospace" min="1" value="1" required oninput="hitungLabaKeluar()">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Tanggal</label>
                        <input type="date" name="tanggal" class="form-control" value="<?= date('Y-m-d') ?>" required>
                    </div>
                </div>

                <div class="mb-3" id="groupHargaTransaksi">
                    <label class="form-label small fw-semibold" id="labelHargaTransaksi">Harga Jual per Satuan (Rp)</label>
                    <input type="number" name="harga_transaksi" id="modalHargaTransaksi" class="form-control font-monospace" step="1000" oninput="hitungLabaKeluar()">
                    <small class="text-muted" id="subtextHargaTransaksi">Harga acuan diambil dari katalog, dapat diubah jika ada diskon/nego.</small>
                </div>

                <!-- Box Estimasi Laba Keluar -->
                <div class="p-3 rounded border bg-light mb-3" id="boxLabaModal">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <small class="text-muted fw-semibold">Estimasi Untung/Rugi Transaksi Ini:</small>
                        <span class="fw-bold font-monospace fs-6" id="labelEstimasiLaba">Rp 0</span>
                    </div>
                    <div class="small text-muted" id="labelRincianHitungan">Modal beli: Rp 0 / unit</div>
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-semibold">Keterangan / Catatan Transaksi</label>
                    <input type="text" name="keterangan" class="form-control" placeholder="Contoh: Terjual ke Pak Ahmad, kulak tambahan Shopee...">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-sm btn-primary">Simpan Transaksi</button>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
function updateBarangInfo() {
    const opt = document.getElementById('modalBarangId').selectedOptions[0];
    if (!opt || !opt.value) return;

    const modalBeli = parseFloat(opt.dataset.beli) || 0;
    const hargaJual = parseFloat(opt.dataset.jual) || 0;
    const jenis = document.getElementById('modalJenis').value;

    if (jenis === 'keluar') {
        document.getElementById('modalHargaTransaksi').value = hargaJual;
    } else if (jenis === 'masuk') {
        document.getElementById('modalHargaTransaksi').value = modalBeli;
    }

    hitungLabaKeluar();
}

function toggleJenisForm() {
    const jenis = document.getElementById('modalJenis').value;
    const labelHarga = document.getElementById('labelHargaTransaksi');
    const subtext = document.getElementById('subtextHargaTransaksi');
    const boxLaba = document.getElementById('boxLabaModal');

    if (jenis === 'keluar') {
        labelHarga.innerText = 'Harga Jual per Satuan (Rp)';
        subtext.innerText = 'Berapa harga jual transaksi ini ke pembeli.';
        boxLaba.style.display = 'block';
    } else if (jenis === 'masuk') {
        labelHarga.innerText = 'Harga Kulak / Modal Beli per Satuan (Rp)';
        subtext.innerText = 'Biaya modal per unit barang masuk ini.';
        boxLaba.style.display = 'none';
    } else {
        labelHarga.innerText = 'Harga Standar (Opsional)';
        subtext.innerText = 'Penyesuaian stok langsung memperbarui saldo akhir fisik.';
        boxLaba.style.display = 'none';
    }

    updateBarangInfo();
}

function hitungLabaKeluar() {
    const opt = document.getElementById('modalBarangId').selectedOptions[0];
    const jenis = document.getElementById('modalJenis').value;
    if (!opt || !opt.value || jenis !== 'keluar') return;

    const modalBeli = parseFloat(opt.dataset.beli) || 0;
    const hargaJual = parseFloat(document.getElementById('modalHargaTransaksi').value) || 0;
    const qty = parseInt(document.getElementById('modalJumlah').value) || 0;

    const selisihPerUnit = hargaJual - modalBeli;
    const totalLaba = selisihPerUnit * qty;
    const persen = modalBeli > 0 ? ((selisihPerUnit / modalBeli) * 100).toFixed(1) : 0;

    const labelLaba = document.getElementById('labelEstimasiLaba');
    if (totalLaba >= 0) {
        labelLaba.innerText = '+Rp ' + Math.round(totalLaba).toLocaleString('id-ID') + ' (+' + persen + '%)';
        labelLaba.className = 'fw-bold font-monospace fs-6 text-success';
    } else {
        labelLaba.innerText = '-Rp ' + Math.round(Math.abs(totalLaba)).toLocaleString('id-ID') + ' (' + persen + '%)';
        labelLaba.className = 'fw-bold font-monospace fs-6 text-danger';
    }

    document.getElementById('labelRincianHitungan').innerText = 
        `Modal beli: Rp ${Math.round(modalBeli).toLocaleString('id-ID')} | Jual: Rp ${Math.round(hargaJual).toLocaleString('id-ID')}`;
}
</script>
<?= $this->endSection() ?>
