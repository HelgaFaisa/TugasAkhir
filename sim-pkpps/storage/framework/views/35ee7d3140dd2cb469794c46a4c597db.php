<?php $__env->startSection('title', 'Detail Kepulangan'); ?>

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <h2><i class="fas fa-info-circle"></i> Detail Izin Kepulangan</h2>
    <a href="<?php echo e(route('santri.kepulangan.index')); ?>" class="btn btn-secondary btn-sm hover-lift">
        <i class="fas fa-arrow-left"></i> Kembali
    </a>
</div>


<div style="padding: 16px 20px; border-radius: var(--border-radius); margin-bottom: 14px; display: flex; align-items: center; gap: 14px;
    background: <?php echo e($kepulangan->status == 'Disetujui' ? 'linear-gradient(135deg, #E8F7F2, #D4F1E3)' : ($kepulangan->status == 'Ditolak' ? 'linear-gradient(135deg, #FFE8EA, #FFD5D8)' : ($kepulangan->status == 'Menunggu' ? 'linear-gradient(135deg, #FFF8E1, #FFF3CD)' : 'linear-gradient(135deg, #E2E3E5, #D6D8DB)'))); ?>;
    border-left: 5px solid <?php echo e($kepulangan->status == 'Disetujui' ? 'var(--success-color)' : ($kepulangan->status == 'Ditolak' ? 'var(--danger-color)' : ($kepulangan->status == 'Menunggu' ? 'var(--warning-color)' : 'var(--text-light)'))); ?>;
    box-shadow: var(--shadow-sm);">
    <div style="width: 52px; height: 52px; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0;
        background: <?php echo e($kepulangan->status == 'Disetujui' ? 'var(--success-color)' : ($kepulangan->status == 'Ditolak' ? 'var(--danger-color)' : ($kepulangan->status == 'Menunggu' ? 'var(--warning-color)' : '#6c757d'))); ?>;">
        <i class="fas <?php echo e($kepulangan->status == 'Menunggu' ? 'fa-clock' : ($kepulangan->status == 'Disetujui' ? 'fa-check-circle' : ($kepulangan->status == 'Ditolak' ? 'fa-times-circle' : 'fa-flag-checkered'))); ?>"
           style="font-size: 1.5rem; color: white;"></i>
    </div>
    <div>
        <p style="margin: 0; font-size: 0.8rem; color: var(--text-light);">Status Izin</p>
        <h3 style="margin: 2px 0; font-size: 1.1rem; color: var(--text-color);"><?php echo e($kepulangan->status); ?></h3>
        <?php if($kepulangan->status == 'Disetujui' && $kepulangan->is_aktif): ?>
            <span class="badge badge-success badge-sm"><i class="fas fa-home"></i> Sedang Dalam Periode Pulang</span>
        <?php elseif($kepulangan->status == 'Disetujui' && $kepulangan->is_terlambat): ?>
            <span class="badge badge-danger badge-sm"><i class="fas fa-exclamation-triangle"></i> Terlambat Kembali</span>
        <?php endif; ?>
    </div>
</div>

<div style="display: grid; grid-template-columns: 3fr 2fr; gap: 14px;">

    
    <div>
        <div class="content-box" style="margin-bottom: 14px;">
            <h4 style="margin: 0 0 14px 0; color: var(--primary-color); font-size: 0.92rem; border-bottom: 2px solid var(--primary-light); padding-bottom: 8px;">
                <i class="fas fa-clipboard-list"></i> Informasi Izin
            </h4>
            <table class="detail-table">
                <tr>
                    <th><i class="fas fa-hashtag"></i> ID Kepulangan</th>
                    <td><strong><?php echo e($kepulangan->id_kepulangan); ?></strong></td>
                </tr>
                <tr>
                    <th><i class="fas fa-calendar-plus"></i> Tanggal Pengajuan</th>
                    <td><?php echo e($kepulangan->tanggal_izin_formatted); ?></td>
                </tr>
                <tr>
                    <th><i class="fas fa-plane-departure"></i> Tanggal Pulang</th>
                    <td><strong style="color: var(--primary-color);"><?php echo e($kepulangan->tanggal_pulang_formatted); ?></strong></td>
                </tr>
                <tr>
                    <th><i class="fas fa-plane-arrival"></i> Tanggal Kembali</th>
                    <td><strong style="color: var(--primary-color);"><?php echo e($kepulangan->tanggal_kembali_formatted); ?></strong></td>
                </tr>
                <tr>
                    <th><i class="fas fa-hourglass-half"></i> Durasi Izin</th>
                    <td>
                        <span class="badge badge-info"><?php echo e($kepulangan->durasi_izin); ?> hari</span>
                    </td>
                </tr>
                <tr>
                    <th><i class="fas fa-comment-alt"></i> Alasan</th>
                    <td><strong><?php echo e($kepulangan->alasan); ?></strong></td>
                </tr>
                <?php if($kepulangan->approved_by): ?>
                <tr>
                    <th><i class="fas fa-user-check"></i> Disetujui Oleh</th>
                    <td><?php echo e($kepulangan->approved_by); ?></td>
                </tr>
                <tr>
                    <th><i class="fas fa-calendar-check"></i> Tanggal Diproses</th>
                    <td><?php echo e($kepulangan->approved_at_formatted); ?></td>
                </tr>
                <?php endif; ?>
                <?php if($kepulangan->catatan): ?>
                <tr>
                    <th><i class="fas fa-sticky-note"></i> Catatan Admin</th>
                    <td style="white-space: pre-wrap;"><?php echo e($kepulangan->catatan); ?></td>
                </tr>
                <?php endif; ?>
            </table>
        </div>

        
        <?php if($kepulangan->status == 'Menunggu'): ?>
        <div class="info-box" style="background: linear-gradient(135deg, #FFF8E1, #FFF3CD); border-color: var(--warning-color);">
            <i class="fas fa-clock" style="color: var(--warning-color);"></i>
            <strong>Menunggu Persetujuan:</strong> Izin kepulangan Anda sedang dalam proses review oleh pengurus. Mohon bersabar.
        </div>
        <?php elseif($kepulangan->status == 'Disetujui'): ?>
            <?php if($kepulangan->is_aktif): ?>
            <div class="info-box" style="background: linear-gradient(135deg, #E3F2FD, #D1E9F9); border-color: var(--info-color);">
                <i class="fas fa-home" style="color: var(--info-color);"></i>
                <strong>Sedang Pulang:</strong> Anda sedang dalam periode kepulangan. Pastikan kembali tepat waktu pada <strong><?php echo e($kepulangan->tanggal_kembali_formatted); ?></strong>.
            </div>
            <?php elseif($kepulangan->is_terlambat): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-triangle"></i>
                <strong>Terlambat Kembali!</strong> Anda telah melewati tanggal kembali yang dijadwalkan. Segera hubungi pengurus pesantren!
            </div>
            <?php else: ?>
            <div class="info-box" style="background: linear-gradient(135deg, #E8F7F2, #D4F1E3); border-color: var(--success-color);">
                <i class="fas fa-check-circle" style="color: var(--success-color);"></i>
                <strong>Izin Disetujui:</strong> Kepulangan Anda telah disetujui. Pastikan pulang dan kembali sesuai jadwal.
            </div>
            <?php endif; ?>
        <?php elseif($kepulangan->status == 'Ditolak'): ?>
        <div class="alert alert-danger">
            <i class="fas fa-times-circle"></i>
            <strong>Izin Ditolak:</strong> Maaf, izin kepulangan Anda tidak disetujui.
            <?php if($kepulangan->catatan): ?> Alasan: <strong><?php echo e($kepulangan->catatan); ?></strong><?php endif; ?>
        </div>
        <?php elseif($kepulangan->status == 'Selesai'): ?>
        <div class="info-box" style="background: linear-gradient(135deg, #E2E3E5, #D6D8DB); border-color: var(--text-light);">
            <i class="fas fa-flag-checkered"></i>
            <strong>Kepulangan Selesai:</strong> Anda telah menyelesaikan periode kepulangan ini.
        </div>
        <?php endif; ?>
    </div>

    
    <div>
        <div class="content-box" style="margin-bottom: 14px;">
            <h4 style="margin: 0 0 14px 0; color: var(--primary-color); font-size: 0.92rem; border-bottom: 2px solid var(--primary-light); padding-bottom: 8px;">
                <i class="fas fa-chart-pie"></i> Kuota Tahun <?php echo e(date('Y')); ?>

            </h4>

            
            <div style="text-align: center; padding: 16px; background: linear-gradient(135deg,
                <?php echo e($totalHariTahunIni > 12 ? '#ff5252 0%, #f48fb1 100%' : ($persenKuota >= 80 ? '#ffd54f 0%, #ffb74d 100%' : '#81c784 0%, #66bb6a 100%')); ?>);
                border-radius: var(--border-radius-sm); margin-bottom: 14px;">
                <p style="margin: 0; font-size: 0.8rem; color: rgba(255,255,255,0.9);">Total Hari Terpakai</p>
                <p style="margin: 4px 0; font-size: 2.2rem; font-weight: 800; color: white; line-height: 1;"><?php echo e($totalHariTahunIni); ?></p>
                <p style="margin: 0; font-size: 0.85rem; color: rgba(255,255,255,0.9);">dari 12 hari</p>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom: 14px;">
                <div style="text-align: center; padding: 12px; background: var(--bg-color); border-radius: var(--border-radius-sm);">
                    <p style="margin: 0; font-size: 0.72rem; color: var(--text-light);">Sisa Kuota</p>
                    <p style="margin: 4px 0 0 0; font-size: 1.6rem; font-weight: 700; color: <?php echo e($sisaKuota > 0 ? 'var(--success-color)' : 'var(--danger-color)'); ?>; line-height: 1;">
                        <?php echo e($sisaKuota); ?>

                    </p>
                    <p style="margin: 2px 0 0 0; font-size: 0.72rem; color: var(--text-light);">hari</p>
                </div>
                <div style="text-align: center; padding: 12px; background: var(--bg-color); border-radius: var(--border-radius-sm);">
                    <p style="margin: 0; font-size: 0.72rem; color: var(--text-light);">Persentase</p>
                    <p style="margin: 4px 0 0 0; font-size: 1.6rem; font-weight: 700; color: <?php echo e($persenKuota >= 100 ? 'var(--danger-color)' : ($persenKuota >= 80 ? '#E6B85C' : 'var(--success-color)')); ?>; line-height: 1;">
                        <?php echo e($persenKuota); ?>%
                    </p>
                    <p style="margin: 2px 0 0 0; font-size: 0.72rem; color: var(--text-light);">terpakai</p>
                </div>
            </div>

            
            <div>
                <div style="width: 100%; height: 12px; background: var(--primary-light); border-radius: 6px; overflow: hidden; margin-bottom: 4px;">
                    <div style="width: <?php echo e($persenKuota); ?>%; height: 100%; border-radius: 6px;
                        background: <?php echo e($persenKuota >= 100 ? 'linear-gradient(90deg, #FF8B94, #ff5252)' : ($persenKuota >= 80 ? 'linear-gradient(90deg, #FFD56B, #ff9800)' : 'linear-gradient(90deg, var(--primary-color), var(--primary-dark))')); ?>;
                        transition: width 0.8s ease;"></div>
                </div>
                <p style="margin: 0; font-size: 0.72rem; color: var(--text-light); text-align: right;"><?php echo e($persenKuota); ?>% dari kuota terpakai</p>
            </div>
        </div>

        
        <?php if($riwayatLain->count() > 0): ?>
        <div class="content-box">
            <h4 style="margin: 0 0 12px 0; color: var(--primary-color); font-size: 0.88rem; border-bottom: 2px solid var(--primary-light); padding-bottom: 8px;">
                <i class="fas fa-history"></i> Riwayat Lain Tahun Ini
            </h4>
            <div style="display: flex; flex-direction: column; gap: 8px;">
                <?php $__currentLoopData = $riwayatLain; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <a href="<?php echo e(route('santri.kepulangan.show', $item->id_kepulangan)); ?>"
                   style="display: flex; align-items: center; gap: 8px; padding: 8px; background: var(--bg-color); border-radius: var(--border-radius-sm); text-decoration: none; transition: var(--transition-fast); border-left: 3px solid <?php echo e($item->status == 'Disetujui' ? 'var(--success-color)' : ($item->status == 'Ditolak' ? 'var(--danger-color)' : ($item->status == 'Menunggu' ? 'var(--warning-color)' : 'var(--text-light)'))); ?>;"
                   onmouseover="this.style.background='var(--primary-light)'"
                   onmouseout="this.style.background='var(--bg-color)'">
                    <div style="flex: 1; min-width: 0;">
                        <p style="margin: 0; font-size: 0.78rem; font-weight: 600; color: var(--text-color); overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                            <?php echo e($item->id_kepulangan); ?>

                        </p>
                        <p style="margin: 2px 0 0 0; font-size: 0.72rem; color: var(--text-light);">
                            <?php echo e($item->tanggal_pulang_formatted); ?>

                            <span class="badge badge-info badge-sm" style="margin-left: 4px;"><?php echo e($item->durasi_izin); ?>h</span>
                        </p>
                    </div>
                    <span class="badge badge-<?php echo e($item->status == 'Disetujui' ? 'success' : ($item->status == 'Ditolak' ? 'danger' : ($item->status == 'Menunggu' ? 'warning' : 'secondary'))); ?>" style="font-size: 0.68rem; white-space: nowrap;">
                        <?php echo e($item->status); ?>

                    </span>
                </a>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
            <a href="<?php echo e(route('santri.kepulangan.index')); ?>" style="display: block; text-align: center; margin-top: 10px; font-size: 0.78rem; color: var(--primary-color); text-decoration: none;">
                Lihat semua riwayat →
            </a>
        </div>
        <?php endif; ?>
    </div>
</div>


<div style="margin-top: 14px; text-align: center; display: flex; gap: 10px; justify-content: center; flex-wrap: wrap;">
    <a href="<?php echo e(route('santri.kepulangan.index')); ?>" class="btn btn-primary hover-lift">
        <i class="fas fa-list"></i> Semua Riwayat
    </a>
    <a href="<?php echo e(route('santri.dashboard')); ?>" class="btn btn-secondary hover-lift">
        <i class="fas fa-home"></i> Dashboard
    </a>
</div>

<style>
@media (max-width: 768px) {
    div[style*="grid-template-columns: 3fr 2fr"] {
        grid-template-columns: 1fr !important;
    }
}
</style>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\TugasAkhir\sim-pkpps\resources\views/santri/kepulangan/show.blade.php ENDPATH**/ ?>