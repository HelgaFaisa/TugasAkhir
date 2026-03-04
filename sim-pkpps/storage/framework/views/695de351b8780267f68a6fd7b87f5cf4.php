

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <h2><i class="fas fa-chart-line"></i> Data Capaian Santri</h2>
</div>


<?php if(session('success')): ?>
    <div class="alert alert-success">
        <i class="fas fa-check-circle"></i> <?php echo e(session('success')); ?>

    </div>
<?php endif; ?>

<?php if(session('error')): ?>
    <div class="alert alert-danger">
        <i class="fas fa-exclamation-circle"></i> <?php echo e(session('error')); ?>

    </div>
<?php endif; ?>


<div class="content-box" style="margin-bottom: 14px;">
    <div style="display: flex; gap: 10px; flex-wrap: wrap; align-items: center;">
        <a href="<?php echo e(route('admin.capaian.create')); ?>" class="btn btn-success" style="padding: 9px 18px;">
            <i class="fas fa-plus"></i> Input Capaian
        </a>
        <a href="<?php echo e(route('admin.capaian.akses-santri')); ?>" class="btn btn-primary" style="padding: 9px 18px;">
            <i class="fas fa-unlock-alt"></i> Kelola Akses Input Santri
        </a>
    </div>
</div>


<div class="content-box" style="margin-bottom: 14px;">
    <form method="GET" action="<?php echo e(route('admin.capaian.index')); ?>" class="filter-form-inline">
        <div style="display: flex; gap: 10px; flex-wrap: wrap; align-items: center;">
            
            <select name="id_kelas" class="form-control" style="width: 220px;" onchange="this.form.submit()">
                <option value="">Semua Kelas</option>
                <?php
                    $kelompokGrouped = $kelasList->groupBy(fn($k) => $k->kelompok->nama_kelompok ?? 'Lainnya');
                ?>
                <?php $__currentLoopData = $kelompokGrouped; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $namaKelompok => $kelasGroup): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <optgroup label="<?php echo e($namaKelompok); ?>">
                        <?php $__currentLoopData = $kelasGroup; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $kls): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($kls->id); ?>" <?php echo e($selectedKelas == $kls->id ? 'selected' : ''); ?>>
                                <?php echo e($kls->nama_kelas); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </optgroup>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>

            
            <select name="id_semester" class="form-control" style="width: 250px;">
                <?php $__currentLoopData = $semesters; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $semester): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($semester->id_semester); ?>" <?php echo e($selectedSemester == $semester->id_semester ? 'selected' : ''); ?>>
                        <?php echo e($semester->nama_semester); ?>

                    </option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>

            
            <input type="text" name="search" class="form-control" placeholder="Cari nama santri / NIS..." 
                   value="<?php echo e($search ?? ''); ?>" style="width: 300px;">

            <button type="submit" class="btn btn-primary">
                <i class="fas fa-search"></i> Filter
            </button>

            <?php if($selectedKelas || $search): ?>
                <a href="<?php echo e(route('admin.capaian.index', ['id_semester' => $selectedSemester])); ?>" class="btn btn-secondary">
                    <i class="fas fa-redo"></i> Reset
                </a>
            <?php endif; ?>
        </div>
    </form>
</div>


<div class="content-box">
    <?php if($selectedKelas): ?>
        <?php $selectedKelasObj = $kelasList->firstWhere('id', $selectedKelas); ?>
        <div style="margin-bottom: 15px; padding: 12px 15px; background: #e3f2fd; border-left: 4px solid #2196F3; border-radius: 4px;">
            <span style="color: #1976D2; font-weight: 600;">
                <i class="fas fa-filter"></i> Menampilkan data kelas: <strong><?php echo e($selectedKelasObj->nama_kelas ?? 'Unknown'); ?></strong>
                <?php if($selectedKelasObj && $selectedKelasObj->kelompok): ?>
                    (<?php echo e($selectedKelasObj->kelompok->nama_kelompok); ?>)
                <?php endif; ?>
            </span>
        </div>
    <?php endif; ?>
    
    <?php if($santriData->count() > 0): ?>
        <div class="table-wrapper">
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 5%;">No</th>
                    <th style="width: 15%;">NIS</th>
                    <th style="width: 30%;">Nama Santri</th>
                    <th style="width: 10%;">Kelas</th>
                    <th style="width: 15%;">Total Materi</th>
                    <th style="width: 15%;">Total Progress</th>
                    <th class="text-center" style="width: 10%;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php $__currentLoopData = $santriData; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td><?php echo e($index + 1); ?></td>
                        <td><strong><?php echo e($data['santri']->nis); ?></strong></td>
                        <td><?php echo e($data['santri']->nama_lengkap); ?></td>
                        <td>
                            <span class="badge badge-secondary"><?php echo e($data['santri']->kelas); ?></span>
                        </td>
                        <td class="text-center">
                            <span class="badge badge-info"><?php echo e($data['total_materi']); ?> materi</span>
                        </td>
                        <td>
                            <?php
                                $progress = $data['total_progress'];
                                if ($progress >= 100) {
                                    $badgeClass = 'badge-success';
                                    $icon = 'fa-check-circle';
                                } elseif ($progress >= 75) {
                                    $badgeClass = 'badge-primary';
                                    $icon = 'fa-battery-three-quarters';
                                } elseif ($progress >= 50) {
                                    $badgeClass = 'badge-warning';
                                    $icon = 'fa-battery-half';
                                } elseif ($progress >= 25) {
                                    $badgeClass = 'badge-danger';
                                    $icon = 'fa-battery-quarter';
                                } else {
                                    $badgeClass = 'badge-secondary';
                                    $icon = 'fa-battery-empty';
                                }
                            ?>
                            <span class="badge <?php echo e($badgeClass); ?>">
                                <i class="fas <?php echo e($icon); ?>"></i> <?php echo e(number_format($progress, 2)); ?>%
                            </span>
                        </td>
                        <td class="text-center">
                            <a href="<?php echo e(route('admin.capaian.riwayat-santri', ['id_santri' => $data['santri']->id_santri, 'id_semester' => $selectedSemester])); ?>" 
                               class="btn btn-sm btn-primary" title="Lihat Detail Capaian">
                                <i class="fas fa-eye"></i> Show
                            </a>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
        </div>
    <?php else: ?>
        <div class="empty-state">
            <i class="fas fa-inbox"></i>
            <h3>Tidak Ada Data</h3>
            <p>
                <?php if($search): ?>
                    Tidak ditemukan santri dengan kata kunci "<strong><?php echo e($search); ?></strong>".
                <?php else: ?>
                    Belum ada santri dengan data capaian.
                <?php endif; ?>
            </p>
            <?php if($search || $selectedKelas): ?>
                <a href="<?php echo e(route('admin.capaian.index')); ?>" class="btn btn-secondary">
                    <i class="fas fa-redo"></i> Reset Filter
                </a>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\TugasAkhir\sim-pkpps\resources\views/admin/capaian/index.blade.php ENDPATH**/ ?>