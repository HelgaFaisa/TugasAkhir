
<?php if($alerts['santriAlpaBeruntun']->isNotEmpty() || $alerts['sppJatuhTempo']->isNotEmpty() || $alerts['kepulanganPending']->isNotEmpty()): ?>
<div class="content-section">
    <h3><i class="fas fa-exclamation-circle"></i> Peringatan & Tindak Lanjut</h3>
    <div class="dash-alerts">

        
        <?php if($alerts['santriAlpaBeruntun']->isNotEmpty()): ?>
        <div class="alert alert-danger">
            <div class="alert-body">
                <strong><i class="fas fa-user-times"></i> Santri Alpa Beruntun (7 Hari Terakhir)</strong>
                <ul class="alert-list">
                    <?php $__currentLoopData = $alerts['santriAlpaBeruntun']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li><?php echo e($s->nama); ?> <span class="badge badge-danger badge-sm"><?php echo e($s->total_alpa); ?>x alpa</span></li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
            </div>
        </div>
        <?php endif; ?>

        
        <?php if($alerts['sppJatuhTempo']->isNotEmpty()): ?>
        <div class="alert alert-warning">
            <div class="alert-body">
                <strong><i class="fas fa-file-invoice-dollar"></i> SPP Jatuh Tempo</strong>
                <ul class="alert-list">
                    <?php $__currentLoopData = $alerts['sppJatuhTempo']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li>
                        <?php echo e($s->santri->nama_lengkap ?? '-'); ?>

                        — Bln <?php echo e($s->bulan); ?>/<?php echo e($s->tahun); ?>

                        <small>(jatuh tempo <?php echo e($s->batas_bayar->translatedFormat('d M Y')); ?>)</small>
                    </li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
            </div>
        </div>
        <?php endif; ?>

        
        <?php if($alerts['kepulanganPending']->isNotEmpty()): ?>
        <div class="alert alert-info">
            <div class="alert-body">
                <strong><i class="fas fa-home"></i> Pengajuan Kepulangan Menunggu Review</strong>
                <ul class="alert-list">
                    <?php $__currentLoopData = $alerts['kepulanganPending']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li>
                        <?php echo e($k->santri->nama_lengkap ?? '-'); ?>

                        — <?php echo e($k->tanggal_pulang->translatedFormat('d M')); ?> s.d <?php echo e($k->tanggal_kembali->translatedFormat('d M Y')); ?>

                        <small>(<?php echo e($k->alasan); ?>)</small>
                    </li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
            </div>
        </div>
        <?php endif; ?>

    </div>
</div>
<?php endif; ?>
<?php /**PATH C:\xampp\htdocs\TugasAkhir\sim-pkpps\resources\views/admin/dashboard/_alert-panel.blade.php ENDPATH**/ ?>