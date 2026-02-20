


<?php $__env->startSection('title', 'Riwayat Pembayaran SPP'); ?>

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <h2><i class="fas fa-history"></i> Riwayat Pembayaran SPP</h2>
</div>

<!-- Info Santri -->
<div class="content-box" style="margin-bottom: 25px;">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;">
        <div>
            <h3 style="margin: 0; color: var(--primary-color);"><?php echo e($santri->nama_lengkap); ?></h3>
            <p style="margin: 5px 0 0 0; color: var(--text-light);">
                <?php echo e($santri->id_santri); ?> • <?php echo e($santri->nis ?? '-'); ?> • <?php echo e($santri->kelas_lengkap); ?>

            </p>
        </div>
        <div>
            <a href="<?php echo e(route('admin.santri.show', $santri->id)); ?>" class="btn btn-primary btn-sm">
                <i class="fas fa-user"></i> Profil Santri
            </a>
            <a href="<?php echo e(route('admin.pembayaran-spp.index')); ?>" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </div>
</div>

<!-- Statistik -->
<div class="row-cards" style="margin-bottom: 25px;">
    <div class="card card-success">
        <h3>Total Terbayar</h3>
        <div class="card-value"><?php echo e('Rp ' . number_format($totalBayar, 0, ',', '.')); ?></div>
        <i class="fas fa-check-circle card-icon"></i>
    </div>

    <div class="card card-danger">
        <h3>Total Tunggakan</h3>
        <div class="card-value"><?php echo e('Rp ' . number_format($totalTunggakan, 0, ',', '.')); ?></div>
        <i class="fas fa-exclamation-triangle card-icon"></i>
    </div>

    <div class="card card-warning">
        <h3>Pembayaran Telat</h3>
        <div class="card-value"><?php echo e($jumlahTelat); ?></div>
        <i class="fas fa-clock card-icon"></i>
    </div>
</div>

<!-- Tabel Riwayat -->
<div class="content-box">
    <h4 style="margin-bottom: 20px; color: var(--primary-dark);">
        <i class="fas fa-list"></i> Daftar Pembayaran
    </h4>

    <div style="overflow-x: auto;">
        <table class="data-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>ID Pembayaran</th>
                    <th>Periode</th>
                    <th>Nominal</th>
                    <th>Batas Bayar</th>
                    <th>Tanggal Bayar</th>
                    <th>Status</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $pembayaranSpp; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $spp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td><?php echo e($pembayaranSpp->firstItem() + $index); ?></td>
                        <td><strong><?php echo e($spp->id_pembayaran); ?></strong></td>
                        <td><?php echo e($spp->periode_lengkap); ?></td>
                        <td><strong><?php echo e($spp->nominal_format); ?></strong></td>
                        <td>
                            <?php echo e($spp->batas_bayar->format('d/m/Y')); ?>

                            <?php if($spp->isTelat()): ?>
                                <br><small style="color: #FF8B94;">
                                    <i class="fas fa-exclamation-triangle"></i> Telat
                                </small>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if($spp->tanggal_bayar): ?>
                                <?php echo e($spp->tanggal_bayar->format('d/m/Y')); ?>

                            <?php else: ?>
                                <span class="text-muted">-</span>
                            <?php endif; ?>
                        </td>
                        <td><?php echo $spp->status_badge; ?></td>
                        <td class="text-center">
                            <a href="<?php echo e(route('admin.pembayaran-spp.show', $spp->id)); ?>" 
                               class="btn btn-sm btn-primary" 
                               title="Detail">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="<?php echo e(route('admin.pembayaran-spp.edit', $spp->id)); ?>" 
                               class="btn btn-sm btn-warning" 
                               title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="8" class="text-center" style="padding: 40px;">
                            <i class="fas fa-inbox" style="font-size: 3rem; color: #ccc; display: block; margin-bottom: 15px;"></i>
                            <p style="color: #999;">Belum ada riwayat pembayaran untuk santri ini.</p>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <?php if($pembayaranSpp->hasPages()): ?>
        <div style="margin-top: 20px;">
            <?php echo e($pembayaranSpp->links()); ?>

        </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\TugasAkhir\sim-pkpps\resources\views/admin/pembayaran-spp/riwayat.blade.php ENDPATH**/ ?>