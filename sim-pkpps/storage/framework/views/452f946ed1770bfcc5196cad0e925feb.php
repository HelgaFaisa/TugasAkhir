<?php if($paginator->hasPages()): ?>
    <nav class="pagination" role="navigation" aria-label="Pagination Navigation">
        
        <?php if($paginator->onFirstPage()): ?>
            <span class="disabled" aria-disabled="true">
                <i class="fas fa-chevron-left"></i>
            </span>
        <?php else: ?>
            <a href="<?php echo e($paginator->previousPageUrl()); ?>" rel="prev">
                <i class="fas fa-chevron-left"></i>
            </a>
        <?php endif; ?>

        
        <?php $__currentLoopData = $elements; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $element): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            
            <?php if(is_string($element)): ?>
                <span class="disabled"><?php echo e($element); ?></span>
            <?php endif; ?>

            
            <?php if(is_array($element)): ?>
                <?php $__currentLoopData = $element; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $page => $url): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php if($page == $paginator->currentPage()): ?>
                        <span class="active"><span><?php echo e($page); ?></span></span>
                    <?php else: ?>
                        <a href="<?php echo e($url); ?>"><?php echo e($page); ?></a>
                    <?php endif; ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php endif; ?>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

        
        <?php if($paginator->hasMorePages()): ?>
            <a href="<?php echo e($paginator->nextPageUrl()); ?>" rel="next">
                <i class="fas fa-chevron-right"></i>
            </a>
        <?php else: ?>
            <span class="disabled">
                <i class="fas fa-chevron-right"></i>
            </span>
        <?php endif; ?>
    </nav>
<?php endif; ?>
<?php /**PATH C:\xampp\htdocs\TugasAkhir\sim-pkpps\resources\views/vendor/pagination/custom.blade.php ENDPATH**/ ?>