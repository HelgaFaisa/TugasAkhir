

<?php $__env->startSection('title', 'Riwayat Kegiatan & Absensi'); ?>

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <h2><i class="fas fa-calendar-check"></i> Riwayat Kegiatan & Absensi</h2>
    <p style="margin: 5px 0 0 0; color: var(--text-light);">
        <?php echo e($santri->nama_lengkap); ?> - Kelas <?php echo e($santri->kelas); ?>

    </p>
</div>


<div class="content-box" style="margin-bottom: 25px; background: linear-gradient(135deg, #E8F7F2 0%, #D4F1E3 100%); border: 2px solid var(--primary-color);">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
        <h3 style="margin: 0; color: var(--primary-dark);">
            <i class="fas fa-clock"></i> Jadwal Kegiatan Hari Ini (<?php echo e(ucfirst($hariIni)); ?>)
        </h3>
        <span class="badge badge-primary badge-lg">
            <i class="fas fa-calendar-day"></i> <?php echo e(\Carbon\Carbon::now()->locale('id')->isoFormat('D MMMM YYYY')); ?>

        </span>
    </div>
    
    <?php if($jadwalHariIni->count() > 0): ?>
        <div style="display: flex; flex-direction: column; gap: 12px;">
            <?php $__currentLoopData = $jadwalHariIni; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $jadwal): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div style="background: white; padding: 18px; border-radius: var(--border-radius-sm); border-left: 4px solid <?php echo e(isset($absensiHariIni[$jadwal->kegiatan_id]) ? 'var(--success-color)' : 'var(--warning-color)'); ?>; display: flex; justify-content: space-between; align-items: center; box-shadow: var(--shadow-sm);">
                <div style="flex: 1;">
                    <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 8px;">
                        <span class="badge badge-info"><?php echo e($jadwal->kategori->nama_kategori); ?></span>
                        <h4 style="margin: 0; font-size: 1.1rem; color: var(--text-color);"><?php echo e($jadwal->nama_kegiatan); ?></h4>
                    </div>
                    <div style="display: flex; align-items: center; gap: 15px; font-size: 0.9rem; color: var(--text-light);">
                        <span><i class="fas fa-clock"></i> <?php echo e(date('H:i', strtotime($jadwal->waktu_mulai))); ?> - <?php echo e(date('H:i', strtotime($jadwal->waktu_selesai))); ?></span>
                        <?php if($jadwal->materi): ?>
                        <span><i class="fas fa-book"></i> <?php echo e($jadwal->materi); ?></span>
                        <?php endif; ?>
                    </div>
                </div>
                <div>
                    <?php if(isset($absensiHariIni[$jadwal->kegiatan_id])): ?>
                        <span class="badge badge-success badge-lg">
                            <i class="fas fa-check-circle"></i> <?php echo e($absensiHariIni[$jadwal->kegiatan_id]); ?>

                        </span>
                    <?php else: ?>
                        <span class="badge badge-warning badge-lg">
                            <i class="fas fa-hourglass-half"></i> Belum Absen
                        </span>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    <?php else: ?>
        <div class="empty-state" style="padding: 40px; text-align: center;">
            <i class="fas fa-calendar-times" style="font-size: 3rem; color: #ccc;"></i>
            <p style="margin-top: 15px; color: var(--text-light);">Tidak ada jadwal kegiatan untuk hari ini.</p>
        </div>
    <?php endif; ?>
</div>


<div class="row-cards" style="margin-bottom: 25px;">
    <div class="card card-success">
        <h3><i class="fas fa-check-circle"></i> Total Kehadiran</h3>
        <div class="card-value"><?php echo e($stats30Hari['Hadir'] ?? 0); ?></div>
        <div class="card-icon"><i class="fas fa-check-circle"></i></div>
        <p style="margin-top: 10px; font-size: 0.85rem; color: var(--text-light);">30 hari terakhir</p>
    </div>
    
    <div class="card card-info">
        <h3><i class="fas fa-percentage"></i> Persentase Kehadiran</h3>
        <div class="card-value"><?php echo e($persentaseKehadiran); ?>%</div>
        <div class="card-icon"><i class="fas fa-chart-line"></i></div>
        <div style="margin-top: 10px;">
            <div class="progress-bar">
                <div class="progress-fill" style="width: <?php echo e($persentaseKehadiran); ?>%; background: var(--info-color);"></div>
            </div>
        </div>
    </div>
    
    <div class="card card-warning">
        <h3><i class="fas fa-exclamation-triangle"></i> Izin / Sakit / Alpa</h3>
        <div class="card-value"><?php echo e(($stats30Hari['Izin'] ?? 0) + ($stats30Hari['Sakit'] ?? 0) + ($stats30Hari['Alpa'] ?? 0)); ?></div>
        <div class="card-icon"><i class="fas fa-exclamation-triangle"></i></div>
        <p style="margin-top: 10px; font-size: 0.85rem; color: var(--text-light);">
            Izin: <?php echo e($stats30Hari['Izin'] ?? 0); ?> | Sakit: <?php echo e($stats30Hari['Sakit'] ?? 0); ?> | Alpa: <?php echo e($stats30Hari['Alpa'] ?? 0); ?>

        </p>
    </div>
    
    <div class="card card-primary">
        <h3><i class="fas fa-list-check"></i> Total Kegiatan</h3>
        <div class="card-value"><?php echo e($totalKegiatan30Hari); ?></div>
        <div class="card-icon"><i class="fas fa-list-check"></i></div>
        <p style="margin-top: 10px; font-size: 0.85rem; color: var(--text-light);">30 hari terakhir</p>
    </div>
</div>


<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(450px, 1fr)); gap: 25px; margin-bottom: 25px;">
    
    
    <div class="content-box">
        <h3 style="margin-bottom: 20px; color: var(--primary-dark);">
            <i class="fas fa-chart-line"></i> Tren Kehadiran (4 Minggu Terakhir)
        </h3>
        <canvas id="chartTrenKehadiran" style="max-height: 300px;"></canvas>
    </div>
    
    
    <div class="content-box">
        <h3 style="margin-bottom: 20px; color: var(--primary-dark);">
            <i class="fas fa-chart-bar"></i> Kehadiran per Kategori Kegiatan
        </h3>
        <canvas id="chartKategori" style="max-height: 300px;"></canvas>
    </div>
</div>


<div class="content-box">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 15px;">
        <h3 style="margin: 0; color: var(--text-color);">
            <i class="fas fa-history"></i> Riwayat Absensi Lengkap
        </h3>
        
        
        <form method="GET" action="<?php echo e(route('santri.kegiatan.index')); ?>" style="display: flex; gap: 10px; flex-wrap: wrap;">
            <input type="month" name="bulan" class="form-control" value="<?php echo e(request('bulan')); ?>" 
                   style="max-width: 200px;" placeholder="Pilih Bulan">
            <select name="status" class="form-control" style="max-width: 150px;">
                <option value="">Semua Status</option>
                <option value="Hadir" <?php echo e(request('status') == 'Hadir' ? 'selected' : ''); ?>>Hadir</option>
                <option value="Izin" <?php echo e(request('status') == 'Izin' ? 'selected' : ''); ?>>Izin</option>
                <option value="Sakit" <?php echo e(request('status') == 'Sakit' ? 'selected' : ''); ?>>Sakit</option>
                <option value="Alpa" <?php echo e(request('status') == 'Alpa' ? 'selected' : ''); ?>>Alpa</option>
            </select>
            <button type="submit" class="btn btn-primary btn-sm">
                <i class="fas fa-filter"></i> Filter
            </button>
            <a href="<?php echo e(route('santri.kegiatan.index')); ?>" class="btn btn-secondary btn-sm">
                <i class="fas fa-redo"></i> Reset
            </a>
        </form>
    </div>
    
    <?php if($riwayats->count() > 0): ?>
        <div class="table-container">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Tanggal</th>
                        <th>Kegiatan</th>
                        <th>Kategori</th>
                        <th>Waktu Absen</th>
                        <th>Status</th>
                        <th>Metode</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $riwayats; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $absensi): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td><?php echo e($riwayats->firstItem() + $index); ?></td>
                        <td><?php echo e($absensi->tanggal_formatted); ?></td>
                        <td><strong><?php echo e($absensi->kegiatan->nama_kegiatan); ?></strong></td>
                        <td><span class="badge badge-info"><?php echo e($absensi->kegiatan->kategori->nama_kategori); ?></span></td>
                        <td><?php echo e($absensi->waktu_absen_formatted); ?></td>
                        <td>
                            <span class="badge <?php echo e($absensi->status_badge_class); ?>">
                                <i class="fas fa-<?php echo e($absensi->status == 'Hadir' ? 'check' : ($absensi->status == 'Izin' ? 'info-circle' : ($absensi->status == 'Sakit' ? 'heartbeat' : 'times'))); ?>-circle"></i>
                                <?php echo e($absensi->status); ?>

                            </span>
                        </td>
                        <td><span class="badge badge-secondary"><?php echo e($absensi->metode_absen); ?></span></td>
                        <td class="text-center">
                            <a href="<?php echo e(route('santri.kegiatan.show', $absensi->kegiatan_id)); ?>" class="btn btn-sm btn-primary" title="Lihat Detail Kegiatan">
                                <i class="fas fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
        
        
        <div style="margin-top: 20px;">
            <?php echo e($riwayats->links()); ?>

        </div>
    <?php else: ?>
        <div class="empty-state">
            <i class="fas fa-inbox"></i>
            <h3>Belum Ada Riwayat Absensi</h3>
            <p>Riwayat absensi Anda akan muncul di sini setelah mengikuti kegiatan.</p>
        </div>
    <?php endif; ?>
</div>


<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    
    // ============================================
    // GRAFIK 1: Tren Kehadiran per Minggu (LINE CHART)
    // ============================================
    const ctxTren = document.getElementById('chartTrenKehadiran');
    if (ctxTren) {
        new Chart(ctxTren.getContext('2d'), {
            type: 'line',
            data: {
                labels: [
                    <?php $__currentLoopData = $dataGrafikMingguan; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    '<?php echo e($data["minggu"]); ?>',
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                ],
                datasets: [
                    {
                        label: 'Hadir',
                        data: [
                            <?php $__currentLoopData = $dataGrafikMingguan; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php echo e($data['hadir']); ?>,
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        ],
                        borderColor: 'rgba(111, 186, 157, 1)',
                        backgroundColor: 'rgba(111, 186, 157, 0.2)',
                        borderWidth: 3,
                        pointRadius: 5,
                        pointBackgroundColor: 'rgba(111, 186, 157, 1)',
                        tension: 0.4,
                        fill: true
                    },
                    {
                        label: 'Total Kegiatan',
                        data: [
                            <?php $__currentLoopData = $dataGrafikMingguan; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php echo e($data['total']); ?>,
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        ],
                        borderColor: 'rgba(129, 198, 232, 1)',
                        backgroundColor: 'rgba(129, 198, 232, 0.1)',
                        borderWidth: 2,
                        pointRadius: 4,
                        pointBackgroundColor: 'rgba(129, 198, 232, 1)',
                        tension: 0.4,
                        fill: true,
                        borderDash: [5, 5]
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                        labels: {
                            font: { size: 12, weight: '600' },
                            padding: 15
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.dataset.label + ': ' + context.parsed.y;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1,
                            font: { size: 12, weight: '600' }
                        },
                        grid: { color: 'rgba(0, 0, 0, 0.05)' }
                    },
                    x: {
                        ticks: { font: { size: 12, weight: '600' } },
                        grid: { display: false }
                    }
                }
            }
        });
    }
    
    // ============================================
    // GRAFIK 2: Kehadiran per Kategori (BAR CHART)
    // ============================================
    const ctxKategori = document.getElementById('chartKategori');
    if (ctxKategori) {
        new Chart(ctxKategori.getContext('2d'), {
            type: 'bar',
            data: {
                labels: [
                    <?php $__currentLoopData = $statsByKategori; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $stat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    '<?php echo e($stat->nama_kategori); ?>',
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                ],
                datasets: [{
                    label: 'Kehadiran',
                    data: [
                        <?php $__currentLoopData = $statsByKategori; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $stat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php echo e($stat->hadir); ?>,
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    ],
                    backgroundColor: [
                        'rgba(111, 186, 157, 0.8)',
                        'rgba(129, 198, 232, 0.8)',
                        'rgba(255, 213, 107, 0.8)',
                        'rgba(255, 139, 148, 0.8)',
                        'rgba(179, 157, 219, 0.8)',
                    ],
                    borderColor: [
                        'rgba(111, 186, 157, 1)',
                        'rgba(129, 198, 232, 1)',
                        'rgba(255, 213, 107, 1)',
                        'rgba(255, 139, 148, 1)',
                        'rgba(179, 157, 219, 1)',
                    ],
                    borderWidth: 2,
                    borderRadius: 8,
                    borderSkipped: false
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return 'Hadir: ' + context.parsed.y + ' kali';
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1,
                            font: { size: 12, weight: '600' }
                        },
                        grid: { color: 'rgba(0, 0, 0, 0.05)' }
                    },
                    x: {
                        ticks: { 
                            font: { size: 11 },
                            maxRotation: 45,
                            minRotation: 45
                        },
                        grid: { display: false }
                    }
                }
            }
        });
    }
});
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\TugasAkhir\sim-pkpps\resources\views/santri/kegiatan/index.blade.php ENDPATH**/ ?>