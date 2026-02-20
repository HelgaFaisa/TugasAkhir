
<div class="content-section">
    <h3><i class="fas fa-list-alt"></i> Jadwal Kegiatan — <?php echo e($hari); ?></h3>
    <div class="content-box">
        <?php if($kegiatan->isEmpty()): ?>
            <p class="text-muted">Tidak ada kegiatan terjadwal hari ini.</p>
        <?php else: ?>
            <div class="table-responsive">
                <table class="data-table">
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
                                <strong><?php echo e($k->nama_kegiatan); ?></strong>
                                <?php if($k->belum_input): ?>
                                    <span class="badge badge-danger badge-sm">Belum input absensi!</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo e($k->kategori->nama_kategori ?? '-'); ?></td>
                            <td>
                                <?php echo e(is_string($k->waktu_mulai) ? $k->waktu_mulai : $k->waktu_mulai->format('H:i')); ?>

                                —
                                <?php echo e(is_string($k->waktu_selesai) ? $k->waktu_selesai : $k->waktu_selesai->format('H:i')); ?>

                            </td>
                            <td>
                                <?php if($k->status_kegiatan === 'berlangsung'): ?>
                                    <span class="badge badge-info">Berlangsung</span>
                                <?php elseif($k->status_kegiatan === 'selesai'): ?>
                                    <span class="badge badge-success">Selesai</span>
                                <?php else: ?>
                                    <span class="badge badge-secondary">Belum Mulai</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if($k->total_absensi > 0): ?>
                                    <div class="progress-bar-wrap">
                                        <div class="progress-bar-fill" style="width: <?php echo e($k->persen_kehadiran); ?>%"></div>
                                    </div>
                                    <small><?php echo e($k->persen_kehadiran); ?>% (<?php echo e($k->total_absensi); ?> data)</small>
                                <?php else: ?>
                                    <small class="text-muted">—</small>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>
<?php /**PATH C:\xampp\htdocs\TugasAkhir\sim-pkpps\resources\views/admin/dashboard/_jadwal-kegiatan.blade.php ENDPATH**/ ?>