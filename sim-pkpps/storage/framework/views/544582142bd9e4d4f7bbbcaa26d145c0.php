


<?php $__env->startSection('title', 'Edit Pembayaran SPP'); ?>

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <h2><i class="fas fa-edit"></i> Edit Pembayaran SPP</h2>
</div>

<?php if(session('error')): ?>
    <div class="alert alert-danger">
        <i class="fas fa-exclamation-circle"></i> <?php echo e(session('error')); ?>

    </div>
<?php endif; ?>

<div class="content-box">
    <form action="<?php echo e(route('admin.pembayaran-spp.update', $pembayaranSpp->id)); ?>" method="POST">
        <?php echo csrf_field(); ?>
        <?php echo method_field('PUT'); ?>

        <!-- ID Pembayaran (Read Only) -->
        <div class="form-group">
            <label><i class="fas fa-hashtag form-icon"></i> ID Pembayaran</label>
            <input type="text" class="form-control" value="<?php echo e($pembayaranSpp->id_pembayaran); ?>" disabled>
        </div>

        <!-- Pilih Santri -->
        <div class="form-group">
            <label><i class="fas fa-user form-icon"></i> Pilih Santri <span style="color: red;">*</span></label>
            <select name="id_santri" class="form-control <?php $__errorArgs = ['id_santri'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
                <option value="">-- Pilih Santri --</option>
                <?php $__currentLoopData = $santris; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $santri): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($santri->id_santri); ?>" 
                            <?php echo e(old('id_santri', $pembayaranSpp->id_santri) == $santri->id_santri ? 'selected' : ''); ?>>
                        <?php echo e($santri->id_santri); ?> - <?php echo e($santri->nama_lengkap); ?> (<?php echo e($santri->kelas); ?>)
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
            <!-- Bulan -->
            <div class="form-group">
                <label><i class="fas fa-calendar form-icon"></i> Bulan <span style="color: red;">*</span></label>
                <select name="bulan" class="form-control <?php $__errorArgs = ['bulan'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
                    <option value="">-- Pilih Bulan --</option>
                    <?php for($i = 1; $i <= 12; $i++): ?>
                        <option value="<?php echo e($i); ?>" 
                                <?php echo e(old('bulan', $pembayaranSpp->bulan) == $i ? 'selected' : ''); ?>>
                            <?php echo e(DateTime::createFromFormat('!m', $i)->format('F')); ?>

                        </option>
                    <?php endfor; ?>
                </select>
                <?php $__errorArgs = ['bulan'];
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

            <!-- Tahun -->
            <div class="form-group">
                <label><i class="fas fa-calendar-alt form-icon"></i> Tahun <span style="color: red;">*</span></label>
                <input type="number" 
                       name="tahun" 
                       class="form-control <?php $__errorArgs = ['tahun'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                       value="<?php echo e(old('tahun', $pembayaranSpp->tahun)); ?>" 
                       min="2020" 
                       max="2100"
                       required>
                <?php $__errorArgs = ['tahun'];
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

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <!-- Nominal -->
            <div class="form-group">
                <label><i class="fas fa-money-bill-wave form-icon"></i> Nominal (Rp) <span style="color: red;">*</span></label>
                <input type="number" 
                       name="nominal" 
                       class="form-control <?php $__errorArgs = ['nominal'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                       value="<?php echo e(old('nominal', $pembayaranSpp->nominal)); ?>" 
                       min="0" 
                       step="1000"
                       required>
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
            </div>

            <!-- Batas Bayar -->
            <div class="form-group">
                <label><i class="fas fa-clock form-icon"></i> Batas Bayar <span style="color: red;">*</span></label>
                <input type="date" 
                       name="batas_bayar" 
                       class="form-control <?php $__errorArgs = ['batas_bayar'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                       value="<?php echo e(old('batas_bayar', $pembayaranSpp->batas_bayar->format('Y-m-d'))); ?>"
                       required>
                <?php $__errorArgs = ['batas_bayar'];
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

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <!-- Status -->
            <div class="form-group">
                <label><i class="fas fa-info-circle form-icon"></i> Status <span style="color: red;">*</span></label>
                <select name="status" class="form-control <?php $__errorArgs = ['status'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
                    <option value="Belum Lunas" <?php echo e(old('status', $pembayaranSpp->status) == 'Belum Lunas' ? 'selected' : ''); ?>>
                        Belum Lunas
                    </option>
                    <option value="Lunas" <?php echo e(old('status', $pembayaranSpp->status) == 'Lunas' ? 'selected' : ''); ?>>
                        Lunas
                    </option>
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

            <!-- Tanggal Bayar -->
            <div class="form-group">
                <label><i class="fas fa-calendar-check form-icon"></i> Tanggal Bayar</label>
                <input type="date" 
                       name="tanggal_bayar" 
                       class="form-control <?php $__errorArgs = ['tanggal_bayar'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                       value="<?php echo e(old('tanggal_bayar', $pembayaranSpp->tanggal_bayar ? $pembayaranSpp->tanggal_bayar->format('Y-m-d') : '')); ?>">
                <?php $__errorArgs = ['tanggal_bayar'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <div class="invalid-feedback"><?php echo e($message); ?></div>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                <small class="form-text">Kosongkan jika belum dibayar.</small>
            </div>
        </div>

        <!-- Keterangan -->
        <div class="form-group">
            <label><i class="fas fa-comment form-icon"></i> Keterangan</label>
            <textarea name="keterangan" 
                      class="form-control <?php $__errorArgs = ['keterangan'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                      rows="3" 
                      placeholder="Catatan tambahan (opsional)"><?php echo e(old('keterangan', $pembayaranSpp->keterangan)); ?></textarea>
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
        </div>

        <!-- Buttons -->
        <div style="display: flex; gap: 10px; margin-top: 25px;">
            <button type="submit" class="btn btn-success hover-shadow">
                <i class="fas fa-save"></i> Update Data
            </button>
            <a href="<?php echo e(route('admin.pembayaran-spp.index')); ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </form>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
// Auto-fill tanggal bayar jika status diubah ke Lunas
document.querySelector('select[name="status"]').addEventListener('change', function() {
    const tanggalBayar = document.querySelector('input[name="tanggal_bayar"]');
    if (this.value === 'Lunas' && !tanggalBayar.value) {
        const today = new Date().toISOString().split('T')[0];
        tanggalBayar.value = today;
    }
});
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\TugasAkhir\sim-pkpps\resources\views/admin/pembayaran-spp/edit.blade.php ENDPATH**/ ?>