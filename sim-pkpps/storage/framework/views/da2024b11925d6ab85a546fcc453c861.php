
<div class="content-box" style="margin-bottom:16px;">
    <h4 style="margin:0 0 12px;font-size:.88rem;font-weight:700;color:var(--text-color);display:flex;align-items:center;gap:8px;">
        <span style="display:inline-flex;align-items:center;justify-content:center;width:24px;height:24px;background:linear-gradient(135deg,var(--primary-color),var(--primary-dark));border-radius:6px;flex-shrink:0;">
            <i class="fas fa-calendar-day" style="font-size:.7rem;color:#fff;"></i>
        </span>
        Jadwal Kegiatan <?php echo e($hari); ?>

    </h4>

    <?php if($kegiatan->isEmpty()): ?>
        <div class="empty-state" style="padding:20px;">
            <i class="fas fa-calendar-times"></i>
            <p>Tidak ada kegiatan terjadwal hari ini.</p>
        </div>
    <?php else: ?>
    <div class="table-responsive" style="overflow-x:auto;">
        <div class="table-wrapper">
        <table class="data-table" style="margin-top:0;">
            <thead>
                <tr>
                    <th>Kegiatan</th>
                    <th>Kategori</th>
                    <th>Waktu</th>
                    <th>Status</th>
                    <th>Kehadiran</th>
                </tr>
            </thead>
            <tbody>
                <?php $__currentLoopData = $kegiatan; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr class="<?php echo e($k->belum_input ? 'row-danger' : ''); ?>">
                    <td>
                        <strong style="font-size:.82rem;"><?php echo e($k->nama_kegiatan); ?></strong>
                        <?php if($k->belum_input): ?>
                            <span class="badge badge-danger badge-sm" style="display:inline-flex;margin-left:6px;animation:slideInDown .4s;">
                                <i class="fas fa-exclamation-triangle"></i> Belum input absensi!
                            </span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <span class="badge badge-info"><?php echo e($k->kategori->nama_kategori ?? '-'); ?></span>
                    </td>
                    <td style="font-size:.78rem;font-weight:600;white-space:nowrap;color:var(--text-color);">
                        <?php echo e(is_string($k->waktu_mulai) ? $k->waktu_mulai : $k->waktu_mulai->format('H:i')); ?>

                        <span style="color:var(--text-light);margin:0 2px;"> - </span>
                        <?php echo e(is_string($k->waktu_selesai) ? $k->waktu_selesai : $k->waktu_selesai->format('H:i')); ?>

                    </td>
                    <td>
                        <?php if($k->status_kegiatan === 'berlangsung'): ?>
                            <span class="badge badge-success" style="animation:slideInDown .5s;">
                                <i class="fas fa-circle" style="font-size:.45rem;"></i> Berlangsung
                            </span>
                        <?php elseif($k->status_kegiatan === 'selesai'): ?>
                            <span class="badge badge-primary">
                                <i class="fas fa-check"></i> Selesai
                            </span>
                        <?php else: ?>
                            <span class="badge badge-secondary">
                                <i class="fas fa-clock"></i> Belum Mulai
                            </span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if($k->total_absensi > 0): ?>
                            <div class="progress-bar-wrap">
                                <div class="progress-bar-fill" style="width:<?php echo e($k->persen_kehadiran); ?>%;"></div>
                            </div>
                            <small style="font-size:.68rem;color:var(--text-light);">
                                <?php echo e($k->persen_kehadiran); ?>%
                                <span style="color:#bbb;">(<?php echo e($k->total_absensi); ?> data)</span>
                            </small>
                        <?php else: ?>
                            <small class="text-muted"></small>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
        </div>
    </div>
    <?php endif; ?>
</div><?php /**PATH C:\xampp\htdocs\TugasAkhir\sim-pkpps\resources\views/admin/dashboard/_jadwal-kegiatan.blade.php ENDPATH**/ ?>