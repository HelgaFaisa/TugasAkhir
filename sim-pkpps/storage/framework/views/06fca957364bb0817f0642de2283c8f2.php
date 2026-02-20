
<div class="content-section">
    <h3><i class="fas fa-rss"></i> Aktivitas Terbaru</h3>
    <div class="content-box">
        <?php if($feed->isEmpty()): ?>
            <p class="text-muted">Belum ada aktivitas tercatat.</p>
        <?php else: ?>
            <ul class="feed-list">
                <?php $__currentLoopData = $feed; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <li class="feed-item">
                    <span class="feed-icon feed-icon-<?php echo e($item->color); ?>">
                        <i class="fas <?php echo e($item->icon); ?>"></i>
                    </span>
                    <div class="feed-body">
                        <p><?php echo e($item->text); ?></p>
                        <small class="text-muted"><?php echo e($item->time->diffForHumans()); ?></small>
                    </div>
                </li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        <?php endif; ?>
    </div>
</div>
<?php /**PATH C:\xampp\htdocs\TugasAkhir\sim-pkpps\resources\views/admin/dashboard/_feed-aktivitas.blade.php ENDPATH**/ ?>