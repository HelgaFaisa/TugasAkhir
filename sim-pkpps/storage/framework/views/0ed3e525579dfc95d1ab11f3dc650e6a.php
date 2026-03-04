

<?php $__env->startSection('title', 'Klasifikasi Pelanggaran'); ?>

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <h2><i class="fas fa-tags"></i> Klasifikasi Pelanggaran</h2>
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
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 14px;">
        <h3 style="margin: 0; color: var(--primary-color);">
            <i class="fas fa-list"></i> Daftar Klasifikasi
        </h3>
        <div style="display: flex; gap: 10px;">
            <a href="<?php echo e(route('admin.kategori-pelanggaran.index')); ?>" class="btn btn-info">
                <i class="fas fa-list-ul"></i> Kategori Pelanggaran
            </a>
            <a href="<?php echo e(route('admin.klasifikasi-pelanggaran.create')); ?>" class="btn btn-primary">
                <i class="fas fa-plus-circle"></i> Tambah Klasifikasi
            </a>
        </div>
    </div>

    <?php if($data->isNotEmpty()): ?>
        <div class="table-wrapper">
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 50px;">No</th>
                    <th style="width: 100px;">ID</th>
                    <th>Nama Klasifikasi</th>
                    <th style="width: 120px; text-align: center;">Jumlah Pelanggaran</th>
                    <th style="width: 80px; text-align: center;">Urutan</th>
                    <th style="width: 100px; text-align: center;">Status</th>
                    <th style="width: 200px; text-align: center;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php $__currentLoopData = $data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td><?php echo e($index + 1); ?></td>
                        <td><span class="badge badge-primary"><?php echo e($item->id_klasifikasi); ?></span></td>
                        <td>
                            <strong><?php echo e($item->nama_klasifikasi); ?></strong>
                            <?php if($item->deskripsi): ?>
                                <br><small style="color: var(--text-light);"><?php echo e(Str::limit($item->deskripsi, 80)); ?></small>
                            <?php endif; ?>
                        </td>
                        <td style="text-align: center;">
                            <span class="badge badge-info"><?php echo e($item->pelanggarans_count); ?></span>
                        </td>
                        <td style="text-align: center;"><?php echo e($item->urutan); ?></td>
                        <td style="text-align: center;">
                            <?php if($item->is_active): ?>
                                <span class="badge badge-success">Aktif</span>
                            <?php else: ?>
                                <span class="badge badge-secondary">Nonaktif</span>
                            <?php endif; ?>
                        </td>
                        <td style="text-align: center;">
                            <div style="display: flex; justify-content: center; gap: 8px;">
                                <a href="<?php echo e(route('admin.klasifikasi-pelanggaran.show', $item)); ?>" 
                                   class="btn btn-sm btn-success" 
                                   title="Detail">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="<?php echo e(route('admin.klasifikasi-pelanggaran.edit', $item)); ?>" 
                                   class="btn btn-sm btn-warning" 
                                   title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="<?php echo e(route('admin.klasifikasi-pelanggaran.destroy', $item)); ?>" 
                                      method="POST" 
                                      style="display: inline;"
                                      onsubmit="return confirm('Yakin ingin menghapus?');">
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
        </div>
    <?php else: ?>
        <div class="empty-state">
            <i class="fas fa-folder-open"></i>
            <h3>Belum ada klasifikasi</h3>
            <p>Mulai dengan menambahkan klasifikasi pelanggaran.</p>
            <a href="<?php echo e(route('admin.klasifikasi-pelanggaran.create')); ?>" class="btn btn-primary">
                <i class="fas fa-plus"></i> Tambah Klasifikasi
            </a>
        </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\TugasAkhir\sim-pkpps\resources\views/admin/klasifikasi_pelanggaran/index.blade.php ENDPATH**/ ?>