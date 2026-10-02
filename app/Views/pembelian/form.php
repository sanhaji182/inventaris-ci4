<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<form method="POST" action="/pembelian/save" id="formPembelian">
    <?= csrf_field() ?>
    <div class="row g-3">
        <div class="col-lg-4">
            <div class="card p-3">
                <h6 class="fw-bold mb-3">Informasi Pembelian</h6>
                <div class="mb-3">
                    <label class="form-label small fw-semibold">No. Faktur</label>
                    <input type="text" name="no" class="form-control form-control-sm" value="<?= esc($nextNo) ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Tanggal</label>
                    <input type="date" name="tanggal" class="form-control form-control-sm" value="<?= date('Y-m-d') ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Supplier</label>
                    <select name="supplier_id" class="form-select form-select-sm">
                        <option value="">-- Non Supplier --</option>
                        <?php foreach ($supplier as $s): ?>
                            <option value="<?= (int) $s['id'] ?>"><?= esc($s['nama']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Sumber Pembelian</label>
                    <select name="sumber" class="form-select form-select-sm">
                        <?php foreach ($sumber as $sb): ?>
                            <option value="<?= $sb ?>"><?= $sb ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Link Marketplace (opsional)</label>
                    <input type="url" name="url" class="form-control form-control-sm" placeholder="https://tokopedia.com/...">
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Metode Pembayaran</label>
                    <select name="metode" id="metodeSelect" class="form-select form-select-sm" onchange="toggleTermin()">
                        <option value="tunai">Tunai / Cash</option>
                        <option value="transfer">Transfer Bank</option>
                        <option value="termin">Termin / Utang</option>
                    </select>
                </div>
                <div id="blockTermin" style="display:none">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Uang Muka / DP (Rp)</label>
                        <input type="number" name="uang_muka" id="uangMuka" class="form-control form-control-sm" value="0" step="1000" oninput="hitungTotal()">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Jatuh Tempo Utang</label>
                        <input type="date" name="jatuh_tempo" class="form-control form-control-sm" value="<?= date('Y-m-d', strtotime('+30 days')) ?>">
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Catatan</label>
                    <textarea name="catatan" class="form-control form-control-sm" rows="2"></textarea>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <span class="fw-bold">Daftar Unit Masuk</span>
                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="tambahBaris()"><i class="bi bi-plus-lg me-1"></i> Tambah Baris</button>
                </div>
                <div class="table-responsive">
                    <table class="table table-sm mb-0 align-middle" id="tabelItems">
                        <thead class="table-light">
                            <tr>
                                <th>Barang Katalog</th>
                                <th style="width:170px">IMEI / Serial</th>
                                <th style="width:110px">Kondisi</th>
                                <th style="width:140px" class="text-end">Harga Beli</th>
                                <th style="width:40px"></th>
                            </tr>
                        </thead>
                        <tbody id="bodyItems">
                            <!-- Baris pertama otomatis -->
                        </tbody>
                        <tfoot class="table-light">
                            <tr>
                                <th colspan="3" class="text-end">Total Pembelian:</th>
                                <th class="text-end" id="labelTotal">Rp 0</th>
                                <th></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <div class="card-footer bg-white d-flex justify-content-end gap-2">
                    <a href="/pembelian" class="btn btn-sm btn-secondary">Batal</a>
                    <button type="submit" class="btn btn-sm btn-primary">Simpan Transaksi</button>
                </div>
            </div>
        </div>
    </div>
</form>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
const barangOptions = `<?php foreach ($barang as $b): ?>
    <option value="<?= (int) $b['id'] ?>"><?= esc($b['kode']) ?> · <?= esc($b['nama']) ?></option>
<?php endforeach; ?>`;

function tambahBaris() {
    const tbody = document.getElementById('bodyItems');
    const tr = document.createElement('tr');
    tr.innerHTML = `
        <td>
            <select name="item_barang_id[]" class="form-select form-select-sm" required>
                <option value="">Pilih Barang...</option>
                ${barangOptions}
            </select>
        </td>
        <td><input type="text" name="item_imei[]" class="form-control form-control-sm" placeholder="IMEI / SN"></td>
        <td>
            <select name="item_kondisi[]" class="form-select form-select-sm">
                <option value="mulus">Mulus</option>
                <option value="lecet">Lecet</option>
                <option value="minus">Minus</option>
                <option value="batangan">Batangan</option>
                <option value="fullset">Fullset</option>
            </select>
        </td>
        <td>
            <input type="number" name="item_harga_beli[]" class="form-control form-control-sm text-end input-harga" step="1000" required value="0" oninput="hitungTotal()">
        </td>
        <td class="text-center">
            <button type="button" class="btn btn-sm btn-link text-danger p-0" onclick="hapusBaris(this)"><i class="bi bi-x-lg"></i></button>
        </td>
    `;
    tbody.appendChild(tr);
}

function hapusBaris(btn) {
    const tbody = document.getElementById('bodyItems');
    if (tbody.children.length > 1) {
        btn.closest('tr').remove();
        hitungTotal();
    }
}

function hitungTotal() {
    let total = 0;
    document.querySelectorAll('.input-harga').forEach(inp => {
        total += parseFloat(inp.value) || 0;
    });
    document.getElementById('labelTotal').innerText = 'Rp ' + total.toLocaleString('id-ID');
}

function toggleTermin() {
    const isTermin = document.getElementById('metodeSelect').value === 'termin';
    document.getElementById('blockTermin').style.display = isTermin ? 'block' : 'none';
}

// Inisialisasi baris pertama
tambahBaris();
</script>
<?= $this->endSection() ?>
