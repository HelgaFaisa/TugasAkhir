<?php $__env->startSection('content'); ?>
<div class="page-header">
    <h2><i class="fas fa-chart-bar"></i> Rekap Absensi: <?php echo e($kegiatan->nama_kegiatan); ?></h2>
</div>


<div style="background: #fff; border-radius: 12px; padding: 18px 22px; margin-bottom: 16px; box-shadow: 0 2px 12px rgba(0,0,0,0.06); border-left: 4px solid #2563eb;">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px; margin-bottom: 12px;">
        <div>
            <h3 style="margin: 0; font-size: 1rem; color: #1a2332;">
                <i class="fas fa-users" style="color: #2563eb;"></i> Total Semua Santri: <strong><?php echo e($totalSantriEligible); ?></strong>
            </h3>
            <p style="margin: 4px 0 0; font-size: 0.84rem; color: #6b7280;">
                Sudah absen: <strong style="color: #059669;"><?php echo e($santriSudahAbsen); ?></strong>
                &nbsp;·&nbsp;
                Belum absen: <strong style="color: <?php echo e($belumAbsen > 0 ? '#dc2626' : '#059669'); ?>;"><?php echo e($belumAbsen); ?></strong>
            </p>
        </div>
        <div style="text-align: right;">
            <div style="font-size: 1.6rem; font-weight: 800; color: <?php echo e($persenHadir >= 85 ? '#059669' : ($persenHadir >= 70 ? '#d97706' : '#dc2626')); ?>;">
                <?php echo e($persenHadir); ?>%
            </div>
            <div style="font-size: 0.78rem; color: #6b7280;">Kehadiran</div>
        </div>
    </div>
    
    <div style="height: 28px; background: #f3f4f6; border-radius: 14px; overflow: hidden; display: flex;">
        <?php
            $pctHadir     = $totalSantriEligible > 0 ? round(($stats['Hadir'] ?? 0) / $totalSantriEligible * 100, 1) : 0;
            $pctTerlambat = $totalSantriEligible > 0 ? round(($stats['Terlambat'] ?? 0) / $totalSantriEligible * 100, 1) : 0;
            $pctIzin      = $totalSantriEligible > 0 ? round(($stats['Izin'] ?? 0) / $totalSantriEligible * 100, 1) : 0;
            $pctSakit     = $totalSantriEligible > 0 ? round(($stats['Sakit'] ?? 0) / $totalSantriEligible * 100, 1) : 0;
            $pctAlpa      = $totalSantriEligible > 0 ? round(($stats['Alpa'] ?? 0) / $totalSantriEligible * 100, 1) : 0;
            $pctBelum     = $totalSantriEligible > 0 ? round($belumAbsen / $totalSantriEligible * 100, 1) : 0;
        ?>
        <?php if($pctHadir > 0): ?>
        <div style="width: <?php echo e($pctHadir); ?>%; background: #22c55e; display: flex; align-items: center; justify-content: center; color: white; font-size: 0.73rem; font-weight: 700;" title="Hadir: <?php echo e($stats['Hadir'] ?? 0); ?>">
            <?php echo e(($stats['Hadir'] ?? 0) > 0 ? ($stats['Hadir'] ?? 0) : ''); ?>

        </div>
        <?php endif; ?>
        <?php if($pctTerlambat > 0): ?>
        <div style="width: <?php echo e($pctTerlambat); ?>%; background: #FF9800; display: flex; align-items: center; justify-content: center; color: white; font-size: 0.73rem; font-weight: 700;" title="Terlambat: <?php echo e($stats['Terlambat'] ?? 0); ?>">
            <?php echo e(($stats['Terlambat'] ?? 0) > 0 ? ($stats['Terlambat'] ?? 0) : ''); ?>

        </div>
        <?php endif; ?>
        <?php if($pctIzin > 0): ?>
        <div style="width: <?php echo e($pctIzin); ?>%; background: #f59e0b; display: flex; align-items: center; justify-content: center; color: white; font-size: 0.73rem; font-weight: 700;" title="Izin: <?php echo e($stats['Izin'] ?? 0); ?>">
            <?php echo e(($stats['Izin'] ?? 0) > 0 ? ($stats['Izin'] ?? 0) : ''); ?>

        </div>
        <?php endif; ?>
        <?php if($pctSakit > 0): ?>
        <div style="width: <?php echo e($pctSakit); ?>%; background: #3b82f6; display: flex; align-items: center; justify-content: center; color: white; font-size: 0.73rem; font-weight: 700;" title="Sakit: <?php echo e($stats['Sakit'] ?? 0); ?>">
            <?php echo e(($stats['Sakit'] ?? 0) > 0 ? ($stats['Sakit'] ?? 0) : ''); ?>

        </div>
        <?php endif; ?>
        <?php if($pctAlpa > 0): ?>
        <div style="width: <?php echo e($pctAlpa); ?>%; background: #ef4444; display: flex; align-items: center; justify-content: center; color: white; font-size: 0.73rem; font-weight: 700;" title="Alpa: <?php echo e($stats['Alpa'] ?? 0); ?>">
            <?php echo e(($stats['Alpa'] ?? 0) > 0 ? ($stats['Alpa'] ?? 0) : ''); ?>

        </div>
        <?php endif; ?>
        <?php if($pctBelum > 0): ?>
        <div style="width: <?php echo e($pctBelum); ?>%; background: #d1d5db; display: flex; align-items: center; justify-content: center; color: #6b7280; font-size: 0.73rem; font-weight: 700;" title="Belum Absen: <?php echo e($belumAbsen); ?>">
            <?php echo e($belumAbsen > 0 ? $belumAbsen : ''); ?>

        </div>
        <?php endif; ?>
    </div>
    <div style="display: flex; gap: 14px; flex-wrap: wrap; margin-top: 8px; font-size: 0.75rem; color: #6b7280;">
        <span><span style="display:inline-block;width:10px;height:10px;border-radius:2px;background:#22c55e;margin-right:3px;"></span> Hadir</span>
        <span><span style="display:inline-block;width:10px;height:10px;border-radius:2px;background:#FF9800;margin-right:3px;"></span> Terlambat</span>
        <span><span style="display:inline-block;width:10px;height:10px;border-radius:2px;background:#f59e0b;margin-right:3px;"></span> Izin</span>
        <span><span style="display:inline-block;width:10px;height:10px;border-radius:2px;background:#3b82f6;margin-right:3px;"></span> Sakit</span>
        <span><span style="display:inline-block;width:10px;height:10px;border-radius:2px;background:#ef4444;margin-right:3px;"></span> Alpa</span>
        <span><span style="display:inline-block;width:10px;height:10px;border-radius:2px;background:#d1d5db;margin-right:3px;"></span> Belum Absen</span>
    </div>
</div>

<div class="row-cards">
    <div class="card card-success">
        <h3>Hadir</h3>
        <div class="card-value"><?php echo e($stats['Hadir'] ?? 0); ?></div>
        <i class="fas fa-check-circle card-icon"></i>
    </div>
    <div class="card" style="border-top: 3px solid #FF9800;">
        <h3>Terlambat</h3>
        <div class="card-value"><?php echo e($stats['Terlambat'] ?? 0); ?></div>
        <i class="fas fa-clock card-icon" style="color: #FF9800;"></i>
    </div>
    <div class="card card-warning">
        <h3>Izin</h3>
        <div class="card-value"><?php echo e($stats['Izin'] ?? 0); ?></div>
        <i class="fas fa-info-circle card-icon"></i>
    </div>
    <div class="card card-info">
        <h3>Sakit</h3>
        <div class="card-value"><?php echo e($stats['Sakit'] ?? 0); ?></div>
        <i class="fas fa-heartbeat card-icon"></i>
    </div>
    <div class="card card-danger">
        <h3>Alpa</h3>
        <div class="card-value"><?php echo e($stats['Alpa'] ?? 0); ?></div>
        <i class="fas fa-times-circle card-icon"></i>
    </div>
    <div class="card" style="border-top: 3px solid #9ca3af;">
        <h3>Belum Absen</h3>
        <div class="card-value" style="color: <?php echo e($belumAbsen > 0 ? '#dc2626' : '#6b7280'); ?>;"><?php echo e($belumAbsen); ?></div>
        <i class="fas fa-hourglass-half card-icon" style="color: #9ca3af;"></i>
    </div>
</div>

<div class="content-box">
    <div style="margin-bottom: 14px;">
        <form method="GET" class="filter-form-inline">
            <input type="date" name="tanggal" class="form-control" value="<?php echo e(request('tanggal')); ?>">
            <input type="month" name="bulan" class="form-control" value="<?php echo e(request('bulan')); ?>" placeholder="Pilih Bulan">
            
            <select name="kelas_id" class="form-control" style="max-width: 200px;">
                <option value="">Semua Kelas</option>
                <?php $__currentLoopData = $kelasFilterList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $kelas): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($kelas->id); ?>" <?php echo e(request('kelas_id') == $kelas->id ? 'selected' : ''); ?>>
                        <?php echo e($kelas->nama_kelas); ?>

                    </option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>

            <button type="submit" class="btn btn-primary">
                <i class="fas fa-filter"></i> Filter
            </button>

            <?php if(request()->hasAny(['tanggal', 'bulan', 'kelas_id'])): ?>
                <a href="<?php echo e(route('admin.absensi-kegiatan.rekap', $kegiatan->kegiatan_id)); ?>" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Reset
                </a>
            <?php endif; ?>

            <a href="<?php echo e(route('admin.kegiatan.index')); ?>" class="btn btn-secondary" style="margin-left: auto;">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </form>
    </div>

    <?php if($absensis->count() > 0): ?>
        <?php $__currentLoopData = $absensiPerKelas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $namaKelas => $kelasAbsensis): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="content-box" style="margin-bottom: 18px;">
                <h4 style="margin: 0 0 12px; color: var(--primary-color);">
                    <i class="fas fa-school"></i> Kelas: <?php echo e($namaKelas); ?>

                    <span class="badge badge-secondary" style="font-size: 0.8rem; margin-left: 6px;">
                        <?php echo e($kelasAbsensis->count()); ?> data
                    </span>
                </h4>

                <table class="data-table">
                    <thead>
                        <tr>
                            <th style="width: 50px;">No</th>
                            <th style="width: 100px;">Tanggal</th>
                            <th style="width: 100px;">ID Santri</th>
                            <th>Nama Santri</th>
                            <th style="width: 120px; text-align: center;">Status</th>
                            <th style="width: 100px;">Metode</th>
                            <th style="width: 100px;">Waktu</th>
                            <th style="width: 120px; text-align: center;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $kelasAbsensis; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $absensi): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td><?php echo e($index + 1); ?></td>
                            <td><?php echo e($absensi->tanggal->format('d/m/Y')); ?></td>
                            <td><strong><?php echo e($absensi->id_santri); ?></strong></td>
                            <td><?php echo e($absensi->santri->nama_lengkap); ?></td>
                            <td class="text-center"><?php echo $absensi->status_badge; ?></td>
                            <td>
                                <?php if($absensi->metode_absen == 'RFID'): ?>
                                    <span class="badge badge-primary"><i class="fas fa-id-card"></i> RFID</span>
                                <?php elseif($absensi->metode_absen == 'Import_Mesin'): ?>
                                    <span class="badge" style="background: #7c3aed; color: white;"><i class="fas fa-desktop"></i> Mesin</span>
                                <?php else: ?>
                                    <span class="badge badge-secondary"><i class="fas fa-hand-pointer"></i> Manual</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo e($absensi->waktu_absen ? date('H:i', strtotime($absensi->waktu_absen)) : '-'); ?></td>
                            <td class="text-center">
                                <div style="display: flex; gap: 4px; justify-content: center;">
                                    <a href="<?php echo e(route('admin.absensi-kegiatan.edit', $absensi->id)); ?>" class="btn btn-sm btn-warning" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="<?php echo e(route('admin.absensi-kegiatan.hapus', $absensi->id)); ?>" method="POST" style="display: inline;" onsubmit="return confirm('Yakin hapus absensi <?php echo e($absensi->santri->nama_lengkap); ?>?');">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('DELETE'); ?>
                                        <button type="submit" class="btn btn-sm btn-danger" title="Hapus">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    <?php else: ?>
        <div class="empty-state">
            <i class="fas fa-clipboard"></i>
            <h3>Belum Ada Data Absensi</h3>
            <p>Silakan input absensi terlebih dahulu.</p>
            <a href="<?php echo e(route('admin.absensi-kegiatan.input', $kegiatan->kegiatan_id)); ?>" class="btn btn-success">
                <i class="fas fa-clipboard-check"></i> Input Absensi
            </a>
        </div>
    <?php endif; ?>

    
    <?php if($santriBelumAbsen->count() > 0): ?>
        <div class="content-box" style="margin-top: 18px; border-left: 4px solid #f59e0b;">
            <h4 style="margin: 0 0 12px; color: #d97706;">
                <i class="fas fa-exclamation-triangle"></i> Santri Belum Absen (<?php echo e($santriBelumAbsen->count()); ?> orang)
                <?php if(request('tanggal')): ?>
                <span style="font-size: 0.8rem; font-weight: 400; color: #6b7280; margin-left: 6px;">
                    Tanggal: <?php echo e(\Carbon\Carbon::parse(request('tanggal'))->format('d/m/Y')); ?>

                </span>
                <?php endif; ?>
            </h4>

            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 50px;">No</th>
                        <th style="width: 100px;">ID Santri</th>
                        <th>Nama Santri</th>
                        <th style="width: 150px;">Kelas</th>
                        <th style="width: 120px; text-align: center;">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $santriBelumAbsen; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $santri): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td><?php echo e($index + 1); ?></td>
                        <td><strong><?php echo e($santri->id_santri); ?></strong></td>
                        <td><?php echo e($santri->nama_lengkap); ?></td>
                        <td><?php echo e(optional(optional($santri->kelasPrimary)->kelas)->nama_kelas ?? '-'); ?></td>
                        <td class="text-center">
                            <span class="badge" style="background: #fef3c7; color: #92400e; padding: 4px 10px; border-radius: 12px; font-size: 0.8rem;">
                                <i class="fas fa-hourglass-half"></i> Belum Absen
                            </span>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\TugasAkhir\sim-pkpps\resources\views/admin/kegiatan/absensi/rekap.blade.php ENDPATH**/ ?>