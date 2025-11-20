

<?php $__env->startSection('title', 'Tambah Berita Baru'); ?>

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <h2><i class="fas fa-plus-circle"></i> Tambah Berita Baru</h2>
</div>

<!-- Alert Errors -->
<?php if($errors->any()): ?>
    <div class="alert alert-danger">
        <strong><i class="fas fa-exclamation-circle"></i> Terdapat kesalahan:</strong>
        <ul style="margin: 10px 0 0 20px;">
            <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <li><?php echo e($error); ?></li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </ul>
    </div>
<?php endif; ?>

<div class="content-box">
    <form action="<?php echo e(route('admin.berita.store')); ?>" method="POST" enctype="multipart/form-data">
        <?php echo csrf_field(); ?>
        
        <!-- Judul Berita -->
        <div class="form-group">
            <label for="judul">
                <i class="fas fa-heading form-icon"></i>
                Judul Berita <span style="color: var(--danger-color);">*</span>
            </label>
            <input type="text" 
                   id="judul" 
                   name="judul" 
                   class="form-control <?php $__errorArgs = ['judul'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                   value="<?php echo e(old('judul')); ?>" 
                   placeholder="Masukkan judul berita..." 
                   required>
            <?php $__errorArgs = ['judul'];
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

        <!-- Konten Berita -->
        <div class="form-group">
            <label for="konten">
                <i class="fas fa-align-left form-icon"></i>
                Konten Berita <span style="color: var(--danger-color);">*</span>
            </label>
            <textarea id="konten" 
                      name="konten" 
                      class="form-control <?php $__errorArgs = ['konten'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                      rows="10" 
                      placeholder="Tulis konten berita di sini..." 
                      required><?php echo e(old('konten')); ?></textarea>
            <?php $__errorArgs = ['konten'];
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

        <!-- Penulis & Gambar -->
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <div class="form-group">
                <label for="penulis">
                    <i class="fas fa-user-edit form-icon"></i>
                    Penulis <span style="color: var(--danger-color);">*</span>
                </label>
                <input type="text" 
                       id="penulis" 
                       name="penulis" 
                       class="form-control <?php $__errorArgs = ['penulis'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                       value="<?php echo e(old('penulis')); ?>" 
                       placeholder="Nama penulis berita" 
                       required>
                <?php $__errorArgs = ['penulis'];
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
                <label for="gambar">
                    <i class="fas fa-image form-icon"></i>
                    Gambar Berita (Opsional)
                </label>
                <input type="file" 
                       id="gambar" 
                       name="gambar" 
                       class="form-control <?php $__errorArgs = ['gambar'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                       accept="image/*">
                <small class="form-text">Format: JPG, PNG, GIF. Maksimal 2MB.</small>
                <?php $__errorArgs = ['gambar'];
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

        <!-- Target & Status -->
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <div class="form-group">
                <label for="target_berita">
                    <i class="fas fa-bullseye form-icon"></i>
                    Target Berita <span style="color: var(--danger-color);">*</span>
                </label>
                <select id="target_berita" 
                        name="target_berita" 
                        class="form-control <?php $__errorArgs = ['target_berita'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                        required>
                    <option value="">-- Pilih Target --</option>
                    <option value="semua" <?php echo e(old('target_berita') == 'semua' ? 'selected' : ''); ?>>
                        Semua Santri
                    </option>
                    <option value="kelas_tertentu" <?php echo e(old('target_berita') == 'kelas_tertentu' ? 'selected' : ''); ?>>
                        Kelas Tertentu
                    </option>
                    <option value="santri_tertentu" <?php echo e(old('target_berita') == 'santri_tertentu' ? 'selected' : ''); ?>>
                        Santri Tertentu
                    </option>
                </select>
                <?php $__errorArgs = ['target_berita'];
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
                <label for="status">
                    <i class="fas fa-toggle-on form-icon"></i>
                    Status Berita <span style="color: var(--danger-color);">*</span>
                </label>
                <select id="status" 
                        name="status" 
                        class="form-control <?php $__errorArgs = ['status'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                        required>
                    <option value="">-- Pilih Status --</option>
                    <option value="draft" <?php echo e(old('status') == 'draft' ? 'selected' : ''); ?>>
                        Draft (Belum Dipublikasi)
                    </option>
                    <option value="published" <?php echo e(old('status') == 'published' ? 'selected' : ''); ?>>
                        Published (Dipublikasi)
                    </option>
                </select>
                <?php $__errorArgs = ['status'];
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

        <!-- Section: Pilih Kelas Tertentu -->
        <div id="kelas-section" class="form-group" style="display: none;">
            <label>
                <i class="fas fa-graduation-cap form-icon"></i>
                Pilih Kelas yang Akan Menerima Berita <span style="color: var(--danger-color);">*</span>
            </label>
            <div style="border: 2px solid var(--primary-light); border-radius: var(--border-radius-sm); padding: 20px; background-color: var(--primary-light);">
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 15px;">
                    <?php $__currentLoopData = $kelasOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $kelas): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div style="background: white; padding: 12px; border-radius: var(--border-radius-sm); box-shadow: var(--shadow-sm);">
                        <label style="display: flex; align-items: center; margin: 0; cursor: pointer;">
                            <input type="checkbox" 
                                   id="kelas_<?php echo e($kelas); ?>" 
                                   name="target_kelas[]" 
                                   value="<?php echo e($kelas); ?>" 
                                   class="kelas-checkbox"
                                   style="margin-right: 10px; width: 18px; height: 18px;"
                                   <?php echo e(in_array($kelas, old('target_kelas', [])) ? 'checked' : ''); ?>>
                            <span style="font-weight: 600; color: var(--text-color);">
                                Kelas <?php echo e($kelas); ?>

                            </span>
                        </label>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
            <small class="form-text">
                <i class="fas fa-info-circle"></i>
                <span id="selected-kelas-count">0</span> kelas dipilih dari <?php echo e(count($kelasOptions)); ?> total kelas.
            </small>
        </div>

        <!-- Section: Pilih Santri Tertentu -->
        <div id="santri-section" class="form-group" style="display: none;">
            <label>
                <i class="fas fa-users form-icon"></i>
                Pilih Santri yang Akan Menerima Berita <span style="color: var(--danger-color);">*</span>
            </label>
            
            <!-- Select All -->
            <div style="background: var(--primary-light); padding: 12px; border-radius: var(--border-radius-sm); margin-bottom: 10px;">
                <label style="display: flex; align-items: center; margin: 0; cursor: pointer; font-weight: 600;">
                    <input type="checkbox" 
                           id="select-all" 
                           style="margin-right: 10px; width: 20px; height: 20px;">
                    <span style="color: var(--primary-dark);">
                        <i class="fas fa-check-double"></i> Pilih Semua Santri
                    </span>
                </label>
            </div>
            
            <!-- List Santri -->
            <div style="border: 2px solid var(--primary-light); border-radius: var(--border-radius-sm); padding: 15px; max-height: 400px; overflow-y: auto; background-color: #FAFAFA;">
                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 12px;">
                    <?php $__currentLoopData = $santri; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div style="background: white; padding: 12px; border-radius: var(--border-radius-sm); box-shadow: var(--shadow-sm); transition: all 0.2s ease;">
                        <label style="display: flex; align-items: center; gap: 10px; margin: 0; cursor: pointer;">
                            <input type="checkbox" 
                                id="santri_<?php echo e($s->id_santri); ?>" 
                                name="santri_tertentu[]" 
                                value="<?php echo e($s->id_santri); ?>" 
                                class="santri-checkbox"
                                style="width: 18px; height: 18px; flex-shrink: 0;"
                                <?php echo e(in_array($s->id_santri, old('santri_tertentu', [])) ? 'checked' : ''); ?>>
                            
                            <!-- Hanya tampilkan initial, tanpa foto -->
                            <div style="width: 40px; height: 40px; border-radius: 50%; background: var(--primary-color); display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; flex-shrink: 0;">
                                <?php echo e(strtoupper(substr($s->nama_lengkap, 0, 1))); ?>

                            </div>
                            
                            <div style="flex-grow: 1; min-width: 0;">
                                <div style="font-weight: 600; color: var(--primary-color); font-size: 0.85em;">
                                    <?php echo e($s->id_santri); ?>

                                </div>
                                <div style="font-weight: 500; color: var(--text-color); font-size: 0.9em; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                    <?php echo e($s->nama_lengkap); ?>

                                </div>
                                <div style="font-size: 0.8em; color: var(--text-light);">
                                    <?php echo e($s->kelas); ?>

                                </div>
                            </div>
                        </label>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
            <small class="form-text">
                <i class="fas fa-info-circle"></i>
                <span id="selected-count">0</span> santri dipilih dari <?php echo e($santri->count()); ?> total santri aktif.
            </small>
        </div>

        <!-- Submit Buttons -->
        <div style="display: flex; gap: 10px; margin-top: 30px; padding-top: 20px; border-top: 2px solid var(--primary-light);">
            <button type="submit" class="btn btn-success">
                <i class="fas fa-save"></i> Simpan Berita
            </button>
            <a href="<?php echo e(route('admin.berita.index')); ?>" class="btn btn-secondary">
                <i class="fas fa-times"></i> Batal
            </a>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const targetBerita = document.getElementById('target_berita');
    const santriSection = document.getElementById('santri-section');
    const kelasSection = document.getElementById('kelas-section');
    const selectAll = document.getElementById('select-all');
    const santriCheckboxes = document.querySelectorAll('.santri-checkbox');
    const kelasCheckboxes = document.querySelectorAll('.kelas-checkbox');
    const selectedCount = document.getElementById('selected-count');
    const selectedKelasCount = document.getElementById('selected-kelas-count');

    // Toggle sections berdasarkan target berita
    targetBerita.addEventListener('change', function() {
        santriSection.style.display = 'none';
        kelasSection.style.display = 'none';
        
        if (this.value === 'santri_tertentu') {
            santriSection.style.display = 'block';
        } else if (this.value === 'kelas_tertentu') {
            kelasSection.style.display = 'block';
        } else {
            // Reset checkboxes
            if (selectAll) selectAll.checked = false;
            santriCheckboxes.forEach(cb => cb.checked = false);
            kelasCheckboxes.forEach(cb => cb.checked = false);
            updateSelectedCount();
            updateSelectedKelasCount();
        }
    });

    // Trigger on page load jika ada old value
    if (targetBerita.value === 'santri_tertentu') {
        santriSection.style.display = 'block';
    } else if (targetBerita.value === 'kelas_tertentu') {
        kelasSection.style.display = 'block';
    }

    // Select All functionality untuk santri
    if (selectAll) {
        selectAll.addEventListener('change', function() {
            santriCheckboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
            updateSelectedCount();
        });
    }

    // Update select all ketika checkbox santri individual berubah
    santriCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const checkedCount = document.querySelectorAll('.santri-checkbox:checked').length;
            if (selectAll) {
                selectAll.checked = checkedCount === santriCheckboxes.length;
                selectAll.indeterminate = checkedCount > 0 && checkedCount < santriCheckboxes.length;
            }
            updateSelectedCount();
        });
    });

    // Update counter untuk kelas
    kelasCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateSelectedKelasCount);
    });

    // Functions untuk update counter
    function updateSelectedCount() {
        const checkedCount = document.querySelectorAll('.santri-checkbox:checked').length;
        if (selectedCount) selectedCount.textContent = checkedCount;
    }

    function updateSelectedKelasCount() {
        const checkedCount = document.querySelectorAll('.kelas-checkbox:checked').length;
        if (selectedKelasCount) selectedKelasCount.textContent = checkedCount;
    }

    // Initial count
    updateSelectedCount();
    updateSelectedKelasCount();
});
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\TugasAkhir\sim-pkpps\resources\views/admin/berita/create.blade.php ENDPATH**/ ?>