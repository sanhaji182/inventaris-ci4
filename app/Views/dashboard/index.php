<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<!-- Bento Stat Grid -->
<div class="row g-3 mb-4">
    <!-- Omzet -->
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card h-100 p-3 p-xl-4">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="label">Omzet Bulan Ini</span>
                <div class="icon-box primary">
                    <i class="bi bi-wallet2"></i>
                </div>
            </div>
            <div class="value"><?= rupiah($bulanIni['omzet']) ?></div>
            <div class="sub-label">
                <span class="text-success fw-semibold"><i class="bi bi-arrow-up-right me-1"></i><?= (int) $bulanIni['trx'] ?> Transaksi</span>
                <span>tercatat</span>
            </div>
        </div>
    </div>

    <!-- Laba Kotor -->
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card h-100 p-3 p-xl-4">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="label">Laba Kotor Riil</span>
                <div class="icon-box success">
                    <i class="bi bi-graph-up-arrow"></i>
                </div>
            </div>
            <div class="value <?= $labaBulanIni >= 0 ? 'text-success' : 'text-danger' ?>">
                <?= rupiah($labaBulanIni) ?>
            </div>
            <div class="sub-label">
                <span class="text-muted">Margin riil dari selisih modal per unit</span>
            </div>
        </div>
    </div>

    <!-- Unit Tersedia -->
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card h-100 p-3 p-xl-4">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="label">Stok Fisik Tersedia</span>
                <div class="icon-box info">
                    <i class="bi bi-phone"></i>
                </div>
            </div>
            <div class="value"><?= (int) ($stokMap['tersedia']['jml'] ?? 0) ?> <span class="fs-6 fw-normal text-muted">unit</span></div>
            <div class="sub-label">
                <span class="text-muted">Aset modal: <strong><?= rupiah($stokMap['tersedia']['nilai'] ?? 0) ?></strong></span>
            </div>
        </div>
    </div>

    <!-- Utang Dagang -->
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card h-100 p-3 p-xl-4">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="label">Utang Belum Lunas</span>
                <div class="icon-box danger">
                    <i class="bi bi-credit-card-2-front"></i>
                </div>
            </div>
            <div class="value text-danger"><?= rupiah($utangRingkas['sisa']) ?></div>
            <div class="sub-label">
                <span class="badge-dot badge-soft-danger"><?= (int) $utangRingkas['jml'] ?> Faktur Aktif</span>
            </div>
        </div>
    </div>
</div>

<!-- Chart & Aging Grid -->
<div class="row g-3 mb-4">
    <!-- Chart Interaktif -->
    <div class="col-lg-8">
        <div class="card h-100">
            <div class="card-header">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-activity text-primary fs-5"></i>
                    <div>
                        <div class="fw-bold">Tren Penjualan & Laba Riil</div>
                        <div class="text-muted small" style="font-size:0.75rem;">Perbandingan arus kas omzet terhadap margin kotor harian</div>
                    </div>
                </div>
                <div class="btn-group btn-group-sm" role="group">
                    <button type="button" class="btn btn-outline-secondary active" id="btnRange14">14 Hari</button>
                    <button type="button" class="btn btn-outline-secondary" id="btnRange7">7 Hari</button>
                </div>
            </div>
            <div class="card-body">
                <div style="height: 280px; position: relative;">
                    <canvas id="chartTren"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Aging Utang Analysis -->
    <div class="col-lg-4">
        <div class="card h-100">
            <div class="card-header">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-hourglass-split text-warning fs-5"></i>
                    <div>
                        <div class="fw-bold">Analisis Umur Utang (Aging)</div>
                        <div class="text-muted small" style="font-size:0.75rem;">Kategori keterlambatan pembayaran termin</div>
                    </div>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Bucket</th>
                            <th class="text-center">Jml</th>
                            <th class="text-end">Nominal</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($aging as $a): ?>
                        <tr>
                            <td>
                                <span class="badge-dot badge-soft-<?= esc($a['warna']) ?>">
                                    <?= esc($a['label']) ?>
                                </span>
                            </td>
                            <td class="text-center fw-semibold"><?= (int) $a['jumlah'] ?></td>
                            <td class="text-end font-monospace fw-semibold"><?= rupiah($a['total']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="card-footer d-flex justify-content-between align-items-center">
                <span class="small text-muted">Jatuh tempo terdekat diprioritaskan</span>
                <a href="/utang" class="btn btn-sm btn-link text-decoration-none p-0">Kelola Utang &rarr;</a>
            </div>
        </div>
    </div>
</div>

<!-- Aktivitas Terkini Section -->
<div class="row g-3">
    <!-- Penjualan Terakhir -->
    <div class="col-lg-6">
        <div class="card h-100">
            <div class="table-filter-bar">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-bag-check text-success fs-5"></i>
                    <span class="fw-bold">Penjualan Terakhir</span>
                </div>
                <div class="table-search-input">
                    <i class="bi bi-search"></i>
                    <input type="text" placeholder="Filter penjualan..." data-table-search="#tablePenjualan">
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle" id="tablePenjualan">
                    <thead>
                        <tr>
                            <th>No Faktur</th>
                            <th>Tanggal</th>
                            <th class="text-end">Total</th>
                            <th>Kasir</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($terakhirPenjualan as $t): ?>
                        <tr>
                            <td>
                                <a href="/penjualan/detail/<?= (int) $t['id'] ?>" class="chip-imei" title="Lihat Faktur">
                                    <i class="bi bi-receipt"></i>
                                    <span><?= esc($t['no']) ?></span>
                                </a>
                            </td>
                            <td><span class="small text-muted"><?= esc($t['tanggal']) ?></span></td>
                            <td class="text-end font-monospace fw-bold text-success"><?= rupiah($t['total']) ?></td>
                            <td>
                                <span class="badge badge-soft-secondary">
                                    <i class="bi bi-person me-1"></i><?= esc($t['user_nama'] ?? '-') ?>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="card-footer d-flex justify-content-between align-items-center">
                <span class="small text-muted">Menampilkan transaksi kasir terbaru</span>
                <a href="/penjualan" class="btn btn-sm btn-outline-secondary">Semua Transaksi</a>
            </div>
        </div>
    </div>

    <!-- Utang Jatuh Tempo -->
    <div class="col-lg-6">
        <div class="card h-100">
            <div class="table-filter-bar">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-exclamation-circle text-danger fs-5"></i>
                    <span class="fw-bold">Jatuh Tempo Terdekat</span>
                </div>
                <div class="table-search-input">
                    <i class="bi bi-search"></i>
                    <input type="text" placeholder="Filter supplier..." data-table-search="#tableUtang">
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle" id="tableUtang">
                    <thead>
                        <tr>
                            <th>Kode</th>
                            <th>Supplier</th>
                            <th>Jatuh Tempo</th>
                            <th class="text-end">Sisa Bayar</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($terakhirUtang as $u): ?>
                        <tr>
                            <td>
                                <a href="/utang/detail/<?= (int) $u['id'] ?>" class="chip-imei">
                                    <i class="bi bi-hash"></i>
                                    <span><?= esc($u['kode']) ?></span>
                                </a>
                            </td>
                            <td><span class="fw-semibold small"><?= esc($u['supplier_nama'] ?? '-') ?></span></td>
                            <td>
                                <div class="small fw-semibold"><?= esc($u['jatuh_tempo']) ?></div>
                                <?php if ($u['jatuh_tempo'] < date('Y-m-d')): ?>
                                    <span class="badge-dot badge-soft-danger pulse" style="font-size:0.7rem;">
                                        Telat <?= (int) $u['hari_telat'] ?> hari
                                    </span>
                                <?php else: ?>
                                    <span class="badge-dot badge-soft-info" style="font-size:0.7rem;">
                                        Sisa <?= abs((int) $u['hari_telat']) ?> hari
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="text-end font-monospace fw-bold text-danger">
                                <?= rupiah($u['nominal'] - $u['terbayar']) ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="card-footer d-flex justify-content-between align-items-center">
                <span class="small text-muted">Prioritas pelunasan supplier</span>
                <a href="/utang" class="btn btn-sm btn-outline-secondary">Daftar Utang</a>
            </div>
        </div>
    </div>
</div>

<!-- Alert Stok di Bawah Minimum -->
<?php if ($stokAlert): ?>
<div class="card mt-4 border-warning">
    <div class="card-header bg-warning-subtle text-warning-emphasis d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center gap-2">
            <i class="bi bi-shield-exclamation fs-5"></i>
            <span class="fw-bold">Peringatan Stok di Bawah Batas Minimum (Restock Alert)</span>
        </div>
        <span class="badge bg-warning text-dark"><?= count($stokAlert) ?> Barang</span>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th>Kode</th>
                    <th>Nama Barang</th>
                    <th class="text-center">Stok Minimum</th>
                    <th class="text-center">Sisa Tersedia</th>
                    <th class="text-end">Tindakan</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($stokAlert as $s): ?>
                <tr>
                    <td><span class="chip-imei"><?= esc($s['kode']) ?></span></td>
                    <td><strong><?= esc($s['nama']) ?></strong></td>
                    <td class="text-center text-muted"><?= (int) $s['stok_min'] ?> unit</td>
                    <td class="text-center">
                        <span class="badge-dot badge-soft-danger pulse">
                            <?= (int) $s['tersedia'] ?> unit
                        </span>
                    </td>
                    <td class="text-end">
                        <a href="/pembelian/form" class="btn btn-sm btn-primary">
                            <i class="bi bi-cart-plus me-1"></i> Beli Stok
                        </a>
                    </td>
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
document.addEventListener('DOMContentLoaded', () => {
    const rawTren = <?= json_encode(array_map(fn($r) => [
        'omzet' => (float) $r['omzet'],
        'laba'  => (float) $r['laba'],
    ], $tren)) ?>;
    
    const allDates = Object.keys(rawTren);
    let activeRange = 14;

    function getChartColors() {
        const isDark = document.documentElement.getAttribute('data-theme') === 'dark';
        return {
            gridColor: isDark ? 'rgba(255, 255, 255, 0.06)' : 'rgba(0, 0, 0, 0.05)',
            textColor: isDark ? '#94a3b8' : '#64748b',
            omzetLine: '#6366f1',
            omzetGradTop: isDark ? 'rgba(99, 102, 241, 0.35)' : 'rgba(99, 102, 241, 0.18)',
            labaLine: '#10b981',
            labaGradTop: isDark ? 'rgba(16, 185, 129, 0.35)' : 'rgba(16, 185, 129, 0.18)',
        };
    }

    const ctx = document.getElementById('chartTren').getContext('2d');
    let chartInstance = null;

    function renderChart() {
        const colors = getChartColors();
        const displayDates = allDates.slice(-activeRange);

        const gradOmzet = ctx.createLinearGradient(0, 0, 0, 260);
        gradOmzet.addColorStop(0, colors.omzetGradTop);
        gradOmzet.addColorStop(1, 'rgba(99, 102, 241, 0.0)');

        const gradLaba = ctx.createLinearGradient(0, 0, 0, 260);
        gradLaba.addColorStop(0, colors.labaGradTop);
        gradLaba.addColorStop(1, 'rgba(16, 185, 129, 0.0)');

        const config = {
            type: 'line',
            data: {
                labels: displayDates.map(d => {
                    const parts = d.split('-');
                    return parts[2] + '/' + parts[1];
                }),
                datasets: [
                    {
                        label: 'Omzet Penjualan',
                        data: displayDates.map(d => rawTren[d].omzet),
                        borderColor: colors.omzetLine,
                        backgroundColor: gradOmzet,
                        borderWidth: 2.5,
                        pointRadius: 3,
                        pointHoverRadius: 6,
                        pointBackgroundColor: colors.omzetLine,
                        fill: true,
                        tension: 0.35,
                    },
                    {
                        label: 'Laba Kotor Riil',
                        data: displayDates.map(d => rawTren[d].laba),
                        borderColor: colors.labaLine,
                        backgroundColor: gradLaba,
                        borderWidth: 2.5,
                        pointRadius: 3,
                        pointHoverRadius: 6,
                        pointBackgroundColor: colors.labaLine,
                        fill: true,
                        tension: 0.35,
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    mode: 'index',
                    intersect: false,
                },
                plugins: {
                    legend: {
                        position: 'top',
                        align: 'end',
                        labels: {
                            color: colors.textColor,
                            font: { family: "'Plus Jakarta Sans', sans-serif", size: 12, weight: 600 },
                            usePointStyle: true,
                            boxWidth: 8,
                            padding: 15
                        }
                    },
                    tooltip: {
                        backgroundColor: document.documentElement.getAttribute('data-theme') === 'dark' ? '#1f293d' : '#0f172a',
                        titleColor: '#f8fafc',
                        bodyColor: '#e2e8f0',
                        padding: 12,
                        cornerRadius: 8,
                        titleFont: { family: "'Plus Jakarta Sans', sans-serif", size: 12, weight: 700 },
                        bodyFont: { family: "'JetBrains Mono', monospace", size: 12 },
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) label += ': ';
                                if (context.parsed.y !== null) {
                                    label += 'Rp ' + context.parsed.y.toLocaleString('id-ID');
                                }
                                return label;
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { color: colors.textColor, font: { family: "'Plus Jakarta Sans', sans-serif", size: 11 } }
                    },
                    y: {
                        grid: { color: colors.gridColor },
                        ticks: {
                            color: colors.textColor,
                            font: { family: "'JetBrains Mono', monospace", size: 11 },
                            callback: function(v) {
                                if (v >= 1e6) return 'Rp ' + (v / 1e6).toFixed(1) + 'M';
                                if (v >= 1e3) return 'Rp ' + (v / 1e3).toFixed(0) + 'k';
                                return 'Rp ' + v;
                            }
                        }
                    }
                }
            }
        };

        if (chartInstance) {
            chartInstance.destroy();
        }
        chartInstance = new Chart(ctx, config);
    }

    renderChart();

    // Range Toggles
    const btn14 = document.getElementById('btnRange14');
    const btn7 = document.getElementById('btnRange7');

    btn14?.addEventListener('click', () => {
        activeRange = 14;
        btn14.classList.add('active');
        btn7.classList.remove('active');
        renderChart();
    });

    btn7?.addEventListener('click', () => {
        activeRange = 7;
        btn7.classList.add('active');
        btn14.classList.remove('active');
        renderChart();
    });

    // Theme Change Re-render
    window.addEventListener('themeChanged', () => {
        renderChart();
    });
});
</script>
<?= $this->endSection() ?>
