

<?php $__env->startSection('title', 'Dashboard Santri'); ?>

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <h2><i class="fas fa-tachometer-alt"></i> Dashboard Progres</h2>
    <p style="margin: 5px 0 0 0; color: var(--text-light);">
        Selamat datang, <strong><?php echo e($data['nama_santri']); ?></strong> - Kelas <?php echo e($data['kelas']); ?>

        <?php if($semesterAktif): ?>
        <span class="badge badge-success" style="margin-left: 10px;">
            <i class="fas fa-calendar-alt"></i> <?php echo e($semesterAktif->nama_semester); ?>

        </span>
        <?php endif; ?>
    </p>
</div>


<?php if(isset($statusKesehatan) && $statusKesehatan): ?>
<div class="alert alert-danger">
    <i class="fas fa-exclamation-triangle"></i>
    <strong>Perhatian:</strong> Anda sedang dalam perawatan UKP sejak <?php echo e($statusKesehatan->tanggal_masuk_formatted); ?> 
    (<?php echo e($statusKesehatan->lama_dirawat); ?> hari). Keluhan: <strong><?php echo e($statusKesehatan->keluhan); ?></strong>.
    <a href="<?php echo e(route('santri.kesehatan.index')); ?>" style="color: inherit; text-decoration: underline; font-weight: 600;">Lihat Detail</a>
</div>
<?php endif; ?>

<?php if(isset($kepulanganAktif) && $kepulanganAktif): ?>
<div class="alert alert-info">
    <i class="fas fa-home"></i>
    <strong>Sedang Pulang:</strong> Anda sedang dalam periode kepulangan 
    (<?php echo e($kepulanganAktif->tanggal_pulang_formatted); ?> - <?php echo e($kepulanganAktif->tanggal_kembali_formatted); ?>). 
    Pastikan kembali tepat waktu!
    <a href="<?php echo e(route('santri.kepulangan.show', $kepulanganAktif->id_kepulangan)); ?>" style="color: inherit; text-decoration: underline; font-weight: 600;">Lihat Detail</a>
</div>
<?php endif; ?>


<div class="row-cards">
    
    <div class="card card-success">
        <h3><i class="fas fa-book-quran"></i> Progres Al-Qur'an</h3>
        <div class="card-value"><?php echo e($data['progres_quran']); ?>%</div>
        <div class="card-icon"><i class="fas fa-book-quran"></i></div>
        <div style="margin-top: 10px;">
            <div class="progress-bar">
                <div class="progress-fill" style="width: <?php echo e($data['progres_quran']); ?>%; background: var(--success-color);"></div>
            </div>
        </div>
    </div>
    
    
    <div class="card card-info">
        <h3><i class="fas fa-scroll"></i> Progres Hadist</h3>
        <div class="card-value"><?php echo e($data['progres_hadist']); ?>%</div>
        <div class="card-icon"><i class="fas fa-scroll"></i></div>
        <div style="margin-top: 10px;">
            <div class="progress-bar">
                <div class="progress-fill" style="width: <?php echo e($data['progres_hadist']); ?>%; background: var(--info-color);"></div>
            </div>
        </div>
    </div>
    
    
    <div class="card card-warning">
        <h3><i class="fas fa-book"></i> Materi Tambahan</h3>
        <div class="card-value"><?php echo e($data['progres_materi_tambahan']); ?>%</div>
        <div class="card-icon"><i class="fas fa-book"></i></div>
        <div style="margin-top: 10px;">
            <div class="progress-bar">
                <div class="progress-fill" style="width: <?php echo e($data['progres_materi_tambahan']); ?>%; background: var(--warning-color);"></div>
            </div>
        </div>
    </div>
    
    
    <div class="card card-primary">
        <h3><i class="fas fa-wallet"></i> Saldo Uang Saku</h3>
        <div class="card-value-small"><?php echo e('Rp ' . number_format($data['saldo_uang_saku'], 0, ',', '.')); ?></div>
        <div class="card-icon"><i class="fas fa-wallet"></i></div>
        <div style="margin-top: 10px;">
            <a href="<?php echo e(route('santri.uang-saku.index')); ?>" class="btn btn-sm btn-primary" style="width: 100%; justify-content: center;">
                <i class="fas fa-eye"></i> Lihat Riwayat
            </a>
        </div>
    </div>
</div>


<?php if($beritaTerbaru->isNotEmpty()): ?>
<div class="content-box" style="margin-top: 25px;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
        <h3 style="margin: 0; color: var(--primary-color);">
            <i class="fas fa-newspaper"></i> Berita Terbaru (7 Hari Terakhir)
        </h3>
        <a href="<?php echo e(route('santri.berita.index')); ?>" class="btn btn-sm btn-primary hover-lift">
            <i class="fas fa-arrow-right"></i> Lihat Semua
        </a>
    </div>
    
    <div style="display: flex; flex-direction: column; gap: 12px;">
        <?php $__currentLoopData = $beritaTerbaru; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $berita): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <a href="<?php echo e(route('santri.berita.show', $berita->id_berita)); ?>" 
           style="display: flex; justify-content: space-between; align-items: center; padding: 15px; background: linear-gradient(135deg, #FEFFFF 0%, #F8FBF9 100%); border-radius: var(--border-radius-sm); border-left: 4px solid var(--primary-color); text-decoration: none; transition: var(--transition-base);"
           onmouseover="this.style.boxShadow='var(--shadow-md)'; this.style.transform='translateX(5px)';"
           onmouseout="this.style.boxShadow='none'; this.style.transform='translateX(0)';">
            <div style="flex: 1;">
                <h4 style="margin: 0 0 5px 0; color: var(--text-color); font-size: 0.95rem; font-weight: 600;">
                    <i class="fas fa-circle" style="font-size: 0.5rem; color: var(--primary-color); margin-right: 8px;"></i>
                    <?php echo e($berita->judul); ?>

                </h4>
                <p style="margin: 0; font-size: 0.85rem; color: var(--text-light);">
                    <i class="fas fa-calendar"></i> <?php echo e($berita->created_at->diffForHumans()); ?>

                </p>
            </div>
            <span class="badge badge-primary">
                <i class="fas fa-chevron-right"></i>
            </span>
        </a>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
</div>
<?php endif; ?>


<div class="content-box" style="margin-top: 25px;">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;">
        <div style="flex: 1;">
            <h3 style="margin: 0 0 5px 0; color: var(--<?php echo e($data['poin_pelanggaran'] > 0 ? 'danger' : 'success'); ?>-color);">
                <i class="fas fa-exclamation-triangle"></i> Total Poin Pelanggaran: <strong><?php echo e($data['poin_pelanggaran']); ?></strong>
            </h3>
            <?php if($data['poin_pelanggaran'] > 0): ?>
                <p style="margin: 0; color: var(--text-light); font-size: 0.9rem;">
                    Anda memiliki <?php echo e($data['poin_pelanggaran']); ?> poin pelanggaran. Jaga kedisiplinan!
                </p>
            <?php else: ?>
                <p style="margin: 0; color: var(--text-light); font-size: 0.9rem;">
                    <i class="fas fa-check-circle" style="color: var(--success-color);"></i> Alhamdulillah, tidak ada catatan pelanggaran.
                </p>
            <?php endif; ?>
        </div>
        <a href="<?php echo e(route('santri.pelanggaran.index')); ?>" class="btn btn-<?php echo e($data['poin_pelanggaran'] > 0 ? 'danger' : 'success'); ?>">
            <i class="fas fa-eye"></i> Lihat Riwayat
        </a>
    </div>
</div>


<?php if($capaianPerMateri->count() > 0 || array_sum($distribusiStatus) > 0): ?>
<div class="content-box" style="margin-top: 25px;">
    <h3 style="margin-bottom: 20px; color: var(--primary-dark);">
        <i class="fas fa-chart-line"></i> Visualisasi Capaian Pembelajaran
    </h3>
    
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); gap: 25px; margin-bottom: 30px;">
        

        <?php if(array_sum($distribusiStatus) > 0): ?>
        <div style="background: white; padding: 25px; border-radius: var(--border-radius); box-shadow: var(--shadow-md); border: 2px solid var(--primary-light);">
            <h4 style="margin: 0 0 20px 0; font-size: 1.1rem; color: var(--text-color); text-align: center; font-weight: 700;">
                <i class="fas fa-chart-pie"></i> Distribusi Status Pembelajaran
            </h4>
            
            
            <div style="display: flex; align-items: center; gap: 25px; flex-wrap: wrap;">
                
                <div style="flex: 1; min-width: 250px; max-width: 350px;">
                    <canvas id="chartPieStatus" style="max-height: 300px;"></canvas>
                </div>
                
                
                <div style="flex: 1; min-width: 200px; display: flex; flex-direction: column; gap: 12px;">
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <div style="width: 24px; height: 24px; background: linear-gradient(135deg, #6FBA9D 0%, #5EA98C 100%); border-radius: 4px; flex-shrink: 0;"></div>
                        <div style="flex: 1;">
                            <div style="font-weight: 700; color: var(--text-color); font-size: 0.9rem;">Selesai (100%)</div>
                            <div style="font-size: 0.75rem; color: var(--text-light);"><?php echo e($distribusiStatus['selesai']); ?> materi</div>
                        </div>
                    </div>
                    
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <div style="width: 24px; height: 24px; background: linear-gradient(135deg, #81C6E8 0%, #6AB0D4 100%); border-radius: 4px; flex-shrink: 0;"></div>
                        <div style="flex: 1;">
                            <div style="font-weight: 700; color: var(--text-color); font-size: 0.9rem;">Hampir Selesai (75-99%)</div>
                            <div style="font-size: 0.75rem; color: var(--text-light);"><?php echo e($distribusiStatus['hampir_selesai']); ?> materi</div>
                        </div>
                    </div>
                    
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <div style="width: 24px; height: 24px; background: linear-gradient(135deg, #FFD56B 0%, #E6B85C 100%); border-radius: 4px; flex-shrink: 0;"></div>
                        <div style="flex: 1;">
                            <div style="font-weight: 700; color: var(--text-color); font-size: 0.9rem;">Sedang Berjalan (25-74%)</div>
                            <div style="font-size: 0.75rem; color: var(--text-light);"><?php echo e($distribusiStatus['sedang_berjalan']); ?> materi</div>
                        </div>
                    </div>
                    
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <div style="width: 24px; height: 24px; background: linear-gradient(135deg, #FF8B94 0%, #E77580 100%); border-radius: 4px; flex-shrink: 0;"></div>
                        <div style="flex: 1;">
                            <div style="font-weight: 700; color: var(--text-color); font-size: 0.9rem;">Baru Dimulai (0-24%)</div>
                            <div style="font-size: 0.75rem; color: var(--text-light);"><?php echo e($distribusiStatus['baru_dimulai']); ?> materi</div>
                        </div>
                    </div>
                </div>
            </div>
            
            
            <div style="margin-top: 20px; padding-top: 20px; border-top: 1px solid #f0f0f0; display: flex; justify-content: space-between; gap: 8px; flex-wrap: wrap;">
                <div style="flex: 1; min-width: 70px; text-align: center; padding: 10px 8px; background: linear-gradient(135deg, #E8F7F2 0%, #D4F1E3 100%); border-radius: 6px; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
                    <div style="font-size: 1.3rem; font-weight: 700; color: var(--success-color); line-height: 1;"><?php echo e($distribusiStatus['selesai']); ?></div>
                    <div style="font-size: 0.75rem; color: var(--text-color); font-weight: 600; margin-top: 4px; line-height: 1.2;">Selesai</div>
                </div>
                <div style="flex: 1; min-width: 70px; text-align: center; padding: 10px 8px; background: linear-gradient(135deg, #E3F2FD 0%, #D1E9F9 100%); border-radius: 6px; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
                    <div style="font-size: 1.3rem; font-weight: 700; color: var(--info-color); line-height: 1;"><?php echo e($distribusiStatus['hampir_selesai']); ?></div>
                    <div style="font-size: 0.75rem; color: var(--text-color); font-weight: 600; margin-top: 4px; line-height: 1.2;">Hampir Selesai</div>
                </div>
                <div style="flex: 1; min-width: 70px; text-align: center; padding: 10px 8px; background: linear-gradient(135deg, #FFF8E1 0%, #FFF3CD 100%); border-radius: 6px; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
                    <div style="font-size: 1.3rem; font-weight: 700; color: var(--warning-color); line-height: 1;"><?php echo e($distribusiStatus['sedang_berjalan']); ?></div>
                    <div style="font-size: 0.75rem; color: var(--text-color); font-weight: 600; margin-top: 4px; line-height: 1.2;">Sedang Berjalan</div>
                </div>
                <div style="flex: 1; min-width: 70px; text-align: center; padding: 10px 8px; background: linear-gradient(135deg, #FFE8EA 0%, #FFD5D8 100%); border-radius: 6px; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
                    <div style="font-size: 1.3rem; font-weight: 700; color: var(--danger-color); line-height: 1;"><?php echo e($distribusiStatus['baru_dimulai']); ?></div>
                    <div style="font-size: 0.75rem; color: var(--text-color); font-weight: 600; margin-top: 4px; line-height: 1.2;">Baru Dimulai</div>
                </div>
            </div>
        </div>
        <?php endif; ?>
        
        
        <div style="background: white; padding: 25px; border-radius: var(--border-radius); box-shadow: var(--shadow-md); border: 2px solid var(--primary-light);">
            <h4 style="margin: 0 0 20px 0; font-size: 1.1rem; color: var(--text-color); text-align: center; font-weight: 700;">
                <i class="fas fa-chart-line"></i> Perbandingan Progress Kategori
            </h4>
            <div style="max-width: 450px; margin: 0 auto;">
                <canvas id="chartLineKategori" style="max-height: 300px;"></canvas>
            </div>
            <div style="margin-top: 20px; display: flex; justify-content: center; gap: 20px; flex-wrap: wrap;">
                <div style="text-align: center;">
                    <div style="width: 60px; height: 60px; border-radius: 50%; background: linear-gradient(135deg, var(--success-color), #5EA98C); display: flex; align-items: center; justify-content: center; margin: 0 auto 8px; color: white; font-size: 1.1rem; font-weight: 700; box-shadow: 0 4px 8px rgba(111, 186, 157, 0.3);">
                        <?php echo e($data['progres_quran']); ?>%
                    </div>
                    <div style="font-size: 0.85rem; color: var(--text-color); font-weight: 600;">Al-Qur'an</div>
                </div>
                <div style="text-align: center;">
                    <div style="width: 60px; height: 60px; border-radius: 50%; background: linear-gradient(135deg, var(--info-color), #6AB0D4); display: flex; align-items: center; justify-content: center; margin: 0 auto 8px; color: white; font-size: 1.1rem; font-weight: 700; box-shadow: 0 4px 8px rgba(129, 198, 232, 0.3);">
                        <?php echo e($data['progres_hadist']); ?>%
                    </div>
                    <div style="font-size: 0.85rem; color: var(--text-color); font-weight: 600;">Hadist</div>
                </div>
                <div style="text-align: center;">
                    <div style="width: 60px; height: 60px; border-radius: 50%; background: linear-gradient(135deg, var(--warning-color), #E6B85C); display: flex; align-items: center; justify-content: center; margin: 0 auto 8px; color: white; font-size: 1.1rem; font-weight: 700; box-shadow: 0 4px 8px rgba(255, 213, 107, 0.3);">
                        <?php echo e($data['progres_materi_tambahan']); ?>%
                    </div>
                    <div style="font-size: 0.85rem; color: var(--text-color); font-weight: 600;">Materi Tambahan</div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>


<?php if($capaianPerMateri->count() > 0): ?>
<div class="content-box" style="margin-top: 25px;">
    <h3 style="margin-bottom: 20px; color: var(--primary-dark);">
        <i class="fas fa-chart-bar"></i> Progress per Materi (Top 10)
    </h3>
    
    <div style="background: white; padding: 25px; border-radius: var(--border-radius); box-shadow: var(--shadow-sm); border: 2px solid var(--primary-light);">
        <canvas id="chartBarMateri" style="max-height: 450px;"></canvas>
    </div>
    
    <div style="margin-top: 20px; text-align: center;">
        <a href="<?php echo e(route('santri.capaian.index')); ?>" class="btn btn-primary">
            <i class="fas fa-list"></i> Lihat Semua Capaian Detail
        </a>
    </div>
</div>
<?php endif; ?>


<div class="content-box" style="margin-top: 25px; background: linear-gradient(135deg, #E8F7F2 0%, #D4F1E3 100%); border: 2px solid var(--primary-color);">
    <h4 style="margin: 0 0 15px 0; color: var(--primary-dark);">
        <i class="fas fa-lightbulb"></i> Tips Hari Ini
    </h4>
    <p style="margin: 0; color: var(--text-color); line-height: 1.6;">
        💡 <strong>Jaga Kedisiplinan:</strong> Hindari pelanggaran dengan mematuhi tata tertib pondok. 
        Lihat <strong><a href="<?php echo e(route('santri.pelanggaran.kategori')); ?>" style="color: var(--primary-color);">Daftar Kategori Pelanggaran</a></strong> 
        untuk mengetahui peraturan yang berlaku.
    </p>
</div>


<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    
    // ============================================
    // GRAFIK 1: Distribusi Status (PIE CHART)
    // ============================================
    <?php if(array_sum($distribusiStatus) > 0): ?>
    const ctxPieStatus = document.getElementById('chartPieStatus');
    if (ctxPieStatus) {
        new Chart(ctxPieStatus.getContext('2d'), {
            type: 'pie',
            data: {
                // labels: ['Selesai (100%)', 'Hampir Selesai (75-99%)', 'Sedang Berjalan (25-74%)', 'Baru Dimulai (0-24%)'],
                datasets: [{
                    data: [
                        <?php echo e($distribusiStatus['selesai']); ?>,
                        <?php echo e($distribusiStatus['hampir_selesai']); ?>,
                        <?php echo e($distribusiStatus['sedang_berjalan']); ?>,
                        <?php echo e($distribusiStatus['baru_dimulai']); ?>

                    ],
                    backgroundColor: [
                        'rgba(111, 186, 157, 0.9)',
                        'rgba(129, 198, 232, 0.9)',
                        'rgba(255, 213, 107, 0.9)',
                        'rgba(255, 139, 148, 0.9)',
                    ],
                    borderColor: '#fff',
                    borderWidth: 3
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
                                size: 12,
                                weight: '600'
                            }
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.parsed || 0;
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                                return label + ': ' + value + ' materi (' + percentage + '%)';
                            }
                        }
                    }
                }
            }
        });
    }
    <?php endif; ?>
    
    // ============================================
    // GRAFIK 2: Perbandingan Kategori (LINE CHART)
    // ============================================
    const ctxLineKategori = document.getElementById('chartLineKategori');
    if (ctxLineKategori) {
        new Chart(ctxLineKategori.getContext('2d'), {
            type: 'line',
            data: {
                labels: ['Al-Qur\'an', 'Hadist', 'Materi Tambahan'],
                datasets: [{
                    label: 'Progress (%)',
                    data: [
                        <?php echo e($data['progres_quran']); ?>,
                        <?php echo e($data['progres_hadist']); ?>,
                        <?php echo e($data['progres_materi_tambahan']); ?>

                    ],
                    backgroundColor: 'rgba(111, 186, 157, 0.2)',
                    borderColor: 'rgba(111, 186, 157, 1)',
                    borderWidth: 4,
                    pointBackgroundColor: [
                        'rgba(111, 186, 157, 1)',
                        'rgba(129, 198, 232, 1)',
                        'rgba(255, 213, 107, 1)'
                    ],
                    pointBorderColor: '#fff',
                    pointBorderWidth: 3,
                    pointRadius: 8,
                    pointHoverRadius: 10,
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100,
                        ticks: {
                            stepSize: 20,
                            callback: function(value) {
                                return value + '%';
                            },
                            font: {
                                size: 12,
                                weight: '600'
                            }
                        },
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)'
                        }
                    },
                    x: {
                        ticks: {
                            font: {
                                size: 12,
                                weight: '600'
                            }
                        },
                        grid: {
                            display: false
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return 'Progress: ' + context.parsed.y.toFixed(1) + '%';
                            }
                        }
                    }
                }
            }
        });
    }
    
    // ============================================
    // GRAFIK 3: Progress per Materi (BAR CHART - Vertikal)
    // ============================================
    <?php if($capaianPerMateri->count() > 0): ?>
    const ctxBarMateri = document.getElementById('chartBarMateri');
    if (ctxBarMateri) {
        new Chart(ctxBarMateri.getContext('2d'), {
            type: 'bar',
            data: {
                labels: [
                    <?php $__currentLoopData = $capaianPerMateri; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        '<?php echo e(Str::limit($item->materi->nama_kitab, 30)); ?>',
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                ],
                datasets: [{
                    label: 'Progress (%)',
                    data: [
                        <?php $__currentLoopData = $capaianPerMateri; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php echo e($item->persentase); ?>,
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    ],
                    backgroundColor: [
                        <?php $__currentLoopData = $capaianPerMateri; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php if($item->materi->kategori == 'Al-Qur\'an'): ?>
                                'rgba(111, 186, 157, 0.8)',
                            <?php elseif($item->materi->kategori == 'Hadist'): ?>
                                'rgba(129, 198, 232, 0.8)',
                            <?php else: ?>
                                'rgba(255, 213, 107, 0.8)',
                            <?php endif; ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    ],
                    borderColor: [
                        <?php $__currentLoopData = $capaianPerMateri; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php if($item->materi->kategori == 'Al-Qur\'an'): ?>
                                'rgba(111, 186, 157, 1)',
                            <?php elseif($item->materi->kategori == 'Hadist'): ?>
                                'rgba(129, 198, 232, 1)',
                            <?php else: ?>
                                'rgba(255, 213, 107, 1)',
                            <?php endif; ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
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
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return 'Progress: ' + context.parsed.y.toFixed(1) + '%';
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100,
                        ticks: {
                            stepSize: 10,
                            callback: function(value) {
                                return value + '%';
                            },
                            font: {
                                size: 12,
                                weight: '600'
                            }
                        },
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)'
                        },
                        title: {
                            display: true,
                            text: 'Persentase (%)',
                            font: {
                                size: 14,
                                weight: 'bold'
                            }
                        }
                    },
                    x: {
                        ticks: {
                            font: {
                                size: 11
                            },
                            maxRotation: 45,
                            minRotation: 45
                        },
                        grid: {
                            display: false
                        },
                        title: {
                            display: true,
                            text: 'Materi',
                            font: {
                                size: 14,
                                weight: 'bold'
                            }
                        }
                    }
                }
            }
        });
    }
    <?php endif; ?>
});
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\TugasAkhir\sim-pkpps\resources\views/santri/dashboardSantri.blade.php ENDPATH**/ ?>