


<?php $__env->startSection('content'); ?>
<style>
.page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; }
.page-header h2 { margin: 0; color: var(--primary-dark); font-size: 1.5rem; display: flex; align-items: center; gap: 10px; }
.btn-back { padding: 8px 16px; background: #6B7280; color: #fff; border: none; border-radius: 8px; font-size: 0.85rem; text-decoration: none; display: inline-flex; align-items: center; gap: 6px; transition: background 0.2s; }
.btn-back:hover { background: #4B5563; color: #fff; }

.info-box-header { background: linear-gradient(135deg, var(--primary-color), #059669); color: #fff; padding: 20px 24px; border-radius: 12px; margin-bottom: 14px; box-shadow: 0 4px 12px rgba(16,185,129,0.2); }
.info-box-header h3 { margin: 0 0 6px; font-size: 1.3rem; display: flex; align-items: center; gap: 10px; }
.info-box-header .meta { opacity: 0.9; font-size: 0.88rem; display: flex; flex-wrap: wrap; gap: 12px; margin-top: 6px; }
.info-box-header .periode-tag { background: rgba(255,255,255,0.2); padding: 3px 10px; border-radius: 20px; font-size: 0.8rem; display: inline-flex; align-items: center; gap: 5px; }

/* 6 KPI cards */
.stats-row {
    display: grid;
    grid-template-columns: repeat(6, 1fr);
    gap: 12px;
    margin-bottom: 14px;
}
@media (max-width: 1100px) { .stats-row { grid-template-columns: repeat(3, 1fr); } }
@media (max-width: 600px)  { .stats-row { grid-template-columns: repeat(2, 1fr); } }

.stat-card { background: #fff; padding: 14px 12px; border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); display: flex; align-items: center; gap: 12px; }
.stat-card .icon { font-size: 1.4rem; width: 40px; height: 40px; flex-shrink: 0; display: flex; align-items: center; justify-content: center; border-radius: 10px; }
.stat-card.hadir     .icon { background: #D1FAE5; color: #065F46; }
.stat-card.terlambat .icon { background: #FFF3E0; color: #E65100; }
.stat-card.izin      .icon { background: #FEF3C7; color: #92400E; }
.stat-card.sakit     .icon { background: #DBEAFE; color: #1E40AF; }
.stat-card.alpa      .icon { background: #FEE2E2; color: #991B1B; }
.stat-card.pulang    .icon { background: #F3E8FF; color: #6B21A8; }
.stat-card .content { flex: 1; min-width: 0; }
.stat-card .label { font-size: 0.78rem; color: var(--text-light); margin-bottom: 2px; }
.stat-card .value { font-size: 1.5rem; font-weight: 700; color: var(--primary-dark); line-height: 1.2; }

.filter-box  { background: #fff; padding: 14px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); margin-bottom: 14px; }
.filter-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 11px; }
.form-group  { margin: 0; }
.form-group label { display: block; font-size: 0.85rem; margin-bottom: 5px; color: var(--text-light); font-weight: 500; }
.form-control { width: 100%; padding: 8px 12px; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 0.85rem; }
.btn-filter  { background: var(--primary-color); color: #fff; border: none; padding: 9px 16px; border-radius: 8px; cursor: pointer; font-size: 0.85rem; display: inline-flex; align-items: center; gap: 6px; transition: all 0.2s; }
.btn-filter:hover { background: #059669; }
.btn-reset   { background: #6B7280; color: #fff; border: none; padding: 9px 12px; border-radius: 8px; font-size: 0.85rem; display: inline-flex; align-items: center; gap: 6px; text-decoration: none; }
.btn-reset:hover { background: #4B5563; color: #fff; }

.day-group   { margin-bottom: 18px; background: #fff; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); overflow: hidden; }
.day-header  { background: linear-gradient(135deg, #f0fdf4 0%, #E8F7F2 100%); padding: 14px 18px; display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #E8F7F2; cursor: pointer; transition: background 0.2s; user-select: none; }
.day-header:hover { background: linear-gradient(135deg, #E8F7F2 0%, #d1f2e8 100%); }
.day-title   { font-weight: 700; font-size: 1rem; color: var(--primary-dark); display: flex; align-items: center; gap: 8px; }
.day-title i { color: var(--primary-color); }
.day-stats   { display: flex; gap: 8px; align-items: center; flex-wrap: wrap; }
.mini-badge  { padding: 4px 10px; border-radius: 20px; font-size: 0.75rem; font-weight: 600; display: inline-flex; align-items: center; gap: 4px; }
.mini-badge.hadir     { background: #D1FAE5; color: #065F46; }
.mini-badge.terlambat { background: #FFF3E0; color: #E65100; }
.mini-badge.izin      { background: #FEF3C7; color: #92400E; }
.mini-badge.sakit     { background: #DBEAFE; color: #1E40AF; }
.mini-badge.alpa      { background: #FEE2E2; color: #991B1B; }
.mini-badge.pulang    { background: #F3E8FF; color: #6B21A8; }
.day-body table { width: 100%; border-collapse: collapse; }
.day-body table thead { background: #f8fafc; }
.day-body table th { padding: 10px 14px; text-align: left; font-weight: 600; font-size: 0.82rem; color: #64748b; border-bottom: 1px solid #e2e8f0; }
.day-body table td { padding: 9px 14px; font-size: 0.85rem; border-bottom: 1px solid #f1f5f9; }
.day-body table tbody tr:last-child td { border-bottom: none; }
.day-body table tbody tr:hover { background: #f8fafc; }
.toggle-icon { transition: transform 0.3s; font-size: 0.85rem; color: #94a3b8; }
.toggle-icon.collapsed { transform: rotate(-90deg); }

.empty-state { text-align: center; padding: 36px 20px; color: var(--text-light); background: #fff; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); }
.empty-state i { font-size: 3.5rem; margin-bottom: 14px; opacity: 0.3; display: block; }

.pagination { display: flex; justify-content: center; align-items: center; gap: 6px; margin-top: 14px; }
.pagination a, .pagination span { padding: 6px 12px; border: 1px solid #e2e8f0; border-radius: 6px; font-size: 0.82rem; text-decoration: none; color: var(--text-dark); transition: all 0.2s; }
.pagination a:hover { background: var(--primary-color); color: #fff; border-color: var(--primary-color); }
.pagination .active { background: var(--primary-color); color: #fff; border-color: var(--primary-color); font-weight: 600; }
.pagination .disabled { color: #cbd5e1; cursor: not-allowed; }

@media (max-width: 768px) {
    .day-header { flex-direction: column; align-items: flex-start; gap: 8px; }
    .day-body table th:nth-child(2), .day-body table td:nth-child(2) { display: none; }
    .day-body table th:nth-child(7), .day-body table td:nth-child(7) { display: none; }
}
</style>

<?php
    // Ambil range dari query string (diteruskan dari index)
    $defaultDari   = now()->startOfWeek(\Carbon\Carbon::MONDAY)->format('Y-m-d');
    $defaultSampai = now()->endOfWeek(\Carbon\Carbon::SUNDAY)->format('Y-m-d');
    $filterDari    = request('tanggal_dari',   $defaultDari);
    $filterSampai  = request('tanggal_sampai', $defaultSampai);
    $filterBulan   = request('bulan', '');

    if ($filterBulan) {
        $periodeLabel = 'Bulan ' . \Carbon\Carbon::parse($filterBulan . '-01')->locale('id')->isoFormat('MMMM Y');
    } else {
        $periodeLabel = \Carbon\Carbon::parse($filterDari)->locale('id')->isoFormat('D MMM Y')
                      . ' – '
                      . \Carbon\Carbon::parse($filterSampai)->locale('id')->isoFormat('D MMM Y');
    }

    // Hitung KPI dari data yang sudah difilter (semua halaman, bukan hanya halaman ini)
    // $stats sudah dihitung di controller berdasarkan filter — gunakan langsung
    // Tapi jika controller belum menghitung per filter, hitung dari koleksi paginator saat ini
    // Gunakan $stats dari controller jika ada, fallback ke hitung manual
    $statsHadir     = $stats['Hadir']     ?? 0;
    $statsTerlambat = $stats['Terlambat'] ?? 0;
    $statsIzin      = $stats['Izin']      ?? 0;
    $statsSakit     = $stats['Sakit']     ?? 0;
    $statsAlpa      = $stats['Alpa']      ?? 0;
    $statsPulang    = $stats['Pulang']    ?? 0;
?>

<div class="page-header">
    <h2><i class="fas fa-file-alt"></i> Detail Riwayat: <?php echo e($kegiatan->nama_kegiatan); ?></h2>
    <a href="<?php echo e(route('admin.riwayat-kegiatan.index', ['tanggal_dari' => $filterDari, 'tanggal_sampai' => $filterSampai])); ?>"
       class="btn-back">
        <i class="fas fa-arrow-left"></i> Kembali
    </a>
</div>


<div class="info-box-header">
    <h3><i class="fas fa-clipboard-check"></i> <?php echo e($kegiatan->nama_kegiatan); ?></h3>
    <div class="meta">
        <span><i class="fas fa-tag"></i> <?php echo e($kegiatan->kategori->nama_kategori); ?></span>
        <span><i class="fas fa-clock"></i> <?php echo e(date('H:i', strtotime($kegiatan->waktu_mulai))); ?> - <?php echo e(date('H:i', strtotime($kegiatan->waktu_selesai))); ?></span>
        <span><i class="fas fa-calendar-day"></i> <?php echo e($kegiatan->hari); ?></span>
        <?php if($kegiatan->kelasKegiatan->count() > 0): ?>
            <span><i class="fas fa-users"></i> <?php echo e($kegiatan->kelasKegiatan->pluck('nama_kelas')->implode(', ')); ?></span>
        <?php else: ?>
            <span><i class="fas fa-globe"></i> Umum</span>
        <?php endif; ?>
        <span class="periode-tag"><i class="fas fa-calendar-check"></i> <?php echo e($periodeLabel); ?></span>
    </div>
</div>


<?php if(isset($totalSantriEligible)): ?>
<div style="background: #fff; border-radius: 12px; padding: 16px 20px; margin-bottom: 14px; box-shadow: 0 2px 12px rgba(0,0,0,0.06); border-left: 4px solid #2563eb;">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px; margin-bottom: 10px;">
        <div>
            <h4 style="margin: 0; font-size: 1rem; color: #1a2332;">
                <i class="fas fa-users" style="color: #2563eb;"></i> Total Semua Santri: <strong><?php echo e($totalSantriEligible); ?></strong>
            </h4>
            <p style="margin: 4px 0 0; font-size: 0.84rem; color: #6b7280;">
                Sudah absen: <strong style="color: #059669;"><?php echo e($totalRecorded); ?></strong>
                &nbsp;·&nbsp;
                Belum absen: <strong style="color: <?php echo e(($totalSantriEligible - $totalRecorded) > 0 ? '#dc2626' : '#059669'); ?>;"><?php echo e(max(0, $totalSantriEligible - $totalRecorded)); ?></strong>
            </p>
        </div>
        <div style="text-align: right;">
            <div style="font-size: 1.5rem; font-weight: 800; color: <?php echo e($persenHadir >= 85 ? '#059669' : ($persenHadir >= 70 ? '#d97706' : '#dc2626')); ?>;">
                <?php echo e($persenHadir); ?>%
            </div>
            <div style="font-size: 0.78rem; color: #6b7280;">Kehadiran</div>
        </div>
    </div>
    
    <?php
        $pctSudah = $totalSantriEligible > 0 ? round($totalRecorded / $totalSantriEligible * 100, 1) : 0;
        $pctBelumRiwayat = 100 - $pctSudah;
    ?>
    <div style="height: 24px; background: #f3f4f6; border-radius: 12px; overflow: hidden; display: flex;">
        <?php if($pctSudah > 0): ?>
        <div style="width: <?php echo e($pctSudah); ?>%; background: linear-gradient(90deg, #22c55e, #16a34a); display: flex; align-items: center; justify-content: center; color: white; font-size: 0.73rem; font-weight: 700;" title="Sudah Absen: <?php echo e($totalRecorded); ?>">
            <?php echo e($totalRecorded); ?>

        </div>
        <?php endif; ?>
        <?php if($pctBelumRiwayat > 0 && ($totalSantriEligible - $totalRecorded) > 0): ?>
        <div style="width: <?php echo e($pctBelumRiwayat); ?>%; background: #d1d5db; display: flex; align-items: center; justify-content: center; color: #6b7280; font-size: 0.73rem; font-weight: 700;" title="Belum Absen: <?php echo e($totalSantriEligible - $totalRecorded); ?>">
            <?php echo e($totalSantriEligible - $totalRecorded); ?>

        </div>
        <?php endif; ?>
    </div>
</div>
<?php endif; ?>


<div class="stats-row">
    <div class="stat-card hadir">
        <div class="icon"><i class="fas fa-check-circle"></i></div>
        <div class="content">
            <div class="label">Hadir</div>
            <div class="value"><?php echo e($statsHadir); ?></div>
        </div>
    </div>
    <div class="stat-card terlambat">
        <div class="icon"><i class="fas fa-clock"></i></div>
        <div class="content">
            <div class="label">Terlambat</div>
            <div class="value"><?php echo e($statsTerlambat); ?></div>
        </div>
    </div>
    <div class="stat-card izin">
        <div class="icon"><i class="fas fa-envelope"></i></div>
        <div class="content">
            <div class="label">Izin</div>
            <div class="value"><?php echo e($statsIzin); ?></div>
        </div>
    </div>
    <div class="stat-card sakit">
        <div class="icon"><i class="fas fa-heartbeat"></i></div>
        <div class="content">
            <div class="label">Sakit</div>
            <div class="value"><?php echo e($statsSakit); ?></div>
        </div>
    </div>
    <div class="stat-card alpa">
        <div class="icon"><i class="fas fa-times-circle"></i></div>
        <div class="content">
            <div class="label">Alpa</div>
            <div class="value"><?php echo e($statsAlpa); ?></div>
        </div>
    </div>
    <div class="stat-card pulang">
        <div class="icon"><i class="fas fa-home"></i></div>
        <div class="content">
            <div class="label">Pulang</div>
            <div class="value"><?php echo e($statsPulang); ?></div>
        </div>
    </div>
</div>


<div class="filter-box">
    <form method="GET">
        
        <input type="hidden" name="tanggal_dari"   value="<?php echo e($filterDari); ?>">
        <input type="hidden" name="tanggal_sampai" value="<?php echo e($filterSampai); ?>">

        <div class="filter-grid">
            <div class="form-group">
                <label for="id_santri">Santri</label>
                <select name="id_santri" id="id_santri" class="form-control">
                    <option value="">-- Semua Santri --</option>
                    <?php $__currentLoopData = $santris; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($s->id_santri); ?>" <?php echo e(request('id_santri') == $s->id_santri ? 'selected' : ''); ?>>
                            <?php echo e($s->nama_lengkap); ?>

                        </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>

            <div class="form-group">
                <label for="id_kelas">Kelas</label>
                <select name="id_kelas" id="id_kelas" class="form-control">
                    <option value="">-- Semua Kelas --</option>
                    <?php $__currentLoopData = $kelasList->groupBy('kelompok.nama_kelompok'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $kelompokNama => $kelasList_group): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <optgroup label="<?php echo e($kelompokNama); ?>">
                            <?php $__currentLoopData = $kelasList_group; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $kelas): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($kelas->id); ?>" <?php echo e(request('id_kelas') == $kelas->id ? 'selected' : ''); ?>>
                                    <?php echo e($kelas->nama_kelas); ?>

                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </optgroup>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>

            <div class="form-group">
                <label for="status">Status</label>
                <select name="status" id="status" class="form-control">
                    <option value="">-- Semua Status --</option>
                    <option value="Hadir"     <?php echo e(request('status') == 'Hadir'     ? 'selected' : ''); ?>>Hadir</option>
                    <option value="Terlambat" <?php echo e(request('status') == 'Terlambat' ? 'selected' : ''); ?>>Terlambat</option>
                    <option value="Izin"      <?php echo e(request('status') == 'Izin'      ? 'selected' : ''); ?>>Izin</option>
                    <option value="Sakit"     <?php echo e(request('status') == 'Sakit'     ? 'selected' : ''); ?>>Sakit</option>
                    <option value="Alpa"      <?php echo e(request('status') == 'Alpa'      ? 'selected' : ''); ?>>Alpa</option>
                    <option value="Pulang"    <?php echo e(request('status') == 'Pulang'    ? 'selected' : ''); ?>>Pulang</option>
                </select>
            </div>

            <div class="form-group">
                <label for="tanggal_spesifik">Filter Tanggal Spesifik</label>
                <input type="date" name="tanggal_spesifik" id="tanggal_spesifik" class="form-control"
                       value="<?php echo e(request('tanggal_spesifik')); ?>"
                       min="<?php echo e($filterDari); ?>" max="<?php echo e($filterSampai); ?>">
            </div>

            <div style="display: flex; align-items: flex-end; gap: 10px;">
                <button type="submit" class="btn-filter" style="flex: 1;">
                    <i class="fas fa-filter"></i> Filter
                </button>
                <?php if(request()->hasAny(['id_santri', 'id_kelas', 'status', 'tanggal_spesifik'])): ?>
                    <a href="<?php echo e(route('admin.riwayat-kegiatan.show', $kegiatan->id)); ?>?tanggal_dari=<?php echo e($filterDari); ?>&tanggal_sampai=<?php echo e($filterSampai); ?>"
                       class="btn-reset">
                        <i class="fas fa-times"></i>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </form>
</div>


<?php if($riwayats->count() > 0): ?>

    <?php
        $grouped = $riwayats->getCollection()->groupBy(function($item) {
            return $item->tanggal->format('Y-m-d');
        })->sortKeysDesc();
    ?>

    <?php $__currentLoopData = $grouped; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tanggal => $records): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php
            $tglCarbon    = \Carbon\Carbon::parse($tanggal);
            $hariIndo     = $tglCarbon->locale('id')->isoFormat('dddd');
            $tglFormatted = $tglCarbon->locale('id')->isoFormat('D MMMM Y');

            $dayHadir     = $records->where('status', 'Hadir')->count();
            $dayTerlambat = $records->where('status', 'Terlambat')->count();
            $dayIzin      = $records->where('status', 'Izin')->count();
            $daySakit     = $records->where('status', 'Sakit')->count();
            $dayAlpa      = $records->where('status', 'Alpa')->count();
            $dayPulang    = $records->where('status', 'Pulang')->count();
            $dayTotal     = $records->count();

            // Group per kelas kegiatan (khusus) atau kelas_name santri (umum)
            $isUmum = $kegiatan->kelasKegiatan->isEmpty();
            if ($isUmum) {
                $recordsPerKelas = $records->groupBy(fn($r) =>
                    optional(optional($r->santri->kelasSantri->first())->kelas)->nama_kelas ?? 'Tanpa Kelas'
                )->sortKeys();
            } else {
                $recordsPerKelas = collect();
                $placedIds = [];
                foreach ($kegiatan->kelasKegiatan as $kls) {
                    $inKelas = $records->filter(function($r) use ($kls, &$placedIds) {
                        if (in_array($r->id, $placedIds)) return false;
                        return $r->santri->kelasSantri->contains('id_kelas', $kls->id);
                    });
                    foreach ($inKelas as $r) $placedIds[] = $r->id;
                    if ($inKelas->count() > 0) $recordsPerKelas[$kls->nama_kelas] = $inKelas;
                }
                $lainnya = $records->filter(fn($r) => !in_array($r->id, $placedIds));
                if ($lainnya->count() > 0) $recordsPerKelas['Kelas Lain'] = $lainnya;
            }
        ?>

        <div class="day-group">
            <div class="day-header" onclick="toggleDay(this)">
                <div class="day-title">
                    <i class="fas fa-calendar-day"></i>
                    <?php echo e($hariIndo); ?>, <?php echo e($tglFormatted); ?>

                    <span style="font-weight: 400; font-size: 0.85rem; color: #94a3b8; margin-left: 4px;">(<?php echo e($dayTotal); ?> santri)</span>
                </div>
                <div class="day-stats">
                    <?php if($dayHadir > 0): ?>
                        <span class="mini-badge hadir"><i class="fas fa-check"></i> <?php echo e($dayHadir); ?></span>
                    <?php endif; ?>
                    <?php if($dayTerlambat > 0): ?>
                        <span class="mini-badge terlambat"><i class="fas fa-clock"></i> <?php echo e($dayTerlambat); ?></span>
                    <?php endif; ?>
                    <?php if($dayIzin > 0): ?>
                        <span class="mini-badge izin"><i class="fas fa-envelope"></i> <?php echo e($dayIzin); ?></span>
                    <?php endif; ?>
                    <?php if($daySakit > 0): ?>
                        <span class="mini-badge sakit"><i class="fas fa-heartbeat"></i> <?php echo e($daySakit); ?></span>
                    <?php endif; ?>
                    <?php if($dayAlpa > 0): ?>
                        <span class="mini-badge alpa"><i class="fas fa-times"></i> <?php echo e($dayAlpa); ?></span>
                    <?php endif; ?>
                    <?php if($dayPulang > 0): ?>
                        <span class="mini-badge pulang"><i class="fas fa-home"></i> <?php echo e($dayPulang); ?></span>
                    <?php endif; ?>
                    <i class="fas fa-chevron-down toggle-icon"></i>
                </div>
            </div>
            <div class="day-body">
                <?php $__currentLoopData = $recordsPerKelas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $namaKelas => $kelasRecords): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div style="background: linear-gradient(135deg, #f0fdf4, #e8f5e9); padding: 8px 18px; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #e2e8f0;">
                    <span style="font-size: 0.85rem; font-weight: 600; color: #065f46;">
                        <i class="fas fa-school"></i> <?php echo e($namaKelas); ?>

                    </span>
                    <span style="background: #6FBAA5; color: white; padding: 2px 10px; border-radius: 10px; font-size: 0.75rem; font-weight: 600;">
                        <?php echo e($kelasRecords->count()); ?> santri
                    </span>
                </div>
                <table>
                    <thead>
                        <tr>
                            <th style="width: 45px;">No</th>
                            <th style="width: 90px;">ID Santri</th>
                            <th>Nama Santri</th>
                            <th style="width: 90px; text-align: center;">Status</th>
                            <th style="width: 80px; text-align: center;">Waktu</th>
                            <th style="width: 80px;">Metode</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $kelasRecords->values(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $riwayat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td><?php echo e($index + 1); ?></td>
                            <td><strong><?php echo e($riwayat->id_santri); ?></strong></td>
                            <td>
                                <a href="<?php echo e(route('admin.riwayat-kegiatan.detail-santri', $riwayat->id_santri)); ?>"
                                   style="color: var(--primary-color); text-decoration: none; font-weight: 500;">
                                    <?php echo e($riwayat->santri->nama_lengkap); ?>

                                </a>
                            </td>
                            <td style="text-align: center;"><?php echo $riwayat->status_badge; ?></td>
                            <td style="text-align: center;">
                                <?php echo e($riwayat->waktu_absen ? \Carbon\Carbon::parse($riwayat->waktu_absen)->format('H:i') : '-'); ?>

                            </td>
                            <td>
                                <?php if($riwayat->metode_absen == 'RFID'): ?>
                                    <span style="background: #DBEAFE; color: #1E40AF; padding: 3px 8px; border-radius: 10px; font-size: 0.75rem; font-weight: 600;">
                                        <i class="fas fa-id-card"></i> RFID
                                    </span>
                                <?php elseif($riwayat->metode_absen == 'Import_Mesin'): ?>
                                    <span style="background: #EDE9FE; color: #6B21A8; padding: 3px 8px; border-radius: 10px; font-size: 0.75rem; font-weight: 600;">
                                        <i class="fas fa-desktop"></i> Mesin
                                    </span>
                                <?php else: ?>
                                    <span style="background: #E5E7EB; color: #374151; padding: 3px 8px; border-radius: 10px; font-size: 0.75rem; font-weight: 600;">
                                        <i class="fas fa-hand-pointer"></i> Manual
                                    </span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

    <div class="pagination">
        <?php echo $riwayats->appends(request()->query())->links('pagination::simple-bootstrap-4'); ?>

    </div>

<?php else: ?>
    <div class="empty-state">
        <i class="fas fa-inbox"></i>
        <h3>Tidak Ada Riwayat</h3>
        <p>Tidak ada data absensi untuk periode <strong><?php echo e($periodeLabel); ?></strong>.</p>
    </div>
<?php endif; ?>

<script>
function toggleDay(header) {
    var body = header.nextElementSibling;
    var icon = header.querySelector('.toggle-icon');
    if (body.style.display === 'none') {
        body.style.display = 'block';
        icon.classList.remove('collapsed');
    } else {
        body.style.display = 'none';
        icon.classList.add('collapsed');
    }
}
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\TugasAkhir\sim-pkpps\resources\views/admin/kegiatan/riwayat/show.blade.php ENDPATH**/ ?>