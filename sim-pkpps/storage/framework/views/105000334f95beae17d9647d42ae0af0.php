

<?php $__env->startSection('title', 'Detail Capaian'); ?>

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <h2><i class="fas fa-book-reader"></i> Detail Capaian</h2>
</div>

<div class="content-box">
    <div class="detail-header">
        <h3><i class="fas fa-info-circle"></i> Informasi Capaian</h3>
        <a href="<?php echo e(route('santri.capaian.index')); ?>" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>
    
    
    <div class="detail-section">
        <h4><i class="fas fa-book"></i> Informasi Materi</h4>
        <table class="detail-table">
            <tr>
                <th>Nama Materi</th>
                <td><strong><?php echo e($capaian->materi->nama_kitab); ?></strong></td>
            </tr>
            <tr>
                <th>Kategori</th>
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
            </tr>
            <tr>
                <th>Total Halaman</th>
                <td><?php echo e($capaian->materi->total_halaman); ?> halaman (Hal. <?php echo e($capaian->materi->halaman_mulai); ?> - <?php echo e($capaian->materi->halaman_akhir); ?>)</td>
            </tr>
        </table>
    </div>
    
    
    <div class="detail-section">
        <h4><i class="fas fa-chart-line"></i> Progress Pembelajaran</h4>
        <table class="detail-table">
            <tr>
                <th>Persentase Selesai</th>
                <td>
                    <div style="display: flex; align-items: center; gap: 15px;">
                        <div class="progress-bar" style="flex: 1;">
                            <div class="progress-fill" style="width: <?php echo e($capaian->persentase); ?>%; background: 
                                <?php if($capaian->persentase >= 100): ?> var(--success-color)
                                <?php elseif($capaian->persentase >= 75): ?> var(--info-color)
                                <?php elseif($capaian->persentase >= 50): ?> var(--warning-color)
                                <?php else: ?> var(--danger-color)
                                <?php endif; ?>
                            ;"></div>
                        </div>
                        <span style="font-weight: 700; font-size: 1.2rem; color: var(--primary-color);">
                            <?php echo e(number_format($capaian->persentase, 2)); ?>%
                        </span>
                    </div>
                </td>
            </tr>
            <tr>
                <th>Jumlah Halaman Selesai</th>
                <td>
                    <strong style="color: var(--primary-color); font-size: 1.1rem;">
                        <?php echo e(count($capaian->pages_array)); ?>

                    </strong> dari <?php echo e($capaian->materi->total_halaman); ?> halaman
                </td>
            </tr>
            <tr>
                <th>Detail Halaman Selesai</th>
                <td>
                    <div style="background: var(--primary-light); padding: 12px; border-radius: var(--border-radius-sm); font-family: monospace; color: var(--text-color); line-height: 1.8;">
                        <?php echo e($capaian->halaman_selesai); ?>

                    </div>
                </td>
            </tr>
            <tr>
                <th>Tanggal Input</th>
                <td><?php echo e(\Carbon\Carbon::parse($capaian->tanggal_input)->format('d F Y')); ?></td>
            </tr>
            <tr>
                <th>Semester</th>
                <td>
                    <span class="badge badge-primary">
                        <?php echo e($capaian->semester->nama_semester); ?> - <?php echo e($capaian->semester->tahun_ajaran); ?>

                    </span>
                </td>
            </tr>
            <?php if($capaian->catatan): ?>
            <tr>
                <th>Catatan</th>
                <td>
                    <div style="background: #FFF8E1; padding: 12px; border-radius: var(--border-radius-sm); border-left: 4px solid var(--warning-color);">
                        <i class="fas fa-sticky-note" style="color: var(--warning-color); margin-right: 8px;"></i>
                        <?php echo e($capaian->catatan); ?>

                    </div>
                </td>
            </tr>
            <?php endif; ?>
        </table>
    </div>
    
    
    <div class="detail-section">
        <h4><i class="fas fa-th"></i> Visualisasi Halaman yang Diselesaikan</h4>
        <div style="background: white; padding: 20px; border-radius: var(--border-radius-sm); box-shadow: var(--shadow-sm);">
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(40px, 1fr)); gap: 8px;">
                <?php
                    $completedPages = $capaian->pages_array;
                    $totalPages = $capaian->materi->total_halaman;
                    $startPage = $capaian->materi->halaman_mulai;
                ?>
                
                <?php for($i = $startPage; $i <= ($startPage + $totalPages - 1); $i++): ?>
                    <?php
                        $isCompleted = in_array($i, $completedPages);
                    ?>
                    <div style="
                        width: 40px;
                        height: 40px;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        border-radius: 6px;
                        font-size: 0.8rem;
                        font-weight: 600;
                        <?php echo e($isCompleted 
                            ? 'background: linear-gradient(135deg, var(--success-color), #5EA98C); color: white; box-shadow: 0 2px 4px rgba(111, 186, 157, 0.3);' 
                            : 'background: #F5F5F5; color: #999; border: 1px dashed #ddd;'); ?>

                    ">
                        <?php echo e($i); ?>

                    </div>
                <?php endfor; ?>
            </div>
            
            <div style="display: flex; justify-content: center; gap: 25px; margin-top: 20px; padding-top: 15px; border-top: 1px solid #E0E0E0;">
                <div style="display: flex; align-items: center; gap: 8px;">
                    <div style="width: 20px; height: 20px; background: linear-gradient(135deg, var(--success-color), #5EA98C); border-radius: 4px;"></div>
                    <span style="font-size: 0.85rem; color: var(--text-color);">Selesai (<?php echo e(count($completedPages)); ?>)</span>
                </div>
                <div style="display: flex; align-items: center; gap: 8px;">
                    <div style="width: 20px; height: 20px; background: #F5F5F5; border: 1px dashed #ddd; border-radius: 4px;"></div>
                    <span style="font-size: 0.85rem; color: var(--text-color);">Belum (<?php echo e($totalPages - count($completedPages)); ?>)</span>
                </div>
            </div>
        </div>
    </div>
    
    
    <div style="text-align: center; margin-top: 30px;">
        <?php if($capaian->persentase >= 100): ?>
            <div style="background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%); padding: 20px; border-radius: var(--border-radius); border: 2px solid var(--success-color);">
                <i class="fas fa-check-circle" style="font-size: 3rem; color: var(--success-color); margin-bottom: 10px;"></i>
                <h3 style="margin: 0; color: #155724;">Alhamdulillah, Materi Selesai! 🎉</h3>
                <p style="margin: 8px 0 0 0; color: #155724;">Terus semangat untuk materi berikutnya!</p>
            </div>
        <?php elseif($capaian->persentase >= 75): ?>
            <div style="background: linear-gradient(135deg, #d1ecf1 0%, #bee5eb 100%); padding: 20px; border-radius: var(--border-radius); border: 2px solid var(--info-color);">
                <i class="fas fa-fire" style="font-size: 3rem; color: var(--info-color); margin-bottom: 10px;"></i>
                <h3 style="margin: 0; color: #0c5460;">Hampir Selesai! 💪</h3>
                <p style="margin: 8px 0 0 0; color: #0c5460;">Tinggal sedikit lagi, pertahankan semangatmu!</p>
            </div>
        <?php elseif($capaian->persentase >= 50): ?>
            <div style="background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%); padding: 20px; border-radius: var(--border-radius); border: 2px solid var(--warning-color);">
                <i class="fas fa-hourglass-half" style="font-size: 3rem; color: var(--warning-color); margin-bottom: 10px;"></i>
                <h3 style="margin: 0; color: #856404;">Setengah Perjalanan! ⚡</h3>
                <p style="margin: 8px 0 0 0; color: #856404;">Terus berjuang, jangan menyerah!</p>
            </div>
        <?php else: ?>
            <div style="background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%); padding: 20px; border-radius: var(--border-radius); border: 2px solid var(--danger-color);">
                <i class="fas fa-seedling" style="font-size: 3rem; color: var(--danger-color); margin-bottom: 10px;"></i>
                <h3 style="margin: 0; color: #721c24;">Baru Memulai! 🌱</h3>
                <p style="margin: 8px 0 0 0; color: #721c24;">Setiap perjalanan dimulai dari langkah pertama. Semangat!</p>
            </div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\TugasAkhir\sim-pkpps\resources\views/santri/capaian/show.blade.php ENDPATH**/ ?>