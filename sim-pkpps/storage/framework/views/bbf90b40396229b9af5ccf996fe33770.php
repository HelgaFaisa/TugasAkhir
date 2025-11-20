

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <h2><i class="fas fa-plus-circle"></i> Tambah Transaksi Uang Saku</h2>
</div>

<div class="form-container">
    <form action="<?php echo e(route('admin.uang-saku.store')); ?>" method="POST" id="transaksiForm">
        <?php echo csrf_field(); ?>

        <div class="form-group">
            <label for="id_santri">
                <i class="fas fa-user form-icon"></i>
                Pilih Santri <span style="color: red;">*</span>
            </label>
            <select name="id_santri" id="id_santri" class="form-control <?php $__errorArgs = ['id_santri'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
                <option value="">-- Pilih Santri --</option>
                <?php $__currentLoopData = $santriList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $santri): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($santri->id_santri); ?>" 
                        <?php echo e((old('id_santri', request('id_santri')) == $santri->id_santri) ? 'selected' : ''); ?>>
                        <?php echo e($santri->id_santri); ?> - <?php echo e($santri->nama_lengkap); ?>

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

        <div class="form-group">
            <label for="jenis_transaksi">
                <i class="fas fa-exchange-alt form-icon"></i>
                Jenis Transaksi <span style="color: red;">*</span>
            </label>
            <select name="jenis_transaksi" id="jenis_transaksi" class="form-control <?php $__errorArgs = ['jenis_transaksi'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
                <option value="">-- Pilih Jenis --</option>
                <option value="pemasukan" <?php echo e(old('jenis_transaksi') == 'pemasukan' ? 'selected' : ''); ?>>
                    Pemasukan (Terima Uang Saku)
                </option>
                <option value="pengeluaran" <?php echo e(old('jenis_transaksi') == 'pengeluaran' ? 'selected' : ''); ?>>
                    Pengeluaran (Gunakan Uang Saku)
                </option>
            </select>
            <?php $__errorArgs = ['jenis_transaksi'];
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

        <div class="form-group">
            <label for="nominal">
                <i class="fas fa-money-bill-wave form-icon"></i>
                Nominal (Rp) <span style="color: red;">*</span>
            </label>
            <input type="number" name="nominal" id="nominal" class="form-control <?php $__errorArgs = ['nominal'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                   value="<?php echo e(old('nominal')); ?>" placeholder="Contoh: 50000" min="1" step="1" required>
            <?php $__errorArgs = ['nominal'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                <div class="invalid-feedback"><?php echo e($message); ?></div>
            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            <small class="form-text">Masukkan nominal tanpa titik atau koma</small>
        </div>

        <div class="form-group">
            <label for="tanggal_transaksi">
                <i class="fas fa-calendar form-icon"></i>
                Tanggal Transaksi <span style="color: red;">*</span>
            </label>
            <input type="date" name="tanggal_transaksi" id="tanggal_transaksi" 
                   class="form-control <?php $__errorArgs = ['tanggal_transaksi'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                   value="<?php echo e(old('tanggal_transaksi', date('Y-m-d'))); ?>" required>
            <?php $__errorArgs = ['tanggal_transaksi'];
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

        <div class="form-group">
            <label for="keterangan">
                <i class="fas fa-sticky-note form-icon"></i>
                Keterangan
            </label>
            <textarea name="keterangan" id="keterangan" class="form-control <?php $__errorArgs = ['keterangan'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                      rows="4" placeholder="Contoh: Uang saku bulan Januari 2025"><?php echo e(old('keterangan')); ?></textarea>
            <?php $__errorArgs = ['keterangan'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                <div class="invalid-feedback"><?php echo e($message); ?></div>
            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            <small class="form-text">Opsional - Jelaskan detail transaksi</small>
        </div>

        <div class="btn-group">
            <button type="submit" class="btn btn-success hover-lift">
                <i class="fas fa-save"></i> Simpan Transaksi
            </button>
            <a href="<?php echo e(route('admin.uang-saku.index')); ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </form>
</div>

<script>
    // Format nominal input (tambah separator ribuan saat blur)
    document.getElementById('nominal').addEventListener('blur', function(e) {
        if (this.value) {
            const value = parseInt(this.value.replace(/\D/g, ''));
            if (!isNaN(value)) {
                this.value = value;
            }
        }
    });

    // Validasi form sebelum submit
    document.getElementById('transaksiForm').addEventListener('submit', function(e) {
        const nominal = document.getElementById('nominal').value;
        
        if (nominal && parseInt(nominal) < 1) {
            e.preventDefault();
            alert('Nominal harus lebih dari 0');
            return false;
        }
    });
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\TugasAkhir\sim-pkpps\resources\views/admin/uang-saku/create.blade.php ENDPATH**/ ?>