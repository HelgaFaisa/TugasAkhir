

<?php $__env->startSection('title', 'Capaian Al-Qur\'an & Hadist'); ?>

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <h2><i class="fas fa-book-reader"></i> Capaian Al-Qur'an & Hadist</h2>
    <p style="margin: 5px 0 0 0; color: var(--text-light);">
        Pantau progres hafalan dan pembelajaran Anda
    </p>
</div>


<?php if(session('success')): ?>
<div class="alert alert-success">
    <i class="fas fa-check-circle"></i> <?php echo e(session('success')); ?>

</div>
<?php endif; ?>


<div class="row-cards">
    
    <div class="card card-primary">
        <h3><i class="fas fa-list-check"></i> Total Materi</h3>
        <div class="card-value"><?php echo e($totalCapaian); ?></div>
        <div class="card-icon"><i class="fas fa-list-check"></i></div>
    </div>
    
    
    <div class="card card-info">
        <h3><i class="fas fa-chart-line"></i> Rata-rata Progress</h3>
        <div class="card-value"><?php echo e(number_format($rataRataPersentase, 1)); ?>%</div>
        <div class="card-icon"><i class="fas fa-chart-line"></i></div>
        <div style="margin-top: 10px;">
            <div class="progress-bar">
                <div class="progress-fill" style="width: <?php echo e($rataRataPersentase); ?>%; background: var(--info-color);"></div>
            </div>
        </div>
    </div>
    
    
    <div class="card card-success">
        <h3><i class="fas fa-check-circle"></i> Materi Selesai</h3>
        <div class="card-value"><?php echo e($materiSelesai); ?></div>
        <div class="card-icon"><i class="fas fa-check-circle"></i></div>
        <?php if($totalCapaian > 0): ?>
        <p style="margin-top: 10px; color: var(--text-light); font-size: 0.9rem;">
            <?php echo e(number_format(($materiSelesai / $totalCapaian) * 100, 1)); ?>% dari total materi
        </p>
        <?php endif; ?>
    </div>
    
    
    <div class="card card-secondary">
        <h3><i class="fas fa-graduation-cap"></i> Kelas</h3>
        <div class="card-value-small"><?php echo e($santri->kelas); ?></div>
        <div class="card-icon"><i class="fas fa-graduation-cap"></i></div>
        <p style="margin-top: 10px; color: var(--text-light); font-size: 0.9rem;">
            NIS: <?php echo e($santri->nis ?? '-'); ?>

        </p>
    </div>
</div>


<div class="content-box" style="margin-bottom: 25px;">
    <h3 style="margin-bottom: 20px; color: var(--primary-dark);">
        <i class="fas fa-chart-pie"></i> Statistik per Kategori
    </h3>
    
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 20px;">
        <?php $__currentLoopData = $statistikKategori; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $kategori => $data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div style="background: linear-gradient(135deg, #FFFFFF 0%, #FEFFFE 100%); border-radius: var(--border-radius-sm); padding: 20px; box-shadow: var(--shadow-sm); border-left: 4px solid 
            <?php if($kategori == 'Al-Qur\'an'): ?> var(--success-color)
            <?php elseif($kategori == 'Hadist'): ?> var(--info-color)
            <?php else: ?> var(--warning-color)
            <?php endif; ?>
        ;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                <h4 style="margin: 0; font-size: 1rem; color: var(--text-color);"><?php echo e($kategori); ?></h4>
                <span style="background: 
                    <?php if($kategori == 'Al-Qur\'an'): ?> rgba(111, 186, 157, 0.1)
                    <?php elseif($kategori == 'Hadist'): ?> rgba(129, 198, 232, 0.1)
                    <?php else: ?> rgba(255, 213, 107, 0.1)
                    <?php endif; ?>
                ; padding: 5px 10px; border-radius: 20px; font-size: 0.85rem; font-weight: 600; color: 
                    <?php if($kategori == 'Al-Qur\'an'): ?> var(--success-color)
                    <?php elseif($kategori == 'Hadist'): ?> var(--info-color)
                    <?php else: ?> var(--warning-color)
                    <?php endif; ?>
                ;">
                    <?php echo e($data['count']); ?> Materi
                </span>
            </div>
            
            <div style="margin-bottom: 12px;">
                <div style="display: flex; justify-content: space-between; margin-bottom: 5px;">
                    <span style="font-size: 0.85rem; color: var(--text-light);">Progress</span>
                    <span style="font-size: 0.85rem; font-weight: 600; color: var(--text-color);"><?php echo e(number_format($data['avg'], 1)); ?>%</span>
                </div>
                <div class="progress-bar">
                    <div class="progress-fill" style="width: <?php echo e($data['avg']); ?>%; background: 
                        <?php if($kategori == 'Al-Qur\'an'): ?> var(--success-color)
                        <?php elseif($kategori == 'Hadist'): ?> var(--info-color)
                        <?php else: ?> var(--warning-color)
                        <?php endif; ?>
                    ;"></div>
                </div>
            </div>
            
            <div style="display: flex; justify-content: space-between; font-size: 0.85rem; color: var(--text-light);">
                <span><i class="fas fa-check-circle" style="color: var(--success-color); margin-right: 5px;"></i> <?php echo e($data['selesai']); ?> Selesai</span>
                <span><i class="fas fa-hourglass-half" style="color: var(--warning-color); margin-right: 5px;"></i> <?php echo e($data['count'] - $data['selesai']); ?> Berlangsung</span>
            </div>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
</div>


<div class="content-box" style="margin-bottom: 25px;">
    <h3 style="margin-bottom: 20px; color: var(--primary-dark);">
        <i class="fas fa-chart-bar"></i> Visualisasi Progress
    </h3>
    
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); gap: 25px;">
        
        <div style="background: white; padding: 20px; border-radius: var(--border-radius-sm); box-shadow: var(--shadow-sm);">
            <h4 style="margin: 0 0 15px 0; font-size: 0.95rem; color: var(--text-color); text-align: center;">Progress per Kategori</h4>
            <canvas id="chartKategori" style="max-height: 300px;"></canvas>
        </div>
        
        
        <div style="background: white; padding: 20px; border-radius: var(--border-radius-sm); box-shadow: var(--shadow-sm);">
            <h4 style="margin: 0 0 15px 0; font-size: 0.95rem; color: var(--text-color); text-align: center;">Distribusi Persentase</h4>
            <canvas id="chartDistribusi" style="max-height: 300px;"></canvas>
        </div>
    </div>
</div>


<div class="content-box">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 15px;">
        <h3 style="margin: 0; color: var(--primary-dark);">
            <i class="fas fa-list"></i> Daftar Capaian
        </h3>
        
        
        <form method="GET" style="display: flex; gap: 10px; align-items: center;">
            <select name="id_semester" class="form-control" style="width: auto; min-width: 200px;" onchange="this.form.submit()">
                <option value="">-- Semua Semester --</option>
                <?php $__currentLoopData = $semesters; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $semester): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($semester->id_semester); ?>" <?php echo e($selectedSemester == $semester->id_semester ? 'selected' : ''); ?>>
                    <?php echo e($semester->nama_semester); ?>

                    <?php if($semesterAktif && $semester->id_semester == $semesterAktif->id_semester): ?> 
                        <span style="color: var(--success-color);">● Aktif</span> 
                    <?php endif; ?>
                </option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </form>
    </div>
    
    <?php if($capaians->count() > 0): ?>
    <div class="table-container">
        <table class="data-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Materi</th>
                    <th>Kategori</th>
                    <th>Halaman Selesai</th>
                    <th>Progress</th>
                    <th>Tanggal Input</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php $__currentLoopData = $capaians; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $capaian): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td><?php echo e($index + 1); ?></td>
                    <td>
                        <strong><?php echo e($capaian->materi->nama_kitab); ?></strong>
                    </td>
                    <td>
                        <span class="badge 
                            <?php if($capaian->materi->kategori == 'Al-Qur\'an'): ?> badge-success
                            <?php elseif($capaian->materi->kategori == 'Hadist'): ?> badge-info
                            <?php else: ?> badge-warning
                            <?php endif; ?>
                        ">
                            <?php echo e($capaian->materi->kategori); ?>

                        </span>
                    </td>
                    <td>
                        <span style="font-size: 0.9rem; color: var(--text-light);">
                            <?php echo e(count($capaian->pages_array)); ?> dari <?php echo e($capaian->materi->total_halaman); ?> halaman
                        </span>
                    </td>
                    <td>
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <div class="progress-bar" style="flex: 1; height: 8px;">
                                <div class="progress-fill" style="width: <?php echo e($capaian->persentase); ?>%; background: 
                                    <?php if($capaian->persentase >= 100): ?> var(--success-color)
                                    <?php elseif($capaian->persentase >= 75): ?> var(--info-color)
                                    <?php elseif($capaian->persentase >= 50): ?> var(--warning-color)
                                    <?php else: ?> var(--danger-color)
                                    <?php endif; ?>
                                ;"></div>
                            </div>
                            <span style="font-weight: 600; min-width: 50px; text-align: right; color: var(--text-color);">
                                <?php echo e(number_format($capaian->persentase, 1)); ?>%
                            </span>
                        </div>
                    </td>
                    <td><?php echo e(\Carbon\Carbon::parse($capaian->tanggal_input)->format('d M Y')); ?></td>
                    <td class="text-center">
                        <a href="<?php echo e(route('santri.capaian.show', $capaian->id)); ?>" class="btn btn-sm btn-primary">
                            <i class="fas fa-eye"></i> Detail
                        </a>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
    </div>
    <?php else: ?>
    <div class="empty-state">
        <i class="fas fa-book-open"></i>
        <h3>Belum Ada Data Capaian</h3>
        <p>
            <?php if($selectedSemester): ?>
                Tidak ada data capaian untuk semester yang dipilih.
            <?php else: ?>
                Belum ada data capaian yang tercatat untuk Anda.
            <?php endif; ?>
        </p>
    </div>
    <?php endif; ?>
</div>


<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Chart Kategori
    const ctxKategori = document.getElementById('chartKategori').getContext('2d');
    new Chart(ctxKategori, {
        type: 'bar',
        data: {
            labels: ['Al-Qur\'an', 'Hadist', 'Materi Tambahan'],
            datasets: [{
                label: 'Progress (%)',
                data: [
                    <?php echo e($statistikKategori['Al-Qur\'an']['avg']); ?>,
                    <?php echo e($statistikKategori['Hadist']['avg']); ?>,
                    <?php echo e($statistikKategori['Materi Tambahan']['avg']); ?>

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
                    display: false
                }
            },
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
            }
        }
    });
    
    // Chart Distribusi
    const ctxDistribusi = document.getElementById('chartDistribusi').getContext('2d');
    new Chart(ctxDistribusi, {
        type: 'doughnut',
        data: {
            labels: ['0-25%', '26-50%', '51-75%', '76-99%', '100%'],
            datasets: [{
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
                borderColor: '#fff',
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
});
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\TugasAkhir\sim-pkpps\resources\views/santri/capaian/index.blade.php ENDPATH**/ ?>