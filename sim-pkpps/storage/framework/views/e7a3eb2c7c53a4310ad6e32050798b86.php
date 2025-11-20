<!-- resources/views/santri/auth/login.blade.php -->


<?php $__env->startSection('title', 'Login Santri'); ?>

<?php $__env->startSection('auth-content'); ?>
<div class="auth-header">
    <h2><i class="fas fa-user-graduate"></i> Login Santri/Wali</h2>
    <p>Akses progres dan laporan santri.</p>
</div>

<?php if($errors->any()): ?>
    <div class="alert alert-danger">
        <?php echo e($errors->first()); ?>

    </div>
<?php endif; ?>

<form method="POST" action="<?php echo e(route('santri.login')); ?>" class="data-form">
    <?php echo csrf_field(); ?>

    <div class="form-group">
        <label for="username">Username (ID Santri/Wali)</label>
        <input type="text" id="username" name="username" value="<?php echo e(old('username')); ?>" class="form-control" required autofocus>
    </div>

    <div class="form-group">
        <label for="password">Password</label>
        <input type="password" id="password" name="password" class="form-control" required>
    </div>
    
    <div class="form-group" style="display:flex; align-items: center;">
        <input type="checkbox" name="remember" id="remember" style="width: auto; margin-right: 10px;">
        <label for="remember" style="font-weight: normal; margin-bottom: 0;">Ingat Saya</label>
    </div>

    <div class="form-group">
        <button type="submit" class="btn btn-primary btn-full">
            <i class="fas fa-sign-in-alt"></i> Masuk
        </button>
    </div>

    <p style="text-align: center; font-size: 0.9rem; margin-top: 15px;">
        Lupa akun? Hubungi Admin.
    </p>
</form>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('auth.auth_layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\TugasAkhir\sim-pkpps\resources\views/santri/auth/login.blade.php ENDPATH**/ ?>