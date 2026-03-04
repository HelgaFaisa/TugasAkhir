


<?php $__env->startSection('title', 'Daftar Kategori Pelanggaran'); ?>

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <h2><i class="fas fa-list-ul"></i> Daftar Kategori Pelanggaran & Poin</h2>
</div>


<div style="background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark, #1a4980) 100%);
            color: white; padding: 20px 24px; border-radius: var(--border-radius); margin-bottom: 18px;
            display: flex; align-items: center; justify-content: space-between; gap: 16px; flex-wrap: wrap;
            box-shadow: 0 4px 15px rgba(0,0,0,0.15);">
    <div>
        <div style="font-size: 1.1em; font-weight: 700; margin-bottom: 4px;">
            <i class="fas fa-book-open"></i> Panduan Poin Pelanggaran
        </div>
        <div style="opacity: 0.85; font-size: 0.9em;">
            Berikut adalah daftar kategori pelanggaran beserta poin yang berlaku di pondok.
            Poin akan dilebur jika kafaroh telah diselesaikan.
        </div>
    </div>
    <a href="<?php echo e(route('santri.pelanggaran.index')); ?>" class="btn btn-secondary btn-sm"
       style="background: rgba(255,255,255,0.2); border: 1px solid rgba(255,255,255,0.4); color: white; white-space: nowrap;">
        <i class="fas fa-arrow-left"></i> Kembali ke Riwayat
    </a>
</div>


<div class="content-box" style="margin-bottom: 18px;">
    <h4 style="margin: 0 0 12px; color: var(--primary-color); font-size: 0.95em;">
        <i class="fas fa-info-circle"></i> Keterangan Tingkat Poin
    </h4>
    <div style="display: flex; gap: 12px; flex-wrap: wrap;">
        <div style="display: flex; align-items: center; gap: 8px; background: #fff3cd; padding: 8px 14px; border-radius: 50px; font-size: 0.88em;">
            <span style="background: var(--warning-color); color: #333; padding: 2px 10px; border-radius: 50px; font-weight: 700; font-size: 0.9em;">
                <i class="fas fa-star"></i> 1â€“5
            </span>
            <span style="color: #856404;">Ringan</span>
        </div>
        <div style="display: flex; align-items: center; gap: 8px; background: #fdecea; padding: 8px 14px; border-radius: 50px; font-size: 0.88em;">
            <span style="background: var(--danger-color); color: white; padding: 2px 10px; border-radius: 50px; font-weight: 700; font-size: 0.9em;">
                <i class="fas fa-star"></i> 6â€“15
            </span>
            <span style="color: #a71d2a;">Sedang</span>
        </div>
        <div style="display: flex; align-items: center; gap: 8px; background: #f5eaea; padding: 8px 14px; border-radius: 50px; font-size: 0.88em;">
            <span style="background: linear-gradient(135deg, #dc3545, #a71d2a); color: white; padding: 2px 10px; border-radius: 50px; font-weight: 700; font-size: 0.9em;">
                <i class="fas fa-fire"></i> 16+
            </span>
            <span style="color: #a71d2a;">Berat</span>
        </div>
        <div style="display: flex; align-items: center; gap: 8px; background: #eafaf1; padding: 8px 14px; border-radius: 50px; font-size: 0.88em;">
            <i class="fas fa-hands" style="color: var(--success-color);"></i>
            <span style="color: #155724;">Kafaroh â†’ Poin dilebur menjadi 0</span>
        </div>
    </div>
</div>


<div class="content-box">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 14px;">
        <h3 style="margin: 0; color: var(--primary-color);">
            <i class="fas fa-table"></i> Daftar Lengkap Kategori Pelanggaran
        </h3>
        <span class="badge badge-info">
            <?php echo e($kategoriList->count()); ?> kategori
        </span>
    </div>

    <?php if($kategoriList->count() > 0): ?>
        <div style="overflow-x: auto;">
            <div class="table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 6%;">No</th>
                        <th style="width: 12%;">Kode</th>
                        <th style="width: 55%;">Jenis Pelanggaran</th>
                        <th style="width: 14%; text-align: center;">Poin</th>
                        <th style="width: 13%; text-align: center;">Tingkat</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $kategoriList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $kategori): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php
                        $poin = $kategori->poin;
                        if ($poin <= 5) {
                            $badgeClass  = 'badge-warning';
                            $badgeStyle  = '';
                            $tingkat     = 'Ringan';
                            $tingkatBg   = '#fff3cd';
                            $tingkatColor= '#856404';
                        } elseif ($poin <= 15) {
                            $badgeClass  = 'badge-danger';
                            $badgeStyle  = '';
                            $tingkat     = 'Sedang';
                            $tingkatBg   = 'var(--danger-color)';
                            $tingkatColor= 'white';
                        } else {
                            $badgeClass  = 'badge-danger';
                            $badgeStyle  = 'background: linear-gradient(135deg, #dc3545 0%, #a71d2a 100%);';
                            $tingkat     = 'Berat';
                            $tingkatBg   = 'linear-gradient(135deg, #dc3545 0%, #a71d2a 100%)';
                            $tingkatColor= 'white';
                        }
                    ?>
                    <tr>
                        <td style="color: var(--text-light); font-size: 0.9em;"><?php echo e($index + 1); ?></td>
                        <td>
                            <span class="badge badge-primary" style="font-size: 0.85em;">
                                <?php echo e($kategori->id_kategori); ?>

                            </span>
                        </td>
                        <td>
                            <strong><?php echo e($kategori->nama_pelanggaran); ?></strong>
                            <?php if($kategori->kafaroh ?? false): ?>
                            <br><small style="color: var(--text-light);">
                                <i class="fas fa-hands"></i>
                                <?php echo e(\Illuminate\Support\Str::limit($kategori->kafaroh, 60)); ?>

                            </small>
                            <?php endif; ?>
                        </td>
                        <td style="text-align: center;">
                            <span class="badge <?php echo e($badgeClass); ?>" style="font-size: 0.9em; padding: 5px 12px; <?php echo e($badgeStyle); ?>">
                                <i class="fas fa-fire"></i> <?php echo e($poin); ?>

                            </span>
                        </td>
                        <td style="text-align: center;">
                            <span style="background: <?php echo e($tingkatBg); ?>; color: <?php echo e($tingkatColor); ?>;
                                         padding: 4px 10px; border-radius: 50px; font-size: 0.82em; font-weight: 600;">
                                <?php echo e($tingkat); ?>

                            </span>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
            </div>
        </div>

        
        <?php
            $ringan = $kategoriList->where('poin', '<=', 5)->count();
            $sedang = $kategoriList->whereBetween('poin', [6, 15])->count();
            $berat  = $kategoriList->where('poin', '>', 15)->count();
        ?>
        <div style="margin-top: 18px; padding-top: 16px; border-top: 1px solid #eee;
                    display: flex; gap: 14px; flex-wrap: wrap; align-items: center;">
            <span style="color: var(--text-light); font-size: 0.88em; font-weight: 600;">Total per tingkat:</span>
            <span style="background: #fff3cd; color: #856404; padding: 4px 12px; border-radius: 50px; font-size: 0.85em;">
                Ringan: <?php echo e($ringan); ?>

            </span>
            <span style="background: #fdecea; color: #a71d2a; padding: 4px 12px; border-radius: 50px; font-size: 0.85em;">
                Sedang: <?php echo e($sedang); ?>

            </span>
            <span style="background: linear-gradient(135deg, #dc3545, #a71d2a); color: white; padding: 4px 12px; border-radius: 50px; font-size: 0.85em;">
                Berat: <?php echo e($berat); ?>

            </span>
        </div>
    <?php else: ?>
        <div class="empty-state">
            <i class="fas fa-list-alt"></i>
            <h3>Belum Ada Kategori</h3>
            <p>Daftar kategori pelanggaran belum tersedia. Hubungi pengurus pondok untuk informasi lebih lanjut.</p>
        </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\TugasAkhir\sim-pkpps\resources\views/santri/pelanggaran/kategori.blade.php ENDPATH**/ ?>