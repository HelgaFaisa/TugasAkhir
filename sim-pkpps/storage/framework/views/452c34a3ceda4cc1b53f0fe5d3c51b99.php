

<?php $__env->startSection('title', $berita->judul); ?>

<?php $__env->startSection('content'); ?>
<div class="content-box">
    
    <div class="detail-header">
        <div>
            <h3 style="margin-bottom: 5px;"><?php echo e($berita->judul); ?></h3>
            <div style="display: flex; gap: 20px; color: var(--text-light); font-size: 0.9rem; margin-top: 10px;">
                <span><i class="fas fa-user"></i> <?php echo e($berita->penulis); ?></span>
                <span><i class="fas fa-calendar"></i> <?php echo e($berita->created_at->format('d F Y, H:i')); ?> WIB</span>
                <span><i class="fas fa-eye"></i> Sudah dibaca</span>
            </div>
        </div>
        <a href="<?php echo e(route('santri.berita.index')); ?>" class="btn btn-secondary btn-sm hover-lift">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>
    
    <hr style="border: none; border-top: 2px solid var(--primary-light); margin: 20px 0;">
    
    
    <?php if($berita->gambar): ?>
        <div style="text-align: center; margin: 30px 0;">
            <img src="<?php echo e(asset('storage/' . $berita->gambar)); ?>" 
                 alt="<?php echo e($berita->judul); ?>" 
                 style="max-width: 100%; max-height: 500px; border-radius: var(--border-radius); box-shadow: var(--shadow-md); object-fit: contain;">
        </div>
    <?php endif; ?>
    
    
    <div style="font-size: 1rem; line-height: 1.8; color: var(--text-color); margin-top: 25px;">
        <?php echo nl2br(e($berita->konten)); ?>

    </div>
    
    
    <div style="margin-top: 40px; padding-top: 20px; border-top: 1px solid var(--primary-light); text-align: center;">
        <p style="color: var(--text-light); margin: 0;">
            <i class="fas fa-clock"></i> Dipublikasikan pada <?php echo e($berita->created_at->format('d F Y, H:i')); ?> WIB
        </p>
    </div>
</div>


<div style="margin-top: 20px; text-align: center;">
    <a href="<?php echo e(route('santri.berita.index')); ?>" class="btn btn-primary hover-lift">
        <i class="fas fa-list"></i> Lihat Berita Lainnya
    </a>
    <a href="<?php echo e(route('santri.dashboard')); ?>" class="btn btn-secondary hover-lift">
        <i class="fas fa-home"></i> Kembali ke Dashboard
    </a>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\TugasAkhir\sim-pkpps\resources\views/santri/berita/show.blade.php ENDPATH**/ ?>