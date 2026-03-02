<?php $__env->startSection('title', 'Berita & Pengumuman'); ?>

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <h2><i class="fas fa-newspaper"></i> Berita & Pengumuman</h2>
</div>

<?php if($berita->isEmpty()): ?>
    <div class="content-box">
        <div class="empty-state">
            <i class="fas fa-newspaper" style="color: var(--primary-color); opacity: 0.3;"></i>
            <h3>Belum Ada Berita</h3>
            <p>Belum ada berita atau pengumuman yang dipublikasikan untuk Anda saat ini.</p>
            <a href="<?php echo e(route('santri.dashboard')); ?>" class="btn btn-secondary">
                <i class="fas fa-home"></i> Kembali ke Dashboard
            </a>
        </div>
    </div>
<?php else: ?>
    <div class="content-box">
        
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 18px; padding-bottom: 14px; border-bottom: 1px solid #eee; flex-wrap: wrap; gap: 10px;">
            <span style="color: var(--text-light); font-size: 0.9em;">
                <i class="fas fa-list"></i>
                Menampilkan <?php echo e($berita->firstItem()); ?>–<?php echo e($berita->lastItem()); ?> dari <strong><?php echo e($berita->total()); ?></strong> berita
            </span>
            <span style="color: var(--text-light); font-size: 0.85em;">
                <i class="fas fa-sort-amount-down"></i> Terbaru dahulu
            </span>
        </div>

        
        <div style="display: flex; flex-direction: column; gap: 14px;">
            <?php $__currentLoopData = $berita; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <a href="<?php echo e(route('santri.berita.show', $item->id_berita)); ?>"
               style="display: flex; gap: 0; background: white; border: 1px solid #e8eaed;
                      border-radius: var(--border-radius); text-decoration: none; color: inherit;
                      overflow: hidden; transition: box-shadow 0.2s, transform 0.2s;"
               onmouseover="this.style.boxShadow='0 4px 20px rgba(0,0,0,0.1)'; this.style.transform='translateY(-1px)';"
               onmouseout="this.style.boxShadow='none'; this.style.transform='translateY(0)';">

                
                <div style="flex-shrink: 0; width: 130px; min-height: 110px;
                            background: linear-gradient(135deg, var(--primary-light) 0%, var(--primary-color) 100%);
                            display: flex; align-items: center; justify-content: center; overflow: hidden;">
                    <?php if($item->gambar): ?>
                        <img src="<?php echo e(asset('storage/' . $item->gambar)); ?>"
                             alt="<?php echo e($item->judul); ?>"
                             style="width: 100%; height: 100%; object-fit: cover; min-height: 110px;">
                    <?php else: ?>
                        <i class="fas fa-newspaper" style="font-size: 2.5em; color: white; opacity: 0.4;"></i>
                    <?php endif; ?>
                </div>

                
                <div style="flex: 1; padding: 14px 18px; display: flex; flex-direction: column; justify-content: space-between; min-width: 0;">
                    <div>
                        <h4 style="margin: 0 0 7px; color: var(--primary-color); font-size: 1.05em; line-height: 1.4;
                                   display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                            <?php echo e($item->judul); ?>

                        </h4>
                        <p style="margin: 0; color: var(--text-light); font-size: 0.88em; line-height: 1.5;
                                  display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                            <?php echo e(\Illuminate\Support\Str::limit(strip_tags($item->konten), 150)); ?>

                        </p>
                    </div>
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-top: 10px; flex-wrap: wrap; gap: 8px;">
                        <div style="display: flex; gap: 14px; font-size: 0.82em; color: var(--text-light);">
                            <span><i class="fas fa-user"></i> <?php echo e($item->penulis); ?></span>
                            <span><i class="fas fa-calendar"></i> <?php echo e($item->created_at->isoFormat('D MMM YYYY')); ?></span>
                        </div>
                        <span class="badge badge-primary" style="font-size: 0.78em;">
                            <i class="fas fa-arrow-right"></i> Baca
                        </span>
                    </div>
                </div>
            </a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>

        
        <div style="margin-top: 20px; padding-top: 14px; border-top: 1px solid #eee;
                    display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
            <p style="margin: 0; color: var(--text-light); font-size: 0.85em;">
                Halaman <?php echo e($berita->currentPage()); ?> dari <?php echo e($berita->lastPage()); ?>

            </p>
            <?php echo e($berita->links()); ?>

        </div>
    </div>
<?php endif; ?>

<style>
@media (max-width: 600px) {
    a[style*="flex-shrink: 0; width: 130px"] {
        flex-direction: column !important;
    }
    a[style*="flex-shrink: 0; width: 130px"] > div:first-child {
        width: 100% !important;
        min-height: 160px !important;
    }
}
</style>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\TugasAkhir\sim-pkpps\resources\views/santri/berita/index.blade.php ENDPATH**/ ?>