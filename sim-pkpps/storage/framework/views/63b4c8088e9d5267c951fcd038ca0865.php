

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <h2><i class="fas fa-id-card"></i> Kelola Kartu RFID Santri</h2>
</div>

<?php if(session('success')): ?>
    <div class="alert alert-success">
        <i class="fas fa-check-circle"></i> <?php echo e(session('success')); ?>

    </div>
<?php endif; ?>

<?php if(session('error')): ?>
    <div class="alert alert-danger">
        <i class="fas fa-exclamation-circle"></i> <?php echo e(session('error')); ?>

    </div>
<?php endif; ?>

<div class="content-box">
    <div style="margin-bottom: 14px;">
        <form method="GET" class="filter-form-inline">
            <select name="filter" class="form-control">
                <option value="">-- Semua Santri --</option>
                <option value="ada_rfid" <?php echo e(request('filter') == 'ada_rfid' ? 'selected' : ''); ?>>Sudah Punya RFID</option>
                <option value="belum_rfid" <?php echo e(request('filter') == 'belum_rfid' ? 'selected' : ''); ?>>Belum Punya RFID</option>
            </select>

            <button type="submit" class="btn btn-primary">
                <i class="fas fa-filter"></i> Filter
            </button>

            <?php if(request('filter')): ?>
                <a href="<?php echo e(route('admin.kartu-rfid.index')); ?>" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Reset
                </a>
            <?php endif; ?>
        </form>
    </div>

    <?php if($santris->count() > 0): ?>
        <div class="table-wrapper">
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 50px;">No</th>
                    <th style="width: 100px;">ID Santri</th>
                    <th>Nama Santri</th>
                    <th style="width: 100px;">Kelas</th>
                    <th style="width: 200px;">UID RFID</th>
                    <th style="width: 120px; text-align: center;">Status</th>
                    <th style="width: 250px; text-align: center;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php $__currentLoopData = $santris; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $santri): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td><?php echo e($santris->firstItem() + $index); ?></td>
                    <td><strong><?php echo e($santri->id_santri); ?></strong></td>
                    <td><?php echo e($santri->nama_lengkap); ?></td>
                    <td><span class="badge badge-secondary"><?php echo e($santri->kelasSantri->first()?->kelas?->nama_kelas ?? '-'); ?></span></td>
                    <td>
                        <?php if($santri->rfid_uid): ?>
                            <code style="font-size: 0.85rem;"><?php echo e($santri->rfid_uid); ?></code>
                        <?php else: ?>
                            <span style="color: var(--text-light);">-</span>
                        <?php endif; ?>
                    </td>
                    <td class="text-center">
                        <?php if($santri->rfid_uid): ?>
                            <span class="badge badge-success"><i class="fas fa-check"></i> Terdaftar</span>
                        <?php else: ?>
                            <span class="badge badge-warning"><i class="fas fa-exclamation"></i> Belum</span>
                        <?php endif; ?>
                    </td>
                    <td class="text-center">
                        <?php if($santri->rfid_uid): ?>
                            <a href="<?php echo e(route('admin.kartu-rfid.cetak', $santri->id_santri)); ?>" class="btn btn-sm btn-primary" title="Cetak Kartu" target="_blank">
                                <i class="fas fa-print"></i> Cetak
                            </a>
                            <form action="<?php echo e(route('admin.kartu-rfid.hapus', $santri->id_santri)); ?>" method="POST" style="display: inline-block;" onsubmit="return confirm('Yakin ingin menghapus RFID ini?')">
                                <?php echo csrf_field(); ?>
                                <?php echo method_field('DELETE'); ?>
                                <button type="submit" class="btn btn-sm btn-danger" title="Hapus RFID">
                                    <i class="fas fa-trash"></i> Hapus
                                </button>
                            </form>
                        <?php else: ?>
                            <a href="<?php echo e(route('admin.kartu-rfid.daftar', $santri->id_santri)); ?>" class="btn btn-sm btn-success" title="Daftarkan RFID">
                                <i class="fas fa-id-card"></i> Daftarkan RFID
                            </a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
        </div>

        <div style="margin-top: 14px;">
            <?php echo e($santris->links()); ?>

        </div>
    <?php else: ?>
        <div class="empty-state">
            <i class="fas fa-users-slash"></i>
            <h3>Tidak Ada Data Santri</h3>
            <p>Belum ada santri aktif yang terdaftar.</p>
        </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\TugasAkhir\sim-pkpps\resources\views/admin/kegiatan/kartu/index.blade.php ENDPATH**/ ?>