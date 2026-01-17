

<?php $__env->startSection('title', 'Tambah Data Kesehatan Santri'); ?>

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <h2><i class="fas fa-plus-circle"></i> Tambah Data Kesehatan Santri</h2>
</div>

<!-- Content Box -->
<div class="content-box">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h3 style="margin: 0; color: var(--primary-color);">
            <i class="fas fa-file-medical"></i> Form Data Kesehatan
        </h3>
        <a href="<?php echo e(route('admin.kesehatan-santri.index')); ?>" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    <form action="<?php echo e(route('admin.kesehatan-santri.store')); ?>" method="POST">
        <?php echo csrf_field(); ?>

        <!-- Pilih Santri -->
        <div class="form-group">
            <label for="id_santri"><i class="fas fa-user form-icon"></i>Santri *</label>
            <select name="id_santri" id="id_santri" class="form-control <?php $__errorArgs = ['id_santri'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
                <option value="">-- Pilih Santri --</option>
                <?php $__currentLoopData = $santri; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($s->id_santri); ?>" <?php echo e(old('id_santri') == $s->id_santri ? 'selected' : ''); ?>>
                        <?php echo e($s->id_santri); ?> - <?php echo e($s->nama_lengkap); ?> (<?php echo e($s->kelas); ?>)
                    </option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
            <?php $__errorArgs = ['id_santri'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                <div class="invalid-feedback"><?php echo e($message); ?></div>
            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <!-- Tanggal Masuk -->
            <div class="form-group">
                <label for="tanggal_masuk"><i class="fas fa-calendar-plus form-icon"></i>Tanggal Masuk UKP *</label>
                <input type="date" 
                       name="tanggal_masuk" 
                       id="tanggal_masuk" 
                       class="form-control <?php $__errorArgs = ['tanggal_masuk'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                       value="<?php echo e(old('tanggal_masuk', date('Y-m-d'))); ?>" 
                       max="<?php echo e(date('Y-m-d')); ?>"
                       required>
                <?php $__errorArgs = ['tanggal_masuk'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <div class="invalid-feedback"><?php echo e($message); ?></div>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <!-- Status -->
            <div class="form-group">
                <label for="status"><i class="fas fa-info-circle form-icon"></i>Status *</label>
                <select name="status" id="status" class="form-control <?php $__errorArgs = ['status'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
                    <option value="">-- Pilih Status --</option>
                    <option value="dirawat" <?php echo e(old('status') == 'dirawat' ? 'selected' : ''); ?>>Dirawat</option>
                    <option value="sembuh" <?php echo e(old('status') == 'sembuh' ? 'selected' : ''); ?>>Sembuh</option>
                    <option value="izin" <?php echo e(old('status') == 'izin' ? 'selected' : ''); ?>>Izin Pulang</option>
                </select>
                <?php $__errorArgs = ['status'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <div class="invalid-feedback"><?php echo e($message); ?></div>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
        </div>

        <!-- Tanggal Keluar (Hidden by default) -->
        <div id="tanggal_keluar_group" class="form-group" style="display: none;">
            <label for="tanggal_keluar"><i class="fas fa-calendar-check form-icon"></i>Tanggal Keluar UKP</label>
            <input type="date" 
                   name="tanggal_keluar" 
                   id="tanggal_keluar" 
                   class="form-control <?php $__errorArgs = ['tanggal_keluar'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                   value="<?php echo e(old('tanggal_keluar')); ?>" 
                   max="<?php echo e(date('Y-m-d')); ?>">
            <?php $__errorArgs = ['tanggal_keluar'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                <div class="invalid-feedback"><?php echo e($message); ?></div>
            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            <small class="form-text">
                <i class="fas fa-info-circle"></i> Kosongkan jika santri masih dirawat
            </small>
        </div>

        <!-- Keluhan -->
        <div class="form-group">
            <label for="keluhan"><i class="fas fa-notes-medical form-icon"></i>Keluhan *</label>
            <textarea name="keluhan" 
                      id="keluhan" 
                      rows="4" 
                      class="form-control <?php $__errorArgs = ['keluhan'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                      placeholder="Tuliskan keluhan atau gejala yang dialami santri..."
                      required><?php echo e(old('keluhan')); ?></textarea>
            <?php $__errorArgs = ['keluhan'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                <div class="invalid-feedback"><?php echo e($message); ?></div>
            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            <small class="form-text">Maksimal 1000 karakter</small>
        </div>

        <!-- Catatan -->
        <div class="form-group">
            <label for="catatan"><i class="fas fa-clipboard form-icon"></i>Catatan Petugas</label>
            <textarea name="catatan" 
                      id="catatan" 
                      rows="3" 
                      class="form-control <?php $__errorArgs = ['catatan'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                      placeholder="Catatan tambahan dari petugas kesehatan..."><?php echo e(old('catatan')); ?></textarea>
            <?php $__errorArgs = ['catatan'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                <div class="invalid-feedback"><?php echo e($message); ?></div>
            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            <small class="form-text">Maksimal 1000 karakter (opsional)</small>
        </div>

        <!-- Buttons -->
        <div style="display: flex; gap: 10px; justify-content: flex-end; margin-top: 30px;">
            <a href="<?php echo e(route('admin.kesehatan-santri.index')); ?>" class="btn btn-secondary">
                <i class="fas fa-times"></i> Batal
            </a>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Simpan Data
            </button>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const statusSelect = document.getElementById('status');
    const tanggalKeluarGroup = document.getElementById('tanggal_keluar_group');
    const tanggalKeluarInput = document.getElementById('tanggal_keluar');
    const tanggalMasukInput = document.getElementById('tanggal_masuk');
    
    // Function to toggle tanggal keluar visibility
    function toggleTanggalKeluar() {
        if (statusSelect.value === 'dirawat') {
            tanggalKeluarGroup.style.display = 'none';
            tanggalKeluarInput.value = '';
            tanggalKeluarInput.removeAttribute('required');
        } else {
            tanggalKeluarGroup.style.display = 'block';
            if (statusSelect.value === 'sembuh' || statusSelect.value === 'izin') {
                tanggalKeluarInput.setAttribute('required', 'required');
            }
        }
    }
    
    // Set minimum date for tanggal_keluar based on tanggal_masuk
    function setMinTanggalKeluar() {
        if (tanggalMasukInput.value) {
            tanggalKeluarInput.min = tanggalMasukInput.value;
        }
    }
    
    // Event listeners
    statusSelect.addEventListener('change', toggleTanggalKeluar);
    tanggalMasukInput.addEventListener('change', setMinTanggalKeluar);
    
    // Initialize on page load
    toggleTanggalKeluar();
    setMinTanggalKeluar();
    
    // Character counter
    function setupCharacterCounter(textareaId, maxLength) {
        const textarea = document.getElementById(textareaId);
        const counter = document.createElement('div');
        counter.style.cssText = 'text-align: right; font-size: 0.85em; color: #7F8C8D; margin-top: 5px;';
        
        function updateCounter() {
            const remaining = maxLength - textarea.value.length;
            counter.textContent = `${textarea.value.length}/${maxLength} karakter`;
            counter.style.color = remaining < 50 ? '#E74C3C' : '#7F8C8D';
        }
        
        textarea.addEventListener('input', updateCounter);
        
        // Insert counter after the last sibling (after form-text if exists)
        const lastSibling = textarea.parentNode.lastElementChild;
        if (lastSibling.classList && lastSibling.classList.contains('form-text')) {
            lastSibling.parentNode.appendChild(counter);
        } else {
            textarea.parentNode.appendChild(counter);
        }
        
        updateCounter();
    }
    
    setupCharacterCounter('keluhan', 1000);
    setupCharacterCounter('catatan', 1000);
});
</script>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\TugasAkhir\sim-pkpps\resources\views/admin/kesehatan-santri/create.blade.php ENDPATH**/ ?>