@extends('layouts.app')

@section('title', 'Kegiatan & Absensi')

@section('content')
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
.kg-hero::before { content:''; position:absolute; top:-50px; right:-50px; width:180px; height:180px; border-radius:50%; background:rgba(255,255,255,0.05); pointer-events:none; }
.kg-hero::after  { content:''; position:absolute; bottom:-40px; left:38%; width:140px; height:140px; border-radius:50%; background:rgba(255,255,255,0.04); pointer-events:none; }
.kg-hero-row { display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px; position:relative; z-index:1; }
.kg-hero-title { font-size:1.25rem; font-weight:800; margin:0 0 3px; }
.kg-hero-sub   { font-size:0.85rem; opacity:0.8; margin:0; }
.kg-hero-right { text-align:right; }
.kg-hero-day   { font-size:1rem; font-weight:700; opacity:0.9; }
.kg-hero-date  { font-size:0.8rem; opacity:0.7; }
.kg-hero-badges { display:flex; gap:7px; flex-wrap:wrap; margin-top:14px; position:relative; z-index:1; }
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
.kg-kpi-bar  { margin-top:7px; height:4px; background:#f0f0f0; border-radius:2px; overflow:hidden; }
.kg-kpi-fill { height:100%; border-radius:2px; }

/* ── TABS ── */
.kg-tabs { display:flex; gap:4px; background:var(--bg); border-radius:12px; padding:5px; margin-bottom:18px; border:1px solid var(--br); overflow-x:auto; }
.kg-tab { flex:1; min-width:90px; padding:9px 14px; border:none; background:transparent; border-radius:8px; font-size:0.82rem; font-weight:600; color:var(--mu); cursor:pointer; transition:all 0.18s; display:flex; align-items:center; justify-content:center; gap:6px; white-space:nowrap; }
.kg-tab:hover  { background:white; color:var(--g); }
.kg-tab.active { background:white; color:var(--g); box-shadow:0 2px 8px rgba(0,0,0,0.09); }
.kg-panel { display:none; }
.kg-panel.active { display:block; }

/* ── FILTER BAR ── */
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
.kg-jadwal-card.s-hadir     { border-left-color:#2bbd8e; }
.kg-jadwal-card.s-terlambat { border-left-color:#f59e0b; }
.kg-jadwal-card.s-izin      { border-left-color:#3b82f6; }
.kg-jadwal-card.s-sakit     { border-left-color:#8b5cf6; }
.kg-jadwal-card.s-alpa      { border-left-color:#e53e3e; }
.kg-jadwal-card.s-pulang    { border-left-color:#0d9488; }
.kg-jadwal-card.s-belum     { border-left-color:#f5a623; }
.kg-time { min-width:58px; text-align:center; }
.kg-time-main { font-size:0.93rem; font-weight:700; color:var(--g); }
.kg-time-end  { font-size:0.71rem; color:var(--mu); font-weight:500; }
.kg-divider   { width:1px; height:34px; background:var(--br); flex-shrink:0; }
.kg-jinfo     { flex:1; min-width:0; }
.kg-jname     { font-weight:700; font-size:0.89rem; color:var(--tx); white-space:nowrap; overflow:hidden; text-overflow:ellipsis; margin:0 0 3px; }
.kg-jmeta     { font-size:0.76rem; color:var(--mu); display:flex; gap:8px; flex-wrap:wrap; }
.kg-detail-btn {
    padding:5px 11px; background:var(--g); color:white !important;
    border-radius:7px; font-size:0.75rem; font-weight:600;
    text-decoration:none !important; display:inline-flex;
    align-items:center; gap:4px; white-space:nowrap; flex-shrink:0;
    transition:background 0.15s;
}
.kg-detail-btn:hover { background:#155c47; }

/* Pill status */
.kpill { padding:3px 11px; border-radius:20px; font-size:0.75rem; font-weight:700; flex-shrink:0; }
.kpill.hadir     { background:#d1fae5; color:#065f46; }
.kpill.terlambat { background:#fef3c7; color:#92400e; }
.kpill.belum     { background:#fef3c7; color:#92400e; }
.kpill.izin      { background:#dbeafe; color:#1e40af; }
.kpill.sakit     { background:#ede9fe; color:#5b21b6; }
.kpill.alpa      { background:#fee2e2; color:#991b1b; }
.kpill.pulang    { background:#ccfbf1; color:#0f766e; }

/* ── STATISTIK LAYOUT ── */
.kg-stat-grid { display:grid; grid-template-columns:1fr 1fr; gap:14px; margin-bottom:14px; }
.kg-chart-box { background:white; border-radius:12px; padding:18px; box-shadow:var(--sh); }
.kg-chart-title { font-size:0.86rem; font-weight:700; color:var(--tx); margin-bottom:14px; display:flex; align-items:center; gap:6px; }

/* ── RECENT ABSENSI ── */
.kg-recent-list { display:flex; flex-direction:column; gap:6px; }
.kg-recent-item {
    display:flex; align-items:center; gap:11px;
    background:white; border-radius:10px; padding:10px 13px;
    box-shadow:var(--sh); border-left:3px solid transparent;
    transition:transform 0.15s;
}
.kg-recent-item:hover { transform:translateX(3px); }
.kg-recent-item.s-hadir     { border-left-color:#2bbd8e; }
.kg-recent-item.s-terlambat { border-left-color:#f59e0b; }
.kg-recent-item.s-izin      { border-left-color:#3b82f6; }
.kg-recent-item.s-sakit     { border-left-color:#8b5cf6; }
.kg-recent-item.s-alpa      { border-left-color:#e53e3e; }
.kg-recent-item.s-pulang    { border-left-color:#0d9488; }
.kg-recent-ic {
    width:32px; height:32px; border-radius:8px; flex-shrink:0;
    display:flex; align-items:center; justify-content:center; font-size:0.82rem;
}
.s-hadir     .kg-recent-ic { background:#d1fae5; color:#059669; }
.s-terlambat .kg-recent-ic { background:#fef3c7; color:#d97706; }
.s-izin      .kg-recent-ic { background:#dbeafe; color:#2563eb; }
.s-sakit     .kg-recent-ic { background:#ede9fe; color:#7c3aed; }
.s-alpa      .kg-recent-ic { background:#fee2e2; color:#dc2626; }
.s-pulang    .kg-recent-ic { background:#ccfbf1; color:#0f766e; }
.kg-recent-info  { flex:1; min-width:0; }
.kg-recent-name  { font-weight:700; font-size:0.84rem; color:var(--tx); white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
.kg-recent-meta  { font-size:0.72rem; color:var(--mu); margin-top:2px; display:flex; gap:8px; flex-wrap:wrap; }
.kg-recent-right { text-align:right; flex-shrink:0; }
.kg-recent-date  { font-size:0.69rem; color:var(--mu); opacity:0.7; margin-top:3px; }

/* ── MINI CALENDAR ── */
.kg-cal-mini { width:220px; flex-shrink:0; }
.kg-cal-mini-grid { display:grid; grid-template-columns:repeat(7,1fr); gap:2px; }
.kg-cal-mini-dname { text-align:center; font-size:0.6rem; font-weight:700; color:var(--mu); padding-bottom:3px; text-transform:uppercase; }
.kg-cal-mini-cell { aspect-ratio:1; border-radius:4px; display:flex; align-items:center; justify-content:center; font-size:0.63rem; font-weight:600; cursor:default; transition:transform 0.1s; }
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

@media (max-width:768px) {
    .kg-kpi-row  { grid-template-columns:repeat(3,1fr); }
    .kg-stat-grid { grid-template-columns:1fr; }
}
</style>

{{-- ── HERO ── --}}
<div class="kg-hero">
    <div class="kg-hero-row">
        <div>
            <h1 class="kg-hero-title"><i class="fas fa-calendar-check"></i> Kegiatan & Absensi</h1>
            <p class="kg-hero-sub">{{ $santri->nama_lengkap }} &nbsp;·&nbsp; Kelas {{ $namaKelas }}</p>
        </div>
        <div class="kg-hero-right">
            <div class="kg-hero-day">{{ \Carbon\Carbon::now()->locale('id')->isoFormat('dddd') }}</div>
            <div class="kg-hero-date">{{ \Carbon\Carbon::now()->locale('id')->isoFormat('D MMMM YYYY') }}</div>
        </div>
    </div>
    <div class="kg-hero-badges">
        <span class="kg-badge"><i class="fas fa-id-card"></i> {{ $santri->nis ?? $santri->id_santri }}</span>
        <span class="kg-badge"><i class="fas fa-chart-line"></i> {{ $persentaseKehadiran }}% kehadiran</span>
        @if($streak > 0)
            <span class="kg-badge fire"><i class="fas fa-fire"></i> Streak {{ $streak }}x hadir</span>
        @endif
    </div>
</div>

{{-- ── KPI 5 CARDS ── --}}
<div class="kg-kpi-row">
    <div class="kg-kpi c-green">
        <div class="kg-kpi-ic"><i class="fas fa-list-alt"></i></div>
        <div class="kg-kpi-v">{{ $expectedTotal }}</div>
        <div class="kg-kpi-l">Wajib Hadir</div>
        <div class="kg-kpi-bar"><div class="kg-kpi-fill" style="width:100%;background:#2bbd8e;"></div></div>
    </div>
    <div class="kg-kpi c-blue">
        <div class="kg-kpi-ic"><i class="fas fa-check-circle"></i></div>
        <div class="kg-kpi-v">{{ $hadirEfektif }}</div>
        <div class="kg-kpi-l">Hadir
            @if($terlambatRange > 0)
                <span style="display:block;font-size:0.67rem;color:#f59e0b;">(+{{ $terlambatRange }} terlambat)</span>
            @endif
        </div>
        <div class="kg-kpi-bar"><div class="kg-kpi-fill" style="width:{{ $expectedTotal > 0 ? round($hadirEfektif/$expectedTotal*100) : 0 }}%;background:#3b82f6;"></div></div>
    </div>
    <div class="kg-kpi c-gold">
        <div class="kg-kpi-ic"><i class="fas fa-percentage"></i></div>
        <div class="kg-kpi-v">{{ $persentaseKehadiran }}%</div>
        <div class="kg-kpi-l">Kehadiran</div>
        <div class="kg-kpi-bar"><div class="kg-kpi-fill" style="width:{{ $persentaseKehadiran }}%;background:#f5a623;"></div></div>
    </div>
    <div class="kg-kpi c-orange">
        <div class="kg-kpi-ic"><i class="fas fa-hourglass-half"></i></div>
        <div class="kg-kpi-v">{{ $belumAbsenRange }}</div>
        <div class="kg-kpi-l">Belum Absen</div>
        <div class="kg-kpi-bar"><div class="kg-kpi-fill" style="width:{{ $expectedTotal > 0 ? round($belumAbsenRange/$expectedTotal*100) : 0 }}%;background:#f97316;"></div></div>
    </div>
    <div class="kg-kpi c-red">
        <div class="kg-kpi-ic"><i class="fas fa-times-circle"></i></div>
        <div class="kg-kpi-v">{{ $alpaRange }}</div>
        <div class="kg-kpi-l">Alpa</div>
        <div class="kg-kpi-bar"><div class="kg-kpi-fill" style="width:{{ $expectedTotal > 0 ? round($alpaRange/$expectedTotal*100) : 0 }}%;background:#e53e3e;"></div></div>
    </div>
</div>

{{-- ── TABS — 2 tab saja ── --}}
<div class="kg-tabs">
    <button class="kg-tab {{ $activeTab === 'statistik' ? 'active' : '' }}" onclick="switchTab('statistik',this)">
        <i class="fas fa-chart-bar"></i> Statistik
    </button>
    <button class="kg-tab {{ $activeTab === 'jadwal' ? 'active' : '' }}" onclick="switchTab('jadwal',this)">
        <i class="fas fa-clock"></i> Jadwal
        @if($jadwalDalamRange->count() > 0)
            <span style="background:var(--g);color:white;border-radius:10px;padding:1px 6px;font-size:0.67rem;">{{ $jadwalDalamRange->count() }}</span>
        @endif
    </button>
</div>

{{-- ╔══════════════════════════════════════════╗ --}}
{{-- ║  PANEL STATISTIK                         ║ --}}
{{-- ╚══════════════════════════════════════════╝ --}}
<div class="kg-panel {{ $activeTab === 'statistik' ? 'active' : '' }}" id="panel-statistik">

    <form method="GET" action="{{ route('santri.kegiatan.index') }}" id="formStat">
        <input type="hidden" name="tab" value="statistik">
        <input type="hidden" name="preset_jad"    value="{{ $jadPreset }}">
        <input type="hidden" name="preset_stat"    id="hStat"     value="{{ $statPreset }}">
        <input type="hidden" name="stat_date_from" id="hStatFrom" value="{{ request('stat_date_from') }}">
        <input type="hidden" name="stat_date_to"   id="hStatTo"   value="{{ request('stat_date_to') }}">

        <div class="kg-tab-filter">
            <div class="kg-fg">
                <label><i class="fas fa-bolt"></i> Periode</label>
                <div class="kg-presets" id="statPresets">
                    @foreach(['today'=>'Hari Ini','this_week'=>'Minggu Ini','last_30'=>'30 Hari','this_month'=>'Bulan Ini','last_month'=>'Bulan Lalu'] as $v=>$l)
                        <button type="button" class="kg-preset-btn {{ $statPreset===$v ? 'active' : '' }}"
                                onclick="setPreset('stat','{{ $v }}')">{{ $l }}</button>
                    @endforeach
                </div>
            </div>
            <div class="kg-fg">
                <label><i class="fas fa-calendar-alt"></i> Kustom</label>
                <div class="kg-date-range">
                    <input type="date" id="inpStatFrom" value="{{ request('stat_date_from', $statFrom->format('Y-m-d')) }}" onchange="setCustom('stat')">
                    <span>—</span>
                    <input type="date" id="inpStatTo"   value="{{ request('stat_date_to',   $statTo->format('Y-m-d')) }}"   onchange="setCustom('stat')">
                </div>
            </div>
            <button type="submit" class="kg-apply-btn"><i class="fas fa-sync-alt"></i> Terapkan</button>
            <div class="kg-filter-label">
                <i class="fas fa-calendar-check"></i>
                {{ $statFrom->locale('id')->isoFormat('D MMM YYYY') }} &ndash; {{ $statTo->locale('id')->isoFormat('D MMM YYYY') }}
            </div>
        </div>
    </form>

    <div class="kg-stat-grid">
        <div class="kg-chart-box">
            <div class="kg-chart-title">
                <i class="fas fa-chart-line" style="color:var(--m);"></i> Tren Kehadiran
                <span style="margin-left:auto;font-size:0.73rem;color:var(--mu);font-weight:500;">{{ $diffDays<=31?'Harian':'Mingguan' }}</span>
            </div>
            <canvas id="chartTren" style="max-height:220px;"></canvas>
        </div>
        <div class="kg-chart-box">
            <div class="kg-chart-title"><i class="fas fa-chart-pie" style="color:var(--gd);"></i> Distribusi Status</div>
            <canvas id="chartDonut" style="max-height:200px;"></canvas>
        </div>
    </div>

    {{-- Absensi Terbaru + Kalender --}}
    <div style="display:grid; grid-template-columns:1fr auto; gap:14px; margin-bottom:14px; align-items:start;">

        {{-- Catatan Absensi Terbaru --}}
        <div class="kg-chart-box" style="min-width:0;">
            <div class="kg-chart-title">
                <i class="fas fa-history" style="color:var(--m);"></i>
                Absensi Terbaru
                <span style="margin-left:auto;font-size:0.71rem;color:var(--mu);font-weight:500;">
                    {{ $statFrom->locale('id')->isoFormat('D MMM') }} – {{ $statTo->locale('id')->isoFormat('D MMM YY') }}
                </span>
            </div>

            @if($recentAbsensi->count() > 0)
                <div class="kg-recent-list">
                    @foreach($recentAbsensi as $ab)
                        @php $sl = strtolower($ab->status); @endphp
                        <div class="kg-recent-item s-{{ $sl }}">
                            <div class="kg-recent-ic">
                                @if($ab->status === 'Hadir')         <i class="fas fa-check"></i>
                                @elseif($ab->status === 'Terlambat') <i class="fas fa-clock"></i>
                                @elseif($ab->status === 'Izin')      <i class="fas fa-info"></i>
                                @elseif($ab->status === 'Sakit')     <i class="fas fa-heartbeat"></i>
                                @elseif($ab->status === 'Alpa')      <i class="fas fa-times"></i>
                                @elseif($ab->status === 'Pulang')    <i class="fas fa-home"></i>
                                @endif
                            </div>
                            <div class="kg-recent-info">
                                <div class="kg-recent-name">{{ $ab->kegiatan->nama_kegiatan }}</div>
                                <div class="kg-recent-meta">
                                    <span><i class="fas fa-tag"></i> {{ $ab->kegiatan->kategori->nama_kategori }}</span>
                                    @if($ab->waktu_absen)
                                        <span><i class="fas fa-clock"></i> {{ \Carbon\Carbon::parse($ab->waktu_absen)->format('H:i') }}</span>
                                    @endif
                                    @php $metode = $ab->metode_absen ?? ''; @endphp
                                    <span>
                                        <i class="fas fa-{{ $metode === 'RFID' ? 'id-card' : ($metode === 'Import_Mesin' ? 'desktop' : 'hand-pointer') }}"></i>
                                        {{ $metode === 'Import_Mesin' ? 'Mesin' : ($metode ?: 'Manual') }}
                                    </span>
                                </div>
                            </div>
                            <div class="kg-recent-right">
                                <span class="kpill {{ $sl }}" style="font-size:0.72rem;padding:2px 9px;">{{ $ab->status }}</span>
                                <div class="kg-recent-date">
                                    {{ \Carbon\Carbon::parse($ab->tanggal)->locale('id')->isoFormat('D MMM') }}
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p style="text-align:center;color:var(--mu);font-size:0.84rem;padding:20px 0;">
                    Belum ada absensi dalam periode ini.
                </p>
            @endif
        </div>

        {{-- Kalender Mini --}}
        @php $calMonth = collect($heatmapMonths)->last(); @endphp
        @if($calMonth)
        <div class="kg-chart-box kg-cal-mini">
            <div class="kg-chart-title" style="margin-bottom:8px;font-size:0.82rem;">
                <i class="fas fa-calendar-alt" style="color:var(--g);"></i> {{ $calMonth['label'] }}
            </div>
            <div style="display:flex;gap:3px;align-items:center;margin-bottom:7px;font-size:0.67rem;color:var(--mu);">
                @foreach(['#f3f4f6','#bbf7d0','#4ade80','#16a34a','#064e2d'] as $hc)
                    <div style="width:7px;height:7px;border-radius:2px;background:{{ $hc }};flex-shrink:0;"></div>
                @endforeach
                <span style="margin-left:2px;">Hadir</span>
            </div>
            <div class="kg-cal-mini-grid">
                @foreach(['S','S','R','K','J','S','M'] as $hn)
                    <div class="kg-cal-mini-dname">{{ $hn }}</div>
                @endforeach
                @for($e = 1; $e < $calMonth['firstDayOfWeek']; $e++)
                    <div></div>
                @endfor
                @foreach($calMonth['days'] as $day)
                    <div class="kg-cal-mini-cell l{{ $day['level'] }} {{ $day['is_today'] ? 'is-today' : '' }} {{ !$day['in_range'] ? 'out-range' : '' }}"
                         title="{{ \Carbon\Carbon::parse($day['date'])->locale('id')->isoFormat('D MMM') }}{{ $day['total'] > 0 ? ': '.$day['count'].'/'.$day['total'].' hadir' : '' }}">
                        {{ $day['day'] }}
                    </div>
                @endforeach
            </div>
        </div>
        @endif

    </div>
</div>

{{-- ╔══════════════════════════════════════════╗ --}}
{{-- ║  PANEL JADWAL                            ║ --}}
{{-- ╚══════════════════════════════════════════╝ --}}
<div class="kg-panel {{ $activeTab === 'jadwal' ? 'active' : '' }}" id="panel-jadwal">

    <form method="GET" action="{{ route('santri.kegiatan.index') }}" id="formJad">
        <input type="hidden" name="tab" value="jadwal">
        <input type="hidden" name="preset_stat"   value="{{ $statPreset }}">
        <input type="hidden" name="preset_jad"    id="hJad"     value="{{ $jadPreset }}">
        <input type="hidden" name="jad_date_from" id="hJadFrom" value="{{ request('jad_date_from') }}">
        <input type="hidden" name="jad_date_to"   id="hJadTo"   value="{{ request('jad_date_to') }}">

        <div class="kg-tab-filter">
            <div class="kg-fg">
                <label><i class="fas fa-bolt"></i> Periode</label>
                <div class="kg-presets" id="jadPresets">
                    @foreach(['today'=>'Hari Ini','this_week'=>'Minggu Ini','this_month'=>'Bulan Ini','last_month'=>'Bulan Lalu'] as $v=>$l)
                        <button type="button" class="kg-preset-btn {{ $jadPreset===$v ? 'active' : '' }}"
                                onclick="setPreset('jad','{{ $v }}')">{{ $l }}</button>
                    @endforeach
                </div>
            </div>
            <div class="kg-fg">
                <label><i class="fas fa-calendar-alt"></i> Kustom</label>
                <div class="kg-date-range">
                    <input type="date" id="inpJadFrom" value="{{ request('jad_date_from', $jadFrom->format('Y-m-d')) }}" onchange="setCustom('jad')">
                    <span>—</span>
                    <input type="date" id="inpJadTo"   value="{{ request('jad_date_to',   $jadTo->format('Y-m-d')) }}"   onchange="setCustom('jad')">
                </div>
            </div>
            <button type="submit" class="kg-apply-btn"><i class="fas fa-sync-alt"></i> Terapkan</button>
            <div class="kg-filter-label">
                <i class="fas fa-calendar-check"></i>
                {{ $jadFrom->locale('id')->isoFormat('D MMM') }} &ndash; {{ $jadTo->locale('id')->isoFormat('D MMM YYYY') }}
            </div>
        </div>
    </form>

    @if($jadwalDalamRange->count() > 0)
        @php
            $hariOrder  = ['Senin','Selasa','Rabu','Kamis','Jumat','Sabtu','Ahad'];
            $jadGrouped = $jadwalDalamRange->groupBy('hari')
                ->sortBy(fn($v,$k) => array_search($k, $hariOrder));
        @endphp
        @foreach($jadGrouped as $hari => $jadwals)
            <div class="kg-jadwal-group">
                <div class="kg-hari-label {{ $hari === $hariIni ? 'today-label' : '' }}">
                    <i class="fas fa-calendar-day"></i> {{ $hari }}
                    @if($hari === $hariIni) <span style="font-size:0.66rem;opacity:0.85;">(Hari Ini)</span> @endif
                </div>
                @foreach($jadwals as $jadwal)
                    @php
                        $statusAbsen = $hari === $hariIni
                            ? ($absensiHariIni[$jadwal->kegiatan_id] ?? null)
                            : ($absensiDalamRange[$jadwal->kegiatan_id] ?? null);
                        $sc = $statusAbsen ? 's-' . strtolower($statusAbsen) : 's-belum';
                    @endphp
                    <div class="kg-jadwal-card {{ $sc }}">
                        <div class="kg-time">
                            <div class="kg-time-main">{{ date('H:i', strtotime($jadwal->waktu_mulai)) }}</div>
                            <div class="kg-time-end">{{ date('H:i', strtotime($jadwal->waktu_selesai)) }}</div>
                        </div>
                        <div class="kg-divider"></div>
                        <div class="kg-jinfo">
                            <div class="kg-jname">{{ $jadwal->nama_kegiatan }}</div>
                            <div class="kg-jmeta">
                                <span><i class="fas fa-tag"></i> {{ $jadwal->kategori->nama_kategori }}</span>
                                @if($jadwal->materi)
                                    <span><i class="fas fa-book"></i> {{ Str::limit($jadwal->materi, 28) }}</span>
                                @endif
                            </div>
                        </div>
                        {{-- Status pill --}}
                        @if($statusAbsen)
                            <span class="kpill {{ strtolower($statusAbsen) }}">
                                @if($statusAbsen === 'Terlambat') <i class="fas fa-clock"></i>
                                @elseif($statusAbsen === 'Pulang') <i class="fas fa-home"></i>
                                @endif
                                {{ $statusAbsen }}
                            </span>
                        @elseif($hari === $hariIni)
                            <span class="kpill belum"><i class="fas fa-hourglass-half"></i> Belum</span>
                        @endif
                        {{-- Tombol Detail → ke show() --}}
                        <a href="{{ route('santri.kegiatan.show', $jadwal->kegiatan_id) }}?from_tab=jadwal"
                           class="kg-detail-btn">
                            <i class="fas fa-chart-bar"></i> Detail
                        </a>
                    </div>
                @endforeach
            </div>
        @endforeach
    @else
        <div class="kg-empty">
            <i class="fas fa-calendar-times"></i>
            <p>Tidak ada kegiatan terjadwal dalam periode ini.</p>
        </div>
    @endif
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
    var cap = scope.charAt(0).toUpperCase() + scope.slice(1);
    document.getElementById('h' + cap).value          = val;
    document.getElementById('h' + cap + 'From').value = '';
    document.getElementById('h' + cap + 'To').value   = '';
    document.getElementById('form' + cap).submit();
}

function setCustom(scope) {
    var cap = scope.charAt(0).toUpperCase() + scope.slice(1);
    document.getElementById('h' + cap).value          = '';
    document.getElementById('h' + cap + 'From').value = document.getElementById('inp' + cap + 'From').value;
    document.getElementById('h' + cap + 'To').value   = document.getElementById('inp' + cap + 'To').value;
    document.querySelectorAll('#' + scope + 'Presets .kg-preset-btn').forEach(b => b.classList.remove('active'));
}

// ── CHARTS ───────────────────────────────────────
function initCharts() {
    const trenLabels = @json(collect($dataGrafik)->pluck('label'));
    const trenHadir  = @json(collect($dataGrafik)->pluck('hadir'));
    const trenTotal  = @json(collect($dataGrafik)->pluck('total'));

    new Chart(document.getElementById('chartTren'), {
        type: 'line',
        data: {
            labels: trenLabels,
            datasets: [
                {
                    label: 'Hadir (incl. Terlambat)',
                    data: trenHadir,
                    borderColor: '#2bbd8e', backgroundColor: 'rgba(43,189,142,0.1)',
                    borderWidth: 3, pointRadius: trenLabels.length > 20 ? 2 : 5,
                    pointBackgroundColor: '#2bbd8e', tension: 0.4, fill: true
                },
                {
                    label: 'Total Tercatat',
                    data: trenTotal,
                    borderColor: '#cbd5e1', backgroundColor: 'transparent',
                    borderWidth: 2, borderDash: [4,4],
                    pointRadius: trenLabels.length > 20 ? 2 : 4,
                    pointBackgroundColor: '#cbd5e1', tension: 0.4
                }
            ]
        },
        options: {
            responsive: true, maintainAspectRatio: true,
            plugins: { legend: { position:'top', labels:{ font:{ size:11, weight:'600' } } } },
            scales: {
                y: { beginAtZero:true, ticks:{ stepSize:1 }, grid:{ color:'rgba(0,0,0,0.04)' } },
                x: { grid:{ display:false }, ticks:{ maxRotation:45, font:{ size:10 }, maxTicksLimit:12 } }
            }
        }
    });

    new Chart(document.getElementById('chartDonut'), {
        type: 'doughnut',
        data: {
            labels: ['Hadir', 'Terlambat', 'Izin', 'Sakit', 'Alpa', 'Pulang', 'Belum Absen'],
            datasets: [{
                data: [
                    {{ $hadirRange }},
                    {{ $terlambatRange }},
                    {{ $izinRange }},
                    {{ $sakitRange }},
                    {{ $alpaRange }},
                    {{ $pulangRange }},
                    {{ $belumAbsenRange }}
                ],
                backgroundColor: ['#2bbd8e','#f59e0b','#3b82f6','#8b5cf6','#e53e3e','#0d9488','#d1d5db'],
                borderWidth: 3, borderColor: '#fff'
            }]
        },
        options: {
            responsive: true, maintainAspectRatio: true, cutout: '65%',
            plugins: {
                legend: { position:'bottom', labels:{ padding:10, font:{ size:10 } } },
                tooltip: { callbacks: {
                    label: function(ctx) {
                        var total = {{ $expectedTotal }};
                        var pct   = total > 0 ? ((ctx.parsed / total) * 100).toFixed(1) : 0;
                        return ctx.label + ': ' + ctx.parsed + ' (' + pct + '%)';
                    }
                }}
            }
        }
    });
}

// ── INIT ─────────────────────────────────────────
document.addEventListener('DOMContentLoaded', function() {
    var tab  = new URLSearchParams(window.location.search).get('tab') || 'statistik';
    var map  = { statistik: 0, jadwal: 1 };
    var idx  = map[tab] ?? 0;
    var tabs = document.querySelectorAll('.kg-tab');
    if (tabs[idx]) tabs[idx].click();
});
</script>
@endsection