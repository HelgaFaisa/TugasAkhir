

<?php $__env->startSection('title', 'Register Admin'); ?>

<?php $__env->startSection('auth-content'); ?>
<div class="auth-header">
    <div class="logo-circle">
        <i class="fas fa-lock-open fa-2x"></i>
    </div>
    <h2>Pendaftaran Akun Admin</h2>
    <p>Mohon gunakan email dan password yang kuat untuk keamanan sistem.</p>
</div>


<?php if($errors->any()): ?>
    <div class="alert alert-danger">
        <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <p><?php echo e($error); ?></p>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
<?php endif; ?>

<form method="POST" action="<?php echo e(route('admin.register')); ?>" class="data-form">
    <?php echo csrf_field(); ?>

    <div class="form-group">
        <label for="email"><i class="fas fa-envelope form-icon"></i> Email Admin</label>
        <input type="email" id="email" name="email" value="<?php echo e(old('email')); ?>" class="form-control <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required autofocus>
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
        <label for="password"><i class="fas fa-key form-icon"></i> Password</label>
        <input type="password" id="password" name="password" class="form-control <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
        <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>

    <div class="form-group">
        <label for="password_confirmation"><i class="fas fa-lock form-icon"></i> Konfirmasi Password</label>
        <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" required>
    </div>

    <div class="form-group action-group">
        <button type="submit" class="btn btn-success btn-full hover-shadow">
            <i class="fas fa-paper-plane"></i> Daftarkan Admin
        </button>
    </div>

    <p style="text-align: center; font-size: 0.9rem; margin-top: 20px;">
        Sudah punya akun? <a href="<?php echo e(route('admin.login')); ?>" class="link-primary">Login di sini</a>
    </p>
</form>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('auth.auth_layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\TugasAkhir\sim-pkpps\resources\views/admin/auth/register.blade.php ENDPATH**/ ?>