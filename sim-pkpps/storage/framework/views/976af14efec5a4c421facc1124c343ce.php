<?php $__env->startSection('content'); ?>
<div class="page-header">
    <h2><i class="fas fa-history"></i> Riwayat Capaian Santri</h2>
</div>


<div class="content-box" style="margin-bottom: 14px;">
    <div style="display: flex; align-items: center; gap: 20px;">
        <div class="icon-wrapper icon-wrapper-lg">
            <i class="fas fa-user-graduate"></i>
        </div>
        <div style="flex: 1;">
            <h3 style="margin: 0 0 5px 0;"><?php echo e($santri->nama_lengkap); ?></h3>
            <p style="margin: 0; color: var(--text-light);">
                <strong>NIS:</strong> <?php echo e($santri->nis); ?> | 
                <strong>Kelas:</strong> <span class="badge badge-secondary"><?php echo e($santri->kelas); ?></span>
            </p>
        </div>
        <div style="text-align: right; display: flex; gap: 10px; justify-content: flex-end;">
            <a href="<?php echo e(route('admin.capaian.index')); ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Kembali ke Data Capaian
            </a>
            <a href="<?php echo e(route('admin.santri.show', $santri)); ?>" class="btn btn-info">
                <i class="fas fa-user"></i> Profil Santri
            </a>
        </div>
    </div>
</div>


<div class="row-cards">
    <div class="card card-info">
        <h3>Total Capaian</h3>
        <div class="card-value"><?php echo e($totalCapaian); ?></div>
        <p class="text-muted">Data capaian tercatat</p>
        <i class="fas fa-clipboard-list card-icon"></i>
    </div>
    <div class="card card-success">
        <h3>Rata-rata Progress</h3>
        <div class="card-value"><?php echo e(number_format($rataRataPersentase, 1)); ?>%</div>
        <p class="text-muted">Progress keseluruhan</p>
        <i class="fas fa-chart-line card-icon"></i>
    </div>
    <div class="card card-primary">
        <h3>Al-Qur'an</h3>
        <div class="card-value-small"><?php echo e(number_format($statistikKategori['Al-Qur\'an'] ?? 0, 1)); ?>%</div>
        <p class="text-muted">Progress kategori</p>
        <i class="fas fa-book-quran card-icon"></i>
    </div>
    <div class="card card-warning">
        <h3>Hadist</h3>
        <div class="card-value-small"><?php echo e(number_format($statistikKategori['Hadist'] ?? 0, 1)); ?>%</div>
        <p class="text-muted">Progress kategori</p>
        <i class="fas fa-scroll card-icon"></i>
    </div>
</div>


<div class="content-box" style="margin-bottom: 14px;">
    <form method="GET" action="<?php echo e(route('admin.capaian.riwayat-santri', $santri->id_santri)); ?>" class="filter-form-inline">
        <select name="id_semester" class="form-control" style="width: 250px;">
            <option value="">Semua Semester</option>
            <?php $__currentLoopData = $semesters; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $semester): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($semester->id_semester); ?>" <?php echo e(request('id_semester') == $semester->id_semester ? 'selected' : ''); ?>>
                    <?php echo e($semester->nama_semester); ?>

                </option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>

        <input type="text" name="search" class="form-control" placeholder="Cari nama materi..." 
               value="<?php echo e(request('search')); ?>" style="width: 300px;">

        <button type="submit" class="btn btn-primary">
            <i class="fas fa-search"></i> Filter
        </button>

        <?php if(request()->filled('id_semester') || request()->filled('search')): ?>
            <a href="<?php echo e(route('admin.capaian.riwayat-santri', $santri->id_santri)); ?>" class="btn btn-secondary">
                <i class="fas fa-redo"></i> Reset
            </a>
        <?php endif; ?>

        <a href="<?php echo e(route('admin.capaian.create', ['id_santri' => $santri->id_santri])); ?>" class="btn btn-success" style="margin-left: auto;">
            <i class="fas fa-plus"></i> Tambah Capaian
        </a>
    </form>
</div>


<div class="content-box">
    <?php if($capaians->count() > 0): ?>
        
        <?php
            $groupedCapaians = $capaians->groupBy(function($item) {
                return $item->materi->kategori;
            });
        ?>

        <?php $__currentLoopData = ['Al-Qur\'an', 'Hadist', 'Materi Tambahan']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $kategori): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php if(isset($groupedCapaians[$kategori]) && $groupedCapaians[$kategori]->count() > 0): ?>
                <div style="margin-bottom: 22px;">
                    <h4 style="color: var(--primary-dark); margin-bottom: 15px; padding-bottom: 10px; border-bottom: 2px solid var(--primary-light);">
                        <i class="fas fa-<?php echo e($kategori == 'Al-Qur\'an' ? 'book-quran' : ($kategori == 'Hadist' ? 'scroll' : 'book')); ?>"></i>
                        Kategori: <?php echo e($kategori); ?>

                    </h4>
                    
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th style="width: 5%;">No</th>
                                <th style="width: 25%;">Materi</th>
                                <th style="width: 15%;">Semester</th>
                                <th style="width: 15%;">Halaman</th>
                                <th style="width: 15%;">Progress</th>
                                <th style="width: 15%;">Tanggal Input</th>
                                <th class="text-center" style="width: 10%;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $groupedCapaians[$kategori]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $capaian): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td><?php echo e($index + 1); ?></td>
                                    <td>
                                        <strong><?php echo e($capaian->materi->nama_kitab); ?></strong><br>
                                        <small class="text-muted">Total: <?php echo e($capaian->materi->total_halaman); ?> hal</small>
                                    </td>
                                    <td>
                                        <small><?php echo e($capaian->semester->nama_semester); ?></small>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge badge-info">
                                            <?php echo e($capaian->jumlah_halaman_selesai); ?> / <?php echo e($capaian->materi->total_halaman); ?>

                                        </span>
                                    </td>
                                    <td>
                                        <?php echo $capaian->persentase_badge; ?>

                                        <div class="progress-bar" style="margin-top: 5px; height: 8px;">
                                            <div class="progress-fill" style="width: <?php echo e($capaian->persentase); ?>%; background: linear-gradient(90deg, var(--primary-color), var(--success-color));"></div>
                                        </div>
                                    </td>
                                    <td><?php echo e($capaian->tanggal_input->format('d/m/Y')); ?></td>
                                    <td class="text-center">
                                        <div class="btn-group">
                                            <a href="<?php echo e(route('admin.capaian.show', $capaian)); ?>" 
                                               class="btn btn-sm btn-info" title="Detail">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="<?php echo e(route('admin.capaian.edit', $capaian)); ?>" 
                                               class="btn btn-sm btn-warning" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

        
        <div style="margin-top: 14px;">
            <?php echo e($capaians->links()); ?>

        </div>
    <?php else: ?>
        <div class="empty-state">
            <i class="fas fa-clipboard-list"></i>
            <h3>Belum Ada Capaian</h3>
            <p>Santri ini belum memiliki data capaian.</p>
            <a href="<?php echo e(route('admin.capaian.create', ['id_santri' => $santri->id_santri])); ?>" class="btn btn-primary">
                <i class="fas fa-plus"></i> Tambah Capaian Pertama
            </a>
        </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\TugasAkhir\sim-pkpps\resources\views/admin/capaian/riwayat-santri.blade.php ENDPATH**/ ?>