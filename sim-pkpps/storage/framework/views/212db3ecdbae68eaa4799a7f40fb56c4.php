<?php $__env->startSection('content'); ?>
<div class="page-header">
    <h2><i class="fas fa-id-card"></i> Daftarkan Kartu RFID</h2>
</div>

<div class="form-container">
    <div class="info-box" style="margin-bottom: 25px;">
        <p><i class="fas fa-info-circle"></i> Tempelkan kartu RFID ke reader, UID akan otomatis terdeteksi pada kolom di bawah.</p>
    </div>

    <form action="<?php echo e(route('admin.kartu-rfid.simpan', $santri->id_santri)); ?>" method="POST">
        <?php echo csrf_field(); ?>

        <div class="form-group">
            <label><i class="fas fa-user form-icon"></i> Data Santri</label>
            <table class="detail-table">
                <tr>
                    <th>ID Santri</th>
                    <td><strong><?php echo e($santri->id_santri); ?></strong></td>
                </tr>
                <tr>
                    <th>Nama Lengkap</th>
                    <td><?php echo e($santri->nama_lengkap); ?></td>
                </tr>
                <tr>
                    <th>Kelas</th>
                    <td><span class="badge badge-secondary"><?php echo e($santri->kelas); ?></span></td>
                </tr>
            </table>
        </div>

        <div class="form-group">
            <label for="rfid_uid">
                <i class="fas fa-barcode form-icon"></i>
                UID RFID <span style="color: red;">*</span>
            </label>
            <div style="display: flex; gap: 10px; align-items: center;">
                <input type="text" 
                       name="rfid_uid" 
                       id="rfid_uid" 
                       class="form-control <?php $__errorArgs = ['rfid_uid'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                       value="<?php echo e(old('rfid_uid')); ?>" 
                       placeholder="Tempelkan kartu ke reader..."
                       required
                       autofocus>
                <button type="button" class="btn btn-warning" onclick="document.getElementById('rfid_uid').value = ''; document.getElementById('rfid_uid').focus();">
                    <i class="fas fa-redo"></i>
                </button>
            </div>
            <?php $__errorArgs = ['rfid_uid'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                <span class="invalid-feedback"><?php echo e($message); ?></span>
            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            <small class="form-text">UID akan otomatis terisi saat kartu ditempelkan ke reader.</small>
        </div>

        <div id="scanStatus" style="padding: 15px; border-radius: var(--border-radius-sm); text-align: center; margin-bottom: 14px; display: none;"></div>

        <div class="btn-group">
            <button type="submit" class="btn btn-success">
                <i class="fas fa-save"></i> Simpan & Daftarkan
            </button>
            <a href="<?php echo e(route('admin.kartu-rfid.index')); ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </form>
</div>

<script>
const rfidInput = document.getElementById('rfid_uid');
const scanStatus = document.getElementById('scanStatus');

rfidInput.addEventListener('input', function() {
    if (this.value.length > 5) {
        scanStatus.style.display = 'block';
        scanStatus.style.background = 'linear-gradient(135deg, #E8F7F2 0%, #D4F1E3 100%)';
        scanStatus.style.color = 'var(--success-color)';
        scanStatus.innerHTML = '<i class="fas fa-check-circle"></i> RFID Terdeteksi: <strong>' + this.value + '</strong>';
    } else {
        scanStatus.style.display = 'none';
    }
});

// Auto-focus ke input
setInterval(() => {
    if (document.activeElement !== rfidInput) {
        rfidInput.focus();
    }
}, 1000);
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\TugasAkhir\sim-pkpps\resources\views/admin/kegiatan/kartu/daftar.blade.php ENDPATH**/ ?>