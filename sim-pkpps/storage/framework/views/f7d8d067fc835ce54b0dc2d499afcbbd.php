


<?php $__env->startSection('title', 'Login Admin'); ?>

<?php $__env->startSection('auth-content'); ?>
<div class="auth-header">
    <h2><i class="fas fa-lock"></i> Admin Login</h2>
    <p>Sistem Informasi Monitoring Santri</p>
</div>


<?php if($errors->any()): ?>
    <div class="alert alert-danger">
        <i class="fas fa-exclamation-circle"></i>
        <?php echo e($errors->first()); ?>

    </div>
<?php endif; ?>


<?php if(session('success')): ?>
    <div class="alert alert-success">
        <i class="fas fa-check-circle"></i>
        <?php echo e(session('success')); ?>

    </div>
<?php endif; ?>

<form method="POST" 
      action="<?php echo e(route('admin.login')); ?>" 
      id="adminLoginForm"
      class="data-form" 
      autocomplete="on">
    <?php echo csrf_field(); ?>

    
    <div class="form-group">
        <label for="username">
            <i class="fas fa-user form-icon"></i>
            Username
        </label>
        <input type="text" 
               id="username" 
               name="username" 
               value="<?php echo e(old('username')); ?>" 
               class="form-control <?php $__errorArgs = ['username'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
               autocomplete="username"
               placeholder="Masukkan username admin"
               required 
               autofocus>
        <?php $__errorArgs = ['username'];
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
        <label for="password">
            <i class="fas fa-lock form-icon"></i>
            Password
        </label>
        <div style="position: relative;">
            <input type="password" 
                   id="password" 
                   name="password" 
                   class="form-control <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                   autocomplete="current-password"
                   placeholder="Masukkan password"
                   style="padding-right: 40px;"
                   required>
            <button type="button" 
                    id="togglePassword" 
                    style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; color: #666; padding: 0; width: 30px; height: 30px; display: flex; align-items: center; justify-content: center;">
                <i class="fas fa-eye" id="eyeIcon"></i>
            </button>
        </div>
        <?php $__errorArgs = ['password'];
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
    
    
    <div class="form-group" style="display:flex; align-items: center;">
        <input type="checkbox" 
               name="remember" 
               id="remember" 
               style="width: auto; margin-right: 10px;">
        <label for="remember" style="font-weight: normal; margin-bottom: 0;">Ingat Saya</label>
    </div>

    
    <div class="form-group">
        <button type="submit" class="btn btn-primary btn-full">
            <i class="fas fa-sign-in-alt"></i> Login
        </button>
    </div>

    
    <p style="text-align: center; font-size: 0.9rem; margin-top: 15px;">
        Admin baru? <a href="<?php echo e(route('admin.register')); ?>" class="link-primary">Daftar Sekarang</a>
    </p>

    
    <div style="text-align: center; margin-top: 20px; padding-top: 20px; border-top: 2px solid var(--primary-light);">
        <p style="color: var(--text-light); margin-bottom: 10px;">
            <i class="fas fa-info-circle"></i> Login sebagai santri/wali?
        </p>
        <a href="<?php echo e(route('santri.login')); ?>" class="link-primary" style="font-size: 0.95rem;">
            <i class="fas fa-user-graduate"></i> Login Santri/Wali
        </a>
    </div>
</form>


<script>
document.addEventListener('DOMContentLoaded', function() {
    // ========================================
    // 1. Toggle Password Visibility
    // ========================================
    const togglePassword = document.getElementById('togglePassword');
    const password = document.getElementById('password');
    const eyeIcon = document.getElementById('eyeIcon');

    if (togglePassword && password && eyeIcon) {
        togglePassword.addEventListener('click', function() {
            // Toggle tipe input
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);
            
            // Toggle icon
            if (type === 'password') {
                eyeIcon.classList.remove('fa-eye-slash');
                eyeIcon.classList.add('fa-eye');
            } else {
                eyeIcon.classList.remove('fa-eye');
                eyeIcon.classList.add('fa-eye-slash');
            }
        });
    }

    // ========================================
    // 2. Auto-refresh CSRF Token (FIX 419)
    // ========================================
    const form = document.getElementById('adminLoginForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            const csrfInput = document.querySelector('input[name="_token"]');
            
            if (!csrfInput) {
                e.preventDefault();
                alert('CSRF token tidak ditemukan. Halaman akan dimuat ulang.');
                window.location.reload();
                return false;
            }
            
            const token = csrfInput.value;
            
            // Cek apakah token valid (minimal 40 karakter)
            if (!token || token.length < 40) {
                e.preventDefault();
                alert('Session expired. Halaman akan dimuat ulang.');
                window.location.reload();
                return false;
            }
        });
    }

    // ========================================
    // 3. Clear Error Message on Input
    // ========================================
    const usernameInput = document.getElementById('username');
    const passwordInput = document.getElementById('password');
    const alertBox = document.querySelector('.alert-danger');

    if (usernameInput && passwordInput && alertBox) {
        usernameInput.addEventListener('input', function() {
            alertBox.style.display = 'none';
        });
        
        passwordInput.addEventListener('input', function() {
            alertBox.style.display = 'none';
        });
    }

    // ========================================
    // 4. Auto-hide Success Alert (setelah 5 detik)
    // ========================================
    const successAlert = document.querySelector('.alert-success');
    if (successAlert) {
        setTimeout(function() {
            successAlert.style.transition = 'opacity 0.5s ease';
            successAlert.style.opacity = '0';
            setTimeout(function() {
                successAlert.remove();
            }, 500);
        }, 5000); // 5 detik
    }

    // ========================================
    // 5. Focus Management
    // ========================================
    // Auto-focus ke username saat halaman load
    if (usernameInput && !usernameInput.value) {
        usernameInput.focus();
    }

    // Enter di username -> pindah ke password
    if (usernameInput && passwordInput) {
        usernameInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                passwordInput.focus();
            }
        });
    }
});
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('auth.auth_layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\TugasAkhir\sim-pkpps\resources\views/admin/auth/login.blade.php ENDPATH**/ ?>