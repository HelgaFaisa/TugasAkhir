

<?php $__env->startSection('title', 'Manajemen Akun Wali Santri'); ?>

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <h2><i class="fas fa-mobile-alt"></i> Manajemen Akun Wali Santri (Mobile App)</h2>
</div>

<?php if(session('success')): ?>
    <div class="alert alert-success"><?php echo session('success'); ?></div>
<?php endif; ?>

<?php if(session('error')): ?>
    <div class="alert alert-danger"><?php echo e(session('error')); ?></div>
<?php endif; ?>

<div class="content-box">
    <div class="alert alert-info">
        <i class="fas fa-info-circle"></i> <strong>Info:</strong> Akun wali digunakan oleh orang tua/wali untuk login di aplikasi mobile dan melihat data santri (anaknya).<br>
        <strong>Format Login:</strong> Username = Nama Santri, Password = NIS Santri
    </div>

    <div class="content-header-flex">
        <a href="<?php echo e(route('admin.users.wali_create')); ?>" class="btn btn-primary"><i class="fas fa-plus"></i> Buat Akun Wali</a>
    </div>

    <h3>Daftar Akun Wali Santri (<?php echo e($users->count()); ?>)</h3>
    <table class="data-table">
        <thead>
            <tr>
                <th>ID Santri</th>
                <th>Nama Santri</th>
                <th>NIS</th>
                <th>Username (Login)</th>
                <th>Password</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php $__empty_1 = true; $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <tr>
                <td><?php echo e($user->role_id); ?></td>
                <td><?php echo e($user->santri->nama_lengkap ?? '-'); ?></td>
                <td><?php echo e($user->santri->nis ?? '-'); ?></td>
                <td><code><?php echo e($user->username); ?></code></td>
                <td><span class="text-muted">NIS: <?php echo e($user->santri->nis ?? '-'); ?></span></td>
                <td>
                    <form action="<?php echo e(route('admin.users.wali_reset_password', $user->id)); ?>" method="POST" style="display:inline;">
                        <?php echo csrf_field(); ?>
                        <button type="submit" class="btn btn-sm btn-warning" onclick="return confirm('Reset password akun <?php echo e($user->name); ?> ke NIS?')">
                            <i class="fas fa-key"></i> Reset
                        </button>
                    </form>
                    <form action="<?php echo e(route('admin.users.wali_destroy', $user->id)); ?>" method="POST" style="display:inline;">
                        <?php echo csrf_field(); ?>
                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Yakin hapus akun wali <?php echo e($user->name); ?>?')">
                            <i class="fas fa-trash"></i> Hapus
                        </button>
                    </form>
                </td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <tr>
                <td colspan="6" class="text-center">Belum ada akun Wali Santri yang terdaftar.</td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>
    
    <h3 style="margin-top: 30px;">Santri Belum Memiliki Akun Wali (<?php echo e($santris_tanpa_wali->count()); ?>)</h3>
    <p>Daftar santri yang belum dibuatkan akun wali untuk login di aplikasi mobile.</p>
    <table class="data-table">
        <thead>
            <tr>
                <th>ID Santri</th>
                <th>NIS</th>
                <th>Nama Santri</th>
                <th>Kelas</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php $__empty_1 = true; $__currentLoopData = $santris_tanpa_wali; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $santri): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <tr>
                <td><?php echo e($santri->id_santri); ?></td>
                <td>
                    <?php if($santri->nis): ?>
                        <?php echo e($santri->nis); ?>

                    <?php else: ?>
                        <span class="text-danger">Belum ada NIS</span>
                    <?php endif; ?>
                </td>
                <td><?php echo e($santri->nama_lengkap); ?></td>
                <td><?php echo e($santri->kelas); ?></td>
                <td>
                    <?php if($santri->nis): ?>
                        <a href="<?php echo e(route('admin.users.wali_create')); ?>" class="btn btn-sm btn-primary">
                            <i class="fas fa-user-plus"></i> Buat Akun
                        </a>
                    <?php else: ?>
                        <span class="text-muted">Isi NIS dulu</span>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <tr>
                <td colspan="5" class="text-center text-success"><i class="fas fa-check"></i> Semua santri sudah memiliki akun wali.</td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', ['isAdmin' => true], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\TugasAkhir\sim-pkpps\resources\views/admin/users/wali_accounts.blade.php ENDPATH**/ ?>