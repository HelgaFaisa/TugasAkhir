

<?php $__env->startSection('title', 'Manajemen Akun Admin'); ?>

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <h2><i class="fas fa-user-shield"></i> Manajemen Akun Admin</h2>
</div>

<?php if(session('success')): ?>
    <div class="alert alert-success"><?php echo e(session('success')); ?></div>
<?php endif; ?>
<?php if(session('error')): ?>
    <div class="alert alert-danger"><?php echo e(session('error')); ?></div>
<?php endif; ?>

<div class="content-box">
    <div class="content-header-flex">
        <a href="<?php echo e(route('admin.users.admin_create')); ?>" class="btn btn-primary">
            <i class="fas fa-plus"></i> Tambah Akun Admin
        </a>
    </div>

    <p class="text-muted" style="margin-top: 8px;">
        Kelola akun untuk role <strong>Akademik</strong> dan <strong>Pamong</strong>.
        Akun <strong>Super Admin</strong> tidak dapat dihapus dari halaman ini.
    </p>

    <div class="table-wrapper">

    <table class="data-table">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama</th>
                <th>Email</th>
                <th>Role</th>
                <th>Dibuat</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php $__empty_1 = true; $__currentLoopData = $admins; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $admin): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <tr>
                <td><?php echo e($i + 1); ?></td>
                <td><?php echo e($admin->name); ?></td>
                <td><?php echo e($admin->email); ?></td>
                <td>
                    <?php if($admin->role === 'super_admin'): ?>
                        <span class="badge" style="background:#6f42c1;color:#fff;padding:3px 8px;border-radius:4px;">Super Admin</span>
                    <?php elseif($admin->role === 'akademik'): ?>
                        <span class="badge" style="background:#0d6efd;color:#fff;padding:3px 8px;border-radius:4px;">Akademik</span>
                    <?php else: ?>
                        <span class="badge" style="background:#198754;color:#fff;padding:3px 8px;border-radius:4px;">Pamong</span>
                    <?php endif; ?>
                </td>
                <td><?php echo e($admin->created_at->format('d/m/Y')); ?></td>
                <td>
                    <?php if($admin->role !== 'super_admin'): ?>
                        <a href="<?php echo e(route('admin.users.admin_edit', $admin->id)); ?>" class="btn btn-sm btn-warning">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                        <form action="<?php echo e(route('admin.users.admin_destroy', $admin->id)); ?>" method="POST" style="display:inline;"
                              onsubmit="return confirm('Yakin hapus akun <?php echo e($admin->name); ?>?')">
                            <?php echo csrf_field(); ?>
                            <?php echo method_field('DELETE'); ?>
                            <button type="submit" class="btn btn-sm btn-danger">
                                <i class="fas fa-trash"></i> Hapus
                            </button>
                        </form>
                    <?php else: ?>
                        <span class="text-muted"><i class="fas fa-lock"></i> Protected</span>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <tr>
                <td colspan="6" class="text-center">Belum ada akun admin terdaftar.</td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>

    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', ['isAdmin' => true], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\TugasAkhir\sim-pkpps\resources\views/admin/users/admin_accounts.blade.php ENDPATH**/ ?>