<?php $__env->startSection('title', 'Kegiatan & Absensi'); ?>

<?php $__env->startSection('content'); ?>
<style>
:root {
    --g:   #1a7a5e;
    --m:   #2bbd8e;
    --sf:  #e8f7f2;
    --gd:  #f5a623;
    --rd:  #e53e3e;
    --bl:  #3b82f6;
    --tx:  #1a2332;
    --mu:  #6b7280;
    --br:  #e2e8f0;
    --wh:  #ffffff;
    --bg:  #f8fafb;
    --ra:  14px;
    --sh:  0 4px 20px rgba(0,0,0,0.07);
    --shl: 0 8px 40px rgba(0,0,0,0.12);
}

/* ── HERO ── */
.kg-hero {
    background: linear-gradient(135deg, #0d3b2e 0%, #1a7a5e 55%, #2bbd8e 100%);
    border-radius: var(--ra);
    padding: 26px 28px 22px;
    margin-bottom: 16px;
    position: relative;
    overflow: hidden;
    color: white;
}
.kg-hero::before { content:''; position:absolute; top:-50px; right:-50px; width:180px; height:180px; border-radius:50%; background:rgba(255,255,255,0.05); }
.kg-hero::after  { content:''; position:absolute; bottom:-40px; left:38%; width:140px; height:140px; border-radius:50%; background:rgba(255,255,255,0.04); }
.kg-hero-row { display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px; }
.kg-hero-title { font-size:1.25rem; font-weight:800; margin:0 0 3px; }
.kg-hero-sub   { font-size:0.85rem; opacity:0.8; margin:0; }
.kg-hero-right { text-align:right; }
.kg-hero-day   { font-size:1rem; font-weight:700; opacity:0.9; }
.kg-hero-date  { font-size:0.8rem; opacity:0.7; }
.kg-hero-badges { display:flex; gap:7px; flex-wrap:wrap; margin-top:14px; }
.kg-badge {
    background:rgba(255,255,255,0.14); border:1px solid rgba(255,255,255,0.2);
    padding:4px 11px; border-radius:20px; font-size:0.79rem; font-weight:600;
    display:inline-flex; align-items:center; gap:5px;
}
.kg-badge.fire { background:linear-gradient(135deg,#f59e0b,#f97316); border:none; }

/* ── KPI CARDS ── */
.kg-kpi-row { display:grid; grid-template-columns:repeat(5,1fr); gap:10px; margin-bottom:16px; }
.kg-kpi { background:white; border-radius:12px; padding:14px 12px; box-shadow:var(--sh); text-align:center; border-top:3px solid transparent; transition:transform 0.18s,box-shadow 0.18s; }
.kg-kpi:hover { transform:translateY(-3px); box-shadow:var(--shl); }
.kg-kpi.c-green  { border-top-color:#2bbd8e; }
.kg-kpi.c-blue   { border-top-color:#3b82f6; }
.kg-kpi.c-gold   { border-top-color:#f5a623; }
.kg-kpi.c-orange { border-top-color:#f97316; }
.kg-kpi.c-red    { border-top-color:#e53e3e; }
.kg-kpi-ic { width:36px; height:36px; border-radius:9px; display:flex; align-items:center; justify-content:center; font-size:0.9rem; margin:0 auto 7px; }
.c-green  .kg-kpi-ic { background:#d1fae5; color:#059669; }
.c-blue   .kg-kpi-ic { background:#dbeafe; color:#2563eb; }
.c-gold   .kg-kpi-ic { background:#fef3c7; color:#d97706; }
.c-orange .kg-kpi-ic { background:#ffedd5; color:#ea580c; }
.c-red    .kg-kpi-ic { background:#fee2e2; color:#dc2626; }
.kg-kpi-v  { font-size:1.7rem; font-weight:800; color:var(--tx); line-height:1; margin-bottom:3px; }
.kg-kpi-l  { font-size:0.73rem; color:var(--mu); font-weight:500; }
.kg-kpi-bar{ margin-top:7px; height:4px; background:#f0f0f0; border-radius:2px; overflow:hidden; }
.kg-kpi-fill { height:100%; border-radius:2px; }

/* ── TABS ── */
.kg-tabs { display:flex; gap:4px; background:var(--bg); border-radius:12px; padding:5px; margin-bottom:18px; border:1px solid var(--br); overflow-x:auto; }
.kg-tab { flex:1; min-width:90px; padding:9px 14px; border:none; background:transparent; border-radius:8px; font-size:0.82rem; font-weight:600; color:var(--mu); cursor:pointer; transition:all 0.18s; display:flex; align-items:center; justify-content:center; gap:6px; white-space:nowrap; }
.kg-tab:hover  { background:white; color:var(--g); }
.kg-tab.active { background:white; color:var(--g); box-shadow:0 2px 8px rgba(0,0,0,0.09); }
.kg-panel { display:none; }
.kg-panel.active { display:block; }

/* ── PER-TAB FILTER BAR ── */
.kg-tab-filter {
    background:white; border-radius:12px; padding:12px 14px;
    margin-bottom:14px; box-shadow:var(--sh);
    display:flex; flex-wrap:wrap; gap:8px; align-items:flex-end;
}
.kg-fg { display:flex; flex-direction:column; gap:3px; }
.kg-fg label { font-size:0.72rem; font-weight:700; color:var(--mu); text-transform:uppercase; letter-spacing:0.4px; }
.kg-presets { display:flex; gap:4px; flex-wrap:wrap; }
.kg-preset-btn { padding:6px 12px; border:1.5px solid var(--br); border-radius:8px; background:white; font-size:0.79rem; font-weight:600; color:var(--mu); cursor:pointer; transition:all 0.15s; white-space:nowrap; }
.kg-preset-btn:hover  { border-color:var(--m); color:var(--g); background:var(--sf); }
.kg-preset-btn.active { border-color:var(--g); background:var(--g); color:white; }
.kg-date-range { display:flex; align-items:center; gap:5px; }
.kg-date-range input[type=date] { padding:6px 9px; border:1.5px solid var(--br); border-radius:8px; font-size:0.8rem; color:var(--tx); background:white; }
.kg-date-range input[type=date]:focus { outline:none; border-color:var(--m); }
.kg-date-range span { color:var(--mu); font-size:0.79rem; font-weight:600; }
.kg-apply-btn { padding:7px 15px; background:var(--g); color:white; border:none; border-radius:8px; font-size:0.81rem; font-weight:700; cursor:pointer; display:inline-flex; align-items:center; gap:5px; }
.kg-apply-btn:hover { background:#155c47; }
.kg-filter-label { font-size:0.77rem; color:var(--mu); padding:5px 9px; background:var(--bg); border-radius:8px; border:1px solid var(--br); display:flex; align-items:center; gap:4px; align-self:center; }
.kg-filter-label i { color:var(--g); }

/* ── JADWAL CARDS ── */
.kg-jadwal-group { margin-bottom:14px; }
.kg-hari-label { font-size:0.77rem; font-weight:700; text-transform:uppercase; letter-spacing:0.8px; color:var(--g); padding:5px 11px; background:var(--sf); border-radius:8px; margin-bottom:8px; display:inline-flex; align-items:center; gap:5px; }
.kg-hari-label.today-label { background:var(--g); color:white; }
.kg-jadwal-card { background:white; border-radius:11px; padding:13px 15px; box-shadow:var(--sh); display:flex; align-items:center; gap:12px; border-left:4px solid var(--br); margin-bottom:7px; transition:transform 0.16s; }
.kg-jadwal-card:hover { transform:translateX(3px); }
.kg-jadwal-card.s-hadir { border-left-color:#2bbd8e; }
.kg-jadwal-card.s-izin  { border-left-color:#3b82f6; }
.kg-jadwal-card.s-sakit { border-left-color:#8b5cf6; }
.kg-jadwal-card.s-alpa  { border-left-color:#e53e3e; }
.kg-jadwal-card.s-belum { border-left-color:#f5a623; }
.kg-time { min-width:58px; text-align:center; }
.kg-time-main { font-size:0.93rem; font-weight:700; color:var(--g); }
.kg-time-end  { font-size:0.71rem; color:var(--mu); font-weight:500; }
.kg-divider   { width:1px; height:34px; background:var(--br); flex-shrink:0; }
.kg-jinfo     { flex:1; min-width:0; }
.kg-jname     { font-weight:700; font-size:0.89rem; color:var(--tx); white-space:nowrap; overflow:hidden; text-overflow:ellipsis; margin:0 0 3px; }
.kg-jmeta     { font-size:0.76rem; color:var(--mu); display:flex; gap:8px; flex-wrap:wrap; }
.kpill { padding:3px 11px; border-radius:20px; font-size:0.75rem; font-weight:700; flex-shrink:0; }
.kpill.hadir { background:#d1fae5; color:#065f46; }
.kpill.belum { background:#fef3c7; color:#92400e; }
.kpill.izin  { background:#dbeafe; color:#1e40af; }
.kpill.sakit { background:#ede9fe; color:#5b21b6; }
.kpill.alpa  { background:#fee2e2; color:#991b1b; }

/* ── RIWAYAT TABLE ── */
.kg-riwayat-extra { display:flex; gap:8px; flex-wrap:wrap; align-items:flex-end; margin-top:8px; border-top:1px solid var(--br); padding-top:10px; }
.kg-riwayat-extra select { padding:6px 9px; border:1.5px solid var(--br); border-radius:8px; font-size:0.8rem; color:var(--tx); background:white; }
.kg-riwayat-extra select:focus { outline:none; border-color:var(--m); }
.kg-riwayat-extra button { padding:6px 13px; background:var(--g); color:white; border:none; border-radius:8px; font-size:0.8rem; font-weight:600; cursor:pointer; display:inline-flex; align-items:center; gap:4px; }
.kg-table-wrap { background:white; border-radius:12px; box-shadow:var(--sh); overflow:hidden; }
.kg-table { width:100%; border-collapse:collapse; }
.kg-table thead tr { background:var(--bg); }
.kg-table th { padding:10px 13px; text-align:left; font-size:0.77rem; font-weight:700; color:var(--mu); text-transform:uppercase; letter-spacing:0.4px; border-bottom:1px solid var(--br); }
.kg-table td { padding:10px 13px; font-size:0.83rem; border-bottom:1px solid #f8fafc; color:var(--tx); }
.kg-table tbody tr:last-child td { border-bottom:none; }
.kg-table tbody tr:hover { background:#f8fafc; }

/* ── STATISTIK LAYOUT ── */
.kg-stat-grid { display:grid; grid-template-columns:1fr 1fr; gap:14px; margin-bottom:14px; }
.kg-chart-box { background:white; border-radius:12px; padding:18px; box-shadow:var(--sh); }
.kg-chart-title { font-size:0.86rem; font-weight:700; color:var(--tx); margin-bottom:14px; display:flex; align-items:center; gap:6px; }

/* ── CONSISTENCY SCORE CARDS ── */
.cs-list { display:flex; flex-direction:column; gap:8px; }
.cs-hidden { display:none; }
.cs-toggle-btn {
    width:100%; margin-top:10px; padding:8px; border:1.5px dashed var(--br);
    background:var(--bg); border-radius:9px; font-size:0.8rem; font-weight:600;
    color:var(--mu); cursor:pointer; display:flex; align-items:center; justify-content:center;
    gap:6px; transition:all 0.18s;
}
.cs-toggle-btn:hover { border-color:var(--m); color:var(--g); background:var(--sf); }
.cs-card {
    background:white; border-radius:11px; padding:13px 15px;
    box-shadow:var(--sh); display:flex; align-items:center; gap:12px;
    border-left:4px solid transparent;
}
.cs-card.tier-top  { border-left-color:#059669; }
.cs-card.tier-good { border-left-color:#2bbd8e; }
.cs-card.tier-fair { border-left-color:#f5a623; }
.cs-card.tier-warn { border-left-color:#f97316; }
.cs-card.tier-crit { border-left-color:#e53e3e; }

.cs-rank { min-width:26px; font-size:0.78rem; font-weight:800; color:var(--mu); text-align:center; }
.cs-info { flex:1; min-width:0; }
.cs-name { font-weight:700; font-size:0.87rem; color:var(--tx); white-space:nowrap; overflow:hidden; text-overflow:ellipsis; margin-bottom:2px; }
.cs-meta { font-size:0.74rem; color:var(--mu); display:flex; gap:8px; }
.cs-right { display:flex; flex-direction:column; align-items:flex-end; gap:5px; }
.cs-score-wrap { display:flex; align-items:center; gap:8px; }
.cs-score { font-size:1.35rem; font-weight:800; color:var(--tx); }
.cs-bar-wrap { width:80px; height:6px; background:#f0f0f0; border-radius:3px; overflow:hidden; }
.cs-bar { height:100%; border-radius:3px; }
.tier-top  .cs-bar { background:#059669; }
.tier-good .cs-bar { background:#2bbd8e; }
.tier-fair .cs-bar { background:#f5a623; }
.tier-warn .cs-bar { background:#f97316; }
.tier-crit .cs-bar { background:#e53e3e; }

.cs-badge {
    padding:2px 9px; border-radius:20px; font-size:0.72rem; font-weight:700;
    display:inline-flex; align-items:center; gap:4px;
}
.cs-badge.tier-top  { background:#d1fae5; color:#065f46; }
.cs-badge.tier-good { background:#e8f7f2; color:#1a7a5e; }
.cs-badge.tier-fair { background:#fef3c7; color:#92400e; }
.cs-badge.tier-warn { background:#ffedd5; color:#9a3412; }
.cs-badge.tier-crit { background:#fee2e2; color:#991b1b; }

/* ── MINI CALENDAR ── */
.kg-cal-mini { width: 220px; flex-shrink: 0; }
.kg-cal-mini-grid {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    gap: 2px;
}
.kg-cal-mini-dname {
    text-align:center; font-size:0.6rem; font-weight:700;
    color:var(--mu); padding-bottom:3px; text-transform:uppercase;
}
.kg-cal-mini-cell {
    aspect-ratio:1; border-radius:4px; display:flex; align-items:center;
    justify-content:center; font-size:0.63rem; font-weight:600;
    cursor:default; transition:transform 0.1s;
}
.kg-cal-mini-cell:hover { transform:scale(1.2); z-index:5; }
.kg-cal-mini-cell.l0 { background:#f3f4f6; color:#9ca3af; }
.kg-cal-mini-cell.l1 { background:#bbf7d0; color:#065f46; }
.kg-cal-mini-cell.l2 { background:#4ade80; color:#14532d; }
.kg-cal-mini-cell.l3 { background:#16a34a; color:white; }
.kg-cal-mini-cell.l4 { background:#064e2d; color:white; }
.kg-cal-mini-cell.is-today { outline:2px solid var(--gd); outline-offset:1px; }
.kg-cal-mini-cell.out-range { opacity:0.3; }

/* ── EMPTY ── */
.kg-empty { text-align:center; padding:36px 20px; color:var(--mu); background:white; border-radius:12px; box-shadow:var(--sh); }
.kg-empty i { font-size:2.8rem; opacity:0.2; display:block; margin-bottom:10px; }

/* ── RESPONSIVE ── */
@media (max-width: 768px) {
    .kg-kpi-row { grid-template-columns:repeat(3,1fr); }
    .kg-kpi-row .c-orange, .kg-kpi-row .c-red { display:none; }
    .kg-stat-grid { grid-template-columns:1fr; }
}
</style>


<div class="kg-hero">
    <div class="kg-hero-row">
        <div>
            <h1 class="kg-hero-title"><i class="fas fa-calendar-check"></i> Kegiatan & Absensi</h1>
            <p class="kg-hero-sub"><?php echo e($santri->nama_lengkap); ?> &nbsp;·&nbsp; Kelas <?php echo e($namaKelas); ?></p>
        </div>
        <div class="kg-hero-right">
            <div class="kg-hero-day"><?php echo e(\Carbon\Carbon::now()->locale('id')->isoFormat('dddd')); ?></div>
            <div class="kg-hero-date"><?php echo e(\Carbon\Carbon::now()->locale('id')->isoFormat('D MMMM YYYY')); ?></div>
        </div>
    </div>
    <div class="kg-hero-badges">
        <span class="kg-badge"><i class="fas fa-id-card"></i> <?php echo e($santri->nis ?? $santri->id_santri); ?></span>
        <span class="kg-badge"><i class="fas fa-chart-line"></i> <?php echo e($persentaseKehadiran); ?>% kehadiran</span>
        <?php if($streak > 0): ?>
            <span class="kg-badge fire"><i class="fas fa-fire"></i> Streak <?php echo e($streak); ?>x hadir</span>
        <?php endif; ?>
    </div>
</div>


<div class="kg-kpi-row">
    <div class="kg-kpi c-green">
        <div class="kg-kpi-ic"><i class="fas fa-list-alt"></i></div>
        <div class="kg-kpi-v"><?php echo e($totalRange); ?></div>
        <div class="kg-kpi-l">Total</div>
        <div class="kg-kpi-bar"><div class="kg-kpi-fill" style="width:100%;background:#2bbd8e;"></div></div>
    </div>
    <div class="kg-kpi c-blue">
        <div class="kg-kpi-ic"><i class="fas fa-check-circle"></i></div>
        <div class="kg-kpi-v"><?php echo e($hadirRange); ?></div>
        <div class="kg-kpi-l">Hadir</div>
        <div class="kg-kpi-bar"><div class="kg-kpi-fill" style="width:<?php echo e($totalRange > 0 ? round($hadirRange/$totalRange*100) : 0); ?>%;background:#3b82f6;"></div></div>
    </div>
    <div class="kg-kpi c-gold">
        <div class="kg-kpi-ic"><i class="fas fa-percentage"></i></div>
        <div class="kg-kpi-v"><?php echo e($persentaseKehadiran); ?>%</div>
        <div class="kg-kpi-l">Persentase</div>
        <div class="kg-kpi-bar"><div class="kg-kpi-fill" style="width:<?php echo e($persentaseKehadiran); ?>%;background:#f5a623;"></div></div>
    </div>
    <div class="kg-kpi c-orange">
        <div class="kg-kpi-ic"><i class="fas fa-info-circle"></i></div>
        <div class="kg-kpi-v"><?php echo e($izinRange + $sakitRange); ?></div>
        <div class="kg-kpi-l">Izin / Sakit</div>
        <div class="kg-kpi-bar"><div class="kg-kpi-fill" style="width:<?php echo e($totalRange > 0 ? round(($izinRange+$sakitRange)/$totalRange*100) : 0); ?>%;background:#f97316;"></div></div>
    </div>
    <div class="kg-kpi c-red">
        <div class="kg-kpi-ic"><i class="fas fa-times-circle"></i></div>
        <div class="kg-kpi-v"><?php echo e($alpaRange); ?></div>
        <div class="kg-kpi-l">Alpa</div>
        <div class="kg-kpi-bar"><div class="kg-kpi-fill" style="width:<?php echo e($totalRange > 0 ? round($alpaRange/$totalRange*100) : 0); ?>%;background:#e53e3e;"></div></div>
    </div>
</div>


<div class="kg-tabs">
    <button class="kg-tab <?php echo e($activeTab === 'statistik' ? 'active' : ''); ?>" onclick="switchTab('statistik',this)">
        <i class="fas fa-chart-bar"></i> Statistik
    </button>
    <button class="kg-tab <?php echo e($activeTab === 'jadwal' ? 'active' : ''); ?>" onclick="switchTab('jadwal',this)">
        <i class="fas fa-clock"></i> Jadwal
        <?php if($jadwalDalamRange->count() > 0): ?>
            <span style="background:var(--g);color:white;border-radius:10px;padding:1px 6px;font-size:0.67rem;"><?php echo e($jadwalDalamRange->count()); ?></span>
        <?php endif; ?>
    </button>
    <button class="kg-tab <?php echo e($activeTab === 'riwayat' ? 'active' : ''); ?>" onclick="switchTab('riwayat',this)">
        <i class="fas fa-history"></i> Riwayat
    </button>
</div>




<div class="kg-panel <?php echo e($activeTab === 'statistik' ? 'active' : ''); ?>" id="panel-statistik">

    
    <form method="GET" action="<?php echo e(route('santri.kegiatan.index')); ?>" id="formStat">
        <input type="hidden" name="tab" value="statistik">
        <?php if(request('filter_status')): ?>   <input type="hidden" name="filter_status"   value="<?php echo e(request('filter_status')); ?>"> <?php endif; ?>
        <?php if(request('filter_kategori')): ?> <input type="hidden" name="filter_kategori" value="<?php echo e(request('filter_kategori')); ?>"> <?php endif; ?>
        <input type="hidden" name="preset_jad" value="<?php echo e($jadPreset); ?>">
        <input type="hidden" name="preset_riw" value="<?php echo e($riwPreset); ?>">
        <input type="hidden" name="preset_stat" id="hStat"     value="<?php echo e($statPreset); ?>">
        <input type="hidden" name="stat_date_from" id="hStatFrom" value="<?php echo e(request('stat_date_from')); ?>">
        <input type="hidden" name="stat_date_to"   id="hStatTo"   value="<?php echo e(request('stat_date_to')); ?>">

        <div class="kg-tab-filter">
            <div class="kg-fg">
                <label><i class="fas fa-bolt"></i> Periode</label>
                <div class="kg-presets" id="statPresets">
                    <?php $__currentLoopData = ['today'=>'Hari Ini','this_week'=>'Minggu Ini','last_30'=>'30 Hari','this_month'=>'Bulan Ini','last_month'=>'Bulan Lalu']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $v=>$l): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <button type="button" class="kg-preset-btn <?php echo e($statPreset===$v ? 'active' : ''); ?>"
                                onclick="setPreset('stat','<?php echo e($v); ?>')"><?php echo e($l); ?></button>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
            <div class="kg-fg">
                <label><i class="fas fa-calendar-alt"></i> Kustom</label>
                <div class="kg-date-range">
                    <input type="date" id="inpStatFrom" value="<?php echo e(request('stat_date_from', $statFrom->format('Y-m-d'))); ?>" onchange="setCustom('stat')">
                    <span>—</span>
                    <input type="date" id="inpStatTo"   value="<?php echo e(request('stat_date_to',   $statTo->format('Y-m-d'))); ?>"   onchange="setCustom('stat')">
                </div>
            </div>
            <button type="submit" class="kg-apply-btn"><i class="fas fa-sync-alt"></i> Terapkan</button>
            <div class="kg-filter-label">
                <i class="fas fa-calendar-check"></i>
                <?php echo e($statFrom->locale('id')->isoFormat('D MMM YYYY')); ?> &ndash; <?php echo e($statTo->locale('id')->isoFormat('D MMM YYYY')); ?>

            </div>
        </div>
    </form>

    <div class="kg-stat-grid">
        
        <div class="kg-chart-box">
            <div class="kg-chart-title">
                <i class="fas fa-chart-line" style="color:var(--m);"></i> Tren Kehadiran
                <span style="margin-left:auto;font-size:0.73rem;color:var(--mu);font-weight:500;"><?php echo e($diffDays<=31?'Harian':'Mingguan'); ?></span>
            </div>
            <canvas id="chartTren" style="max-height:220px;"></canvas>
        </div>

        
        <div class="kg-chart-box">
            <div class="kg-chart-title"><i class="fas fa-chart-pie" style="color:var(--gd);"></i> Distribusi Status</div>
            <canvas id="chartDonut" style="max-height:200px;"></canvas>
        </div>
    </div>

    
    <div style="display:grid; grid-template-columns:1fr auto; gap:14px; margin-bottom:14px; align-items:start;">

    
    <div class="kg-chart-box" style="min-width:0;">
        <div class="kg-chart-title"><i class="fas fa-medal" style="color:var(--gd);"></i> Konsistensi Score per Kegiatan</div>
        <?php if($consistencyScores->count() > 0): ?>
            <div class="cs-list">
                <?php $__currentLoopData = $consistencyScores; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $idx => $cs): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="cs-card tier-<?php echo e($cs->tier); ?><?php echo e($idx >= 4 ? ' cs-hidden' : ''); ?>">
                        <div class="cs-rank">#<?php echo e($idx + 1); ?></div>
                        <div class="cs-info">
                            <div class="cs-name"><?php echo e($cs->nama_kegiatan); ?></div>
                            <div class="cs-meta">
                                <span><i class="fas fa-tag"></i> <?php echo e($cs->nama_kategori); ?></span>
                                <span><i class="fas fa-check"></i> <?php echo e($cs->hadir); ?>/<?php echo e($cs->total); ?> hadir</span>
                                <?php if($cs->alpa > 0): ?>
                                    <span style="color:#e53e3e;"><i class="fas fa-times"></i> <?php echo e($cs->alpa); ?> alpa</span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="cs-right">
                            <div class="cs-score-wrap">
                                <div class="cs-bar-wrap">
                                    <div class="cs-bar" style="width:<?php echo e($cs->score); ?>%;"></div>
                                </div>
                                <div class="cs-score"><?php echo e($cs->score); ?>%</div>
                            </div>
                            <span class="cs-badge tier-<?php echo e($cs->tier); ?>">
                                <?php if($cs->tier === 'top'): ?>   <i class="fas fa-star"></i>
                                <?php elseif($cs->tier === 'good'): ?> <i class="fas fa-thumbs-up"></i>
                                <?php elseif($cs->tier === 'fair'): ?> <i class="fas fa-minus-circle"></i>
                                <?php elseif($cs->tier === 'warn'): ?> <i class="fas fa-exclamation-triangle"></i>
                                <?php else: ?> <i class="fas fa-times-circle"></i>
                                <?php endif; ?>
                                <?php echo e($cs->badge); ?>

                            </span>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        <?php else: ?>
            <p style="text-align:center;color:var(--mu);font-size:0.84rem;padding:20px 0;">
                Belum ada data kehadiran dalam periode ini.
            </p>
        <?php endif; ?>

        <?php if($consistencyScores->count() > 4): ?>
        <button class="cs-toggle-btn" id="csToggleBtn" onclick="toggleCsList()">
            <i class="fas fa-chevron-down" id="csChevron"></i>
            <span id="csToggleText">Lihat <?php echo e($consistencyScores->count() - 4); ?> kegiatan lainnya</span>
        </button>
        <?php endif; ?>
    </div>

    
    <?php $calMonth = collect($heatmapMonths)->last(); ?>
    <?php if($calMonth): ?>
    <div class="kg-chart-box kg-cal-mini">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:8px;">
            <div class="kg-chart-title" style="margin:0; font-size:0.82rem;">
                <i class="fas fa-calendar-alt" style="color:var(--g);"></i> <?php echo e($calMonth['label']); ?>

            </div>
        </div>
        
        <div style="display:flex; gap:3px; align-items:center; margin-bottom:7px; font-size:0.67rem; color:var(--mu);">
            <?php $__currentLoopData = ['#f3f4f6','#bbf7d0','#4ade80','#16a34a','#064e2d']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $hc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div style="width:7px;height:7px;border-radius:2px;background:<?php echo e($hc); ?>;flex-shrink:0;"></div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <span style="margin-left:2px;">Hadir</span>
        </div>
        
        <div class="kg-cal-mini-grid">
            <?php $__currentLoopData = ['S','S','R','K','J','S','M']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $hn): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="kg-cal-mini-dname"><?php echo e($hn); ?></div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php for($e = 1; $e < $calMonth['firstDayOfWeek']; $e++): ?>
                <div></div>
            <?php endfor; ?>
            <?php $__currentLoopData = $calMonth['days']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $day): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="kg-cal-mini-cell l<?php echo e($day['level']); ?> <?php echo e($day['is_today'] ? 'is-today' : ''); ?> <?php echo e(!$day['in_range'] ? 'out-range' : ''); ?>"
                     title="<?php echo e(\Carbon\Carbon::parse($day['date'])->locale('id')->isoFormat('D MMM')); ?><?php echo e($day['total'] > 0 ? ': '.$day['count'].'/'.$day['total'].' hadir' : ''); ?>">
                    <?php echo e($day['day']); ?>

                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
    <?php endif; ?>

    </div>
</div>




<div class="kg-panel <?php echo e($activeTab === 'jadwal' ? 'active' : ''); ?>" id="panel-jadwal">

    <form method="GET" action="<?php echo e(route('santri.kegiatan.index')); ?>" id="formJad">
        <input type="hidden" name="tab" value="jadwal">
        <input type="hidden" name="preset_stat" value="<?php echo e($statPreset); ?>">
        <input type="hidden" name="preset_riw"  value="<?php echo e($riwPreset); ?>">
        <input type="hidden" name="preset_jad"  id="hJad"     value="<?php echo e($jadPreset); ?>">
        <input type="hidden" name="jad_date_from" id="hJadFrom" value="<?php echo e(request('jad_date_from')); ?>">
        <input type="hidden" name="jad_date_to"   id="hJadTo"   value="<?php echo e(request('jad_date_to')); ?>">

        <div class="kg-tab-filter">
            <div class="kg-fg">
                <label><i class="fas fa-bolt"></i> Periode</label>
                <div class="kg-presets" id="jadPresets">
                    <?php $__currentLoopData = ['today'=>'Hari Ini','this_week'=>'Minggu Ini','this_month'=>'Bulan Ini','last_month'=>'Bulan Lalu']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $v=>$l): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <button type="button" class="kg-preset-btn <?php echo e($jadPreset===$v ? 'active' : ''); ?>"
                                onclick="setPreset('jad','<?php echo e($v); ?>')"><?php echo e($l); ?></button>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
            <div class="kg-fg">
                <label><i class="fas fa-calendar-alt"></i> Kustom</label>
                <div class="kg-date-range">
                    <input type="date" id="inpJadFrom" value="<?php echo e(request('jad_date_from', $jadFrom->format('Y-m-d'))); ?>" onchange="setCustom('jad')">
                    <span>—</span>
                    <input type="date" id="inpJadTo"   value="<?php echo e(request('jad_date_to',   $jadTo->format('Y-m-d'))); ?>"   onchange="setCustom('jad')">
                </div>
            </div>
            <button type="submit" class="kg-apply-btn"><i class="fas fa-sync-alt"></i> Terapkan</button>
            <div class="kg-filter-label">
                <i class="fas fa-calendar-check"></i>
                <?php echo e($jadFrom->locale('id')->isoFormat('D MMM')); ?> &ndash; <?php echo e($jadTo->locale('id')->isoFormat('D MMM YYYY')); ?>

            </div>
        </div>
    </form>

    <?php if($jadwalDalamRange->count() > 0): ?>
        <?php
            $hariOrder   = ['Senin','Selasa','Rabu','Kamis','Jumat','Sabtu','Ahad'];
            $jadGrouped  = $jadwalDalamRange->groupBy('hari')
                ->sortBy(fn($v,$k) => array_search($k, $hariOrder));
        ?>
        <?php $__currentLoopData = $jadGrouped; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $hari => $jadwals): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="kg-jadwal-group">
                <div class="kg-hari-label <?php echo e($hari === $hariIni ? 'today-label' : ''); ?>">
                    <i class="fas fa-calendar-day"></i> <?php echo e($hari); ?>

                    <?php if($hari === $hariIni): ?> <span style="font-size:0.66rem;opacity:0.85;">(Hari Ini)</span> <?php endif; ?>
                </div>
                <?php $__currentLoopData = $jadwals; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $jadwal): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php
                        // Tampilkan status: hari ini pakai absensiHariIni, lainnya dari range
                        $statusAbsen = $hari === $hariIni
                            ? ($absensiHariIni[$jadwal->kegiatan_id] ?? null)
                            : ($absensiDalamRange[$jadwal->kegiatan_id] ?? null);
                        $sc = $statusAbsen ? 's-' . strtolower($statusAbsen) : 's-belum';
                    ?>
                    <div class="kg-jadwal-card <?php echo e($sc); ?>">
                        <div class="kg-time">
                            <div class="kg-time-main"><?php echo e(date('H:i', strtotime($jadwal->waktu_mulai))); ?></div>
                            <div class="kg-time-end"><?php echo e(date('H:i', strtotime($jadwal->waktu_selesai))); ?></div>
                        </div>
                        <div class="kg-divider"></div>
                        <div class="kg-jinfo">
                            <div class="kg-jname"><?php echo e($jadwal->nama_kegiatan); ?></div>
                            <div class="kg-jmeta">
                                <span><i class="fas fa-tag"></i> <?php echo e($jadwal->kategori->nama_kategori); ?></span>
                                <?php if($jadwal->materi): ?>
                                    <span><i class="fas fa-book"></i> <?php echo e(Str::limit($jadwal->materi, 28)); ?></span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php if($statusAbsen): ?>
                            <span class="kpill <?php echo e(strtolower($statusAbsen)); ?>"><?php echo e($statusAbsen); ?></span>
                        <?php elseif($hari === $hariIni): ?>
                            <span class="kpill belum"><i class="fas fa-hourglass-half"></i> Belum</span>
                        <?php endif; ?>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    <?php else: ?>
        <div class="kg-empty">
            <i class="fas fa-calendar-times"></i>
            <p>Tidak ada kegiatan terjadwal dalam periode ini.</p>
        </div>
    <?php endif; ?>
</div>




<div class="kg-panel <?php echo e($activeTab === 'riwayat' ? 'active' : ''); ?>" id="panel-riwayat">

    <form method="GET" action="<?php echo e(route('santri.kegiatan.index')); ?>" id="formRiw">
        <input type="hidden" name="tab" value="riwayat">
        <input type="hidden" name="preset_stat" value="<?php echo e($statPreset); ?>">
        <input type="hidden" name="preset_jad"  value="<?php echo e($jadPreset); ?>">
        <input type="hidden" name="preset_riw"  id="hRiw"     value="<?php echo e($riwPreset); ?>">
        <input type="hidden" name="riw_date_from" id="hRiwFrom" value="<?php echo e(request('riw_date_from')); ?>">
        <input type="hidden" name="riw_date_to"   id="hRiwTo"   value="<?php echo e(request('riw_date_to')); ?>">

        <div class="kg-tab-filter">
            <div class="kg-fg">
                <label><i class="fas fa-bolt"></i> Periode</label>
                <div class="kg-presets" id="riwPresets">
                    <?php $__currentLoopData = ['today'=>'Hari Ini','this_week'=>'Minggu Ini','this_month'=>'Bulan Ini','last_month'=>'Bulan Lalu']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $v=>$l): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <button type="button" class="kg-preset-btn <?php echo e($riwPreset===$v ? 'active' : ''); ?>"
                                onclick="setPreset('riw','<?php echo e($v); ?>')"><?php echo e($l); ?></button>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
            <div class="kg-fg">
                <label><i class="fas fa-calendar-alt"></i> Kustom</label>
                <div class="kg-date-range">
                    <input type="date" id="inpRiwFrom" value="<?php echo e(request('riw_date_from', $riwFrom->format('Y-m-d'))); ?>" onchange="setCustom('riw')">
                    <span>—</span>
                    <input type="date" id="inpRiwTo"   value="<?php echo e(request('riw_date_to',   $riwTo->format('Y-m-d'))); ?>"   onchange="setCustom('riw')">
                </div>
            </div>

            
            <div class="kg-riwayat-extra">
                <select name="filter_status">
                    <option value="">Semua Status</option>
                    <?php $__currentLoopData = ['Hadir','Izin','Sakit','Alpa']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($s); ?>" <?php echo e(request('filter_status')===$s ? 'selected' : ''); ?>><?php echo e($s); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
                <select name="filter_kategori">
                    <option value="">Semua Kategori</option>
                    <?php $__currentLoopData = $kategoriList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $kat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($kat->kategori_id); ?>" <?php echo e(request('filter_kategori')==$kat->kategori_id ? 'selected' : ''); ?>><?php echo e($kat->nama_kategori); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>

            <button type="submit" class="kg-apply-btn"><i class="fas fa-sync-alt"></i> Terapkan</button>
            <div class="kg-filter-label">
                <i class="fas fa-calendar-check"></i>
                <?php echo e($riwFrom->locale('id')->isoFormat('D MMM')); ?> &ndash; <?php echo e($riwTo->locale('id')->isoFormat('D MMM YYYY')); ?>

            </div>
            <?php if(request()->hasAny(['filter_status','filter_kategori'])): ?>
                <a href="<?php echo e(route('santri.kegiatan.index')); ?>?tab=riwayat&preset_riw=<?php echo e($riwPreset); ?>"
                   style="font-size:0.78rem;color:var(--mu);text-decoration:none;display:flex;align-items:center;gap:4px;align-self:center;">
                   <i class="fas fa-times"></i> Reset filter
                </a>
            <?php endif; ?>
        </div>
    </form>

    <?php if($riwayats->count() > 0): ?>
        <div class="kg-table-wrap">
            <table class="kg-table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Tanggal</th>
                        <th>Kegiatan</th>
                        <th>Kategori</th>
                        <th>Waktu Absen</th>
                        <th>Status</th>
                        <th style="text-align:center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $riwayats; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $idx => $absensi): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td style="color:var(--mu);font-size:0.76rem;"><?php echo e($riwayats->firstItem() + $idx); ?></td>
                        <td>
                            <div style="font-weight:600;font-size:0.83rem;"><?php echo e(\Carbon\Carbon::parse($absensi->tanggal)->format('d M Y')); ?></div>
                            <div style="font-size:0.72rem;color:var(--mu);"><?php echo e(\Carbon\Carbon::parse($absensi->tanggal)->locale('id')->dayName); ?></div>
                        </td>
                        <td style="font-weight:600;font-size:0.85rem;"><?php echo e($absensi->kegiatan->nama_kegiatan); ?></td>
                        <td>
                            <span style="background:var(--sf);color:var(--g);padding:2px 8px;border-radius:6px;font-size:0.73rem;font-weight:600;">
                                <?php echo e($absensi->kegiatan->kategori->nama_kategori); ?>

                            </span>
                        </td>
                        <td style="color:var(--mu);font-size:0.81rem;">
                            <?php echo e($absensi->waktu_absen ? \Carbon\Carbon::parse($absensi->waktu_absen)->format('H:i') : '-'); ?>

                        </td>
                        <td>
                            <span class="kpill <?php echo e(strtolower($absensi->status)); ?>"><?php echo e($absensi->status); ?></span>
                        </td>
                        <td style="text-align:center;">
                            <a href="<?php echo e(route('santri.kegiatan.show', $absensi->kegiatan_id)); ?>?from_tab=riwayat"
                               style="padding:5px 10px;background:var(--g);color:white;border-radius:7px;font-size:0.76rem;text-decoration:none;display:inline-flex;align-items:center;gap:4px;">
                               <i class="fas fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
        <div style="margin-top:12px;"><?php echo e($riwayats->links()); ?></div>
    <?php else: ?>
        <div class="kg-empty">
            <i class="fas fa-inbox"></i>
            <p>Tidak ada riwayat absensi dalam periode ini.</p>
        </div>
    <?php endif; ?>
</div>


<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
// ── TAB SWITCHER ─────────────────────────────────
function switchTab(name, el) {
    document.querySelectorAll('.kg-tab').forEach(t => t.classList.remove('active'));
    document.querySelectorAll('.kg-panel').forEach(p => p.classList.remove('active'));
    el.classList.add('active');
    document.getElementById('panel-' + name).classList.add('active');
    if (name === 'statistik' && !window._chartsInit) { initCharts(); window._chartsInit = true; }
}

// ── PRESET SETTER ────────────────────────────────
function setPreset(scope, val) {
    document.querySelectorAll('#' + scope + 'Presets .kg-preset-btn').forEach(b => b.classList.remove('active'));
    event.target.classList.add('active');
    document.getElementById('h' + scope.charAt(0).toUpperCase() + scope.slice(1)).value = val;
    document.getElementById('h' + scope.charAt(0).toUpperCase() + scope.slice(1) + 'From').value = '';
    document.getElementById('h' + scope.charAt(0).toUpperCase() + scope.slice(1) + 'To').value   = '';
    document.getElementById('form' + scope.charAt(0).toUpperCase() + scope.slice(1)).submit();
}

function setCustom(scope) {
    var cap = scope.charAt(0).toUpperCase() + scope.slice(1);
    document.getElementById('h' + cap).value = '';
    document.getElementById('h' + cap + 'From').value = document.getElementById('inp' + cap + 'From').value;
    document.getElementById('h' + cap + 'To').value   = document.getElementById('inp' + cap + 'To').value;
    document.querySelectorAll('#' + scope + 'Presets .kg-preset-btn').forEach(b => b.classList.remove('active'));
}

// ── CHARTS ──────────────────────────────────────
function initCharts() {
    const trenLabels = <?php echo json_encode(collect($dataGrafik)->pluck('label'), 15, 512) ?>;
    const trenHadir  = <?php echo json_encode(collect($dataGrafik)->pluck('hadir'), 15, 512) ?>;
    const trenTotal  = <?php echo json_encode(collect($dataGrafik)->pluck('total'), 15, 512) ?>;

    new Chart(document.getElementById('chartTren'), {
        type: 'line',
        data: {
            labels: trenLabels,
            datasets: [
                { label:'Hadir', data:trenHadir, borderColor:'#2bbd8e', backgroundColor:'rgba(43,189,142,0.1)', borderWidth:3, pointRadius:trenLabels.length>20?2:5, pointBackgroundColor:'#2bbd8e', tension:0.4, fill:true },
                { label:'Total', data:trenTotal, borderColor:'#cbd5e1', backgroundColor:'transparent', borderWidth:2, borderDash:[4,4], pointRadius:trenLabels.length>20?2:4, pointBackgroundColor:'#cbd5e1', tension:0.4 }
            ]
        },
        options: {
            responsive:true, maintainAspectRatio:true,
            plugins:{ legend:{ position:'top', labels:{ font:{ size:11, weight:'600' } } } },
            scales:{
                y:{ beginAtZero:true, ticks:{ stepSize:1 }, grid:{ color:'rgba(0,0,0,0.04)' } },
                x:{ grid:{ display:false }, ticks:{ maxRotation:45, font:{ size:10 }, maxTicksLimit:12 } }
            }
        }
    });

    new Chart(document.getElementById('chartDonut'), {
        type: 'doughnut',
        data: {
            labels: ['Hadir','Izin','Sakit','Alpa'],
            datasets:[{ data:[<?php echo e($hadirRange); ?>,<?php echo e($izinRange); ?>,<?php echo e($sakitRange); ?>,<?php echo e($alpaRange); ?>], backgroundColor:['#2bbd8e','#3b82f6','#f5a623','#e53e3e'], borderWidth:3, borderColor:'#fff' }]
        },
        options: {
            responsive:true, maintainAspectRatio:true, cutout:'65%',
            plugins:{ legend:{ position:'bottom', labels:{ padding:12, font:{ size:11 } } } }
        }
    });
}

// ── CONSISTENCY TOGGLE ───────────────────────────
function toggleCsList() {
    var hidden = document.querySelectorAll('.cs-hidden');
    var btn    = document.getElementById('csToggleBtn');
    var chev   = document.getElementById('csChevron');
    var txt    = document.getElementById('csToggleText');
    var count  = <?php echo e($consistencyScores->count() - 4); ?>;
    if (hidden.length > 0) {
        hidden.forEach(el => el.classList.remove('cs-hidden'));
        chev.style.transform = 'rotate(180deg)';
        txt.textContent = 'Sembunyikan';
    } else {
        var all = document.querySelectorAll('.cs-card');
        all.forEach((el, i) => { if (i >= 4) el.classList.add('cs-hidden'); });
        chev.style.transform = '';
        txt.textContent = 'Lihat ' + count + ' kegiatan lainnya';
    }
}

// ── INIT ─────────────────────────────────────────
document.addEventListener('DOMContentLoaded', function() {
    var tab = new URLSearchParams(window.location.search).get('tab') || 'statistik';
    var map = { statistik:0, jadwal:1, riwayat:2 };
    var idx = map[tab] ?? 0;
    var tabs = document.querySelectorAll('.kg-tab');
    if (tabs[idx]) tabs[idx].click();
});
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\TugasAkhir\sim-pkpps\resources\views/santri/kegiatan/index.blade.php ENDPATH**/ ?>