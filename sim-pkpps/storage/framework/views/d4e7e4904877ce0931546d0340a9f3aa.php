

<?php $__env->startSection('title', 'Edit Berita'); ?>

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <h2><i class="fas fa-edit"></i> Edit Berita</h2>
</div>

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
    <form action="<?php echo e(route('admin.berita.update', $berita->id_berita)); ?>" method="POST" enctype="multipart/form-data" id="beritaForm">
        <?php echo csrf_field(); ?>
        <?php echo method_field('PUT'); ?>
        
        <!-- ID Berita (Read-only) -->
        <div style="background: var(--primary-light); padding: 15px; border-radius: var(--border-radius-sm); margin-bottom: 20px;">
            <strong style="color: var(--primary-dark);">
                <i class="fas fa-id-card"></i> ID Berita: <?php echo e($berita->id_berita); ?>

            </strong>
        </div>
        
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
                   value="<?php echo e(old('judul', $berita->judul)); ?>" 
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

        <!-- Konten Berita (Quill Editor) -->
        <div class="form-group">
            <label for="konten">
                <i class="fas fa-file-alt form-icon"></i>
                Konten Berita <span style="color: var(--danger-color);">*</span>
            </label>
            <div id="editor-container" style="min-height: 300px; background: white; border: 1px solid #ddd; border-radius: 4px;"></div>
            <textarea name="konten" 
                      id="konten" 
                      class="form-control <?php $__errorArgs = ['konten'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                      style="display: none;" 
                      required><?php echo e(old('konten', $berita->konten)); ?></textarea>
            <?php $__errorArgs = ['konten'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                <span class="invalid-feedback" style="display: block;"><?php echo e($message); ?></span>
            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            <span class="form-text">
                <i class="fas fa-magic"></i> Gunakan toolbar untuk formatting: Bold, Italic, Daftar, Warna, dsb.
            </span>
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
                       value="<?php echo e(old('penulis', $berita->penulis)); ?>" 
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
                
                <?php if($berita->gambar): ?>
                    <div style="margin-top: 10px;">
                        <strong>Gambar Saat Ini:</strong>
                        <br>
                        <img src="<?php echo e(asset('storage/' . $berita->gambar)); ?>" 
                             alt="Gambar Berita" 
                             style="max-width: 200px; max-height: 150px; border-radius: var(--border-radius-sm); border: 2px solid var(--primary-light); margin-top: 8px;">
                    </div>
                <?php endif; ?>
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
                    <option value="semua" <?php echo e(old('target_berita', $berita->target_berita) == 'semua' ? 'selected' : ''); ?>>
                        Semua Santri
                    </option>
                    <option value="kelas_tertentu" <?php echo e(old('target_berita', $berita->target_berita) == 'kelas_tertentu' ? 'selected' : ''); ?>>
                        Kelas Tertentu
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
                    <option value="draft" <?php echo e(old('status', $berita->status) == 'draft' ? 'selected' : ''); ?>>
                        Draft (Belum Dipublikasi)
                    </option>
                    <option value="published" <?php echo e(old('status', $berita->status) == 'published' ? 'selected' : ''); ?>>
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
        <?php
            $selectedKelas = old('target_kelas', $berita->target_kelas ?? []);
        ?>
        <div id="kelas-section" class="form-group" style="display: <?php echo e(old('target_berita', $berita->target_berita) == 'kelas_tertentu' ? 'block' : 'none'); ?>;">
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
                                   id="kelas_<?php echo e($kelas->id); ?>" 
                                   name="target_kelas[]" 
                                   value="<?php echo e($kelas->id); ?>" 
                                   class="kelas-checkbox"
                                   style="margin-right: 10px; width: 18px; height: 18px;"
                                   <?php echo e(in_array($kelas->id, $selectedKelas) ? 'checked' : ''); ?>>
                            <span style="font-weight: 600; color: var(--text-color);">
                                <?php echo e($kelas->nama_kelas); ?>

                            </span>
                        </label>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
            <small class="form-text">
                <i class="fas fa-info-circle"></i>
                <span id="selected-kelas-count"><?php echo e(count($selectedKelas)); ?></span> kelas dipilih dari <?php echo e($kelasOptions->count()); ?> total kelas.
            </small>
        </div>

        <!-- Submit Buttons -->
        <div style="display: flex; gap: 10px; margin-top: 30px; padding-top: 20px; border-top: 2px solid var(--primary-light);">
            <button type="submit" class="btn btn-success">
                <i class="fas fa-save"></i> Update Berita
            </button>
            <a href="<?php echo e(route('admin.berita.show', $berita->id_berita)); ?>" class="btn btn-primary">
                <i class="fas fa-eye"></i> Lihat Berita
            </a>
            <a href="<?php echo e(route('admin.berita.index')); ?>" class="btn btn-secondary">
                <i class="fas fa-times"></i> Batal
            </a>
        </div>
    </form>
</div>

<!-- Quill Editor CDN -->
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
<script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Quill Editor
    var quill = new Quill('#editor-container', {
        theme: 'snow',
        modules: {
            toolbar: [
                [{ 'header': [1, 2, 3, false] }],
                ['bold', 'italic', 'underline', 'strike'],
                [{ 'color': [] }, { 'background': [] }],
                [{ 'list': 'ordered' }, { 'list': 'bullet' }],
                [{ 'indent': '-1' }, { 'indent': '+1' }],
                [{ 'align': [] }],
                ['clean']
            ]
        },
        placeholder: 'Tulis konten berita di sini...'
    });

    // Load existing content
    var existing = document.getElementById('konten').value;
    if (existing) quill.root.innerHTML = existing;

    // Sync on change
    quill.on('text-change', function() {
        document.getElementById('konten').value = quill.root.innerHTML;
    });

    // Sync on submit + validate
    document.getElementById('beritaForm').onsubmit = function() {
        document.getElementById('konten').value = quill.root.innerHTML;
        if (quill.getText().trim().length === 0) {
            alert('Konten berita tidak boleh kosong!');
            return false;
        }
        return true;
    };

    // Target berita toggle
    var targetBerita = document.getElementById('target_berita');
    var kelasSection = document.getElementById('kelas-section');
    var kelasCheckboxes = document.querySelectorAll('.kelas-checkbox');

    targetBerita.addEventListener('change', function() {
        kelasSection.style.display = this.value === 'kelas_tertentu' ? 'block' : 'none';
        if (this.value !== 'kelas_tertentu') {
            kelasCheckboxes.forEach(function(cb) { cb.checked = false; });
            updateKelasCount();
        }
    });

    // Kelas counter
    kelasCheckboxes.forEach(function(cb) {
        cb.addEventListener('change', updateKelasCount);
    });

    function updateKelasCount() {
        var count = document.querySelectorAll('.kelas-checkbox:checked').length;
        var el = document.getElementById('selected-kelas-count');
        if (el) el.textContent = count;
    }

    updateKelasCount();
});
</script>

<style>
.ql-toolbar { background-color: #f8f9fa; border-radius: 4px 4px 0 0; border-bottom: 2px solid #dee2e6; }
.ql-container { font-size: 14px; font-family: Arial, sans-serif; min-height: 250px; }
.ql-editor { min-height: 250px; max-height: 500px; overflow-y: auto; }
.ql-editor h1 { font-size: 2em; color: #2c3e50; }
.ql-editor h2 { font-size: 1.5em; color: #34495e; }
.ql-editor h3 { font-size: 1.2em; color: #34495e; }
.ql-editor p { margin-bottom: 1em; }
.ql-editor ol, .ql-editor ul { padding-left: 1.5em; margin-bottom: 1em; }
.ql-editor li { margin-bottom: 0.5em; }
</style>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\TugasAkhir\sim-pkpps\resources\views/admin/berita/edit.blade.php ENDPATH**/ ?>