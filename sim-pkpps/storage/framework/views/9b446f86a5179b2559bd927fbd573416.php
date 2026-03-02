<?php $__env->startSection('title', 'Manajemen Akun Santri'); ?>

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <h2><i class="fas fa-user-cog"></i> Manajemen Akun Santri (Web)</h2>
</div>

<?php if(session('success')): ?>
    <div class="alert alert-success"><?php echo session('success'); ?></div>
<?php endif; ?>
<?php if(session('error')): ?>
    <div class="alert alert-danger"><?php echo e(session('error')); ?></div>
<?php endif; ?>
<?php if(session('info')): ?>
    <div class="alert alert-info"><?php echo e(session('info')); ?></div>
<?php endif; ?>

<div class="content-box">
    <div class="alert alert-info">
        <i class="fas fa-info-circle"></i> <strong>Info Login Santri (Web) :  </strong>
          Username = Nama Lengkap Santri &nbsp;|&nbsp; Password = NIS Santri
    </div>

    
    <h3>Daftar Akun Santri (<?php echo e($users->count()); ?>)</h3>
    <table class="data-table">
        <thead>
            <tr>
                <th>ID Santri</th>
                <th>Nama Santri</th>
                <th>NIS</th>
                <th>Username</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php $__empty_1 = true; $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <tr>
                <td><?php echo e($user->id_santri); ?></td>
                <td><?php echo e($user->santri->nama_lengkap ?? '-'); ?></td>
                <td><?php echo e($user->santri->nis ?? '-'); ?></td>
                <td><code><?php echo e($user->username); ?></code></td>
                <td>
                    <form action="<?php echo e(route('admin.users.santri_destroy', $user->id)); ?>"
                          method="POST" style="display:inline;"
                          onsubmit="return confirm('Yakin hapus akun santri <?php echo e($user->santri->nama_lengkap ?? ''); ?>?')">
                        <?php echo csrf_field(); ?>
                        <button type="submit" class="btn btn-sm btn-danger">
                            <i class="fas fa-trash"></i> Hapus
                        </button>
                    </form>
                </td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <tr>
                <td colspan="5" class="text-center">Belum ada akun santri.</td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>

    
    <h3 style="margin-top:22px;">
        Santri Belum Punya Akun (<?php echo e($santris_tanpa_akun->count()); ?>)
    </h3>

    <?php if($santris_tanpa_akun->count() > 0): ?>
    <div style="margin-bottom:12px;">
        <form action="<?php echo e(route('admin.users.santri_buat_semua')); ?>" method="POST" style="display:inline;"
              onsubmit="return confirm('Buat akun untuk SEMUA <?php echo e($santris_tanpa_akun->count()); ?> santri sekaligus?')">
            <?php echo csrf_field(); ?>
            <button type="submit" class="btn btn-success">
                <i class="fas fa-users"></i> Buat Semua Sekaligus (<?php echo e($santris_tanpa_akun->count()); ?>)
            </button>
        </form>
    </div>
    <?php endif; ?>

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
            <?php $__empty_1 = true; $__currentLoopData = $santris_tanpa_akun; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $santri): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
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
                <td><?php echo e($santri->kelas ?? '-'); ?></td>
                <td>
                    <?php if($santri->nis): ?>
                        <form action="<?php echo e(route('admin.users.santri_buat_akun', $santri->id_santri)); ?>"
                              method="POST" style="display:inline;"
                              onsubmit="return confirm('Buat akun untuk <?php echo e($santri->nama_lengkap); ?>?')">
                            <?php echo csrf_field(); ?>
                            <button type="submit" class="btn btn-sm btn-primary">
                                <i class="fas fa-user-plus"></i> Buat Akun
                            </button>
                        </form>
                    <?php else: ?>
                        <span class="text-muted">Isi NIS dulu</span>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <tr>
                <td colspan="5" class="text-center text-success">
                    <i class="fas fa-check"></i> Semua santri sudah punya akun.
                </td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', ['isAdmin' => true], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\TugasAkhir\sim-pkpps\resources\views/admin/users/santri_accounts.blade.php ENDPATH**/ ?>