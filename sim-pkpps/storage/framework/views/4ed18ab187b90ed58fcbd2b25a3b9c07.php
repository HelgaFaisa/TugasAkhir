

<?php $__env->startSection('title', $admin ? 'Edit Akun Admin' : 'Tambah Akun Admin'); ?>

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <h2>
        <i class="fas fa-user-shield"></i>
        <?php echo e($admin ? 'Edit Akun Admin: ' . $admin->name : 'Tambah Akun Admin'); ?>

    </h2>
</div>

<div class="content-box" style="max-width: 540px;">
    <?php if($errors->any()): ?>
        <div class="alert alert-danger">
            <ul style="margin:0;padding-left:18px;">
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li><?php echo e($error); ?></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
    <?php endif; ?>

    <form action="<?php echo e($action); ?>" method="POST" class="data-form">
        <?php echo csrf_field(); ?>
        <?php if($method === 'PUT'): ?>
            <?php echo method_field('PUT'); ?>
        <?php endif; ?>

        <div class="form-group">
            <label for="name">Nama Lengkap *</label>
            <input type="text" id="name" name="name"
                   value="<?php echo e(old('name', $admin->name ?? '')); ?>"
                   class="form-control <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                   required>
            <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>

        <div class="form-group">
            <label for="email">Email (digunakan untuk login) *</label>
            <input type="email" id="email" name="email"
                   value="<?php echo e(old('email', $admin->email ?? '')); ?>"
                   class="form-control <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                   required>
            <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>

        <div class="form-group">
            <label for="role">Role *</label>
            <select id="role" name="role"
                    class="form-control <?php $__errorArgs = ['role'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                    required>
                <option value="">-- Pilih Role --</option>
                <option value="akademik" <?php echo e(old('role', $admin->role ?? '') === 'akademik' ? 'selected' : ''); ?>>
                    Akademik (Data santri, kegiatan, pelanggaran, absensi, rekap)
                </option>
                <option value="pamong" <?php echo e(old('role', $admin->role ?? '') === 'pamong' ? 'selected' : ''); ?>>
                    Pamong (Uang saku, absensi RFID, capaian, kesehatan, kepulangan)
                </option>
            </select>
            <?php $__errorArgs = ['role'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>

        <div class="form-group">
            <label for="password">
                Password <?php echo e($admin ? '(kosongkan jika tidak ingin mengganti)' : '*'); ?>

            </label>
            <input type="password" id="password" name="password"
                   class="form-control <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                   <?php echo e($admin ? '' : 'required'); ?>>
            <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            <small class="form-text text-muted">Minimal 8 karakter.</small>
        </div>

        <div class="form-group">
            <label for="password_confirmation">Konfirmasi Password <?php echo e($admin ? '' : '*'); ?></label>
            <input type="password" id="password_confirmation" name="password_confirmation"
                   class="form-control"
                   <?php echo e($admin ? '' : 'required'); ?>>
        </div>

        <div style="display:flex;gap:10px;margin-top:16px;">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> <?php echo e($admin ? 'Simpan Perubahan' : 'Buat Akun'); ?>

            </button>
            <a href="<?php echo e(route('admin.users.admin_accounts')); ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </form>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', ['isAdmin' => true], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\TugasAkhir\sim-pkpps\resources\views/admin/users/admin_form.blade.php ENDPATH**/ ?>