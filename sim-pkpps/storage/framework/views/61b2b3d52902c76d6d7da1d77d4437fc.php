

<div class="modal-kegiatan-detail">

    
    <div style="background: linear-gradient(135deg, <?php echo e($kegiatan->kategori->warna ?? '#6FBAA5'); ?>, <?php echo e($kegiatan->kategori->warna ?? '#5AA88D'); ?>);
                color: white; padding: 14px; border-radius: 8px; margin-bottom: 14px;">
        <h4 style="margin: 0 0 10px 0; display: flex; align-items: center; gap: 10px;">
            <i class="fas <?php echo e($kegiatan->kategori->icon ?? 'fa-calendar'); ?>"></i>
            <?php echo e($kegiatan->nama_kegiatan); ?>

        </h4>
        <div style="display: flex; gap: 20px; flex-wrap: wrap; font-size: 0.9rem; opacity: 0.95;">
            <span><i class="fas fa-clock"></i> <?php echo e(date('H:i', strtotime($kegiatan->waktu_mulai))); ?> - <?php echo e(date('H:i', strtotime($kegiatan->waktu_selesai))); ?></span>
            <span><i class="fas fa-tag"></i> <?php echo e($kegiatan->kategori->nama_kategori); ?></span>
            <span><i class="fas fa-calendar-day"></i> <?php echo e($kegiatan->hari); ?></span>
            <span>
                <i class="fas fa-layer-group"></i>
                <?php if($kegiatan->kelasKegiatan->isEmpty()): ?>
                    Kegiatan Umum
                <?php else: ?>
                    <?php echo e($kegiatan->kelasKegiatan->pluck('nama_kelas')->implode(', ')); ?>

                <?php endif; ?>
            </span>
        </div>
        <?php if($kegiatan->materi): ?>
            <div style="margin-top: 10px; padding-top: 10px; border-top: 1px solid rgba(255,255,255,0.2);">
                <i class="fas fa-book"></i> Materi: <?php echo e($kegiatan->materi); ?>

            </div>
        <?php endif; ?>
    </div>

    
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(120px, 1fr)); gap: 11px; margin-bottom: 20px;">
        <div style="background: linear-gradient(135deg, #28a745, #218838); color: white; padding: 15px; border-radius: 8px; text-align: center;">
            <div style="font-size: 2rem; font-weight: 700;"><?php echo e($stats['hadir']); ?></div>
            <div style="font-size: 0.85rem; opacity: 0.9; margin-top: 5px;"><i class="fas fa-check-circle"></i> Hadir</div>
        </div>
        <div style="background: linear-gradient(135deg, #ffc107, #e0a800); color: white; padding: 15px; border-radius: 8px; text-align: center;">
            <div style="font-size: 2rem; font-weight: 700;"><?php echo e($stats['izin']); ?></div>
            <div style="font-size: 0.85rem; opacity: 0.9; margin-top: 5px;"><i class="fas fa-info-circle"></i> Izin</div>
        </div>
        <div style="background: linear-gradient(135deg, #17a2b8, #138496); color: white; padding: 15px; border-radius: 8px; text-align: center;">
            <div style="font-size: 2rem; font-weight: 700;"><?php echo e($stats['sakit']); ?></div>
            <div style="font-size: 0.85rem; opacity: 0.9; margin-top: 5px;"><i class="fas fa-heartbeat"></i> Sakit</div>
        </div>
        <div style="background: linear-gradient(135deg, #dc3545, #c82333); color: white; padding: 15px; border-radius: 8px; text-align: center;">
            <div style="font-size: 2rem; font-weight: 700;"><?php echo e($stats['alpa']); ?></div>
            <div style="font-size: 0.85rem; opacity: 0.9; margin-top: 5px;"><i class="fas fa-times-circle"></i> Alpa</div>
        </div>
        <div style="background: linear-gradient(135deg, #6c757d, #5a6268); color: white; padding: 15px; border-radius: 8px; text-align: center;">
            <div style="font-size: 2rem; font-weight: 700;"><?php echo e($stats['belum_absen']); ?></div>
            <div style="font-size: 0.85rem; opacity: 0.9; margin-top: 5px;"><i class="fas fa-hourglass-half"></i> Belum</div>
        </div>
    </div>

    
    <div style="background: #f8f9fa; padding: 15px; border-radius: 8px; margin-bottom: 14px;">
        <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
            <strong style="color: #2c3e50;">Total Kehadiran</strong>
            <strong style="color: <?php echo e($stats['persen_hadir'] >= 85 ? '#28a745' : ($stats['persen_hadir'] >= 70 ? '#ffc107' : '#dc3545')); ?>;">
                <?php echo e($stats['hadir']); ?>/<?php echo e($stats['total']); ?> (<?php echo e($stats['persen_hadir']); ?>%)
            </strong>
        </div>
        <div style="height: 30px; background: #e9ecef; border-radius: 15px; overflow: hidden;">
            <div style="height: 100%; width: <?php echo e($stats['persen_hadir']); ?>%;
                        background: <?php echo e($stats['persen_hadir'] >= 85 ? 'linear-gradient(90deg, #28a745, #20c997)' : ($stats['persen_hadir'] >= 70 ? 'linear-gradient(90deg, #ffc107, #fd7e14)' : 'linear-gradient(90deg, #dc3545, #c82333)')); ?>;
                        display: flex; align-items: center; justify-content: center;
                        color: white; font-weight: 700; font-size: 0.85rem;">
                <?php echo e($stats['persen_hadir']); ?>%
            </div>
        </div>
    </div>

    
    <div>
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
            <h5 style="color: #2c3e50; margin: 0;">
                <i class="fas fa-users"></i> Daftar Absensi (<?php echo e($absensis->count()); ?> dari <?php echo e($stats['total']); ?> santri)
            </h5>
            <input type="text" id="searchSantri_<?php echo e($kegiatan->kegiatan_id); ?>"
                   placeholder="Cari santri..."
                   style="padding: 8px 12px; border: 1px solid #ddd; border-radius: 6px; width: 200px;"
                   onkeyup="filterSantri_<?php echo e($kegiatan->kegiatan_id); ?>()">
        </div>

        <?php if($absensis->count() > 0): ?>
            <?php $__currentLoopData = $absensiPerKelas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $namaKelas => $kelasAbsensis): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div style="margin-bottom: 18px;">
                <div style="background: linear-gradient(135deg, #f0f7f4, #e8f5e9); padding: 10px 14px; border-radius: 8px 8px 0 0; display: flex; justify-content: space-between; align-items: center;">
                    <h6 style="margin: 0; color: #2c6b4f; font-size: 0.9rem;">
                        <i class="fas fa-school"></i> <?php echo e($namaKelas); ?>

                    </h6>
                    <span style="background: #6FBAA5; color: white; padding: 2px 10px; border-radius: 10px; font-size: 0.75rem; font-weight: 600;">
                        <?php echo e($kelasAbsensis->count()); ?> santri
                    </span>
                </div>
                <div style="max-height: 250px; overflow-y: auto; border: 1px solid #e9ecef; border-top: 0; border-radius: 0 0 8px 8px;">
                    <table style="width: 100%; border-collapse: collapse;">
                        <thead style="position: sticky; top: 0; background: white; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
                            <tr style="border-bottom: 2px solid #e9ecef;">
                                <th style="padding: 10px; text-align: left; font-size: 0.8rem; color: #6c757d;">No</th>
                                <th style="padding: 10px; text-align: left; font-size: 0.8rem; color: #6c757d;">ID</th>
                                <th style="padding: 10px; text-align: left; font-size: 0.8rem; color: #6c757d;">Nama Santri</th>
                                <th style="padding: 10px; text-align: center; font-size: 0.8rem; color: #6c757d;">Status</th>
                                <th style="padding: 10px; text-align: center; font-size: 0.8rem; color: #6c757d;">Waktu</th>
                                <th style="padding: 10px; text-align: center; font-size: 0.8rem; color: #6c757d;">Metode</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $kelasAbsensis; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $absensi): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr class="santri-row-<?php echo e($kegiatan->kegiatan_id); ?>"
                                style="border-bottom: 1px solid #f1f3f5;"
                                data-nama="<?php echo e(strtolower($absensi->santri->nama_lengkap)); ?>">
                                <td style="padding: 8px 10px;"><?php echo e($index + 1); ?></td>
                                <td style="padding: 8px 10px; font-weight: 600;"><?php echo e($absensi->santri->id_santri); ?></td>
                                <td style="padding: 8px 10px;"><?php echo e($absensi->santri->nama_lengkap); ?></td>
                                <td style="padding: 8px 10px; text-align: center;">
                                    <?php if($absensi->status == 'Hadir'): ?>
                                        <span style="background: #d4edda; color: #155724; padding: 4px 10px; border-radius: 12px; font-size: 0.8rem; font-weight: 600;">
                                            <i class="fas fa-check-circle"></i> Hadir
                                        </span>
                                    <?php elseif($absensi->status == 'Izin'): ?>
                                        <span style="background: #fff3cd; color: #856404; padding: 4px 10px; border-radius: 12px; font-size: 0.8rem; font-weight: 600;">
                                            <i class="fas fa-info-circle"></i> Izin
                                        </span>
                                    <?php elseif($absensi->status == 'Sakit'): ?>
                                        <span style="background: #d1ecf1; color: #0c5460; padding: 4px 10px; border-radius: 12px; font-size: 0.8rem; font-weight: 600;">
                                            <i class="fas fa-heartbeat"></i> Sakit
                                        </span>
                                    <?php elseif($absensi->status == 'Pulang'): ?>
                                        <span style="background: #FFF3E0; color: #E65100; padding: 4px 10px; border-radius: 12px; font-size: 0.8rem; font-weight: 600;">
                                            <i class="fas fa-home"></i> Pulang
                                        </span>
                                    <?php else: ?>
                                        <span style="background: #f8d7da; color: #721c24; padding: 4px 10px; border-radius: 12px; font-size: 0.8rem; font-weight: 600;">
                                            <i class="fas fa-times-circle"></i> Alpa
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td style="padding: 8px 10px; text-align: center; font-size: 0.85rem; color: #6c757d;">
                                    <?php echo e($absensi->waktu_absen ? date('H:i', strtotime($absensi->waktu_absen)) : '-'); ?>

                                </td>
                                <td style="padding: 8px 10px; text-align: center;">
                                    <?php if($absensi->metode_absen == 'RFID'): ?>
                                        <span style="background: #6FBAA5; color: white; padding: 3px 8px; border-radius: 8px; font-size: 0.75rem;">
                                            <i class="fas fa-id-card"></i> RFID
                                        </span>
                                    <?php else: ?>
                                        <span style="background: #6c757d; color: white; padding: 3px 8px; border-radius: 8px; font-size: 0.75rem;">
                                            <i class="fas fa-hand-pointer"></i> Manual
                                        </span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <?php else: ?>
            <div style="text-align: center; padding: 22px; background: #f8f9fa; border-radius: 8px;">
                <i class="fas fa-inbox" style="font-size: 2.2rem; color: #cbd5e0; margin-bottom: 15px;"></i>
                <p style="color: #6c757d; margin: 0;">Belum ada absensi untuk kegiatan ini.</p>
            </div>
        <?php endif; ?>
    </div>

    
    <div style="margin-top: 25px; padding-top: 20px; border-top: 2px solid #e9ecef; display: flex; gap: 10px; justify-content: flex-end;">
        <a href="<?php echo e(route('admin.absensi-kegiatan.input', $kegiatan->kegiatan_id)); ?>?tanggal=<?php echo e($tanggal); ?>"
           class="btn btn-primary">
            <i class="fas fa-clipboard-check"></i> Input Absensi
        </a>
        <a href="<?php echo e(route('admin.riwayat-kegiatan.index')); ?>?kegiatan_id=<?php echo e($kegiatan->kegiatan_id); ?>&tanggal=<?php echo e($tanggal); ?>"
           class="btn btn-info">
            <i class="fas fa-chart-bar"></i> Lihat Rekap Lengkap
        </a>
        <button class="btn btn-secondary" onclick="closeModal()">
            <i class="fas fa-times"></i> Tutup
        </button>
    </div>
</div>

<script>
function filterSantri_<?php echo e($kegiatan->kegiatan_id); ?>() {
    var val  = document.getElementById('searchSantri_<?php echo e($kegiatan->kegiatan_id); ?>').value.toLowerCase();
    var rows = document.querySelectorAll('.santri-row-<?php echo e($kegiatan->kegiatan_id); ?>');
    rows.forEach(function(row) {
        row.style.display = (row.getAttribute('data-nama') || '').includes(val) ? '' : 'none';
    });
}
</script><?php /**PATH C:\xampp\htdocs\TugasAkhir\sim-pkpps\resources\views/admin/kegiatan/data/partials/detail-modal.blade.php ENDPATH**/ ?>