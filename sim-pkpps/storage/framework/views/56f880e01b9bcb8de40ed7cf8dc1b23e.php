<?php $__env->startSection('title', 'Detail: ' . $kegiatan->nama_kegiatan); ?>

<?php $__env->startSection('content'); ?>
<style>
:root {
    --g:   #1a7a5e;
    --m:   #2bbd8e;
    --sf:  #e8f7f2;
    --tx:  #0f1923;
    --mu:  #64748b;
    --br:  #e8edf2;
    --bg:  #f4f7f9;
    --wh:  #ffffff;
    --sh:  0 2px 12px rgba(0,0,0,0.06);
    --sh2: 0 6px 28px rgba(0,0,0,0.10);
    --ra:  14px;
}

* { box-sizing: border-box; }

/* ──────────────────────────────────────────────
   BREADCRUMB / BACK NAV
────────────────────────────────────────────── */
.sd-nav {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 14px;
    font-size: 0.8rem;
    color: var(--mu);
    flex-wrap: wrap;
}
.sd-nav a {
    color: var(--g);
    text-decoration: none;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 5px 12px;
    background: var(--wh);
    border: 1.5px solid var(--br);
    border-radius: 8px;
    transition: all 0.15s;
}
.sd-nav a:hover { background: var(--sf); border-color: var(--m); }
.sd-nav-sep { color: var(--br); font-size: 1rem; }
.sd-nav-cur { font-weight: 700; color: var(--tx); }

/* ──────────────────────────────────────────────
   HERO CARD — Info Kegiatan
────────────────────────────────────────────── */
.sd-hero {
    background: linear-gradient(135deg, #0b3528 0%, #1a7a5e 60%, #28b585 100%);
    border-radius: var(--ra);
    padding: 24px 26px 20px;
    color: white;
    margin-bottom: 18px;
    position: relative;
    overflow: hidden;
}
.sd-hero::before {
    content: '';
    position: absolute; top: -60px; right: -60px;
    width: 200px; height: 200px; border-radius: 50%;
    background: rgba(255,255,255,0.05); pointer-events: none;
}
.sd-hero-inner { position: relative; z-index: 1; }
.sd-hero-name {
    font-size: 1.35rem; font-weight: 800;
    margin: 0 0 10px; line-height: 1.2;
}
.sd-hero-chips { display: flex; gap: 7px; flex-wrap: wrap; margin-bottom: 18px; }
.sd-chip {
    background: rgba(255,255,255,0.14);
    border: 1px solid rgba(255,255,255,0.22);
    padding: 4px 11px; border-radius: 20px;
    font-size: 0.77rem; font-weight: 600;
    display: inline-flex; align-items: center; gap: 5px;
}

/* ── Besar 3 KPI di hero ── */
.sd-hero-kpi {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 1px;
    background: rgba(255,255,255,0.12);
    border: 1px solid rgba(255,255,255,0.15);
    border-radius: 10px;
    overflow: hidden;
}
.sd-hkpi {
    padding: 12px 14px;
    background: rgba(0,0,0,0.08);
    text-align: center;
}
.sd-hkpi-val {
    font-size: 2rem; font-weight: 900; line-height: 1;
    margin-bottom: 3px;
}
.sd-hkpi-val.clr-great { color: #4ade80; }
.sd-hkpi-val.clr-ok    { color: #fcd34d; }
.sd-hkpi-val.clr-bad   { color: #fca5a5; }
.sd-hkpi-val.clr-white { color: #ffffff; }
.sd-hkpi-lbl { font-size: 0.75rem; opacity: 0.75; font-weight: 600; }
.sd-hkpi-sub { font-size: 0.66rem; opacity: 0.50; margin-top: 2px; }

/* ──────────────────────────────────────────────
   FILTER BAR
────────────────────────────────────────────── */
.sd-filter {
    background: var(--wh);
    border-radius: 12px;
    padding: 12px 14px;
    margin-bottom: 16px;
    box-shadow: var(--sh);
    display: flex; flex-wrap: wrap; gap: 8px; align-items: flex-end;
}
.sd-fg { display: flex; flex-direction: column; gap: 3px; }
.sd-fg label {
    font-size: 0.70rem; font-weight: 700; color: var(--mu);
    text-transform: uppercase; letter-spacing: 0.5px;
}
.sd-presets { display: flex; gap: 4px; flex-wrap: wrap; }
.sd-pbtn {
    padding: 6px 11px; border: 1.5px solid var(--br);
    border-radius: 8px; background: var(--wh);
    font-size: 0.78rem; font-weight: 600; color: var(--mu);
    cursor: pointer; transition: all 0.15s; white-space: nowrap;
}
.sd-pbtn:hover  { border-color: var(--m); color: var(--g); background: var(--sf); }
.sd-pbtn.active { border-color: var(--g); background: var(--g); color: white; }
.sd-drange { display: flex; align-items: center; gap: 5px; }
.sd-drange input[type=date] {
    padding: 6px 9px; border: 1.5px solid var(--br);
    border-radius: 8px; font-size: 0.79rem; color: var(--tx);
}
.sd-drange input[type=date]:focus { outline: none; border-color: var(--m); }
.sd-drange span { font-size: 0.78rem; font-weight: 600; color: var(--mu); }
.sd-apply {
    padding: 7px 14px; background: var(--g); color: white;
    border: none; border-radius: 8px;
    font-size: 0.8rem; font-weight: 700; cursor: pointer;
    display: inline-flex; align-items: center; gap: 5px;
}
.sd-apply:hover { background: #155c47; }
.sd-finfo {
    font-size: 0.75rem; color: var(--mu);
    padding: 5px 9px; background: var(--bg);
    border-radius: 8px; border: 1px solid var(--br);
    display: flex; align-items: center; gap: 4px; align-self: center;
}

/* ──────────────────────────────────────────────
   6 STAT PILL CARDS
────────────────────────────────────────────── */
.sd-pills {
    display: grid;
    grid-template-columns: repeat(6, 1fr);
    gap: 8px;
    margin-bottom: 18px;
}
.sd-pill-card {
    background: var(--wh);
    border-radius: 11px;
    padding: 13px 8px 11px;
    box-shadow: var(--sh);
    text-align: center;
    border-bottom: 3px solid transparent;
    transition: transform 0.15s, box-shadow 0.15s;
}
.sd-pill-card:hover { transform: translateY(-2px); box-shadow: var(--sh2); }
.sd-pill-card.p-green  { border-bottom-color: #2bbd8e; }
.sd-pill-card.p-amber  { border-bottom-color: #f59e0b; }
.sd-pill-card.p-blue   { border-bottom-color: #3b82f6; }
.sd-pill-card.p-purple { border-bottom-color: #8b5cf6; }
.sd-pill-card.p-red    { border-bottom-color: #e53e3e; }
.sd-pill-card.p-teal   { border-bottom-color: #0d9488; }
.sd-pill-icon {
    width: 32px; height: 32px; border-radius: 8px;
    display: flex; align-items: center; justify-content: center;
    font-size: 0.85rem; margin: 0 auto 7px;
}
.p-green  .sd-pill-icon { background: #d1fae5; color: #059669; }
.p-amber  .sd-pill-icon { background: #fef3c7; color: #d97706; }
.p-blue   .sd-pill-icon { background: #dbeafe; color: #2563eb; }
.p-purple .sd-pill-icon { background: #ede9fe; color: #7c3aed; }
.p-red    .sd-pill-icon { background: #fee2e2; color: #dc2626; }
.p-teal   .sd-pill-icon { background: #ccfbf1; color: #0f766e; }
.sd-pill-val { font-size: 1.6rem; font-weight: 800; color: var(--tx); line-height: 1; }
.sd-pill-lbl { font-size: 0.71rem; color: var(--mu); margin-top: 3px; font-weight: 500; }

/* ──────────────────────────────────────────────
   INSIGHT BANNER (pesan sederhana untuk santri)
────────────────────────────────────────────── */
.sd-insight {
    border-radius: 11px;
    padding: 13px 16px;
    margin-bottom: 18px;
    display: flex; align-items: center; gap: 12px;
    font-size: 0.84rem; font-weight: 600;
}
.sd-insight.good { background: #d1fae5; color: #065f46; border: 1px solid #a7f3d0; }
.sd-insight.warn { background: #fef3c7; color: #92400e; border: 1px solid #fde68a; }
.sd-insight.bad  { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }
.sd-insight-ic { font-size: 1.4rem; flex-shrink: 0; }
.sd-insight-text b { display: block; font-size: 0.9rem; margin-bottom: 2px; }

/* ──────────────────────────────────────────────
   2 KOLOM — Chart & Distribusi
────────────────────────────────────────────── */
.sd-row2 {
    display: grid;
    grid-template-columns: 3fr 2fr;
    gap: 14px;
    margin-bottom: 18px;
}
.sd-card {
    background: var(--wh);
    border-radius: 12px;
    padding: 18px;
    box-shadow: var(--sh);
}
.sd-card-title {
    font-size: 0.85rem; font-weight: 700; color: var(--tx);
    margin-bottom: 14px;
    display: flex; align-items: center; gap: 7px;
}
.sd-card-title .badge {
    margin-left: auto;
    font-size: 0.70rem; color: var(--mu); font-weight: 500;
    background: var(--bg); padding: 3px 8px; border-radius: 6px;
    border: 1px solid var(--br);
}

/* Progress distribusi */
.sd-dist-list { display: flex; flex-direction: column; gap: 10px; }
.sd-dist-row  { display: flex; align-items: center; gap: 9px; }
.sd-dist-lbl  { font-size: 0.77rem; font-weight: 600; color: var(--tx); min-width: 72px; }
.sd-dist-track{ flex: 1; height: 9px; background: #f1f5f9; border-radius: 5px; overflow: hidden; }
.sd-dist-fill { height: 100%; border-radius: 5px; transition: width 0.6s ease; }
.sd-dist-val  { font-size: 0.73rem; font-weight: 700; color: var(--tx); min-width: 46px; text-align: right; }
.sd-dist-pct  { font-size: 0.67rem; color: var(--mu); font-weight: 400; }

/* ──────────────────────────────────────────────
   TABEL RIWAYAT
────────────────────────────────────────────── */
.sd-table-wrap {
    background: var(--wh);
    border-radius: 12px;
    box-shadow: var(--sh);
    overflow: hidden;
}
.sd-table-header {
    padding: 13px 16px;
    border-bottom: 1px solid var(--br);
    display: flex; align-items: center; gap: 8px;
}
.sd-table-header-title {
    font-size: 0.86rem; font-weight: 700; color: var(--tx);
    display: flex; align-items: center; gap: 7px;
}
.sd-count-badge {
    margin-left: auto;
    background: var(--sf); color: var(--g);
    padding: 3px 10px; border-radius: 8px;
    font-size: 0.72rem; font-weight: 700;
}
.sd-table { width: 100%; border-collapse: collapse; }
.sd-table thead tr { background: var(--bg); }
.sd-table th {
    padding: 9px 14px;
    text-align: left;
    font-size: 0.72rem; font-weight: 700; color: var(--mu);
    text-transform: uppercase; letter-spacing: 0.5px;
    border-bottom: 1px solid var(--br);
}
.sd-table td {
    padding: 10px 14px;
    font-size: 0.83rem;
    border-bottom: 1px solid #f5f8fa;
    color: var(--tx);
    vertical-align: middle;
}
.sd-table tbody tr:last-child td { border-bottom: none; }
.sd-table tbody tr:hover { background: #fafcfd; }

/* Row highlight berdasarkan status */
.sd-table tbody tr.row-alpa   { background: #fff5f5; }
.sd-table tbody tr.row-alpa:hover { background: #fee2e2; }

/* Status badge di tabel */
.sd-status {
    padding: 4px 11px; border-radius: 20px;
    font-size: 0.76rem; font-weight: 700;
    display: inline-flex; align-items: center; gap: 4px;
}
.sd-status.hadir     { background: #d1fae5; color: #065f46; }
.sd-status.terlambat { background: #fef3c7; color: #92400e; }
.sd-status.izin      { background: #dbeafe; color: #1e40af; }
.sd-status.sakit     { background: #ede9fe; color: #5b21b6; }
.sd-status.alpa      { background: #fee2e2; color: #991b1b; }
.sd-status.pulang    { background: #ccfbf1; color: #0f766e; }

/* Kolom tanggal + hari */
.sd-date-main { font-weight: 700; font-size: 0.84rem; color: var(--tx); }
.sd-date-day  { font-size: 0.71rem; color: var(--mu); margin-top: 1px; }

/* Metode chip */
.sd-metode {
    display: inline-flex; align-items: center; gap: 5px;
    background: var(--bg); border: 1px solid var(--br);
    padding: 3px 9px; border-radius: 7px;
    font-size: 0.74rem; font-weight: 600; color: var(--mu);
}

.sd-empty {
    text-align: center; padding: 44px 20px;
    color: var(--mu); font-size: 0.84rem;
}
.sd-empty i { font-size: 2.6rem; opacity: 0.15; display: block; margin-bottom: 10px; }

/* ──────────────────────────────────────────────
   PAGINATION
────────────────────────────────────────────── */
.sd-pagination { padding: 12px 16px; border-top: 1px solid var(--br); }

@media (max-width: 720px) {
    .sd-pills { grid-template-columns: repeat(3, 1fr); }
    .sd-row2  { grid-template-columns: 1fr; }
    .sd-hero-kpi { grid-template-columns: repeat(3, 1fr); }
    .sd-table th:nth-child(1),
    .sd-table td:nth-child(1) { display: none; }
}
</style>


<div class="sd-nav">
    <a href="<?php echo e(route('santri.kegiatan.index')); ?>?tab=<?php echo e($fromTab ?? 'jadwal'); ?>">
        <i class="fas fa-arrow-left"></i> Kembali ke Jadwal
    </a>
    <span class="sd-nav-sep">›</span>
    <span class="sd-nav-cur"><?php echo e($kegiatan->nama_kegiatan); ?></span>
</div>


<div class="sd-hero">
    <div class="sd-hero-inner">
        <h1 class="sd-hero-name">
            <i class="fas fa-clipboard-list" style="opacity:0.7;margin-right:4px;"></i>
            <?php echo e($kegiatan->nama_kegiatan); ?>

        </h1>
        <div class="sd-hero-chips">
            <span class="sd-chip"><i class="fas fa-tag"></i> <?php echo e($kegiatan->kategori->nama_kategori); ?></span>
            <span class="sd-chip"><i class="fas fa-calendar-day"></i> Setiap <?php echo e($kegiatan->hari); ?></span>
            <span class="sd-chip">
                <i class="fas fa-clock"></i>
                <?php echo e(date('H:i', strtotime($kegiatan->waktu_mulai))); ?> – <?php echo e(date('H:i', strtotime($kegiatan->waktu_selesai))); ?>

            </span>
            <?php if($kegiatan->materi): ?>
                <span class="sd-chip"><i class="fas fa-book-open"></i> <?php echo e(Str::limit($kegiatan->materi, 35)); ?></span>
            <?php endif; ?>
        </div>

        
        <div class="sd-hero-kpi">
            <div class="sd-hkpi">
                <?php
                    $clr = $persentaseHadir >= 85 ? 'clr-great' : ($persentaseHadir >= 70 ? 'clr-ok' : 'clr-bad');
                ?>
                <div class="sd-hkpi-val <?php echo e($clr); ?>"><?php echo e($persentaseHadir); ?>%</div>
                <div class="sd-hkpi-lbl">Tingkat Kehadiran</div>
                <div class="sd-hkpi-sub">hadir + terlambat</div>
            </div>
            <div class="sd-hkpi">
                <div class="sd-hkpi-val clr-white"><?php echo e($hadirEfektif); ?></div>
                <div class="sd-hkpi-lbl">Kali Hadir</div>
                <div class="sd-hkpi-sub">dari <?php echo e($totalAbsensi); ?> tercatat</div>
            </div>
            <div class="sd-hkpi">
                <div class="sd-hkpi-val <?php echo e(($stats['Alpa'] ?? 0) > 0 ? 'clr-bad' : 'clr-great'); ?>">
                    <?php echo e($stats['Alpa'] ?? 0); ?>

                </div>
                <div class="sd-hkpi-lbl">Kali Alpa</div>
                <div class="sd-hkpi-sub">tidak masuk tanpa izin</div>
            </div>
        </div>
    </div>
</div>


<form method="GET" action="<?php echo e(route('santri.kegiatan.show', $kegiatan->kegiatan_id)); ?>" id="filterForm">
    <input type="hidden" name="from_tab"  value="<?php echo e($fromTab ?? 'jadwal'); ?>">
    <input type="hidden" name="preset"    id="hPreset"   value="<?php echo e($preset); ?>">
    <input type="hidden" name="date_from" id="hDateFrom" value="<?php echo e(request('date_from')); ?>">
    <input type="hidden" name="date_to"   id="hDateTo"   value="<?php echo e(request('date_to')); ?>">

    <div class="sd-filter">
        <div class="sd-fg">
            <label><i class="fas fa-bolt"></i> Tampilkan periode</label>
            <div class="sd-presets" id="presetBtns">
                <?php $__currentLoopData = [
                    'this_week'  => 'Minggu Ini',
                    'this_month' => 'Bulan Ini',
                    'last_month' => 'Bulan Lalu',
                    'last_3m'    => '3 Bulan',
                    'all'        => 'Semua Data',
                ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $v => $l): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <button type="button"
                            class="sd-pbtn <?php echo e($preset === $v ? 'active' : ''); ?>"
                            onclick="setPreset('<?php echo e($v); ?>')"><?php echo e($l); ?></button>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
        <div class="sd-fg">
            <label><i class="fas fa-calendar-alt"></i> Rentang kustom</label>
            <div class="sd-drange">
                <input type="date" id="inpFrom" value="<?php echo e($dateFrom->format('Y-m-d')); ?>" onchange="setCustom()">
                <span>—</span>
                <input type="date" id="inpTo"   value="<?php echo e($dateTo->format('Y-m-d')); ?>"   onchange="setCustom()">
            </div>
        </div>
        <button type="submit" class="sd-apply"><i class="fas fa-sync-alt"></i> Terapkan</button>
        <div class="sd-finfo">
            <i class="fas fa-calendar-check" style="color:var(--m);"></i>
            <?php echo e($dateFrom->locale('id')->isoFormat('D MMM YYYY')); ?> –
            <?php echo e($dateTo->locale('id')->isoFormat('D MMM YYYY')); ?>

        </div>
    </div>
</form>


<div class="sd-pills">
    <?php
        $pillData = [
            ['label' => 'Hadir',     'val' => $stats['Hadir']     ?? 0, 'icon' => 'check-circle',  'cls' => 'p-green'],
            ['label' => 'Terlambat', 'val' => $stats['Terlambat'] ?? 0, 'icon' => 'clock',          'cls' => 'p-amber'],
            ['label' => 'Izin',      'val' => $stats['Izin']      ?? 0, 'icon' => 'info-circle',    'cls' => 'p-blue'],
            ['label' => 'Sakit',     'val' => $stats['Sakit']     ?? 0, 'icon' => 'heartbeat',      'cls' => 'p-purple'],
            ['label' => 'Alpa',      'val' => $stats['Alpa']      ?? 0, 'icon' => 'times-circle',   'cls' => 'p-red'],
            ['label' => 'Pulang',    'val' => $stats['Pulang']    ?? 0, 'icon' => 'home',            'cls' => 'p-teal'],
        ];
    ?>
    <?php $__currentLoopData = $pillData; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <div class="sd-pill-card <?php echo e($p['cls']); ?>">
        <div class="sd-pill-icon"><i class="fas fa-<?php echo e($p['icon']); ?>"></i></div>
        <div class="sd-pill-val"><?php echo e($p['val']); ?></div>
        <div class="sd-pill-lbl"><?php echo e($p['label']); ?></div>
    </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>


<?php
    $alpaCount  = $stats['Alpa'] ?? 0;
    $insightCls = $persentaseHadir >= 85 ? 'good' : ($persentaseHadir >= 65 ? 'warn' : 'bad');
    if ($insightCls === 'good') {
        $insightIcon = '🎉';
        $insightJudul = 'Kehadiranmu sangat baik!';
        $insightMsg   = "Kamu hadir $hadirEfektif dari $totalAbsensi sesi yang tercatat. Pertahankan ya!";
    } elseif ($insightCls === 'warn') {
        $insightIcon = '⚠️';
        $insightJudul = 'Kehadiranmu perlu ditingkatkan.';
        $insightMsg   = "Tingkat kehadiranmu $persentaseHadir% dalam periode ini. Yuk lebih rajin lagi!";
    } else {
        $insightIcon = '❗';
        $insightJudul = 'Kehadiranmu sangat rendah.';
        $insightMsg   = "Hanya $persentaseHadir% dari sesi yang dihadiri. Ada $alpaCount kali alpa. Segera konsultasikan ke pembimbing.";
    }
?>
<?php if($totalAbsensi > 0): ?>
<div class="sd-insight <?php echo e($insightCls); ?>">
    <div class="sd-insight-ic"><?php echo e($insightIcon); ?></div>
    <div class="sd-insight-text">
        <b><?php echo e($insightJudul); ?></b>
        <?php echo e($insightMsg); ?>

    </div>
</div>
<?php endif; ?>


<?php
    // $absensiByDate sudah dikirim dari controller: ['Y-m-d' => 'Status', ...]
    // Mencakup SEMUA data dalam range, bukan hanya halaman saat ini

    $statusMeta = [
        'Hadir'     => ['bg'=>'#d1fae5','text'=>'#065f46','border'=>'#6ee7b7','dot'=>'#2bbd8e','icon'=>'✓'],
        'Terlambat' => ['bg'=>'#fef3c7','text'=>'#92400e','border'=>'#fcd34d','dot'=>'#f59e0b','icon'=>'⏰'],
        'Izin'      => ['bg'=>'#dbeafe','text'=>'#1e40af','border'=>'#93c5fd','dot'=>'#3b82f6','icon'=>'I'],
        'Sakit'     => ['bg'=>'#ede9fe','text'=>'#5b21b6','border'=>'#c4b5fd','dot'=>'#8b5cf6','icon'=>'🏥'],
        'Alpa'      => ['bg'=>'#fee2e2','text'=>'#991b1b','border'=>'#fca5a5','dot'=>'#e53e3e','icon'=>'✗'],
        'Pulang'    => ['bg'=>'#ccfbf1','text'=>'#0f766e','border'=>'#5eead4','dot'=>'#0d9488','icon'=>'🏠'],
    ];

    $distItems = [
        ['label'=>'Hadir',     'val'=>$stats['Hadir']     ?? 0, 'color'=>'#2bbd8e', 'emoji'=>'✅'],
        ['label'=>'Terlambat', 'val'=>$stats['Terlambat'] ?? 0, 'color'=>'#f59e0b', 'emoji'=>'⏰'],
        ['label'=>'Izin',      'val'=>$stats['Izin']      ?? 0, 'color'=>'#3b82f6', 'emoji'=>'ℹ️'],
        ['label'=>'Sakit',     'val'=>$stats['Sakit']     ?? 0, 'color'=>'#8b5cf6', 'emoji'=>'🏥'],
        ['label'=>'Alpa',      'val'=>$stats['Alpa']      ?? 0, 'color'=>'#e53e3e', 'emoji'=>'❌'],
        ['label'=>'Pulang',    'val'=>$stats['Pulang']    ?? 0, 'color'=>'#0d9488', 'emoji'=>'🏠'],
    ];

    $hariSingkat = ['Monday'=>'Sen','Tuesday'=>'Sel','Wednesday'=>'Rab',
                    'Thursday'=>'Kam','Friday'=>'Jum','Saturday'=>'Sab','Sunday'=>'Ahd'];
?>

<style>
/* ── Kalender Visual ── */
.sd-cal-wrap {
    background: var(--wh); border-radius: 12px;
    padding: 18px; box-shadow: var(--sh); margin-bottom: 14px;
}
.sd-cal-title {
    font-size: 0.85rem; font-weight: 700; color: var(--tx);
    margin-bottom: 6px; display: flex; align-items: center; gap: 7px;
}
.sd-cal-hint {
    font-size: 0.75rem; color: var(--mu);
    margin-bottom: 14px; padding: 7px 11px;
    background: var(--bg); border-radius: 7px;
    border-left: 3px solid var(--m);
    display: flex; align-items: center; gap: 6px;
}
.sd-legend {
    display: flex; flex-wrap: wrap; gap: 8px; margin-bottom: 14px;
}
.sd-legend-item {
    display: inline-flex; align-items: center; gap: 5px;
    font-size: 0.74rem; font-weight: 600; color: var(--tx);
}
.sd-legend-dot { width: 10px; height: 10px; border-radius: 3px; flex-shrink: 0; }

.sd-cal-grid { display: flex; flex-wrap: wrap; gap: 6px; }
.sd-cal-slot {
    width: 50px; border-radius: 9px; padding: 7px 4px 6px;
    text-align: center; border: 1.5px solid #e8edf2;
    background: #f8fafc; transition: transform 0.12s, box-shadow 0.12s;
    cursor: default;
}
.sd-cal-slot:hover { transform: translateY(-2px); box-shadow: 0 5px 16px rgba(0,0,0,0.13); z-index: 5; }
.sd-cal-slot-icon  { font-size: 1rem; line-height: 1; margin-bottom: 3px; display: block; }
.sd-cal-slot-date  { font-size: 0.69rem; font-weight: 800; line-height: 1; display: block; }
.sd-cal-slot-day   { font-size: 0.60rem; font-weight: 500; opacity: 0.65; display: block; margin-top: 1px; }
.sd-cal-slot-lbl   { font-size: 0.58rem; font-weight: 700; display: block; margin-top: 3px;
                      white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.sd-cal-slot.empty { background: #f1f5f9; border-color: #e2e8f0; opacity: 0.5; }
.sd-cal-slot.is-today { outline: 2px solid #f5a623; outline-offset: 2px; }

/* ── Distribusi ── */
.sd-dist2-wrap {
    background: var(--wh); border-radius: 12px;
    padding: 18px; box-shadow: var(--sh); margin-bottom: 18px;
}
.sd-dist2-title {
    font-size: 0.85rem; font-weight: 700; color: var(--tx);
    margin-bottom: 14px; display: flex; align-items: center; gap: 7px;
}
.sd-dist2-list { display: flex; flex-direction: column; gap: 13px; }
.sd-dist2-row  { display: flex; align-items: center; gap: 10px; }
.sd-dist2-emoji { font-size: 1.05rem; width: 22px; text-align: center; flex-shrink: 0; }
.sd-dist2-lbl  { font-size: 0.82rem; font-weight: 700; color: var(--tx); min-width: 76px; }
.sd-dist2-track { flex: 1; height: 12px; background: #f1f5f9; border-radius: 6px; overflow: hidden; }
.sd-dist2-fill  { height: 100%; border-radius: 6px; }
.sd-dist2-val  { font-size: 0.82rem; font-weight: 800; color: var(--tx); min-width: 30px; text-align: right; }
.sd-dist2-pct  { font-size: 0.72rem; color: var(--mu); min-width: 38px; text-align: right; }
</style>


<div class="sd-cal-wrap">
    <div class="sd-cal-title">
        <i class="fas fa-calendar-alt" style="color:var(--m);"></i>
        Kalender Kehadiran
        <span style="margin-left:auto;font-size:0.71rem;color:var(--mu);font-weight:500;background:var(--bg);border:1px solid var(--br);padding:3px 9px;border-radius:7px;">
            <?php echo e($dateFrom->locale('id')->isoFormat('D MMM')); ?> – <?php echo e($dateTo->locale('id')->isoFormat('D MMM YYYY')); ?>

        </span>
    </div>
    <div class="sd-cal-hint">
        <i class="fas fa-lightbulb" style="color:var(--m);flex-shrink:0;"></i>
        Tiap kotak = 1 hari dalam periode filter. Warna = status absensimu. Kotak abu-abu = tidak ada catatan absensi di hari itu.
    </div>

    <div class="sd-legend">
        <?php $__currentLoopData = $statusMeta; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sLabel => $sMeta): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="sd-legend-item">
                <div class="sd-legend-dot" style="background:<?php echo e($sMeta['dot']); ?>;"></div> <?php echo e($sLabel); ?>

            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <div class="sd-legend-item">
            <div class="sd-legend-dot" style="background:#e2e8f0;"></div> Tidak ada catatan
        </div>
    </div>

    <div class="sd-cal-grid">
        <?php $cursor = $dateFrom->copy(); ?>
        <?php while($cursor->lte($dateTo)): ?>
            <?php
                $tgl    = $cursor->format('Y-m-d');
                $status = $absensiByDate[$tgl] ?? null;
                $meta   = $status ? ($statusMeta[$status] ?? null) : null;
                $hariId = $hariSingkat[$cursor->format('l')] ?? substr($cursor->format('l'), 0, 3);
                $tglFmt = $cursor->format('d/m');
                $isToday = $tgl === \Carbon\Carbon::today()->format('Y-m-d');
            ?>
            <?php if($meta): ?>
                <div class="sd-cal-slot <?php echo e($isToday ? 'is-today' : ''); ?>"
                     style="background:<?php echo e($meta['bg']); ?>;border-color:<?php echo e($meta['border']); ?>;color:<?php echo e($meta['text']); ?>;"
                     title="<?php echo e($cursor->locale('id')->isoFormat('dddd, D MMMM YYYY')); ?> — <?php echo e($status); ?>">
                    <span class="sd-cal-slot-icon"><?php echo e($meta['icon']); ?></span>
                    <span class="sd-cal-slot-date"><?php echo e($tglFmt); ?></span>
                    <span class="sd-cal-slot-day"><?php echo e($hariId); ?></span>
                    <span class="sd-cal-slot-lbl"><?php echo e($status); ?></span>
                </div>
            <?php else: ?>
                <div class="sd-cal-slot empty <?php echo e($isToday ? 'is-today' : ''); ?>"
                     title="<?php echo e($cursor->locale('id')->isoFormat('dddd, D MMMM YYYY')); ?> — Tidak ada catatan">
                    <span class="sd-cal-slot-icon" style="color:#cbd5e1;">–</span>
                    <span class="sd-cal-slot-date" style="color:#94a3b8;"><?php echo e($tglFmt); ?></span>
                    <span class="sd-cal-slot-day" style="color:#94a3b8;"><?php echo e($hariId); ?></span>
                    <span class="sd-cal-slot-lbl" style="color:#d1d5db;">Belum</span>
                </div>
            <?php endif; ?>
            <?php $cursor->addDay(); ?>
        <?php endwhile; ?>
    </div>
</div>


<div class="sd-dist2-wrap">
    <div class="sd-dist2-title">
        <i class="fas fa-align-left" style="color:#f5a623;"></i>
        Perincian Status
        <span style="margin-left:auto;font-size:0.71rem;color:var(--mu);font-weight:500;background:var(--bg);border:1px solid var(--br);padding:3px 9px;border-radius:7px;"><?php echo e($totalAbsensi); ?> total sesi tercatat</span>
    </div>
    <?php if($totalAbsensi > 0): ?>
        <div class="sd-dist2-list">
            <?php $__currentLoopData = $distItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $d): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php if($d['val'] > 0 || in_array($d['label'], ['Hadir','Alpa'])): ?>
                <div class="sd-dist2-row">
                    <div class="sd-dist2-emoji"><?php echo e($d['emoji']); ?></div>
                    <div class="sd-dist2-lbl"><?php echo e($d['label']); ?></div>
                    <div class="sd-dist2-track">
                        <div class="sd-dist2-fill" style="width:<?php echo e(round($d['val']/$totalAbsensi*100)); ?>%;background:<?php echo e($d['color']); ?>;"></div>
                    </div>
                    <div class="sd-dist2-val"><?php echo e($d['val']); ?>×</div>
                    <div class="sd-dist2-pct"><?php echo e(round($d['val']/$totalAbsensi*100)); ?>%</div>
                </div>
                <?php endif; ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    <?php else: ?>
        <div class="sd-empty"><i class="fas fa-inbox"></i><p>Belum ada data absensi.</p></div>
    <?php endif; ?>
</div>


<div class="sd-table-wrap">

    
    <div class="sd-table-header" style="flex-wrap:wrap;gap:8px;">
        <div class="sd-table-header-title">
            <i class="fas fa-history" style="color:var(--m);"></i>
            Rekap Kehadiran
        </div>
        
        <div style="display:flex;align-items:center;gap:6px;flex-wrap:wrap;flex:1;">
            <span style="
                background:#e8f7f2; color:#1a7a5e;
                border:1px solid #a7f3d0;
                padding:4px 11px; border-radius:20px;
                font-size:0.76rem; font-weight:700;
                display:inline-flex; align-items:center; gap:5px;
            ">
                <i class="fas fa-calendar-check"></i>
                <?php echo e($dateFrom->locale('id')->isoFormat('D MMM YYYY')); ?>

                &nbsp;–&nbsp;
                <?php echo e($dateTo->locale('id')->isoFormat('D MMM YYYY')); ?>

            </span>
            <?php
                $presetLabels = [
                    'this_week'  => 'Minggu Ini',
                    'this_month' => 'Bulan Ini',
                    'last_month' => 'Bulan Lalu',
                    'last_3m'    => '3 Bulan Terakhir',
                    'all'        => 'Semua Data',
                    'custom'     => 'Kustom',
                ];
            ?>
            <?php if(isset($presetLabels[$preset])): ?>
                <span style="font-size:0.73rem;color:var(--mu);">
                    (<?php echo e($presetLabels[$preset]); ?>)
                </span>
            <?php endif; ?>
        </div>
        <span class="sd-count-badge"><?php echo e($riwayats->total()); ?> sesi</span>
    </div>

    
    <div style="
        padding:9px 16px;
        background:#f8fafc;
        border-bottom:1px solid var(--br);
        font-size:0.78rem; color:var(--mu);
        display:flex; align-items:center; gap:6px;
    ">
        <i class="fas fa-info-circle" style="color:var(--m);"></i>
        Menampilkan <strong style="color:var(--tx);"><?php echo e($riwayats->total()); ?> catatan absensi</strong>
        kegiatan <strong style="color:var(--tx);"><?php echo e($kegiatan->nama_kegiatan); ?></strong>
        dari tanggal <strong style="color:var(--tx);"><?php echo e($dateFrom->locale('id')->isoFormat('D MMM YYYY')); ?></strong>
        sampai <strong style="color:var(--tx);"><?php echo e($dateTo->locale('id')->isoFormat('D MMM YYYY')); ?></strong>.
        <?php if($riwayats->hasPages()): ?>
            Halaman <?php echo e($riwayats->currentPage()); ?> dari <?php echo e($riwayats->lastPage()); ?>.
        <?php endif; ?>
    </div>

    <?php if($riwayats->count() > 0): ?>
    <div style="overflow-x:auto;">
        <table class="sd-table">
            <thead>
                <tr>
                    <th style="width:42px;">#</th>
                    <th>Tanggal</th>
                    <th>Jam Absen</th>
                    <th>Status</th>
                    <th>Cara Absen</th>
                </tr>
            </thead>
            <tbody>
                <?php $__currentLoopData = $riwayats; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $idx => $absensi): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php
                    $statusLower = strtolower($absensi->status);
                    $rowCls = $statusLower === 'alpa' ? 'row-alpa' : '';
                    $metode = $absensi->metode_absen ?? '';
                    $metodeLabel = $metode === 'Import_Mesin' ? 'Mesin' : ($metode ?: 'Manual');
                    $metodeIcon  = $metode === 'RFID' ? 'id-card' : ($metode === 'Import_Mesin' ? 'desktop' : 'hand-pointer');
                ?>
                <tr class="<?php echo e($rowCls); ?>">
                    <td style="color:#9ca3af;font-size:0.74rem;text-align:center;">
                        <?php echo e($riwayats->firstItem() + $idx); ?>

                    </td>
                    <td>
                        <div class="sd-date-main">
                            <?php echo e(\Carbon\Carbon::parse($absensi->tanggal)->format('d M Y')); ?>

                        </div>
                        <div class="sd-date-day">
                            <?php echo e(\Carbon\Carbon::parse($absensi->tanggal)->locale('id')->isoFormat('dddd')); ?>

                        </div>
                    </td>
                    <td>
                        <?php if($absensi->waktu_absen): ?>
                            <span style="font-weight:700;color:var(--tx);font-size:0.9rem;">
                                <?php echo e(\Carbon\Carbon::parse($absensi->waktu_absen)->format('H:i')); ?>

                            </span>
                            <span style="font-size:0.7rem;color:var(--mu);display:block;">WIB</span>
                        <?php else: ?>
                            <span style="color:#d1d5db;font-size:0.8rem;">—</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <span class="sd-status <?php echo e($statusLower); ?>">
                            <?php if($absensi->status === 'Hadir'): ?>         <i class="fas fa-check"></i>
                            <?php elseif($absensi->status === 'Terlambat'): ?> <i class="fas fa-clock"></i>
                            <?php elseif($absensi->status === 'Izin'): ?>      <i class="fas fa-info"></i>
                            <?php elseif($absensi->status === 'Sakit'): ?>     <i class="fas fa-heartbeat"></i>
                            <?php elseif($absensi->status === 'Alpa'): ?>      <i class="fas fa-times"></i>
                            <?php elseif($absensi->status === 'Pulang'): ?>    <i class="fas fa-home"></i>
                            <?php endif; ?>
                            <?php echo e($absensi->status); ?>

                        </span>
                    </td>
                    <td>
                        <span class="sd-metode">
                            <i class="fas fa-<?php echo e($metodeIcon); ?>"></i> <?php echo e($metodeLabel); ?>

                        </span>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
    </div>
    <div class="sd-pagination">
        <?php echo e($riwayats->appends(request()->query())->links()); ?>

    </div>
    <?php else: ?>
    <div class="sd-empty">
        <i class="fas fa-inbox"></i>
        <p>Belum ada catatan absensi dalam periode ini.</p>
        <p style="font-size:0.76rem;margin-top:4px;">
            Periode aktif: <strong><?php echo e($dateFrom->locale('id')->isoFormat('D MMM YYYY')); ?> – <?php echo e($dateTo->locale('id')->isoFormat('D MMM YYYY')); ?></strong>.
            Coba pilih periode yang lebih luas di filter atas.
        </p>
    </div>
    <?php endif; ?>
</div>

<script>
function setPreset(val) {
    document.querySelectorAll('.sd-pbtn').forEach(b => b.classList.remove('active'));
    event.target.classList.add('active');
    document.getElementById('hPreset').value   = val;
    document.getElementById('hDateFrom').value = '';
    document.getElementById('hDateTo').value   = '';
    document.getElementById('filterForm').submit();
}
function setCustom() {
    document.getElementById('hPreset').value   = '';
    document.getElementById('hDateFrom').value = document.getElementById('inpFrom').value;
    document.getElementById('hDateTo').value   = document.getElementById('inpTo').value;
    document.querySelectorAll('.sd-pbtn').forEach(b => b.classList.remove('active'));
}

</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\TugasAkhir\sim-pkpps\resources\views/santri/kegiatan/show.blade.php ENDPATH**/ ?>