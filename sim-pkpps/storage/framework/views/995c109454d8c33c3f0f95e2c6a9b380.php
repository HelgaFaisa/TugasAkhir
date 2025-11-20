


<?php $__env->startSection('title', 'Data Kategori Pelanggaran'); ?>

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <h2><i class="fas fa-list-ul"></i> Kategori Pelanggaran</h2>
</div>

<!-- Alert Messages -->
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

<!-- Form Tambah & Edit -->
<div class="content-box" style="margin-bottom: 30px;">
    <h3 style="margin-bottom: 20px; color: var(--primary-color);">
        <?php if(isset($kategori)): ?>
            <i class="fas fa-edit"></i> Edit Kategori
        <?php else: ?>
            <i class="fas fa-plus-circle"></i> Tambah Kategori
        <?php endif; ?>
    </h3>
    <form action="<?php if(isset($kategori)): ?><?php echo e(route('admin.kategori-pelanggaran.update', $kategori)); ?><?php else: ?><?php echo e(route('admin.kategori-pelanggaran.store')); ?><?php endif; ?>" method="POST">
        <?php echo csrf_field(); ?>
        <?php if(isset($kategori)): ?>
            <?php echo method_field('PUT'); ?>
        <?php endif; ?>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
            <div class="form-group">
                <label for="nama_pelanggaran">
                    <i class="fas fa-exclamation-triangle form-icon"></i>
                    Nama Pelanggaran <span style="color: var(--danger-color);">*</span>
                </label>
                <input type="text" 
                       name="nama_pelanggaran" 
                       id="nama_pelanggaran"
                       class="form-control <?php $__errorArgs = ['nama_pelanggaran'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                       value="<?php echo e(old('nama_pelanggaran', $kategori->nama_pelanggaran ?? '')); ?>"
                       placeholder="Contoh: Terlambat Sholat, Tidak Rapi"
                       required>
                <?php $__errorArgs = ['nama_pelanggaran'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <span class="invalid-feedback"><?php echo e($message); ?></span>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <div class="form-group">
                <label for="poin">
                    <i class="fas fa-star form-icon"></i>
                    Poin Pelanggaran <span style="color: var(--danger-color);">*</span>
                </label>
                <input type="number" 
                       name="poin" 
                       id="poin" 
                       min="1" 
                       max="100"
                       class="form-control <?php $__errorArgs = ['poin'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                       value="<?php echo e(old('poin', $kategori->poin ?? '')); ?>"
                       placeholder="1-100"
                       required>
                <?php $__errorArgs = ['poin'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <span class="invalid-feedback"><?php echo e($message); ?></span>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                <span class="form-text">Poin antara 1-100</span>
            </div>
        </div>

        <div class="btn-group">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i>
                <?php if(isset($kategori)): ?> Update <?php else: ?> Simpan <?php endif; ?>
            </button>
            <?php if(isset($kategori)): ?>
                <a href="<?php echo e(route('admin.kategori-pelanggaran.index')); ?>" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Batal
                </a>
            <?php endif; ?>
        </div>
    </form>
</div>

<!-- Tabel Data -->
<div class="content-box">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h3 style="margin: 0; color: var(--primary-color);">
            <i class="fas fa-table"></i> Daftar Kategori Pelanggaran
        </h3>
        <span class="badge badge-info" style="font-size: 0.9em;">
            Total: <?php echo e($data->count()); ?> Kategori
        </span>
    </div>

    <?php if($data->isNotEmpty()): ?>
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 50px;">No</th>
                    <th style="width: 120px;">ID Kategori</th>
                    <th>Nama Pelanggaran</th>
                    <th style="width: 120px; text-align: center;">Poin</th>
                    <th style="width: 100px; text-align: center;">Digunakan</th>
                    <th style="width: 200px; text-align: center;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php $__currentLoopData = $data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td><?php echo e($index + 1); ?></td>
                        <td>
                            <span class="badge badge-primary"><?php echo e($item->id_kategori); ?></span>
                        </td>
                        <td>
                            <strong><?php echo e($item->nama_pelanggaran); ?></strong>
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
                                      onsubmit="return confirm('Yakin ingin menghapus kategori <?php echo e($item->nama_pelanggaran); ?>?');">
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
            <h3>Belum ada data kategori pelanggaran</h3>
            <p>Mulai dengan menambahkan kategori pelanggaran baru menggunakan form di atas.</p>
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