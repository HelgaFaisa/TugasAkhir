
<?php if($alerts['santriAlpaBeruntun']->isNotEmpty() || $alerts['sppJatuhTempo']->isNotEmpty() || $alerts['kepulanganPending']->isNotEmpty()): ?>

<div class="content-box" style="margin-bottom:16px;">
    <h4 style="margin:0 0 12px;font-size:.88rem;font-weight:700;color:var(--text-color);display:flex;align-items:center;gap:8px;">
        <span style="display:inline-flex;align-items:center;justify-content:center;width:24px;height:24px;background:linear-gradient(135deg,#FFD56B,#FFAB91);border-radius:6px;flex-shrink:0;">
            <i class="fas fa-bell" style="font-size:.7rem;color:#fff;"></i>
        </span>
        Peringatan &amp; Tindak Lanjut
    </h4>

    <div class="dash-alerts">

        
        <?php if($alerts['santriAlpaBeruntun']->isNotEmpty()): ?>
        <div class="alert alert-danger">
            <div class="alert-body">
                <strong>
                    <i class="fas fa-user-times"></i> Santri Alpa Beruntun (7 Hari Terakhir)
                    <span class="badge badge-danger badge-sm" style="margin-left:6px;"><?php echo e($alerts['santriAlpaBeruntun']->count()); ?> santri</span>
                </strong>
                <ul class="alert-list" style="margin-top:6px;">
                    <?php $__currentLoopData = $alerts['santriAlpaBeruntun']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li style="display:flex;align-items:center;gap:8px;padding:3px 0;">
                        <i class="fas fa-circle" style="font-size:.4rem;color:var(--danger-color);flex-shrink:0;"></i>
                        <span style="flex:1;"><?php echo e($s->nama); ?></span>
                        <span class="badge badge-danger badge-sm"><?php echo e($s->total_alpa); ?>x alpa</span>
                    </li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
            </div>
        </div>
        <?php endif; ?>

        
        <?php if(auth()->user()->isSuperAdmin() && $alerts['sppJatuhTempo']->isNotEmpty()): ?>
        <div class="alert alert-warning">
            <div class="alert-body">
                <strong>
                    <i class="fas fa-file-invoice-dollar"></i> SPP Jatuh Tempo
                    <span class="badge badge-warning badge-sm" style="margin-left:6px;"><?php echo e($alerts['sppJatuhTempo']->count()); ?> tagihan</span>
                </strong>
                <ul class="alert-list" style="margin-top:6px;">
                    <?php $__currentLoopData = $alerts['sppJatuhTempo']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li style="display:flex;align-items:center;gap:8px;padding:3px 0;flex-wrap:wrap;">
                        <i class="fas fa-circle" style="font-size:.4rem;color:#E6B85C;flex-shrink:0;"></i>
                        <span style="flex:1;"><?php echo e($s->santri->nama_lengkap ?? '-'); ?></span>
                        <span class="badge badge-warning badge-sm">Bln <?php echo e($s->bulan); ?>/<?php echo e($s->tahun); ?></span>
                        <small style="color:var(--text-light);">jatuh tempo <?php echo e($s->batas_bayar->translatedFormat('d M Y')); ?></small>
                    </li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
            </div>
        </div>
        <?php endif; ?>

        
        <?php if($alerts['kepulanganPending']->isNotEmpty()): ?>
        <div class="alert alert-info">
            <div class="alert-body">
                <strong>
                    <i class="fas fa-home"></i> Pengajuan Kepulangan Menunggu Review
                    <span class="badge badge-primary badge-sm" style="margin-left:6px;"><?php echo e($alerts['kepulanganPending']->count()); ?> pengajuan</span>
                </strong>
                <ul class="alert-list" style="margin-top:6px;">
                    <?php $__currentLoopData = $alerts['kepulanganPending']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li style="display:flex;align-items:center;gap:8px;padding:3px 0;flex-wrap:wrap;">
                        <i class="fas fa-circle" style="font-size:.4rem;color:var(--info-color);flex-shrink:0;"></i>
                        <span style="flex:1;"><?php echo e($k->santri->nama_lengkap ?? '-'); ?></span>
                        <span class="badge badge-info badge-sm"><?php echo e($k->tanggal_pulang->translatedFormat('d M')); ?> – <?php echo e($k->tanggal_kembali->translatedFormat('d M Y')); ?></span>
                    </li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
            </div>
        </div>
        <?php endif; ?>

    </div>
</div>

<?php endif; ?><?php /**PATH C:\xampp\htdocs\TugasAkhir\sim-pkpps\resources\views/admin/dashboard/_alert-panel.blade.php ENDPATH**/ ?>