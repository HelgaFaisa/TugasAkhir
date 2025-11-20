

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <h2><i class="fas fa-clipboard-check"></i> Absensi Kegiatan</h2>
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
    <div style="margin-bottom: 20px;">
        <form method="GET" class="filter-form-inline">
            <select name="hari" class="form-control">
                <option value="">-- Semua Hari --</option>
                <?php $__currentLoopData = $hariList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $h): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($h); ?>" <?php echo e(request('hari') == $h ? 'selected' : ''); ?>><?php echo e($h); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>

            <button type="submit" class="btn btn-primary">
                <i class="fas fa-filter"></i> Filter
            </button>

            <?php if(request('hari')): ?>
                <a href="<?php echo e(route('admin.absensi-kegiatan.index')); ?>" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Reset
                </a>
            <?php endif; ?>
        </form>
    </div>

    <?php if($kegiatans->count() > 0): ?>
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 50px;">No</th>
                    <th style="width: 100px;">Hari</th>
                    <th style="width: 120px;">Waktu</th>
                    <th>Nama Kegiatan</th>
                    <th style="width: 150px;">Kategori</th>
                    <th style="width: 250px; text-align: center;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php $__currentLoopData = $kegiatans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $kegiatan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td><?php echo e($kegiatans->firstItem() + $index); ?></td>
                    <td><span class="badge badge-primary"><?php echo e($kegiatan->hari); ?></span></td>
                    <td><?php echo e(date('H:i', strtotime($kegiatan->waktu_mulai))); ?> - <?php echo e(date('H:i', strtotime($kegiatan->waktu_selesai))); ?></td>
                    <td><strong><?php echo e($kegiatan->nama_kegiatan); ?></strong></td>
                    <td><?php echo e($kegiatan->kategori->nama_kategori); ?></td>
                    <td class="text-center">
                        <a href="<?php echo e(route('admin.absensi-kegiatan.input', $kegiatan->kegiatan_id)); ?>" class="btn btn-sm btn-success" title="Input Absensi">
                            <i class="fas fa-clipboard-check"></i> Input
                        </a>
                        <a href="<?php echo e(route('admin.absensi-kegiatan.rekap', $kegiatan->kegiatan_id)); ?>" class="btn btn-sm btn-primary" title="Rekap Absensi">
                            <i class="fas fa-chart-bar"></i> Rekap
                        </a>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>

        <div style="margin-top: 20px;">
            <?php echo e($kegiatans->links()); ?>

        </div>
    <?php else: ?>
        <div class="empty-state">
            <i class="fas fa-calendar-times"></i>
            <h3>Belum Ada Kegiatan</h3>
            <p>Silakan tambahkan kegiatan terlebih dahulu.</p>
            <a href="<?php echo e(route('admin.kegiatan.create')); ?>" class="btn btn-success">
                <i class="fas fa-plus"></i> Tambah Kegiatan
            </a>
        </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\TugasAkhir\sim-pkpps\resources\views/admin/kegiatan/absensi/index.blade.php ENDPATH**/ ?>