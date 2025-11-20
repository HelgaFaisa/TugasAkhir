

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <h2><i class="fas fa-calendar-alt"></i> Jadwal Kegiatan Santri</h2>
</div>

<?php if(session('success')): ?>
    <div class="alert alert-success">
        <i class="fas fa-check-circle"></i> <?php echo e(session('success')); ?>

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

            <select name="kategori_id" class="form-control">
                <option value="">-- Semua Kategori --</option>
                <?php $__currentLoopData = $kategoris; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $kat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($kat->kategori_id); ?>" <?php echo e(request('kategori_id') == $kat->kategori_id ? 'selected' : ''); ?>>
                        <?php echo e($kat->nama_kategori); ?>

                    </option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>

            <input type="text" name="search" class="form-control" placeholder="Cari kegiatan..." value="<?php echo e(request('search')); ?>" style="min-width: 200px;">

            <button type="submit" class="btn btn-primary">
                <i class="fas fa-filter"></i> Filter
            </button>

            <?php if(request()->hasAny(['hari', 'kategori_id', 'search'])): ?>
                <a href="<?php echo e(route('admin.kegiatan.index')); ?>" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Reset
                </a>
            <?php endif; ?>

            <a href="<?php echo e(route('admin.kegiatan.create')); ?>" class="btn btn-success" style="margin-left: auto;">
                <i class="fas fa-plus"></i> Tambah Kegiatan
            </a>
        </form>
    </div>

    <?php if($kegiatans->count() > 0): ?>
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 50px;">No</th>
                    <th style="width: 100px;">ID</th>
                    <th style="width: 100px;">Hari</th>
                    <th style="width: 120px;">Waktu</th>
                    <th>Nama Kegiatan</th>
                    <th style="width: 150px;">Kategori</th>
                    <th>Materi</th>
                    <th style="width: 180px; text-align: center;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php $__currentLoopData = $kegiatans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $kegiatan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td><?php echo e($kegiatans->firstItem() + $index); ?></td>
                    <td><strong><?php echo e($kegiatan->kegiatan_id); ?></strong></td>
                    <td><span class="badge badge-primary"><?php echo e($kegiatan->hari); ?></span></td>
                    <td><?php echo e(date('H:i', strtotime($kegiatan->waktu_mulai))); ?> - <?php echo e(date('H:i', strtotime($kegiatan->waktu_selesai))); ?></td>
                    <td><strong><?php echo e($kegiatan->nama_kegiatan); ?></strong></td>
                    <td><?php echo e($kegiatan->kategori->nama_kategori); ?></td>
                    <td><?php echo e(Str::limit($kegiatan->materi, 40) ?? '-'); ?></td>
                    <td class="text-center">
                        <a href="<?php echo e(route('admin.kegiatan.show', $kegiatan)); ?>" class="btn btn-sm btn-primary" title="Detail">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="<?php echo e(route('admin.kegiatan.edit', $kegiatan)); ?>" class="btn btn-sm btn-warning" title="Edit">
                            <i class="fas fa-edit"></i>
                        </a>
                        <form action="<?php echo e(route('admin.kegiatan.destroy', $kegiatan)); ?>" method="POST" style="display: inline-block;" onsubmit="return confirm('Yakin ingin menghapus kegiatan ini?')">
                            <?php echo csrf_field(); ?>
                            <?php echo method_field('DELETE'); ?>
                            <button type="submit" class="btn btn-sm btn-danger" title="Hapus">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
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
            <p>Silakan tambahkan jadwal kegiatan santri.</p>
            <a href="<?php echo e(route('admin.kegiatan.create')); ?>" class="btn btn-success">
                <i class="fas fa-plus"></i> Tambah Kegiatan
            </a>
        </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\TugasAkhir\sim-pkpps\resources\views/admin/kegiatan/data/index.blade.php ENDPATH**/ ?>