<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="row g-3 mb-3">
    <div class="col-6 col-xl-3">
        <div class="card stat-card h-100">
            <div class="card-body d-flex align-items-center justify-content-between">
                <div>
                    <div class="label">Omzet bulan ini</div>
                    <div class="value"><?= rupiah($bulanIni['omzet']) ?></div>
                    <div class="small text-muted"><?= (int) $bulanIni['trx'] ?> transaksi</div>
                </div>
                <div class="icon bg-primary-subtle text-primary"><i class="bi bi-cash-stack"></i></div>
            </div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="card stat-card h-100">
            <div class="card-body d-flex align-items-center justify-content-between">
                <div>
                    <div class="label">Laba bulan ini</div>
                    <div class="value <?= $labaBulanIni >= 0 ? 'text-success' : 'text-danger' ?>">
                        <?= rupiah($labaBulanIni) ?>
                    </div>
                    <div class="small text-muted">dari unit terjual</div>
                </div>
                <div class="icon bg-success-subtle text-success"><i class="bi bi-graph-up-arrow"></i></div>
            </div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="card stat-card h-100">
            <div class="card-body d-flex align-items-center justify-content-between">
                <div>
                    <div class="label">Unit tersedia</div>
                    <div class="value"><?= (int) ($stokMap['tersedia']['jml'] ?? 0) ?></div>
                    <div class="small text-muted">nilai <?= rupiah($stokMap['tersedia']['nilai'] ?? 0) ?></div>
                </div>
                <div class="icon bg-info-subtle text-info"><i class="bi bi-phone"></i></div>
            </div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="card stat-card h-100">
            <div class="card-body d-flex align-items-center justify-content-between">
                <div>
                    <div class="label">Utang belum lunas</div>
                    <div class="value text-danger"><?= rupiah($utangRingkas['sisa']) ?></div>
                    <div class="small text-muted"><?= (int) $utangRingkas['jml'] ?> invoice</div>
                </div>
                <div class="icon bg-danger-subtle text-danger"><i class="bi bi-credit-card-2-front"></i></div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3 mb-3">
    <div class="col-lg-8">
        <div class="card h-100">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <span class="fw-semibold">Tren 14 hari</span>
                <span class="small text-muted">omzet vs laba</span>
            </div>
            <div class="card-body">
                <canvas id="chartTren" height="90"></canvas>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card h-100">
            <div class="card-header bg-white fw-semibold">Aging utang</div>
            <div class="card-body p-2">
                <table class="table table-sm mb-0 align-middle">
                    <thead><tr><th>Bucket</th><th class="text-end">Jumlah</th><th class="text-end">Total</th></tr></thead>
                    <tbody>
                    <?php foreach ($aging as $a): ?>
                        <tr>
                            <td><span class="badge badge-soft-<?= esc($a['warna']) ?>"><?= esc($a['label']) ?></span></td>
                            <td class="text-end"><?= (int) $a['jumlah'] ?></td>
                            <td class="text-end"><?= rupiah($a['total']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="row g-3">
    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-header bg-white fw-semibold">Penjualan terakhir</div>
            <div class="table-responsive">
                <table class="table table-sm mb-0 align-middle">
                    <thead><tr><th>No</th><th>Tanggal</th><th class="text-end">Total</th><th>Oleh</th></tr></thead>
                    <tbody>
                    <?php foreach ($terakhirPenjualan as $t): ?>
                        <tr>
                            <td><a href="/penjualan/detail/<?= (int) $t['id'] ?>"><?= esc($t['no']) ?></a></td>
                            <td><?= esc($t['tanggal']) ?></td>
                            <td class="text-end"><?= rupiah($t['total']) ?></td>
                            <td class="small"><?= esc($t['user_nama'] ?? '-') ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-header bg-white fw-semibold">Utang jatuh tempo terdekat</div>
            <div class="table-responsive">
                <table class="table table-sm mb-0 align-middle">
                    <thead><tr><th>Kode</th><th>Supplier</th><th>Jatuh tempo</th><th class="text-end">Sisa</th></tr></thead>
                    <tbody>
                    <?php foreach ($terakhirUtang as $u): ?>
                        <tr>
                            <td><a href="/utang/detail/<?= (int) $u['id'] ?>"><?= esc($u['kode']) ?></a></td>
                            <td class="small"><?= esc($u['supplier_nama'] ?? '-') ?></td>
                            <td>
                                <?= esc($u['jatuh_tempo']) ?>
                                <?php if ($u['jatuh_tempo'] < date('Y-m-d')): ?>
                                    <span class="badge badge-soft-danger">telat <?= (int) $u['hari_telat'] ?>h</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-end"><?= rupiah($u['nominal'] - $u['terbayar']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php if ($stokAlert): ?>
<div class="card mt-3">
    <div class="card-header bg-white fw-semibold"><i class="bi bi-exclamation-triangle text-warning me-1"></i> Stok di bawah minimum</div>
    <div class="table-responsive">
        <table class="table table-sm mb-0 align-middle">
            <thead><tr><th>Kode</th><th>Barang</th><th class="text-end">Tersedia</th><th class="text-end">Min</th></tr></thead>
            <tbody>
            <?php foreach ($stokAlert as $s): ?>
                <tr>
                    <td><?= esc($s['kode']) ?></td>
                    <td><?= esc($s['nama']) ?></td>
                    <td class="text-end"><span class="badge badge-soft-warning"><?= (int) $s['tersedia'] ?></span></td>
                    <td class="text-end text-muted"><?= (int) $s['stok_min'] ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
const tren = <?= json_encode(array_map(fn($r) => [
    'omzet' => (float) $r['omzet'],
    'laba'  => (float) $r['laba'],
], $tren)) ?>;
const labels = <?= json_encode(array_keys($tren)) ?>;

new Chart(document.getElementById('chartTren'), {
    type: 'line',
    data: {
        labels: labels.map(l => l.slice(5)),
        datasets: [
            { label: 'Omzet', data: labels.map(l => tren[l].omzet), tension: .35, borderColor: '#3b82f6', backgroundColor: 'rgba(59,130,246,.08)', fill: true },
            { label: 'Laba',  data: labels.map(l => tren[l].laba),  tension: .35, borderColor: '#22c55e', backgroundColor: 'rgba(34,197,94,.08)', fill: true },
        ]
    },
    options: {
        maintainAspectRatio: false,
        plugins: { legend: { position: 'bottom' } },
        scales: { y: { ticks: { callback: v => 'Rp ' + (v/1e6) + 'jt' } } }
    }
});
</script>
<?= $this->endSection() ?>
