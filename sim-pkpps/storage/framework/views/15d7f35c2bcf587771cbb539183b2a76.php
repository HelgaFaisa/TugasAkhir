

<?php $__env->startSection('title', 'Pembinaan & Sanksi'); ?>

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <h2><i class="fas fa-book-open"></i> Tata Tertib</h2>
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
            <i class="fas fa-list"></i> Daftar Konten
        </h3>
        <div style="display: flex; gap: 10px;">
            <a href="<?php echo e(route('admin.pembinaan-sanksi.create')); ?>" class="btn btn-primary">
                <i class="fas fa-plus-circle"></i> Tambah Konten
            </a>
        </div>
    </div>

    <?php if($data->isNotEmpty()): ?>
        <div class="alert alert-info" style="margin-bottom: 14px;">
            <i class="fas fa-info-circle"></i>
            <strong>Info:</strong> Konten akan ditampilkan sesuai urutan. Drag atau ubah nomor urutan untuk mengatur tampilan.
        </div>

        <div class="table-wrapper">

        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 50px;">No</th>
                    <th style="width: 100px;">ID</th>
                    <th>Judul</th>
                    <th style="width: 300px;">Preview Konten</th>
                    <th style="width: 80px; text-align: center;">Urutan</th>
                    <th style="width: 100px; text-align: center;">Status</th>
                    <th style="width: 200px; text-align: center;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php $__currentLoopData = $data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td><?php echo e($index + 1); ?></td>
                        <td><span class="badge badge-primary"><?php echo e($item->id_pembinaan); ?></span></td>
                        <td>
                            <strong style="font-size: 1.05em;"><?php echo e($item->judul); ?></strong><br>
                            <small style="color: var(--text-light);">
                                <i class="fas fa-clock"></i> Diubah: <?php echo e($item->updated_at->diffForHumans()); ?>

                            </small>
                        </td>
                        <td>
                            <div style="max-height: 60px; overflow: hidden; color: var(--text-light); font-size: 0.9em; line-height: 1.5;">
                                <?php echo e(Str::limit(strip_tags($item->konten), 100)); ?>

                            </div>
                        </td>
                        <td style="text-align: center;">
                            <span class="badge badge-info" style="font-size: 1em;"><?php echo e($item->urutan); ?></span>
                        </td>
                        <td style="text-align: center;">
                            <?php if($item->is_active): ?>
                                <span class="badge badge-success">
                                    <i class="fas fa-check-circle"></i> Aktif
                                </span>
                            <?php else: ?>
                                <span class="badge badge-secondary">
                                    <i class="fas fa-times-circle"></i> Nonaktif
                                </span>
                            <?php endif; ?>
                        </td>
                        <td style="text-align: center;">
                            <div style="display: flex; justify-content: center; gap: 8px;">
                                <a href="<?php echo e(route('admin.pembinaan-sanksi.show', $item)); ?>" 
                                   class="btn btn-sm btn-success" 
                                   title="Detail & Preview">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="<?php echo e(route('admin.pembinaan-sanksi.edit', $item)); ?>" 
                                   class="btn btn-sm btn-warning" 
                                   title="Edit Konten">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="<?php echo e(route('admin.pembinaan-sanksi.destroy', $item)); ?>" 
                                      method="POST" 
                                      style="display: inline;"
                                      onsubmit="return confirm('Yakin ingin menghapus konten \'<?php echo e($item->judul); ?>\'?\n\nKonten yang dihapus tidak dapat dikembalikan.');">
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
            <h3>Belum ada konten</h3>
            <p>Mulai dengan menambahkan konten baru menggunakan Rich Text Editor.</p>
            <p style="color: var(--text-light); margin-bottom: 14px;">
                Anda dapat membuat peraturan, tata tertib, pembinaan, atau sanksi dengan format yang rapi.
            </p>
            <a href="<?php echo e(route('admin.pembinaan-sanksi.create')); ?>" class="btn btn-primary">
                <i class="fas fa-plus"></i> Tambah Konten Pertama
            </a>
        </div>
    <?php endif; ?>
</div>

<script>
setTimeout(function() {
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        alert.style.transition = 'opacity 0.5s';
        alert.style.opacity = '0';
        setTimeout(() => alert.remove(), 500);
    });
}, 5000);
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\TugasAkhir\sim-pkpps\resources\views/admin/pembinaan_sanksi/index.blade.php ENDPATH**/ ?>