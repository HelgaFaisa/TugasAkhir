


<?php $__env->startSection('title', 'Berita'); ?>

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <h2><i class="fas fa-newspaper"></i> Berita & Pengumuman</h2>
    <p style="margin: 5px 0 0 0; color: var(--text-light);">
        Informasi terbaru untuk <strong><?php echo e($santri->kelas); ?></strong>
    </p>
</div>

<?php if($berita->isEmpty()): ?>
    <div class="empty-state">
        <i class="fas fa-newspaper"></i>
        <h3>Belum Ada Berita</h3>
        <p>Belum ada berita atau pengumuman yang dipublikasikan untuk Anda.</p>
    </div>
<?php else: ?>
    <div class="content-box">
        <div style="display: flex; flex-direction: column; gap: 15px;">
            <?php $__currentLoopData = $berita; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <a href="<?php echo e(route('santri.berita.show', $item->id_berita)); ?>" 
               class="berita-list-item"
               style="display: flex; gap: 15px; padding: 15px; background: linear-gradient(135deg, #FFFFFF 0%, #FEFFFE 100%); border-radius: var(--border-radius-sm); border: 2px solid transparent; text-decoration: none; transition: var(--transition-base); position: relative;"
               onmouseover="this.style.borderColor='var(--primary-light)'; this.style.boxShadow='var(--shadow-md)'; this.style.transform='translateY(-2px)';"
               onmouseout="this.style.borderColor='transparent'; this.style.boxShadow='none'; this.style.transform='translateY(0)';">
                
                
                <?php if(!$item->sudah_dibaca): ?>
                    <span class="badge badge-danger" style="position: absolute; top: 10px; right: 10px; z-index: 10; font-size: 0.75rem;">
                        <i class="fas fa-circle" style="font-size: 0.5em;"></i> Baru
                    </span>
                <?php endif; ?>
                
                
                <div class="berita-thumbnail" style="flex-shrink: 0; width: 120px; height: 120px; border-radius: var(--border-radius-sm); overflow: hidden; background: linear-gradient(135deg, var(--primary-light), var(--primary-color)); box-shadow: var(--shadow-sm);">
                    <?php if($item->gambar): ?>
                        <img src="<?php echo e(asset('storage/' . $item->gambar)); ?>" 
                             alt="<?php echo e($item->judul); ?>" 
                             style="width: 100%; height: 100%; object-fit: cover;">
                    <?php else: ?>
                        <div style="width: 100%; height: 100%; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-newspaper" style="font-size: 2.5rem; color: var(--primary-dark); opacity: 0.3;"></i>
                        </div>
                    <?php endif; ?>
                </div>
                
                
                <div class="berita-content" style="flex: 1; display: flex; flex-direction: column; justify-content: space-between; min-width: 0;">
                    
                    <div>
                        <h3 style="margin: 0 0 8px 0; font-size: 1.1rem; font-weight: 600; color: var(--text-color); line-height: 1.4; overflow: hidden; text-overflow: ellipsis; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical;">
                            <?php echo e($item->judul); ?>

                        </h3>
                        
                        
                        <p class="berita-excerpt" style="margin: 0 0 10px 0; font-size: 0.9rem; color: var(--text-light); line-height: 1.5; overflow: hidden; text-overflow: ellipsis; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical;">
                            <?php echo e(Str::limit(strip_tags($item->konten), 150)); ?>

                        </p>
                    </div>
                    
                    
                    <div class="berita-meta" style="display: flex; flex-wrap: wrap; gap: 15px; align-items: center; font-size: 0.85rem; color: var(--text-light);">
                        <span><i class="fas fa-user"></i> <?php echo e($item->penulis); ?></span>
                        <span><i class="fas fa-calendar"></i> <?php echo e($item->created_at->format('d M Y')); ?></span>
                        <span class="badge badge-primary badge-sm" style="margin-left: auto;">
                            <i class="fas fa-arrow-right"></i> Baca
                        </span>
                    </div>
                </div>
            </a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
        
        
        <div style="margin-top: 25px;">
            <?php echo e($berita->links()); ?>

        </div>
    </div>
<?php endif; ?>


<div class="info-box" style="margin-top: 20px;">
    <i class="fas fa-info-circle"></i>
    <strong>Info:</strong> Berita yang ditandai dengan badge <span class="badge badge-danger badge-sm"><i class="fas fa-circle" style="font-size: 0.6em;"></i> Baru</span> adalah berita yang belum Anda baca. Klik pada berita untuk membaca selengkapnya.
</div>


<div style="margin-top: 20px; text-align: center;">
    <a href="<?php echo e(route('santri.dashboard')); ?>" class="btn btn-secondary hover-lift">
        <i class="fas fa-home"></i> Kembali ke Dashboard
    </a>
</div>


<style>
@media (max-width: 768px) {
    .berita-list-item {
        flex-direction: column !important;
    }
    
    .berita-thumbnail {
        width: 100% !important;
        height: 180px !important;
    }
    
    .berita-excerpt {
        -webkit-line-clamp: 3 !important;
    }
    
    .berita-meta {
        flex-direction: column !important;
        align-items: flex-start !important;
        gap: 8px !important;
    }
    
    .berita-meta .badge {
        margin-left: 0 !important;
    }
}

@media (max-width: 480px) {
    .berita-thumbnail {
        height: 150px !important;
    }
}
</style>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\TugasAkhir\sim-pkpps\resources\views/santri/berita/index.blade.php ENDPATH**/ ?>