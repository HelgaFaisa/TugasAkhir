<?php $__env->startSection('content'); ?>
<div class="page-header">
    <h2><i class="fas fa-plus-circle"></i> Tambah Kegiatan Baru</h2>
</div>

<div class="form-container">
    <form action="<?php echo e(route('admin.kegiatan.store')); ?>" method="POST">
        <?php echo csrf_field(); ?>

        <div class="form-group">
            <label><i class="fas fa-hashtag form-icon"></i> ID Kegiatan (Otomatis)</label>
            <input type="text" class="form-control" value="<?php echo e($nextId); ?>" disabled>
            <small class="form-text">ID akan dibuat otomatis saat disimpan</small>
        </div>

        <div class="form-group">
            <label for="kategori_id">
                <i class="fas fa-list-alt form-icon"></i>
                Kategori Kegiatan <span style="color: red;">*</span>
            </label>
            <select name="kategori_id" id="kategori_id" class="form-control <?php $__errorArgs = ['kategori_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
                <option value="">-- Pilih Kategori --</option>
                <?php $__currentLoopData = $kategoris; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $kat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($kat->kategori_id); ?>" <?php echo e(old('kategori_id') == $kat->kategori_id ? 'selected' : ''); ?>>
                        <?php echo e($kat->nama_kategori); ?>

                    </option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
            <?php $__errorArgs = ['kategori_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="invalid-feedback"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>

        <div class="form-group">
            <label for="nama_kegiatan">
                <i class="fas fa-calendar-check form-icon"></i>
                Nama Kegiatan <span style="color: red;">*</span>
            </label>
            <input type="text" name="nama_kegiatan" id="nama_kegiatan"
                   class="form-control <?php $__errorArgs = ['nama_kegiatan'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                   value="<?php echo e(old('nama_kegiatan')); ?>" placeholder="Nama Kegiatan" required>
            <?php $__errorArgs = ['nama_kegiatan'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="invalid-feedback"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>

        
        <div class="form-group">
            <label>
                <i class="fas fa-calendar-day form-icon"></i>
                Hari Kegiatan <span style="color: red;">*</span>
            </label>
            <small class="text-muted d-block mb-2" style="margin-top: -6px;">
                <i class="fas fa-info-circle"></i>
                Pilih satu atau lebih hari. Kegiatan dibuat otomatis untuk setiap hari yang dipilih.
            </small>

            <div class="hari-pills-wrap">
                <?php $__currentLoopData = $hariList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $h): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php $checked = in_array($h, old('hari', [])); ?>
                    <label class="hari-pill <?php echo e($checked ? 'active' : ''); ?>" for="hari<?php echo e($loop->index); ?>">
                        <input type="checkbox" name="hari[]" value="<?php echo e($h); ?>"
                               id="hari<?php echo e($loop->index); ?>" <?php echo e($checked ? 'checked' : ''); ?>>
                        <?php echo e($h); ?>

                    </label>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
            <?php $__errorArgs = ['hari'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="invalid-feedback d-block"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 11px;">
            <div class="form-group">
                <label for="waktu_mulai">
                    <i class="fas fa-clock form-icon"></i> Waktu Mulai <span style="color: red;">*</span>
                </label>
                <input type="time" name="waktu_mulai" id="waktu_mulai"
                       class="form-control <?php $__errorArgs = ['waktu_mulai'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                       value="<?php echo e(old('waktu_mulai')); ?>" required>
                <?php $__errorArgs = ['waktu_mulai'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="invalid-feedback"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
            <div class="form-group">
                <label for="waktu_selesai">
                    <i class="fas fa-clock form-icon"></i> Waktu Selesai <span style="color: red;">*</span>
                </label>
                <input type="time" name="waktu_selesai" id="waktu_selesai"
                       class="form-control <?php $__errorArgs = ['waktu_selesai'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                       value="<?php echo e(old('waktu_selesai')); ?>" required>
                <?php $__errorArgs = ['waktu_selesai'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="invalid-feedback"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
        </div>

        <div class="form-group">
            <label for="materi">
                <i class="fas fa-book form-icon"></i> Materi/Topik
            </label>
            <input type="text" name="materi" id="materi"
                   class="form-control <?php $__errorArgs = ['materi'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                   value="<?php echo e(old('materi')); ?>" placeholder="Contoh:Bacaan">
            <?php $__errorArgs = ['materi'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="invalid-feedback"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>

        <div class="form-group">
            <label>
                <i class="fas fa-layer-group form-icon"></i> Kelas yang Mengikuti Kegiatan
            </label>
            <small class="text-muted d-block mb-3" style="margin-top: -8px;">
                <i class="fas fa-info-circle"></i>
                Kosongkan jika kegiatan untuk semua santri (umum).
            </small>
            <?php $__currentLoopData = $kelompokKelas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $kelompok): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="card mb-2" style="border: 1px solid #E8F7F2;">
                    <div class="card-header py-2" style="background: linear-gradient(135deg, #E8F7F2 0%, #D4F1E3 100%);">
                        <strong style="color: var(--primary-dark);">
                            <i class="fas fa-folder-open"></i> <?php echo e($kelompok->nama_kelompok); ?>

                        </strong>
                    </div>
                    <div class="card-body py-2">
                        <div class="row">
                            <?php $__empty_1 = true; $__currentLoopData = $kelompok->kelas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $kelas): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <div class="col-md-3 col-sm-4 col-6 mb-2">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox"
                                               name="kelas_ids[]" value="<?php echo e($kelas->id); ?>"
                                               id="kelas<?php echo e($kelas->id); ?>"
                                               <?php echo e(in_array($kelas->id, old('kelas_ids', [])) ? 'checked' : ''); ?>>
                                        <label class="form-check-label" for="kelas<?php echo e($kelas->id); ?>">
                                            <?php echo e($kelas->nama_kelas); ?>

                                        </label>
                                    </div>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <div class="col-12"><small class="text-muted">Tidak ada kelas aktif</small></div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>

        <div class="form-group">
            <label for="keterangan">
                <i class="fas fa-align-left form-icon"></i> Keterangan
            </label>
            <textarea name="keterangan" id="keterangan"
                      class="form-control <?php $__errorArgs = ['keterangan'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                      rows="4" placeholder="Catatan tambahan..."><?php echo e(old('keterangan')); ?></textarea>
            <?php $__errorArgs = ['keterangan'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="invalid-feedback"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>

        <div class="btn-group">
            <button type="submit" class="btn btn-success">
                <i class="fas fa-save"></i> Simpan
            </button>
            <a href="<?php echo e(route('admin.kegiatan.jadwal')); ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </form>
</div>

<style>
.hari-pills-wrap {
    display: flex; flex-wrap: wrap; gap: 8px;
    padding: 12px 14px; border: 1px solid #E8F7F2;
    border-radius: 10px; background: #FAFFFE;
}
.hari-pill {
    display: inline-flex; align-items: center;
    padding: 6px 16px; border-radius: 20px; cursor: pointer;
    border: 1.5px solid #e2e8f0; background: #fff;
    font-size: 0.85rem; font-weight: 500; color: #374151;
    transition: all 0.18s; user-select: none; margin: 0;
}
.hari-pill input[type="checkbox"] { display: none; }
.hari-pill:hover { border-color: var(--primary-color); background: #f0fdf4; }
.hari-pill.active {
    border-color: var(--primary-color); background: #ECFDF5;
    color: var(--primary-dark); font-weight: 600;
}
</style>
<script>
document.querySelectorAll('.hari-pill input[type="checkbox"]').forEach(function(cb) {
    cb.addEventListener('change', function() {
        var label = this.closest('.hari-pill');
        label.classList.toggle('active', this.checked);
    });
});
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\TugasAkhir\sim-pkpps\resources\views/admin/kegiatan/data/create.blade.php ENDPATH**/ ?>