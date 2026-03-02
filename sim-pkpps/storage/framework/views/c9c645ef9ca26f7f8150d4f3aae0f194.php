<?php $__env->startSection('title', 'Riwayat Kepulangan'); ?>

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <h2><i class="fas fa-home"></i> Riwayat Kepulangan</h2>
    <p style="margin: 5px 0 0 0; color: var(--text-light);">
        Riwayat izin pulang <strong><?php echo e($santri->nama_lengkap); ?></strong>
    </p>
</div>


<?php if($terlambat): ?>
<div class="alert alert-danger" style="margin-bottom: 14px;">
    <i class="fas fa-exclamation-triangle"></i>
    <strong>Perhatian!</strong> Anda terlambat kembali ke pesantren sejak
    <strong><?php echo e($terlambat->tanggal_kembali_formatted); ?></strong>. Segera hubungi pengurus!
</div>
<?php elseif($sedangPulang): ?>
<div class="alert alert-info" style="margin-bottom: 14px;">
    <i class="fas fa-home"></i>
    <strong>Anda sedang dalam periode kepulangan.</strong>
    Wajib kembali pada: <strong><?php echo e($sedangPulang->tanggal_kembali_formatted); ?></strong>
    &nbsp;
    <?php
        $sisaHari = Carbon\Carbon::today()->diffInDays($sedangPulang->tanggal_kembali, false);
    ?>
    <?php if($sisaHari >= 0): ?>
        <span class="badge badge-info"><?php echo e($sisaHari); ?> hari lagi</span>
    <?php endif; ?>
</div>
<?php endif; ?>


<div class="content-box" style="margin-bottom: 14px; padding: 20px;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px; flex-wrap: wrap; gap: 8px;">
        <div>
            <h3 style="margin: 0; font-size: 1rem; color: var(--text-color);">
                <i class="fas fa-calendar-alt" style="color: var(--primary-color);"></i>
                Kuota Kepulangan <?php echo e($tahunSekarang); ?>

            </h3>
            <p style="margin: 4px 0 0 0; font-size: 0.82rem; color: var(--text-light);">
                Maksimal 12 hari per tahun
            </p>
        </div>
        <div style="text-align: right;">
            <span style="font-size: 2rem; font-weight: 800; color: <?php echo e($statistik['over_limit'] ? 'var(--danger-color)' : ($statistik['persen_kuota'] >= 80 ? 'var(--warning-color)' : 'var(--primary-color)')); ?>;">
                <?php echo e($statistik['total_hari']); ?>

            </span>
            <span style="font-size: 1rem; color: var(--text-light); font-weight: 600;">/12 hari</span>
        </div>
    </div>

    
    <div style="width: 100%; height: 18px; background: var(--primary-light); border-radius: 9px; overflow: hidden; margin-bottom: 8px;">
        <div style="height: 100%; width: <?php echo e($statistik['persen_kuota']); ?>%;
            background: <?php echo e($statistik['over_limit'] ? 'linear-gradient(90deg, #FF8B94, #ff5252)' : ($statistik['persen_kuota'] >= 80 ? 'linear-gradient(90deg, #FFD56B, #ff9800)' : 'linear-gradient(90deg, var(--primary-color), var(--primary-dark))')); ?>;
            border-radius: 9px; transition: width 0.8s ease; display: flex; align-items: center; justify-content: flex-end; padding-right: 8px;">
            <?php if($statistik['persen_kuota'] > 15): ?>
                <span style="font-size: 0.72rem; font-weight: 700; color: white;"><?php echo e($statistik['persen_kuota']); ?>%</span>
            <?php endif; ?>
        </div>
    </div>

    <div style="display: flex; justify-content: space-between; font-size: 0.78rem; color: var(--text-light);">
        <span>0 hari</span>
        <span style="color: <?php echo e($statistik['sisa_kuota'] > 0 ? 'var(--success-color)' : 'var(--danger-color)'); ?>; font-weight: 600;">
            Sisa: <?php echo e($statistik['sisa_kuota']); ?> hari
        </span>
        <span>12 hari</span>
    </div>

    <?php if($statistik['over_limit']): ?>
    <div style="margin-top: 10px; padding: 8px 12px; background: linear-gradient(135deg, #FFE8EA, #FFD5D8); border-radius: var(--border-radius-sm); border-left: 3px solid var(--danger-color);">
        <i class="fas fa-exclamation-triangle" style="color: var(--danger-color);"></i>
        <span style="font-size: 0.82rem; color: #7C2D35; font-weight: 600;">
            Anda telah melebihi kuota <?php echo e($statistik['total_hari'] - 12); ?> hari!
        </span>
    </div>
    <?php elseif($statistik['persen_kuota'] >= 80): ?>
    <div style="margin-top: 10px; padding: 8px 12px; background: linear-gradient(135deg, #FFF8E1, #FFF3CD); border-radius: var(--border-radius-sm); border-left: 3px solid var(--warning-color);">
        <i class="fas fa-info-circle" style="color: #E6B85C;"></i>
        <span style="font-size: 0.82rem; color: #7C6A2D;">
            Sisa kuota tinggal <strong><?php echo e($statistik['sisa_kuota']); ?> hari</strong>. Gunakan dengan bijak.
        </span>
    </div>
    <?php endif; ?>
</div>


<div class="row-cards" style="margin-bottom: 14px;">
    <div class="card card-info">
        <h3><i class="fas fa-clipboard-list"></i> Total Pengajuan</h3>
        <div class="card-value"><?php echo e($statistik['total_izin']); ?></div>
        <div class="card-icon"><i class="fas fa-clipboard-list"></i></div>
        <p style="margin-top: 10px; font-size: 0.78rem; color: var(--text-light);">Tahun <?php echo e($tahunSekarang); ?></p>
    </div>

    <div class="card card-warning">
        <h3><i class="fas fa-clock"></i> Menunggu</h3>
        <div class="card-value"><?php echo e($statistik['menunggu']); ?></div>
        <div class="card-icon"><i class="fas fa-clock"></i></div>
        <p style="margin-top: 10px; font-size: 0.78rem; color: var(--text-light);">Proses review</p>
    </div>

    <div class="card card-success">
        <h3><i class="fas fa-check-circle"></i> Disetujui</h3>
        <div class="card-value"><?php echo e($statistik['disetujui']); ?></div>
        <div class="card-icon"><i class="fas fa-check-circle"></i></div>
        <p style="margin-top: 10px; font-size: 0.78rem; color: var(--text-light);">Izin diterima</p>
    </div>

    <div class="card card-danger">
        <h3><i class="fas fa-times-circle"></i> Ditolak</h3>
        <div class="card-value"><?php echo e($statistik['ditolak']); ?></div>
        <div class="card-icon"><i class="fas fa-times-circle"></i></div>
        <p style="margin-top: 10px; font-size: 0.78rem; color: var(--text-light);">Tidak disetujui</p>
    </div>
</div>


<div class="content-box" style="margin-bottom: 14px;">
    <form method="GET" class="filter-form-inline">
        <select name="tahun" class="form-control">
            <?php $__currentLoopData = $tahunOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $year): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($year); ?>" <?php echo e($tahunSekarang == $year ? 'selected' : ''); ?>>
                    Tahun <?php echo e($year); ?>

                </option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>

        <select name="status" class="form-control">
            <option value="">Semua Status</option>
            <?php $__currentLoopData = $statusOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($value); ?>" <?php echo e(request('status') == $value ? 'selected' : ''); ?>>
                    <?php echo e($label); ?>

                </option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>

        <button type="submit" class="btn btn-primary btn-sm">
            <i class="fas fa-filter"></i> Filter
        </button>
        <a href="<?php echo e(route('santri.kepulangan.index')); ?>" class="btn btn-secondary btn-sm">
            <i class="fas fa-sync"></i> Reset
        </a>
    </form>
</div>


<?php if($riwayatKepulangan->isEmpty()): ?>
    <div class="empty-state">
        <i class="fas fa-home"></i>
        <h3>Belum Ada Riwayat Kepulangan</h3>
        <p>Anda belum pernah mengajukan izin kepulangan pada periode yang dipilih.</p>
    </div>
<?php else: ?>
    <div class="content-box">
        <h3 style="margin: 0 0 15px 0; color: var(--primary-color); font-size: 0.95rem;">
            <i class="fas fa-list"></i> Daftar Riwayat
            <span class="badge badge-info" style="margin-left: 6px;"><?php echo e($riwayatKepulangan->total()); ?> data</span>
        </h3>

        <div style="display: flex; flex-direction: column; gap: 10px;">
            <?php $__currentLoopData = $riwayatKepulangan; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <a href="<?php echo e(route('santri.kepulangan.show', $item->id_kepulangan)); ?>"
               style="display: flex; gap: 12px; padding: 14px; background: #FEFFFE; border-radius: var(--border-radius-sm);
                      border-left: 4px solid
                      <?php if($item->status == 'Menunggu'): ?> var(--warning-color)
                      <?php elseif($item->status == 'Disetujui'): ?> var(--success-color)
                      <?php elseif($item->status == 'Ditolak'): ?> var(--danger-color)
                      <?php else: ?> var(--text-light) <?php endif; ?>;
                      text-decoration: none; transition: var(--transition-base); box-shadow: var(--shadow-sm);"
               onmouseover="this.style.boxShadow='var(--shadow-md)'; this.style.transform='translateX(4px)';"
               onmouseout="this.style.boxShadow='var(--shadow-sm)'; this.style.transform='translateX(0)';">

                
                <div style="flex-shrink: 0; width: 48px; height: 48px; border-radius: 50%; display: flex; align-items: center; justify-content: center; background:
                    <?php if($item->status == 'Menunggu'): ?> linear-gradient(135deg, #FFF8E1, #FFF3CD)
                    <?php elseif($item->status == 'Disetujui'): ?> linear-gradient(135deg, #E8F7F2, #D4F1E3)
                    <?php elseif($item->status == 'Ditolak'): ?> linear-gradient(135deg, #FFE8EA, #FFD5D8)
                    <?php else: ?> linear-gradient(135deg, #E2E3E5, #D6D8DB) <?php endif; ?>;">
                    <i class="fas
                        <?php if($item->status == 'Menunggu'): ?> fa-clock
                        <?php elseif($item->status == 'Disetujui'): ?> fa-check-circle
                        <?php elseif($item->status == 'Ditolak'): ?> fa-times-circle
                        <?php else: ?> fa-flag-checkered <?php endif; ?>"
                       style="font-size: 1.4rem; color:
                        <?php if($item->status == 'Menunggu'): ?> var(--warning-color)
                        <?php elseif($item->status == 'Disetujui'): ?> var(--success-color)
                        <?php elseif($item->status == 'Ditolak'): ?> var(--danger-color)
                        <?php else: ?> var(--text-light) <?php endif; ?>;"></i>
                </div>

                
                <div style="flex: 1; min-width: 0;">
                    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 5px; gap: 8px;">
                        <p style="margin: 0; font-size: 0.88rem; font-weight: 600; color: var(--text-color); overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                            <?php echo e($item->alasan); ?>

                        </p>
                        <span class="badge badge-<?php echo e($item->status == 'Menunggu' ? 'warning' :
                            ($item->status == 'Disetujui' ? 'success' :
                            ($item->status == 'Ditolak' ? 'danger' : 'secondary'))); ?>" style="white-space: nowrap; flex-shrink: 0;">
                            <?php echo e($item->status); ?>

                        </span>
                    </div>

                    <p style="margin: 0 0 6px 0; font-size: 0.78rem; color: var(--text-light);">
                        <i class="fas fa-hashtag"></i> <?php echo e($item->id_kepulangan); ?>

                    </p>

                    <div style="display: flex; flex-wrap: wrap; gap: 8px; font-size: 0.78rem; color: var(--text-light); align-items: center;">
                        <span>
                            <i class="fas fa-calendar-alt" style="color: var(--primary-color);"></i>
                            <?php echo e($item->tanggal_pulang_formatted); ?> &ndash; <?php echo e($item->tanggal_kembali_formatted); ?>

                        </span>
                        <span class="badge badge-info badge-sm">
                            <i class="fas fa-clock"></i> <?php echo e($item->durasi_izin); ?> hari
                        </span>
                        <?php if($item->status == 'Disetujui'): ?>
                            <?php if($item->is_aktif): ?>
                                <span class="badge badge-success badge-sm">
                                    <i class="fas fa-home"></i> Sedang Pulang
                                </span>
                            <?php elseif($item->is_terlambat): ?>
                                <span class="badge badge-danger badge-sm">
                                    <i class="fas fa-exclamation-triangle"></i> Terlambat
                                </span>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>

                
                <div style="flex-shrink: 0; display: flex; align-items: center;">
                    <i class="fas fa-chevron-right" style="color: var(--text-light); font-size: 0.8rem;"></i>
                </div>
            </a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>

        
        <div style="margin-top: 20px;">
            <?php echo e($riwayatKepulangan->links()); ?>

        </div>
    </div>
<?php endif; ?>


<div class="info-box" style="margin-top: 14px;">
    <i class="fas fa-info-circle"></i>
    <strong>Info:</strong> Kuota kepulangan maksimal <strong>12 hari per tahun</strong>.
    Pastikan Anda merencanakan kepulangan dengan bijak agar tidak melebihi batas kuota.
</div>


<div style="margin-top: 14px; text-align: center;">
    <a href="<?php echo e(route('santri.dashboard')); ?>" class="btn btn-secondary hover-lift">
        <i class="fas fa-home"></i> Kembali ke Dashboard
    </a>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\TugasAkhir\sim-pkpps\resources\views/santri/kepulangan/index.blade.php ENDPATH**/ ?>