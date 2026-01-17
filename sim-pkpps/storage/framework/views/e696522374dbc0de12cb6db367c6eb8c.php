

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <h2><i class="fas fa-chart-pie"></i> Dashboard Capaian Al-Qur'an & Hadist</h2>
</div>


<div class="content-box" style="margin-bottom: 20px;">
    <form method="GET" action="<?php echo e(route('admin.capaian.dashboard')); ?>" class="filter-form-inline">
        <select name="id_santri" class="form-control" style="width: 250px;">
            <option value="">Semua Santri</option>
            <?php $__currentLoopData = $santris; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $santri): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($santri->id_santri); ?>" <?php echo e($idSantri == $santri->id_santri ? 'selected' : ''); ?>>
                    <?php echo e($santri->nama_lengkap); ?> (<?php echo e($santri->kelas); ?>)
                </option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>

        <select name="id_semester" class="form-control" style="width: 200px;">
            <option value="">Semua Semester</option>
            <?php $__currentLoopData = $semesters; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $semester): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($semester->id_semester); ?>" <?php echo e($selectedSemester == $semester->id_semester ? 'selected' : ''); ?>>
                    <?php echo e($semester->nama_semester); ?> <?php if($semester->is_active): ?> ★ <?php endif; ?>
                </option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>

        <select name="kelas" class="form-control" style="width: 150px;">
            <option value="">Semua Kelas</option>
            <option value="Lambatan" <?php echo e($kelas == 'Lambatan' ? 'selected' : ''); ?>>Lambatan</option>
            <option value="Cepatan" <?php echo e($kelas == 'Cepatan' ? 'selected' : ''); ?>>Cepatan</option>
            <option value="PB" <?php echo e($kelas == 'PB' ? 'selected' : ''); ?>>PB</option>
        </select>

        <button type="submit" class="btn btn-primary">
            <i class="fas fa-filter"></i> Filter
        </button>

        <?php if($idSantri || $kelas): ?>
            <a href="<?php echo e(route('admin.capaian.dashboard')); ?>" class="btn btn-secondary">
                <i class="fas fa-redo"></i> Reset
            </a>
        <?php endif; ?>
    </form>
</div>


<div class="row-cards">
    <div class="card card-info">
        <h3>Total Capaian</h3>
        <div class="card-value"><?php echo e($totalCapaian); ?></div>
        <p class="text-muted">Data capaian tercatat</p>
        <i class="fas fa-clipboard-list card-icon"></i>
    </div>
    <div class="card card-success">
        <h3>Total Santri</h3>
        <div class="card-value"><?php echo e($totalSantri); ?></div>
        <p class="text-muted">Santri aktif dengan capaian</p>
        <i class="fas fa-users card-icon"></i>
    </div>
    <div class="card card-primary">
        <h3>Rata-rata Progress</h3>
        <div class="card-value"><?php echo e(number_format($rataRataPersentase, 1)); ?>%</div>
        <p class="text-muted">Progress keseluruhan</p>
        <i class="fas fa-chart-line card-icon"></i>
    </div>
    <div class="card card-warning">
        <h3>Selesai 100%</h3>
        <div class="card-value"><?php echo e($capaianSelesai); ?></div>
        <p class="text-muted">Materi yang diselesaikan</p>
        <i class="fas fa-trophy card-icon"></i>
    </div>
</div>


<div class="row-cards">
    <?php $__currentLoopData = $statistikKategori; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $kategori => $stats): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="card card-<?php echo e($kategori == 'Al-Qur\'an' ? 'primary' : ($kategori == 'Hadist' ? 'success' : 'info')); ?>">
            <h3><?php echo e($kategori); ?></h3>
            <div class="card-value-small"><?php echo e(number_format($stats['avg'], 1)); ?>%</div>
            <p class="text-muted">
                <?php echo e($stats['count']); ?> capaian | <?php echo e($stats['selesai']); ?> selesai
            </p>
            <i class="fas fa-<?php echo e($kategori == 'Al-Qur\'an' ? 'book-quran' : ($kategori == 'Hadist' ? 'scroll' : 'book')); ?> card-icon"></i>
        </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>


<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
    
    <div class="content-box">
        <h4 style="margin: 0 0 20px 0; color: var(--primary-dark);">
            <i class="fas fa-chart-pie"></i> Progress per Kategori
        </h4>
        <canvas id="chartKategori" style="max-height: 300px;"></canvas>
    </div>

    
    <div class="content-box">
        <h4 style="margin: 0 0 20px 0; color: var(--primary-dark);">
            <i class="fas fa-chart-bar"></i> Distribusi Progress Santri
        </h4>
        <canvas id="chartDistribusi" style="max-height: 300px;"></canvas>
    </div>
</div>


<div class="content-box" style="margin-bottom: 20px;">
    <h4 style="margin: 0 0 20px 0; color: var(--primary-dark);">
        <i class="fas fa-chart-line"></i> Trend Progress dari Waktu ke Waktu
    </h4>
    <canvas id="chartTrend" style="max-height: 250px;"></canvas>
</div>


<div class="content-box" style="margin-bottom: 20px;">
    <h4 style="margin: 0 0 20px 0; color: var(--primary-dark);">
        <i class="fas fa-trophy"></i> Top 10 Santri dengan Progress Tertinggi
    </h4>
    <?php if($topSantri->count() > 0): ?>
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 5%;">Rank</th>
                    <th style="width: 10%;">NIS</th>
                    <th style="width: 30%;">Nama Santri</th>
                    <th style="width: 10%;">Kelas</th>
                    <th style="width: 25%;">Rata-rata Progress</th>
                    <th class="text-center" style="width: 20%;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php $__currentLoopData = $topSantri; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td class="text-center">
                            <?php if($index < 3): ?>
                                <span style="font-size: 1.5rem;">
                                    <?php if($index == 0): ?> 🥇
                                    <?php elseif($index == 1): ?> 🥈
                                    <?php else: ?> 🥉
                                    <?php endif; ?>
                                </span>
                            <?php else: ?>
                                <strong><?php echo e($index + 1); ?></strong>
                            <?php endif; ?>
                        </td>
                        <td><?php echo e($item->santri->nis); ?></td>
                        <td><strong><?php echo e($item->santri->nama_lengkap); ?></strong></td>
                        <td><span class="badge badge-secondary"><?php echo e($item->santri->kelas); ?></span></td>
                        <td>
                            <div class="progress-bar" style="height: 25px;">
                                <div class="progress-fill" style="width: <?php echo e($item->rata_rata); ?>%; background: linear-gradient(90deg, var(--primary-color), var(--success-color)); display: flex; align-items: center; justify-content: center; color: white; font-weight: bold;">
                                    <?php echo e(number_format($item->rata_rata, 1)); ?>%
                                </div>
                            </div>
                        </td>
                        <td class="text-center">
                            <a href="<?php echo e(route('admin.capaian.riwayat-santri', $item->id_santri)); ?>" class="btn btn-sm btn-info">
                                <i class="fas fa-eye"></i> Lihat Detail
                            </a>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
    <?php else: ?>
        <div class="empty-state">
            <i class="fas fa-chart-line"></i>
            <p>Belum ada data untuk ditampilkan</p>
        </div>
    <?php endif; ?>
</div>


<?php if($materiTerendah->count() > 0): ?>
<div class="content-box">
    <h4 style="margin: 0 0 20px 0; color: var(--danger-color);">
        <i class="fas fa-exclamation-triangle"></i> Materi yang Perlu Perhatian (Progress < 50%)
    </h4>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 30%;">Nama Materi</th>
                <th style="width: 15%;">Kategori</th>
                <th style="width: 10%;">Kelas</th>
                <th style="width: 10%;">Jumlah Santri</th>
                <th style="width: 20%;">Rata-rata Progress</th>
                <th class="text-center" style="width: 15%;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php $__currentLoopData = $materiTerendah; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td><strong><?php echo e($item->materi->nama_kitab); ?></strong></td>
                    <td><?php echo $item->materi->kategori_badge; ?></td>
                    <td><?php echo $item->materi->kelas_badge; ?></td>
                    <td class="text-center"><?php echo e($item->jumlah_santri); ?> santri</td>
                    <td>
                        <div class="progress-bar" style="height: 20px;">
                            <div class="progress-fill" style="width: <?php echo e($item->rata_rata); ?>%; background: linear-gradient(90deg, var(--danger-color), var(--warning-color)); display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; font-size: 0.85rem;">
                                <?php echo e(number_format($item->rata_rata, 1)); ?>%
                            </div>
                        </div>
                    </td>
                    <td class="text-center">
                        <a href="<?php echo e(route('admin.capaian.detail-materi', $item->id_materi)); ?>" class="btn btn-sm btn-warning">
                            <i class="fas fa-eye"></i> Detail
                        </a>
                    </td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table>
</div>
<?php endif; ?>


<div style="margin-top: 30px; display: flex; gap: 10px; justify-content: center;">
    <a href="<?php echo e(route('admin.capaian.rekap-kelas')); ?>" class="btn btn-primary">
        <i class="fas fa-table"></i> Rekap per Kelas
    </a>
    <a href="<?php echo e(route('admin.capaian.create')); ?>" class="btn btn-success">
        <i class="fas fa-plus"></i> Input Capaian Baru
    </a>
    <a href="<?php echo e(route('admin.materi.index')); ?>" class="btn btn-info">
        <i class="fas fa-book"></i> Master Materi
    </a>
</div>


<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Chart 1: Pie Chart - Progress per Kategori
const ctxKategori = document.getElementById('chartKategori').getContext('2d');
const chartKategori = new Chart(ctxKategori, {
    type: 'pie',
    data: {
        labels: ['Al-Qur\'an', 'Hadist', 'Materi Tambahan'],
        datasets: [{
            label: 'Rata-rata Progress (%)',
            data: [
                <?php echo e(number_format($statistikKategori['Al-Qur\'an']['avg'], 2)); ?>,
                <?php echo e(number_format($statistikKategori['Hadist']['avg'], 2)); ?>,
                <?php echo e(number_format($statistikKategori['Materi Tambahan']['avg'], 2)); ?>

            ],
            backgroundColor: [
                'rgba(111, 186, 157, 0.8)',
                'rgba(129, 198, 232, 0.8)',
                'rgba(255, 213, 107, 0.8)',
            ],
            borderColor: [
                'rgba(111, 186, 157, 1)',
                'rgba(129, 198, 232, 1)',
                'rgba(255, 213, 107, 1)',
            ],
            borderWidth: 2
        }]
        },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: {
                position: 'bottom',
                labels: {
                    padding: 15,
                    font: {
                        size: 12
                    }
                }
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        return context.label + ': ' + context.parsed.toFixed(2) + '%';
                    }
                }
            }
        }
    }
});

// Chart 2: Bar Chart - Distribusi Persentase
const ctxDistribusi = document.getElementById('chartDistribusi').getContext('2d');
const chartDistribusi = new Chart(ctxDistribusi, {
    type: 'bar',
    data: {
        labels: ['0-25%', '26-50%', '51-75%', '76-99%', '100%'],
        datasets: [{
            label: 'Jumlah Santri',
            data: [
                <?php echo e($distribusiPersentase['0-25%']); ?>,
                <?php echo e($distribusiPersentase['26-50%']); ?>,
                <?php echo e($distribusiPersentase['51-75%']); ?>,
                <?php echo e($distribusiPersentase['76-99%']); ?>,
                <?php echo e($distribusiPersentase['100%']); ?>

            ],
            backgroundColor: [
                'rgba(255, 139, 148, 0.8)',
                'rgba(255, 171, 145, 0.8)',
                'rgba(255, 213, 107, 0.8)',
                'rgba(129, 198, 232, 0.8)',
                'rgba(111, 186, 157, 0.8)',
            ],
            borderColor: [
                'rgba(255, 139, 148, 1)',
                'rgba(255, 171, 145, 1)',
                'rgba(255, 213, 107, 1)',
                'rgba(129, 198, 232, 1)',
                'rgba(111, 186, 157, 1)',
            ],
            borderWidth: 2
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    stepSize: 1
                }
            }
        },
        plugins: {
            legend: {
                display: false
            }
        }
    }
});

// Chart 3: Line Chart - Trend Progress (Load via AJAX)
fetch('<?php echo e(route("admin.capaian.api.grafik-data")); ?>?type=trend&id_semester=<?php echo e($selectedSemester); ?>&kelas=<?php echo e($kelas); ?>')
    .then(response => response.json())
    .then(data => {
        const ctxTrend = document.getElementById('chartTrend').getContext('2d');
        const chartTrend = new Chart(ctxTrend, {
            type: 'line',
            data: data,
            options: {
                responsive: true,
                maintainAspectRatio: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100,
                        ticks: {
                            callback: function(value) {
                                return value + '%';
                            }
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: true,
                        position: 'bottom'
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.dataset.label + ': ' + context.parsed.y.toFixed(2) + '%';
                            }
                        }
                    }
                }
            }
        });
    })
    .catch(error => console.error('Error loading trend chart:', error));
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\TugasAkhir\sim-pkpps\resources\views/admin/capaian/dashboard.blade.php ENDPATH**/ ?>