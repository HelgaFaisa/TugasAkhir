


<?php $__env->startSection('title', 'Tambah Riwayat Pelanggaran'); ?>

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <h2><i class="fas fa-plus-circle"></i> Tambah Riwayat Pelanggaran</h2>
</div>

<!-- Breadcrumb -->
<div style="margin-bottom: 20px;">
    <nav style="display: flex; align-items: center; gap: 8px; color: var(--text-light); font-size: 0.9em;">
        <a href="<?php echo e(route('admin.riwayat-pelanggaran.index')); ?>" style="color: var(--primary-color); text-decoration: none;">
            <i class="fas fa-history"></i> Riwayat Pelanggaran
        </a>
        <i class="fas fa-chevron-right" style="font-size: 0.7em;"></i>
        <span>Tambah</span>
    </nav>
</div>

<div class="content-box">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
        <h3 style="margin: 0; color: var(--primary-color);">
            <i class="fas fa-edit"></i> Form Tambah Riwayat
        </h3>
        <div style="background: var(--primary-light); padding: 10px 20px; border-radius: var(--border-radius-sm);">
            <small style="color: var(--text-light);">ID Riwayat Berikutnya:</small>
            <strong style="color: var(--primary-dark); font-size: 1.1em;"><?php echo e($nextIdRiwayat); ?></strong>
        </div>
    </div>

    <form action="<?php echo e(route('admin.riwayat-pelanggaran.store')); ?>" method="POST">
        <?php echo csrf_field(); ?>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
            <!-- Santri -->
            <div class="form-group">
                <label for="id_santri">
                    <i class="fas fa-user form-icon"></i>
                    Santri <span style="color: var(--danger-color);">*</span>
                </label>
                <select name="id_santri" 
                        id="id_santri" 
                        class="form-control <?php $__errorArgs = ['id_santri'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                        required>
                    <option value="">-- Pilih Santri --</option>
                    <?php $__currentLoopData = $santriList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $santri): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($santri->id_santri); ?>" <?php echo e(old('id_santri') == $santri->id_santri ? 'selected' : ''); ?>>
                            <?php echo e($santri->nama_lengkap); ?> - <?php echo e($santri->kelas); ?> (<?php echo e($santri->id_santri); ?>)
                        </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
                <?php $__errorArgs = ['id_santri'];
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

            <!-- Tanggal -->
            <div class="form-group">
                <label for="tanggal">
                    <i class="fas fa-calendar form-icon"></i>
                    Tanggal Pelanggaran <span style="color: var(--danger-color);">*</span>
                </label>
                <input type="date" 
                       name="tanggal" 
                       id="tanggal"
                       class="form-control <?php $__errorArgs = ['tanggal'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                       value="<?php echo e(old('tanggal', date('Y-m-d'))); ?>"
                       required>
                <?php $__errorArgs = ['tanggal'];
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
        </div>

        <!-- Kategori Pelanggaran -->
        <div class="form-group">
            <label for="id_kategori">
                <i class="fas fa-tags form-icon"></i>
                Kategori Pelanggaran <span style="color: var(--danger-color);">*</span>
            </label>
            <select name="id_kategori" 
                    id="id_kategori" 
                    class="form-control <?php $__errorArgs = ['id_kategori'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                    required>
                <option value="">-- Pilih Kategori Pelanggaran --</option>
                <?php $__currentLoopData = $kategoriList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $kategori): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($kategori->id_kategori); ?>" 
                            data-poin="<?php echo e($kategori->poin); ?>"
                            <?php echo e(old('id_kategori') == $kategori->id_kategori ? 'selected' : ''); ?>>
                        <?php echo e($kategori->nama_pelanggaran); ?> - <?php echo e($kategori->poin); ?> Poin (<?php echo e($kategori->id_kategori); ?>)
                    </option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
            <?php $__errorArgs = ['id_kategori'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                <span class="invalid-feedback"><?php echo e($message); ?></span>
            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            
            <!-- Preview Poin -->
            <div id="poin-preview" style="display: none; margin-top: 12px; padding: 12px; background: var(--danger-color); color: white; border-radius: var(--border-radius-sm); text-align: center;">
                <i class="fas fa-fire"></i> 
                <strong>Poin yang akan ditambahkan: <span id="poin-value">0</span> Poin</strong>
            </div>
        </div>

        <!-- Keterangan -->
        <div class="form-group">
            <label for="keterangan">
                <i class="fas fa-comment form-icon"></i>
                Keterangan Tambahan
            </label>
            <textarea name="keterangan" 
                      id="keterangan" 
                      rows="4"
                      class="form-control <?php $__errorArgs = ['keterangan'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                      placeholder="Jelaskan detail pelanggaran (opsional)"><?php echo e(old('keterangan')); ?></textarea>
            <?php $__errorArgs = ['keterangan'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                <span class="invalid-feedback"><?php echo e($message); ?></span>
            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            <span class="form-text">Maksimal 1000 karakter</span>
        </div>

        <div class="btn-group">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Simpan Riwayat
            </button>
            <a href="<?php echo e(route('admin.riwayat-pelanggaran.index')); ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </form>
</div>

<script>
// Preview poin saat kategori dipilih
document.getElementById('id_kategori').addEventListener('change', function() {
    const selectedOption = this.options[this.selectedIndex];
    const poin = selectedOption.getAttribute('data-poin');
    const preview = document.getElementById('poin-preview');
    const poinValue = document.getElementById('poin-value');
    
    if (poin) {
        poinValue.textContent = poin;
        preview.style.display = 'block';
    } else {
        preview.style.display = 'none';
    }
});

// Trigger change event jika ada old value
if (document.getElementById('id_kategori').value) {
    document.getElementById('id_kategori').dispatchEvent(new Event('change'));
}
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\TugasAkhir\sim-pkpps\resources\views/admin/riwayat_pelanggaran/create.blade.php ENDPATH**/ ?>