<?php $__env->startSection('content'); ?>
<div class="page-header">
    <h2><i class="fas fa-list-alt"></i> Kategori Kegiatan</h2>
    <a href="<?php echo e(route('admin.kegiatan.jadwal')); ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
</div>

<?php if(session('success')): ?>
    <div class="alert alert-success">
        <i class="fas fa-check-circle"></i> <?php echo e(session('success')); ?>

    </div>
<?php endif; ?>

<div class="content-box">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 14px;">
        <form method="GET" style="flex: 1; max-width: 400px;">
            <div style="display: flex; gap: 10px;">
                <input type="text" name="search" class="form-control" placeholder="Cari kategori..." value="<?php echo e(request('search')); ?>">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search"></i>
                </button>
                <?php if(request('search')): ?>
                    <a href="<?php echo e(route('admin.kategori-kegiatan.index')); ?>" class="btn btn-secondary">
                        <i class="fas fa-times"></i>
                    </a>
                <?php endif; ?>
            </div>
        </form>
        <a href="<?php echo e(route('admin.kategori-kegiatan.create')); ?>" class="btn btn-success">
            <i class="fas fa-plus"></i> Tambah Kategori
        </a>
    </div>

    <?php if($kategoris->count() > 0): ?>
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 60px;">No</th>
                    <th style="width: 120px;">ID Kategori</th>
                    <th>Nama Kategori</th>
                    <th>Keterangan</th>
                    <th style="width: 150px;">Dibuat</th>
                    <th style="width: 180px; text-align: center;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php $__currentLoopData = $kategoris; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $kategori): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td><?php echo e($kategoris->firstItem() + $index); ?></td>
                    <td><strong><?php echo e($kategori->kategori_id); ?></strong></td>
                    <td><?php echo e($kategori->nama_kategori); ?></td>
                    <td><?php echo e(Str::limit($kategori->keterangan, 50) ?? '-'); ?></td>
                    <td><?php echo e($kategori->created_at->format('d M Y')); ?></td>
                    <td class="text-center">
                        <div style="display: flex; justify-content: center; align-items: center; gap: 8px;">
                            <a href="<?php echo e(route('admin.kategori-kegiatan.show', $kategori)); ?>" class="btn btn-sm btn-primary" title="Detail">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="<?php echo e(route('admin.kategori-kegiatan.edit', $kategori)); ?>" class="btn btn-sm btn-warning" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="<?php echo e(route('admin.kategori-kegiatan.destroy', $kategori)); ?>" method="POST" style="margin: 0;" onsubmit="return confirm('Yakin ingin menghapus kategori ini?')">
                                <?php echo csrf_field(); ?>
                                <?php echo method_field('DELETE'); ?>
                                <button type="submit" class="btn btn-sm btn-danger" title="Hapus">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>

        <div style="margin-top: 14px;">
            <?php echo e($kategoris->links()); ?>

        </div>
    <?php else: ?>
        <div class="empty-state">
            <i class="fas fa-folder-open"></i>
            <h3>Belum Ada Kategori</h3>
            <p>Silakan tambahkan kategori kegiatan baru.</p>
            <a href="<?php echo e(route('admin.kategori-kegiatan.create')); ?>" class="btn btn-success">
                <i class="fas fa-plus"></i> Tambah Kategori
            </a>
        </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\TugasAkhir\sim-pkpps\resources\views/admin/kegiatan/kategori/index.blade.php ENDPATH**/ ?>