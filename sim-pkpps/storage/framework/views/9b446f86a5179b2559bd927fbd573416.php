

<?php $__env->startSection('title', 'Manajemen Akun Santri'); ?>

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <h2><i class="fas fa-user-cog"></i> Manajemen Akun Santri</h2>
</div>

<?php if(session('success')): ?>
    <div class="alert alert-success"><?php echo e(session('success')); ?></div>
<?php endif; ?>

<div class="content-box">
    <div class="content-header-flex">
        <a href="<?php echo e(route('admin.users.santri_create')); ?>" class="btn btn-primary"><i class="fas fa-plus"></i> Buat Akun Santri</a>
    </div>

    <h3>Daftar Akun Santri (<?php echo e($users->count()); ?>)</h3>
    <table class="data-table">
        <thead>
            <tr>
                <th>ID Santri</th>
                <th>Nama</th>
                <th>Username</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php $__empty_1 = true; $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <tr>
                <td><?php echo e($user->role_id); ?></td>
                <td><?php echo e($user->name); ?></td>
                <td><?php echo e($user->username); ?></td>
                <td>
                    <form action="<?php echo e(route('admin.users.santri_reset_password', $user->id)); ?>" method="POST" style="display:inline;">
                        <?php echo csrf_field(); ?>
                        <button type="submit" class="btn btn-sm btn-warning" onclick="return confirm('Reset password akun <?php echo e($user->name); ?> ke NIS?')">
                            <i class="fas fa-key"></i> Reset Password
                        </button>
                    </form>
                    <form action="<?php echo e(route('admin.users.santri_destroy', $user->id)); ?>" method="POST" style="display:inline;">
                        <?php echo csrf_field(); ?>
                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Yakin hapus akun santri <?php echo e($user->name); ?>?')">
                            <i class="fas fa-trash"></i> Hapus Akun
                        </button>
                    </form>
                </td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <tr>
                <td colspan="4" class="text-center">Belum ada akun Santri yang terdaftar.</td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <h3 style="margin-top: 30px;">Data Santri Tanpa Akun (<?php echo e($santris_tanpa_akun->count()); ?>)</h3>
    <p>Berikut adalah data santri yang sudah terdaftar di Data Santri namun belum memiliki akun login. Mereka dapat dipilih saat Anda membuat akun baru.</p>
    
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', ['isAdmin' => true], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\TugasAkhir\sim-pkpps\resources\views/admin/users/santri_accounts.blade.php ENDPATH**/ ?>