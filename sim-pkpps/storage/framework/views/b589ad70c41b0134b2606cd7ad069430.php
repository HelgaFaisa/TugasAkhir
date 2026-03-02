<?php
    $isEdit = isset($santri);
?>

<form action="<?php echo e($isEdit ? route('admin.santri.update', $santri) : route('admin.santri.store')); ?>" method="POST" class="data-form" enctype="multipart/form-data">
    <?php echo csrf_field(); ?>
    <?php if($isEdit): ?>
        <?php echo method_field('PUT'); ?>
    <?php endif; ?>

    <div class="form-group">
        <label for="id_santri">ID Santri</label>
        <input type="text" id="id_santri" name="id_santri" value="<?php echo e($isEdit ? $santri->id_santri : $nextIdSantri ?? 'Otomatis Dibuat'); ?>" class="form-control" disabled>
        <small class="form-text text-muted"><?php echo e($isEdit ? 'ID Santri tidak dapat diubah.' : 'ID akan otomatis di-generate (Contoh: ' . ($nextIdSantri ?? 'S001') . ')'); ?></small>
    </div>

    
    <div class="form-group">
        <label for="foto">
            <i class="fas fa-image form-icon"></i>
            Foto Santri
        </label>
        
        <?php if($isEdit && $santri->foto): ?>
            <div style="margin-bottom: 10px;">
                <img src="<?php echo e(asset('storage/' . $santri->foto)); ?>" 
                     alt="Foto <?php echo e($santri->nama_lengkap); ?>" 
                     style="max-width: 150px; max-height: 150px; border-radius: 8px; border: 2px solid var(--primary-light); object-fit: cover;"
                     loading="lazy">
                <p style="margin-top: 5px; font-size: 0.85rem; color: var(--text-light);">
                    <i class="fas fa-info-circle"></i> Foto saat ini
                </p>
            </div>
        <?php endif; ?>
        
        <input type="file" 
               id="foto" 
               name="foto" 
               class="form-control <?php $__errorArgs = ['foto'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
               accept="image/jpeg,image/jpg,image/png"
               onchange="previewImage(event)">
        
        <?php $__errorArgs = ['foto'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
            <div class="invalid-feedback"><?php echo e($message); ?></div>
        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        
        <small class="form-text text-muted">
            <i class="fas fa-info-circle"></i> 
            Format: JPG, JPEG, atau PNG. Maksimal 2 MB.
            <?php if($isEdit): ?>
                Upload foto baru akan mengganti foto lama.
            <?php endif; ?>
        </small>
        
        
        <img id="preview" 
             style="display: none; margin-top: 10px; max-width: 150px; max-height: 150px; border-radius: 8px; border: 2px solid var(--primary-color); object-fit: cover;" 
             loading="lazy">
    </div>

    <div class="form-group">
        <label for="nis">NIS (Nomor Induk Santri)</label>
        <input type="text" id="nis" name="nis" value="<?php echo e(old('nis', $isEdit ? $santri->nis : '')); ?>" class="form-control <?php $__errorArgs = ['nis'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" placeholder="Masukkan NIS">
        <?php $__errorArgs = ['nis'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>

    <div class="form-group">
        <label for="nama_lengkap">Nama Lengkap *</label>
        <input type="text" id="nama_lengkap" name="nama_lengkap" value="<?php echo e(old('nama_lengkap', $isEdit ? $santri->nama_lengkap : '')); ?>" class="form-control <?php $__errorArgs = ['nama_lengkap'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required placeholder="Masukkan nama lengkap">
        <?php $__errorArgs = ['nama_lengkap'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>

    <div class="form-group">
        <label for="jenis_kelamin">Jenis Kelamin *</label>
        <select id="jenis_kelamin" name="jenis_kelamin" class="form-control <?php $__errorArgs = ['jenis_kelamin'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
            <option value="">Pilih Jenis Kelamin</option>
            <option value="Laki-laki" <?php echo e(old('jenis_kelamin', $isEdit ? $santri->jenis_kelamin : '') == 'Laki-laki' ? 'selected' : ''); ?>>Laki-laki</option>
            <option value="Perempuan" <?php echo e(old('jenis_kelamin', $isEdit ? $santri->jenis_kelamin : '') == 'Perempuan' ? 'selected' : ''); ?>>Perempuan</option>
        </select>
        <?php $__errorArgs = ['jenis_kelamin'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>

    <hr>
    <h4><i class="fas fa-layer-group"></i> Kelas Santri</h4>
    <small class="form-text text-muted kelas-hint">
        <i class="fas fa-info-circle"></i> Pilih kelas untuk setiap kelompok. Bisa pilih lebih dari 1 kelas.
    </small>
    <?php $__errorArgs = ['kelas_ids'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
        <div class="alert alert-danger kelas-error">
            <i class="fas fa-exclamation-triangle"></i> <?php echo e($message); ?>

        </div>
    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>

    <?php if($kelompokKelas->isEmpty()): ?>
        <div class="alert alert-warning">
            <i class="fas fa-exclamation-circle"></i> Belum ada data kelompok kelas. Silakan tambahkan melalui menu <strong>Kelompok Kelas</strong> terlebih dahulu.
        </div>
    <?php endif; ?>

    <?php $__currentLoopData = $kelompokKelas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $kelompok): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php
            $existingKelasIds = ($isEdit && $santri->kelasSantri)
                ? $santri->kelasSantri->filter(fn($sk) => $sk->kelas && $sk->kelas->id_kelompok === $kelompok->id_kelompok)->pluck('id_kelas')->toArray()
                : [];
            $selectedIds = (array) old('kelas_ids.' . $kelompok->id_kelompok, $existingKelasIds);
        ?>
        <div class="kelompok-box <?php echo e($index % 2 === 0 ? 'kelompok-even' : 'kelompok-odd'); ?>">
            <label class="kelompok-label">
                <i class="fas fa-bookmark"></i>
                <?php echo e($kelompok->nama_kelompok); ?>

                <?php if($kelompok->deskripsi): ?>
                    <small><?php echo e($kelompok->deskripsi); ?></small>
                <?php endif; ?>
            </label>

            <?php if($kelompok->kelas->isNotEmpty()): ?>
                <div class="kelas-grid">
                    <?php $__currentLoopData = $kelompok->kelas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $kelas): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <label class="kelas-item <?php echo e(in_array($kelas->id, $selectedIds) ? 'checked' : ''); ?>">
                            <input type="checkbox"
                                   name="kelas_ids[<?php echo e($kelompok->id_kelompok); ?>][]"
                                   value="<?php echo e($kelas->id); ?>"
                                   <?php echo e(in_array($kelas->id, $selectedIds) ? 'checked' : ''); ?>

                                   onchange="this.parentElement.classList.toggle('checked', this.checked)">
                            <span><?php echo e($kelas->nama_kelas); ?></span>
                        </label>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            <?php else: ?>
                <p class="text-muted" style="font-style: italic; margin: 0;">Belum ada kelas di kelompok ini.</p>
            <?php endif; ?>
        </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

    <div class="form-group">
        <label for="status">Status *</label>
        <select id="status" name="status" class="form-control <?php $__errorArgs = ['status'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
            <option value="">Pilih Status</option>
            <option value="Aktif" <?php echo e(old('status', $isEdit ? $santri->status : 'Aktif') == 'Aktif' ? 'selected' : ''); ?>>Aktif</option>
            <option value="Lulus" <?php echo e(old('status', $isEdit ? $santri->status : '') == 'Lulus' ? 'selected' : ''); ?>>Lulus</option>
            <option value="Tidak Aktif" <?php echo e(old('status', $isEdit ? $santri->status : '') == 'Tidak Aktif' ? 'selected' : ''); ?>>Tidak Aktif</option>
        </select>
        <?php $__errorArgs = ['status'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>

    <div class="form-group">
        <label for="alamat_santri">Alamat Santri</label>
        <textarea id="alamat_santri" name="alamat_santri" class="form-control <?php $__errorArgs = ['alamat_santri'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" rows="3" placeholder="Masukkan alamat lengkap"><?php echo e(old('alamat_santri', $isEdit ? $santri->alamat_santri : '')); ?></textarea>
        <?php $__errorArgs = ['alamat_santri'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>

    <div class="form-group">
        <label for="daerah_asal">Daerah Asal</label>
        <input type="text" id="daerah_asal" name="daerah_asal" value="<?php echo e(old('daerah_asal', $isEdit ? $santri->daerah_asal : '')); ?>" class="form-control <?php $__errorArgs = ['daerah_asal'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" placeholder="Masukkan Daerah Asal">
        <?php $__errorArgs = ['daerah_asal'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>

    <hr>
    <h4><i class="fas fa-users"></i> Data Orang Tua / Wali</h4>

    <div class="form-group">
        <label for="nama_orang_tua">Nama Orang Tua</label>
        <input type="text" id="nama_orang_tua" name="nama_orang_tua" value="<?php echo e(old('nama_orang_tua', $isEdit ? $santri->nama_orang_tua : '')); ?>" class="form-control <?php $__errorArgs = ['nama_orang_tua'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" placeholder="Masukkan nama orang tua">
        <?php $__errorArgs = ['nama_orang_tua'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>

    <div class="form-group">
        <label for="nomor_hp_ortu">Nomor HP Orang Tua</label>
        <input type="text" id="nomor_hp_ortu" name="nomor_hp_ortu" value="<?php echo e(old('nomor_hp_ortu', $isEdit ? $santri->nomor_hp_ortu : '')); ?>" class="form-control <?php $__errorArgs = ['nomor_hp_ortu'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" placeholder="Contoh: +6285182261234">
        <?php $__errorArgs = ['nomor_hp_ortu'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>

    <div style="margin-top: 22px; display: flex; gap: 10px;">
        <button type="submit" class="btn btn-success">
            <i class="fas fa-save"></i> <?php echo e($isEdit ? 'Update Data' : 'Simpan Santri'); ?>

        </button>
        <a href="<?php echo e(route('admin.santri.index')); ?>" class="btn btn-secondary">
            <i class="fas fa-times"></i> Batal
        </a>
    </div>
</form>


<script>
function previewImage(event) {
    const preview = document.getElementById('preview');
    const file = event.target.files[0];
    
    if (file) {
        // Validasi ukuran file (2 MB = 2097152 bytes)
        if (file.size > 2097152) {
            alert('Ukuran file terlalu besar! Maksimal 2 MB.');
            event.target.value = '';
            preview.style.display = 'none';
            return;
        }
        
        // Validasi tipe file
        const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png'];
        if (!allowedTypes.includes(file.type)) {
            alert('Format file tidak valid! Hanya JPG, JPEG, dan PNG yang diperbolehkan.');
            event.target.value = '';
            preview.style.display = 'none';
            return;
        }
        
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
            preview.style.display = 'block';
        };
        reader.readAsDataURL(file);
    } else {
        preview.style.display = 'none';
    }
}
</script>

<style>
.kelas-hint { margin-bottom: 15px; display: block; }
.kelas-error { padding: 8px 12px; font-size: 0.9rem; }
.kelompok-box { padding: 15px; border-radius: 0 8px 8px 0; margin-bottom: 15px; border-left: 4px solid #6FBA9D; }
.kelompok-even { background: #FAFFFE; border-left-color: #6FBA9D; }
.kelompok-odd  { background: #F8FBFD; border-left-color: #81C6E8; }
.kelompok-label { font-weight: 600; margin-bottom: 10px; display: block; }
.kelompok-label small { font-weight: normal; color: #7F8C8D; display: block; margin-top: 3px; }
.kelas-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)); gap: 8px; }
.kelas-item { display: flex; align-items: center; padding: 8px 12px; background: #fff; border: 2px solid #E8ECF0; border-radius: 6px; cursor: pointer; transition: border-color .2s; margin: 0; }
.kelas-item.checked { border-color: #6FBA9D; }
.kelas-item input[type="checkbox"] { margin-right: 8px; width: 18px; height: 18px; cursor: pointer; }
.kelas-item span { font-size: 0.9rem; flex: 1; }
</style><?php /**PATH C:\xampp\htdocs\TugasAkhir\sim-pkpps\resources\views/admin/santri/form.blade.php ENDPATH**/ ?>