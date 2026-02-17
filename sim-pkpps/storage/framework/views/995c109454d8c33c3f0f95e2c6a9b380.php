

<?php $__env->startSection('title', 'Master Pelanggaran'); ?>

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <h2><i class="fas fa-list-ul"></i> Master Pelanggaran</h2>
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

<!-- Filter -->
<div class="content-box" style="margin-bottom: 20px;">
    <form method="GET" action="<?php echo e(route('admin.kategori-pelanggaran.index')); ?>">
        <div style="display: grid; grid-template-columns: 2fr 1fr auto; gap: 15px; align-items: end;">
            <div class="form-group" style="margin-bottom: 0;">
                <label for="id_klasifikasi">
                    <i class="fas fa-filter form-icon"></i>
                    Filter Klasifikasi
                </label>
                <select name="id_klasifikasi" id="id_klasifikasi" class="form-control">
                    <option value="">-- Semua Klasifikasi --</option>
                    <?php $__currentLoopData = $klasifikasiList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $kl): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($kl->id_klasifikasi); ?>" 
                                <?php echo e(request('id_klasifikasi') == $kl->id_klasifikasi ? 'selected' : ''); ?>>
                            <?php echo e($kl->nama_klasifikasi); ?>

                        </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>

            <div class="form-group" style="margin-bottom: 0;">
                <label for="is_active">
                    <i class="fas fa-toggle-on form-icon"></i>
                    Status
                </label>
                <select name="is_active" id="is_active" class="form-control">
                    <option value="">-- Semua Status --</option>
                    <option value="1" <?php echo e(request('is_active') == '1' ? 'selected' : ''); ?>>Aktif</option>
                    <option value="0" <?php echo e(request('is_active') == '0' ? 'selected' : ''); ?>>Nonaktif</option>
                </select>
            </div>

            <div class="btn-group" style="margin-bottom: 0;">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search"></i> Filter
                </button>
                <a href="<?php echo e(route('admin.kategori-pelanggaran.index')); ?>" class="btn btn-secondary">
                    <i class="fas fa-redo"></i> Reset
                </a>
            </div>
        </div>
    </form>
</div>

<!-- Tabel Data -->
<div class="content-box">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h3 style="margin: 0; color: var(--primary-color);">
            <i class="fas fa-table"></i> Daftar Pelanggaran
        </h3>
        <div style="display: flex; gap: 10px;">
            <a href="<?php echo e(route('admin.klasifikasi-pelanggaran.index')); ?>" class="btn btn-warning">
                <i class="fas fa-tags"></i> Klasifikasi Pelanggaran
            </a>
            <a href="<?php echo e(route('admin.pembinaan-sanksi.index')); ?>" class="btn btn-success">
                <i class="fas fa-book-open"></i> Pembinaan & Sanksi
            </a>
            <a href="<?php echo e(route('admin.kategori-pelanggaran.create')); ?>" class="btn btn-primary">
                <i class="fas fa-plus-circle"></i> Tambah Pelanggaran
            </a>
        </div>
    </div>

    <?php if($data->isNotEmpty()): ?>
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 50px;">No</th>
                    <th style="width: 100px;">ID</th>
                    <th style="width: 150px;">Klasifikasi</th>
                    <th>Nama Pelanggaran</th>
                    <th style="width: 80px; text-align: center;">Poin</th>
                    <th style="width: 100px; text-align: center;">Digunakan</th>
                    <th style="width: 100px; text-align: center;">Status</th>
                    <th style="width: 200px; text-align: center;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php $__currentLoopData = $data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td><?php echo e($index + 1); ?></td>
                        <td><span class="badge badge-primary"><?php echo e($item->id_kategori); ?></span></td>
                        <td>
                            <?php if($item->klasifikasi): ?>
                                <span class="badge badge-info"><?php echo e($item->klasifikasi->nama_klasifikasi); ?></span>
                            <?php else: ?>
                                <span class="badge badge-secondary">-</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <strong><?php echo e($item->nama_pelanggaran); ?></strong>
                            <?php if($item->kafaroh): ?>
                                <br><small style="color: var(--text-light);">
                                    <i class="fas fa-hands"></i> Kafaroh: <?php echo e(Str::limit($item->kafaroh, 50)); ?>

                                </small>
                            <?php endif; ?>
                        </td>
                        <td style="text-align: center;">
                            <span class="badge badge-danger" style="font-size: 0.9em;">
                                <i class="fas fa-star"></i> <?php echo e($item->poin); ?>

                            </span>
                        </td>
                        <td style="text-align: center;">
                            <span class="badge badge-secondary">
                                <?php echo e($item->riwayatPelanggaran->count()); ?>x
                            </span>
                        </td>
                        <td style="text-align: center;">
                            <?php if($item->is_active): ?>
                                <span class="badge badge-success">Aktif</span>
                            <?php else: ?>
                                <span class="badge badge-secondary">Nonaktif</span>
                            <?php endif; ?>
                        </td>
                        <td style="text-align: center;">
                            <div style="display: flex; justify-content: center; gap: 8px;">
                                <a href="<?php echo e(route('admin.kategori-pelanggaran.show', $item)); ?>" 
                                   class="btn btn-sm btn-success" 
                                   title="Detail">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="<?php echo e(route('admin.kategori-pelanggaran.edit', $item)); ?>" 
                                   class="btn btn-sm btn-warning" 
                                   title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="<?php echo e(route('admin.kategori-pelanggaran.destroy', $item)); ?>" 
                                      method="POST" 
                                      style="display: inline;"
                                      onsubmit="return confirm('Yakin ingin menghapus pelanggaran <?php echo e($item->nama_pelanggaran); ?>?');">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('DELETE'); ?>
                                    <button type="submit" 
                                            class="btn btn-sm btn-danger" 
                                            title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
    <?php else: ?>
        <div class="empty-state">
            <i class="fas fa-folder-open"></i>
            <h3>Belum ada data pelanggaran</h3>
            <p>Silakan tambah pelanggaran baru menggunakan tombol di atas.</p>
            <a href="<?php echo e(route('admin.kategori-pelanggaran.create')); ?>" class="btn btn-primary">
                <i class="fas fa-plus"></i> Tambah Pelanggaran
            </a>
        </div>
    <?php endif; ?>
</div>

<script>
// Auto hide alerts after 5 seconds
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
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\TugasAkhir\sim-pkpps\resources\views/admin/kategori_pelanggaran/index.blade.php ENDPATH**/ ?>