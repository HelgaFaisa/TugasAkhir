

<?php $__env->startSection('content'); ?>
<style>
/* Filter periode */
.period-bar {
    display: flex;
    align-items: center;
    gap: 10px;
    flex-wrap: wrap;
    margin-bottom: 10px;
}
.period-btn {
    padding: 6px 14px;
    border-radius: 20px;
    border: 1px solid #e2e8f0;
    background: #fff;
    cursor: pointer;
    font-size: 0.85rem;
    transition: all 0.2s;
}
.period-btn:hover,
.period-btn.active {
    background: var(--primary-color);
    color: #fff;
    border-color: var(--primary-color);
}
.custom-range {
    display: none;
    align-items: center;
    gap: 8px;
}
.custom-range.show { display: flex; }
.custom-range input[type=date] {
    padding: 5px 10px;
    border: 1px solid #e2e8f0;
    border-radius: 6px;
    font-size: 0.85rem;
}

/* Alert anomali */
.alert-row {
    padding: 10px 14px;
    border-radius: 8px;
    margin-bottom: 8px;
    display: flex;
    align-items: flex-start;
    gap: 10px;
    font-size: 0.88rem;
}
.alert-row.a-danger  { background: #FEF2F2; border-left: 4px solid #EF4444; color: #991B1B; }
.alert-row.a-warning { background: #FFFBEB; border-left: 4px solid #F59E0B; color: #92400E; }
.alert-row.a-info    { background: #EFF6FF; border-left: 4px solid #3B82F6; color: #1E40AF; }
.alert-row.a-success { background: #ECFDF5; border-left: 4px solid #10B981; color: #065F46; }
.alert-text { flex: 1; }
.alert-title { font-weight: 600; }
.alert-desc  { font-size: 0.82rem; opacity: 0.85; }

/* Stat grid — 4 kartu sejajar */
.stat-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 14px;
    margin-bottom: 20px;
}
.stat-box {
    background: #fff;
    border-radius: 10px;
    padding: 14px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.06);
    text-align: center;
}
.stat-label { font-size: 0.8rem; color: #6B7280; margin-bottom: 4px; }
.stat-value { font-size: 1.5rem; font-weight: 700; color: var(--primary-dark); }
.stat-sub   { font-size: 0.78rem; margin-top: 4px; }
.stat-sub.up   { color: #10B981; }
.stat-sub.down { color: #EF4444; }

/* Kartu utama */
.main-cards {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 16px;
    margin-bottom: 20px;
}
.lk-card { background: #fff; border-radius: 10px; padding: 16px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); }
.lk-card-title { font-size: 0.95rem; font-weight: 600; margin-bottom: 12px; display: flex; align-items: center; gap: 8px; }
.lk-card-item {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 7px 0;
    border-bottom: 1px solid #f1f5f9;
    font-size: 0.85rem;
}
.lk-card-item:last-child { border-bottom: none; }
.lk-card-item .item-name { flex: 1; }
.lk-card-empty { padding: 12px; border-radius: 8px; background: #ECFDF5; color: #065F46; font-size: 0.85rem; display: flex; align-items: center; gap: 8px; }
.pattern-item { padding: 8px 0; border-bottom: 1px solid #f1f5f9; font-size: 0.85rem; }
.pattern-item:last-child { border-bottom: none; }
.pattern-item-title { font-weight: 600; display: flex; align-items: center; gap: 6px; }
.pattern-item-desc  { font-size: 0.8rem; color: #6B7280; margin-top: 2px; }

/* Chart */
.chart-wrap {
    background: #fff;
    border-radius: 10px;
    padding: 16px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.06);
    margin-bottom: 20px;
}
.chart-wrap h4 { margin: 0 0 12px; font-size: 0.95rem; color: var(--primary-dark); }

/* Kelas detail */
.kelas-section { margin-bottom: 20px; }
.kelas-section h4 { font-size: 0.95rem; color: var(--primary-dark); margin-bottom: 12px; }
.kelas-section details {
    background: #fff;
    border-radius: 8px;
    margin-bottom: 8px;
    box-shadow: 0 1px 4px rgba(0,0,0,0.05);
    overflow: hidden;
}
.kelas-section summary {
    padding: 12px 16px;
    cursor: pointer;
    font-weight: 600;
    font-size: 0.9rem;
    list-style: none;
}
.kelas-section summary::-webkit-details-marker { display: none; }
.kelas-section summary::before {
    content: '\f054';
    font-family: 'Font Awesome 5 Free';
    font-weight: 900;
    font-size: 0.75rem;
    margin-right: 10px;
    transition: transform 0.2s;
    display: inline-block;
}
.kelas-section details[open] summary::before { transform: rotate(90deg); }
.kelas-section summary:hover { background: #f8fafc; }
.kelas-detail-body { padding: 0 16px 16px; }
.row-low { background: #FEF2F2; }
.text-center { text-align: center; }

@media (max-width: 900px) {
    .main-cards { grid-template-columns: 1fr; }
    .stat-grid   { grid-template-columns: repeat(2, 1fr); }
}
@media (max-width: 768px) {
    .stat-grid { grid-template-columns: repeat(2, 1fr); }
}
</style>


<div class="page-header">
    <h2><i class="fas fa-chart-line"></i> Laporan Kegiatan</h2>
    <div style="display: flex; gap: 8px;">
    </div>
</div>


<div class="content-box" style="margin-bottom: 16px;">
    <form method="GET" id="periodForm" action="<?php echo e(route('admin.laporan-kegiatan.index')); ?>">
        <div class="period-bar">
            <span style="font-size: 0.85rem; color: var(--text-light);">
                <i class="fas fa-calendar-alt"></i> Periode:
            </span>
            <?php $__currentLoopData = ['hari_ini' => 'Hari Ini', 'minggu_ini' => 'Minggu Ini', 'bulan_ini' => 'Bulan Ini', 'semester_ini' => 'Semester', 'custom' => 'Custom']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <button type="button"
                        class="period-btn <?php echo e($periode === $key ? 'active' : ''); ?>"
                        onclick="setPeriode('<?php echo e($key); ?>')"><?php echo e($label); ?></button>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <div class="custom-range <?php echo e($periode === 'custom' ? 'show' : ''); ?>" id="customRange">
                <input type="date" name="tanggal_dari"
                       value="<?php echo e(request('tanggal_dari', $startDate->format('Y-m-d'))); ?>">
                <span>-</span>
                <input type="date" name="tanggal_sampai"
                       value="<?php echo e(request('tanggal_sampai', $endDate->format('Y-m-d'))); ?>">
            </div>
            <input type="hidden" name="periode" id="periodeInput" value="<?php echo e($periode); ?>">
            <button type="submit" class="btn btn-primary btn-sm">
                <i class="fas fa-sync-alt"></i> Terapkan
            </button>
        </div>
    </form>
    <p style="margin: 8px 0 0; font-size: 0.82rem; color: var(--text-light);">
        <i class="fas fa-info-circle"></i> Menampilkan data: <strong><?php echo e($periodeLabel); ?></strong>
    </p>
</div>


<div class="stat-grid">
    <div class="stat-box">
        <div class="stat-label">Rata-rata Kehadiran</div>
        <div class="stat-value"><?php echo e($kpi['avg_kehadiran']); ?>%</div>
        <div class="stat-sub <?php echo e($kpiComparison['avg_kehadiran'] >= 0 ? 'up' : 'down'); ?>">
            <i class="fas fa-arrow-<?php echo e($kpiComparison['avg_kehadiran'] >= 0 ? 'up' : 'down'); ?>"></i>
            <?php echo e(abs($kpiComparison['avg_kehadiran'])); ?>% vs sebelumnya
        </div>
    </div>

    <div class="stat-box">
        <div class="stat-label">Santri Perlu Perhatian</div>
        <div class="stat-value"><?php echo e($kpi['santri_perlu_perhatian']); ?></div>
        <div class="stat-sub <?php echo e($kpiComparison['santri_perlu_perhatian'] <= 0 ? 'up' : 'down'); ?>">
            <i class="fas fa-arrow-<?php echo e($kpiComparison['santri_perlu_perhatian'] <= 0 ? 'down' : 'up'); ?>"></i>
            <?php echo e(abs($kpiComparison['santri_perlu_perhatian'])); ?> vs sebelumnya
        </div>
    </div>

    <div class="stat-box">
        <div class="stat-label">Kehadiran Terbaik</div>
        <div class="stat-value" style="font-size: 1rem;"><?php echo e($kpi['kegiatan_terbaik']['nama']); ?></div>
        <div class="stat-sub up">
            <i class="fas fa-trophy"></i> <?php echo e($kpi['kegiatan_terbaik']['persen']); ?>%
        </div>
    </div>

    <div class="stat-box">
        <div class="stat-label">Kehadiran Terendah</div>
        <?php
            $terendah = !empty($bottomKegiatan) ? $bottomKegiatan[0] : null;
        ?>
        <div class="stat-value" style="font-size: 1rem; color: #EF4444;">
            <?php echo e($terendah ? $terendah['nama_kegiatan'] : '-'); ?>

        </div>
        <div class="stat-sub down">
            <i class="fas fa-arrow-down"></i> <?php echo e($terendah ? $terendah['persen'] . '%' : '-'); ?>

        </div>
    </div>
</div>


<div class="content-box" style="margin-bottom: 16px; padding: 12px;">
    <?php if(!empty($patterns) && count($patterns) > 0): ?>
        <?php $__currentLoopData = $patterns; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="alert-row a-<?php echo e($p['type']); ?>">
                <?php if($p['type'] === 'danger'): ?>
                    <i class="fas fa-exclamation-circle"></i>
                <?php elseif($p['type'] === 'warning'): ?>
                    <i class="fas fa-exclamation-triangle"></i>
                <?php else: ?>
                    <i class="fas fa-info-circle"></i>
                <?php endif; ?>
                <div class="alert-text">
                    <div class="alert-title"><?php echo e($p['title']); ?></div>
                    <div class="alert-desc"><?php echo e($p['description']); ?></div>
                </div>
                <?php if(!empty($p['action_url'])): ?>
                    <a href="<?php echo e($p['action_url']); ?>"
                       class="btn btn-sm btn-<?php echo e($p['type'] === 'danger' ? 'danger' : ($p['type'] === 'warning' ? 'warning' : 'info')); ?>">
                        <?php echo e($p['action_text'] ?? 'Lihat'); ?>

                    </a>
                <?php endif; ?>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    <?php else: ?>
        <div class="alert-row a-success">
            <i class="fas fa-check-circle"></i>
            <div class="alert-text">
                <div class="alert-title">Tidak ada anomali terdeteksi</div>
                <div class="alert-desc">Pola kehadiran dalam kondisi normal.</div>
            </div>
        </div>
    <?php endif; ?>
</div>


<?php
    $topBolos       = $santriPerluPerhatianList ? $santriPerluPerhatianList->sortByDesc('alpa')->take(5) : collect();
    $dangerPatterns = collect($patterns ?? [])->where('type', 'danger');
?>

<div class="main-cards">
    
    <div class="lk-card">
        <div class="lk-card-title" style="color: #EF4444;">
            <i class="fas fa-user-times"></i> Santri Sering Bolos
        </div>
        <?php if($topBolos->count() > 0): ?>
            <?php $__currentLoopData = $topBolos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="lk-card-item">
                    <a href="<?php echo e(route('admin.laporan-kegiatan.detail-santri', $s->id_santri)); ?>"
                       class="item-name" style="color: inherit; text-decoration: none;">
                        <?php echo e($s->nama_lengkap); ?>

                    </a>
                    <span class="badge badge-danger"><?php echo e($s->alpa); ?>x alpa</span>
                    <span class="badge badge-danger"><?php echo e($s->persen); ?>%</span>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <a href="<?php echo e(route('admin.laporan-kegiatan.santri-perlu-perhatian', request()->query())); ?>"
               class="btn btn-sm btn-secondary" style="margin-top: 10px;">
                <i class="fas fa-list"></i> Lihat Semua
            </a>
        <?php else: ?>
            <div class="lk-card-empty">
                <i class="fas fa-check-circle"></i> Tidak ada santri bermasalah
            </div>
        <?php endif; ?>
    </div>

    
    <div class="lk-card">
        <div class="lk-card-title" style="color: #F59E0B;">
            <i class="fas fa-calendar-times"></i> Kegiatan Paling Sepi
        </div>
        <?php if(!empty($bottomKegiatan) && count($bottomKegiatan) > 0): ?>
            <?php $__currentLoopData = $bottomKegiatan; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $kg): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="lk-card-item">
                    <a href="<?php echo e(route('admin.laporan-kegiatan.analisis-kegiatan', $kg['kegiatan_id'])); ?>"
                       class="item-name" style="color: inherit; text-decoration: none;">
                        <?php echo e($kg['nama_kegiatan']); ?>

                    </a>
                    <span class="badge badge-info"><?php echo e($kg['nama_kategori'] ?? '-'); ?></span>
                    <span style="font-weight: 700; color: <?php echo e($kg['persen'] < 70 ? '#EF4444' : ($kg['persen'] < 85 ? '#F59E0B' : '#10B981')); ?>;">
                        <?php echo e($kg['persen']); ?>%
                    </span>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <?php else: ?>
            <div class="lk-card-empty">
                <i class="fas fa-check-circle"></i> Semua kegiatan berjalan baik
            </div>
        <?php endif; ?>
    </div>

    
    <div class="lk-card">
        <div class="lk-card-title" style="color: #EF4444;">
            <i class="fas fa-bell"></i> Perlu Ditindaklanjuti
        </div>
        <?php if($dangerPatterns->count() > 0): ?>
            <?php $__currentLoopData = $dangerPatterns; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="pattern-item">
                    <div class="pattern-item-title">
                        <i class="fas fa-exclamation-circle" style="color: #EF4444;"></i>
                        <?php echo e($p['title']); ?>

                    </div>
                    <div class="pattern-item-desc"><?php echo e($p['description']); ?></div>
                    <?php if(!empty($p['action_url'])): ?>
                        <a href="<?php echo e($p['action_url']); ?>" class="btn btn-sm btn-danger" style="margin-top: 6px;">
                            <?php echo e($p['action_text'] ?? 'Tindakan'); ?>

                        </a>
                    <?php endif; ?>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <?php else: ?>
            <div class="lk-card-empty">
                <i class="fas fa-check-circle"></i> Semua kondisi normal
            </div>
        <?php endif; ?>
    </div>
</div>


<?php
    $avgTrendData   = [];
    $trendLabelsArr = $trendData['labels'] ?? [];
    $trendDatasets  = $trendData['datasets'] ?? [];
    $labelCount     = count($trendLabelsArr);
    for ($li = 0; $li < $labelCount; $li++) {
        $sum = 0; $cnt = 0;
        foreach ($trendDatasets as $ds) {
            if (isset($ds['data'][$li]) && $ds['data'][$li] !== null) {
                $sum += $ds['data'][$li]; $cnt++;
            }
        }
        $avgTrendData[] = $cnt > 0 ? round($sum / $cnt, 1) : 0;
    }
?>

<div class="chart-wrap">
    <h4><i class="fas fa-chart-line"></i> Trend Kehadiran Rata-rata</h4>
    <div style="position: relative; height: 200px;">
        <canvas id="trendChart"></canvas>
    </div>
</div>


<div class="kelas-section">
    <h4><i class="fas fa-school"></i> Kehadiran Per Kelas</h4>
    <?php $__currentLoopData = $kehadiranPerKelas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $kelompok): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <details>
            <summary><?php echo e($kelompok['nama_kelompok']); ?> (<?php echo e(count($kelompok['kelas'])); ?> kelas)</summary>
            <div class="kelas-detail-body">
                <table class="data-table" style="font-size: 0.85rem;">
                    <thead>
                        <tr>
                            <th>Kelas</th>
                            <th class="text-center">Hadir</th>
                            <th class="text-center">Alpa</th>
                            <th class="text-center">% Kehadiran</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $kelompok['kelas']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr class="<?php echo e($k['persen'] < 70 ? 'row-low' : ''); ?>">
                                <td><strong><?php echo e($k['nama_kelas']); ?></strong></td>
                                <td class="text-center"><?php echo e($k['hadir']); ?></td>
                                <td class="text-center"><?php echo e($k['alpa']); ?></td>
                                <td class="text-center">
                                    <strong style="color: <?php echo e($k['persen'] >= 85 ? '#10B981' : ($k['persen'] >= 70 ? '#F59E0B' : '#EF4444')); ?>;">
                                        <?php echo e($k['persen']); ?>%
                                    </strong>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
        </details>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
function setPeriode(p) {
    document.getElementById('periodeInput').value = p;
    document.querySelectorAll('.period-btn').forEach(function(b) { b.classList.remove('active'); });
    event.target.classList.add('active');
    var cr = document.getElementById('customRange');
    if (p === 'custom') {
        cr.classList.add('show');
    } else {
        cr.classList.remove('show');
        document.getElementById('periodForm').submit();
    }
}

new Chart(document.getElementById('trendChart'), {
    type: 'line',
    data: {
        labels: [
            <?php $__currentLoopData = $trendLabelsArr; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                '<?php echo addslashes($label); ?>',
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        ],
        datasets: [{
            label: 'Rata-rata Kehadiran',
            data: [<?php echo e(implode(',', $avgTrendData)); ?>],
            borderColor: '#10B981',
            backgroundColor: 'rgba(16, 185, 129, 0.1)',
            tension: 0.4,
            fill: true,
            pointRadius: 3,
            pointHoverRadius: 5
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: false },
            tooltip: { callbacks: { label: function(ctx) { return ctx.parsed.y + '%'; } } }
        },
        scales: {
            y: { min: 0, max: 100, ticks: { callback: function(v) { return v + '%'; } }, grid: { color: '#f1f5f9' } },
            x: { grid: { display: false } }
        }
    }
});
</script>

<style>
@media print {
    .page-header .btn, .period-bar, .btn, button { display: none !important; }
    .content-box, .lk-card, .stat-box, .chart-wrap { box-shadow: none !important; border: 1px solid #e2e8f0; }
    .kelas-section details { box-shadow: none !important; border: 1px solid #e2e8f0; }
}
</style>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\TugasAkhir\sim-pkpps\resources\views/admin/kegiatan/laporan/index.blade.php ENDPATH**/ ?>