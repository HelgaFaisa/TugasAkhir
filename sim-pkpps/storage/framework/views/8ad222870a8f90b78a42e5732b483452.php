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
<?php if(session('info')): ?>
    <div class="alert alert-info"><?php echo e(session('info')); ?></div>
<?php endif; ?>

<div class="content-box">
    <div class="alert alert-info">
        <i class="fas fa-info-circle"></i> <strong>Info Login Wali (Mobile):</strong><br>
        <strong>Username:</strong> Nama Orang Tua
        <small class="text-muted">(jika ada nama orang tua yang sama, otomatis menjadi "Nama Orang Tua - Nama Santri")</small><br>
        <strong>Password:</strong> NIS Santri
    </div>

    
    <h3>Daftar Akun Wali (<?php echo e($users->count()); ?>)</h3>
    <table class="data-table">
        <thead>
            <tr>
                <th>ID Santri</th>
                <th>Nama Santri</th>
                <th>Nama Orang Tua</th>
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
                <td><?php echo e($user->santri->nama_orang_tua ?? '-'); ?></td>
                <td><?php echo e($user->santri->nis ?? '-'); ?></td>
                <td><code><?php echo e($user->username); ?></code></td>
                <td>
                    <form action="<?php echo e(route('admin.users.wali_destroy', $user->id)); ?>"
                          method="POST" style="display:inline;"
                          onsubmit="return confirm('Yakin hapus akun wali <?php echo e($user->santri->nama_lengkap ?? ''); ?>?')">
                        <?php echo csrf_field(); ?>
                        <button type="submit" class="btn btn-sm btn-danger">
                            <i class="fas fa-trash"></i> Hapus
                        </button>
                    </form>
                </td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <tr>
                <td colspan="6" class="text-center">Belum ada akun wali.</td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>

    
    <h3 style="margin-top:22px;">
        Santri Belum Punya Akun Wali (<?php echo e($santris_tanpa_wali->count()); ?>)
    </h3>

    <?php if($santris_tanpa_wali->count() > 0): ?>
    <div style="margin-bottom:12px;">
        <form action="<?php echo e(route('admin.users.wali_buat_semua')); ?>" method="POST" style="display:inline;"
              onsubmit="return confirm('Buat akun wali untuk SEMUA <?php echo e($santris_tanpa_wali->count()); ?> santri sekaligus?')">
            <?php echo csrf_field(); ?>
            <button type="submit" class="btn btn-success">
                <i class="fas fa-users"></i> Buat Semua Sekaligus (<?php echo e($santris_tanpa_wali->count()); ?>)
            </button>
        </form>
    </div>
    <?php endif; ?>

    <?php
        // Kumpulkan nama ortu yang sudah dipakai di akun existing
        // untuk preview username yang akan dibuat
        $namaOrtuSudahAda = \App\Models\SantriAccount::where('role', 'wali')
            ->pluck('username')
            ->toArray();
        $namaOrtuPreviewDipakai = [];
    ?>

    <table class="data-table">
        <thead>
            <tr>
                <th>ID Santri</th>
                <th>NIS</th>
                <th>Nama Santri</th>
                <th>Nama Orang Tua</th>
                <th>Kelas</th>
                <th>Username (Preview)</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php $__empty_1 = true; $__currentLoopData = $santris_tanpa_wali; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $santri): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <?php
                // Preview username: sama persis dgn logika resolveUsernameWali() di controller
                $previewUsername = null;
                if ($santri->nama_orang_tua) {
                    $usernameDefault = $santri->nama_orang_tua;
                    $sudahDiDb       = in_array($usernameDefault, $namaOrtuSudahAda);
                    $sudahDiMemori   = in_array($usernameDefault, $namaOrtuPreviewDipakai);

                    if ($sudahDiDb || $sudahDiMemori) {
                        $previewUsername = $usernameDefault . ' - ' . $santri->nama_lengkap;
                    } else {
                        $previewUsername = $usernameDefault;
                    }

                    $namaOrtuPreviewDipakai[] = $previewUsername;
                }
            ?>
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
                <td><?php echo e($santri->nama_orang_tua ?? '-'); ?></td>
                <td><?php echo e($santri->kelas ?? '-'); ?></td>
                <td>
                    <?php if($previewUsername): ?>
                        <code style="font-size:.78rem;"><?php echo e($previewUsername); ?></code>
                    <?php else: ?>
                        <span class="text-muted">-</span>
                    <?php endif; ?>
                </td>
                <td>
                    <?php if($santri->nis && $santri->nama_orang_tua): ?>
                        <form action="<?php echo e(route('admin.users.wali_buat_akun', $santri->id_santri)); ?>"
                              method="POST" style="display:inline;"
                              onsubmit="return confirm('Buat akun wali untuk <?php echo e($santri->nama_lengkap); ?>?')">
                            <?php echo csrf_field(); ?>
                            <button type="submit" class="btn btn-sm btn-primary">
                                <i class="fas fa-user-plus"></i> Buat Akun
                            </button>
                        </form>
                    <?php elseif(!$santri->nis): ?>
                        <span class="text-muted">Isi NIS dulu</span>
                    <?php else: ?>
                        <span class="text-muted">Isi nama orang tua dulu</span>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <tr>
                <td colspan="7" class="text-center text-success">
                    <i class="fas fa-check"></i> Semua santri sudah punya akun wali.
                </td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', ['isAdmin' => true], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\TugasAkhir\sim-pkpps\resources\views/admin/users/wali_accounts.blade.php ENDPATH**/ ?>