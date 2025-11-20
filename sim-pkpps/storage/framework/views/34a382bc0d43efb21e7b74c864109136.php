


<?php $__env->startSection('title', 'Detail Riwayat Pelanggaran'); ?>

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <h2><i class="fas fa-info-circle"></i> Detail Riwayat Pelanggaran</h2>
</div>

<!-- Breadcrumb -->
<div style="margin-bottom: 20px;">
    <nav style="display: flex; align-items: center; gap: 8px; color: var(--text-light); font-size: 0.9em;">
        <a href="<?php echo e(route('admin.riwayat-pelanggaran.index')); ?>" style="color: var(--primary-color); text-decoration: none;">
            <i class="fas fa-history"></i> Riwayat Pelanggaran
        </a>
        <i class="fas fa-chevron-right" style="font-size: 0.7em;"></i>
        <span>Detail</span>
    </nav>
</div>

<div style="display: grid; grid-template-columns: 2fr 1fr; gap: 30px;">
    <!-- Detail Riwayat -->
    <div>
        <div class="content-box">
            <div class="detail-header">
                <h3><i class="fas fa-clipboard-list"></i> Informasi Riwayat</h3>
                <div style="display: flex; gap: 10px;">
                    <a href="<?php echo e(route('admin.riwayat-pelanggaran.edit', $riwayatPelanggaran)); ?>" class="btn btn-warning btn-sm">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                    <a href="<?php echo e(route('admin.riwayat-pelanggaran.index')); ?>" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                </div>
            </div>

            <table class="detail-table">
                <tr>
                    <th><i class="fas fa-tag"></i> ID Riwayat</th>
                    <td>
                        <span class="badge badge-secondary" style="font-size: 1em;">
                            <?php echo e($riwayatPelanggaran->id_riwayat); ?>

                        </span>
                    </td>
                </tr>
                <tr>
                    <th><i class="fas fa-calendar"></i> Tanggal Pelanggaran</th>
                    <td>
                        <strong style="font-size: 1.1em;">
                            <?php echo e(\Carbon\Carbon::parse($riwayatPelanggaran->tanggal)->isoFormat('dddd, D MMMM YYYY')); ?>

                        </strong>
                    </td>
                </tr>
                <tr>
                    <th><i class="fas fa-fire"></i> Poin Pelanggaran</th>
                    <td>
                        <span class="badge badge-danger" style="font-size: 1.2em; padding: 10px 20px;">
                            <i class="fas fa-star"></i> <?php echo e($riwayatPelanggaran->poin); ?> Poin
                        </span>
                    </td>
                </tr>
                <tr>
                    <th><i class="fas fa-calendar-plus"></i> Tanggal Dicatat</th>
                    <td><?php echo e($riwayatPelanggaran->created_at->format('d F Y, H:i')); ?> WIB</td>
                </tr>
                <tr>
                    <th><i class="fas fa-calendar-check"></i> Terakhir Diperbarui</th>
                    <td><?php echo e($riwayatPelanggaran->updated_at->format('d F Y, H:i')); ?> WIB</td>
                </tr>
            </table>
        </div>

        <!-- Data Santri -->
        <div class="content-box" style="margin-top: 30px;">
            <h3 style="margin-bottom: 20px; color: var(--primary-color);">
                <i class="fas fa-user"></i> Data Santri
            </h3>
            
            <?php if($riwayatPelanggaran->santri): ?>
                <table class="detail-table">
                    <tr>
                        <th><i class="fas fa-id-card"></i> ID Santri</th>
                        <td>
                            <span class="badge badge-primary"><?php echo e($riwayatPelanggaran->santri->id_santri); ?></span>
                        </td>
                    </tr>
                    <tr>
                        <th><i class="fas fa-user"></i> Nama Lengkap</th>
                        <td>
                            <strong style="font-size: 1.1em;"><?php echo e($riwayatPelanggaran->santri->nama_lengkap); ?></strong>
                        </td>
                    </tr>
                    <tr>
                        <th><i class="fas fa-graduation-cap"></i> Kelas</th>
                        <td><?php echo e($riwayatPelanggaran->santri->kelas); ?></td>
                    </tr>
                    <tr>
                        <th><i class="fas fa-hashtag"></i> NIS</th>
                        <td><?php echo e($riwayatPelanggaran->santri->nis); ?></td>
                    </tr>
                    <tr>
                        <th><i class="fas fa-chart-line"></i> Total Poin Pelanggaran</th>
                        <td>
                            <span class="badge badge-danger" style="font-size: 1em;">
                                <?php echo e($riwayatPelanggaran->santri->total_poin_pelanggaran); ?> Poin
                            </span>
                        </td>
                    </tr>
                </table>

                <div style="margin-top: 15px;">
                    <a href="<?php echo e(route('admin.santri.show', $riwayatPelanggaran->santri)); ?>" class="btn btn-primary" style="width: 100%;">
                        <i class="fas fa-eye"></i> Lihat Detail Santri
                    </a>
                </div>
            <?php else: ?>
                <div style="text-align: center; padding: 30px; color: var(--danger-color);">
                    <i class="fas fa-exclamation-triangle" style="font-size: 3em; margin-bottom: 15px;"></i>
                    <p>Data santri tidak ditemukan</p>
                </div>
            <?php endif; ?>
        </div>

        <!-- Kategori Pelanggaran -->
        <div class="content-box" style="margin-top: 30px;">
            <h3 style="margin-bottom: 20px; color: var(--primary-color);">
                <i class="fas fa-tags"></i> Kategori Pelanggaran
            </h3>
            
            <?php if($riwayatPelanggaran->kategori): ?>
                <table class="detail-table">
                    <tr>
                        <th><i class="fas fa-tag"></i> ID Kategori</th>
                        <td>
                            <span class="badge badge-primary"><?php echo e($riwayatPelanggaran->kategori->id_kategori); ?></span>
                        </td>
                    </tr>
                    <tr>
                        <th><i class="fas fa-exclamation-triangle"></i> Nama Pelanggaran</th>
                        <td>
                            <strong style="font-size: 1.1em;"><?php echo e($riwayatPelanggaran->kategori->nama_pelanggaran); ?></strong>
                        </td>
                    </tr>
                    <tr>
                        <th><i class="fas fa-star"></i> Poin Kategori</th>
                        <td>
                            <span class="badge badge-danger" style="font-size: 1em;">
                                <?php echo e($riwayatPelanggaran->kategori->poin); ?> Poin
                            </span>
                        </td>
                    </tr>
                </table>

                <div style="margin-top: 15px;">
                    <a href="<?php echo e(route('admin.kategori-pelanggaran.show', $riwayatPelanggaran->kategori)); ?>" class="btn btn-primary" style="width: 100%;">
                        <i class="fas fa-eye"></i> Lihat Detail Kategori
                    </a>
                </div>
            <?php else: ?>
                <div style="text-align: center; padding: 30px; color: var(--danger-color);">
                    <i class="fas fa-exclamation-triangle" style="font-size: 3em; margin-bottom: 15px;"></i>
                    <p>Kategori tidak ditemukan</p>
                </div>
            <?php endif; ?>
        </div>

        <!-- Keterangan -->
        <?php if($riwayatPelanggaran->keterangan): ?>
        <div class="content-box" style="margin-top: 30px;">
            <h3 style="margin-bottom: 15px; color: var(--primary-color);">
                <i class="fas fa-comment"></i> Keterangan Tambahan
            </h3>
            <div style="background: var(--bg-color); padding: 15px; border-radius: var(--border-radius-sm); border-left: 4px solid var(--primary-color);">
                <p style="margin: 0; line-height: 1.6;"><?php echo e($riwayatPelanggaran->keterangan); ?></p>
            </div>
        </div>
        <?php endif; ?>

        <!-- Riwayat Pelanggaran Lainnya -->
        <?php if($riwayatLainnya->isNotEmpty()): ?>
        <div class="content-box" style="margin-top: 30px;">
            <h3 style="margin-bottom: 20px; color: var(--primary-color);">
                <i class="fas fa-history"></i> Riwayat Pelanggaran Lainnya (Santri yang Sama)
            </h3>
            
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 50px;">No</th>
                        <th>Tanggal</th>
                        <th>Kategori</th>
                        <th style="text-align: center;">Poin</th>
                        <th style="width: 100px; text-align: center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $riwayatLainnya; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $riwayat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td><?php echo e($index + 1); ?></td>
                        <td><?php echo e(\Carbon\Carbon::parse($riwayat->tanggal)->format('d M Y')); ?></td>
                        <td>
                            <?php if($riwayat->kategori): ?>
                                <strong><?php echo e($riwayat->kategori->nama_pelanggaran); ?></strong>
                            <?php else: ?>
                                <span style="color: var(--danger-color);">-</span>
                            <?php endif; ?>
                        </td>
                        <td style="text-align: center;">
                            <span class="badge badge-danger"><?php echo e($riwayat->poin); ?></span>
                        </td>
                        <td style="text-align: center;">
                            <a href="<?php echo e(route('admin.riwayat-pelanggaran.show', $riwayat)); ?>" class="btn btn-sm btn-success">
                                <i class="fas fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>

            <div style="text-align: center; margin-top: 15px;">
                <a href="<?php echo e(route('admin.riwayat-pelanggaran.index')); ?>?id_santri=<?php echo e($riwayatPelanggaran->id_santri); ?>" class="btn btn-primary">
                    <i class="fas fa-list"></i> Lihat Semua Riwayat Santri Ini
                </a>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <!-- Sidebar -->
    <div>
        <!-- Quick Actions -->
        <div class="card card-primary">
            <h3 style="margin-bottom: 15px;">
                <i class="fas fa-bolt"></i> Aksi Cepat
            </h3>
            <div style="display: flex; flex-direction: column; gap: 10px;">
                <a href="<?php echo e(route('admin.riwayat-pelanggaran.edit', $riwayatPelanggaran)); ?>" class="btn btn-warning" style="width: 100%;">
                    <i class="fas fa-edit"></i> Edit Riwayat
                </a>
                <a href="<?php echo e(route('admin.riwayat-pelanggaran.create')); ?>" class="btn btn-primary" style="width: 100%;">
                    <i class="fas fa-plus"></i> Tambah Riwayat Baru
                </a>
                <a href="<?php echo e(route('admin.riwayat-pelanggaran.index')); ?>" class="btn btn-secondary" style="width: 100%;">
                    <i class="fas fa-list"></i> Semua Riwayat
                </a>
                
                <?php if($riwayatPelanggaran->santri): ?>
                <a href="<?php echo e(route('admin.santri.show', $riwayatPelanggaran->santri)); ?>" class="btn btn-success" style="width: 100%;">
                    <i class="fas fa-user"></i> Lihat Santri
                </a>
                <?php endif; ?>
            </div>
        </div>

        <!-- Statistik Poin -->
        <div class="card card-danger" style="margin-top: 20px;">
            <h3 style="margin-bottom: 15px;">
                <i class="fas fa-fire"></i> Poin Pelanggaran
            </h3>
            <div style="text-align: center;">
                <div class="card-value" style="color: var(--danger-color);"><?php echo e($riwayatPelanggaran->poin); ?></div>
                <p style="margin: 0; color: var(--text-light);">Poin Pelanggaran Ini</p>
            </div>
            
            <?php if($riwayatPelanggaran->santri): ?>
            <div style="margin-top: 20px; padding-top: 20px; border-top: 1px solid rgba(255, 139, 148, 0.3);">
                <div style="text-align: center;">
                    <div class="card-value-small" style="color: var(--danger-color);">
                        <?php echo e($riwayatPelanggaran->santri->total_poin_pelanggaran); ?>

                    </div>
                    <p style="margin: 0; color: var(--text-light);">Total Poin Santri</p>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <!-- Info Box -->
        <div class="card card-info" style="margin-top: 20px;">
            <h3 style="margin-bottom: 10px;">
                <i class="fas fa-info-circle"></i> Informasi
            </h3>
            <p style="font-size: 0.9em; line-height: 1.6; margin: 0; color: var(--text-color);">
                Riwayat ini dicatat pada <strong><?php echo e($riwayatPelanggaran->created_at->format('d F Y')); ?></strong> 
                untuk pelanggaran yang terjadi pada <strong><?php echo e(\Carbon\Carbon::parse($riwayatPelanggaran->tanggal)->format('d F Y')); ?></strong>.
            </p>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\TugasAkhir\sim-pkpps\resources\views/admin/riwayat_pelanggaran/show.blade.php ENDPATH**/ ?>