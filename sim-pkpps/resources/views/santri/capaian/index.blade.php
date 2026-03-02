{{-- resources/views/santri/capaian/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Capaian Materi')

@section('content')

<style>
/* ====== CAPAIAN PAGE STYLES ====== */
.cap-hero {
    background: linear-gradient(135deg, var(--primary-dark, #1a4980) 0%, var(--primary-color) 60%, #2a8c6e 100%);
    border-radius: var(--border-radius);
    padding: 28px 28px 100px;
    margin-bottom: -70px;
    position: relative;
    overflow: hidden;
    color: #fff;
}
.cap-hero::before {
    content: '';
    position: absolute;
    right: -60px; top: -60px;
    width: 260px; height: 260px;
    background: rgba(255,255,255,0.06);
    border-radius: 50%;
}
.cap-hero::after {
    content: '';
    position: absolute;
    right: 80px; bottom: -80px;
    width: 180px; height: 180px;
    background: rgba(255,255,255,0.04);
    border-radius: 50%;
}
.cap-hero h2 { margin: 0 0 6px; font-size: 1.4rem; opacity: 0.92; }
.cap-hero p  { margin: 0; opacity: 0.7; font-size: 0.9em; }

/* Circular Progress */
.circle-group {
    display: flex;
    justify-content: center;
    gap: 28px;
    flex-wrap: wrap;
    margin-top: 12px;
    position: relative;
    z-index: 2;
}
.circle-card {
    background: #fff;
    border-radius: 16px;
    box-shadow: 0 8px 32px rgba(0,0,0,0.13);
    padding: 20px 22px 16px;
    text-align: center;
    min-width: 130px;
    flex: 1;
    max-width: 170px;
    transition: transform 0.2s, box-shadow 0.2s;
    animation: slideUp 0.5s ease both;
}
.circle-card:nth-child(1) { animation-delay: 0.05s; }
.circle-card:nth-child(2) { animation-delay: 0.12s; }
.circle-card:nth-child(3) { animation-delay: 0.19s; }
.circle-card:nth-child(4) { animation-delay: 0.26s; }
.circle-card:hover { transform: translateY(-4px); box-shadow: 0 14px 40px rgba(0,0,0,0.16); }

@keyframes slideUp {
    from { opacity: 0; transform: translateY(20px); }
    to   { opacity: 1; transform: translateY(0); }
}

.circle-wrap {
    position: relative;
    width: 80px; height: 80px;
    margin: 0 auto 10px;
}
.circle-wrap svg { transform: rotate(-90deg); }
.circle-track { fill: none; stroke: #f0f0f0; stroke-width: 7; }
.circle-fill  { fill: none; stroke-width: 7; stroke-linecap: round;
                transition: stroke-dashoffset 1.2s cubic-bezier(0.4,0,0.2,1); }
.circle-center {
    position: absolute; inset: 0;
    display: flex; align-items: center; justify-content: center;
    font-size: 0.88rem; font-weight: 800; line-height: 1.1; flex-direction: column;
}
.circle-label { font-size: 0.7rem; color: var(--text-light); margin-top: 4px; font-weight: 600; }
.circle-sub   { font-size: 0.78rem; color: var(--text-light); margin-top: 2px; }

/* KPI row */
.kpi-strip {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
    gap: 12px;
    margin: 14px 0;
}
.kpi-box {
    background: #fff;
    border-radius: 12px;
    padding: 14px 16px;
    border-left: 4px solid;
    box-shadow: 0 2px 10px rgba(0,0,0,0.06);
    display: flex; align-items: center; gap: 12px;
    transition: transform 0.2s;
}
.kpi-box:hover { transform: translateX(3px); }
.kpi-box .kpi-icon { font-size: 1.5rem; opacity: 0.7; flex-shrink: 0; }
.kpi-box .kpi-val  { font-size: 1.5rem; font-weight: 800; line-height: 1; }
.kpi-box .kpi-lbl  { font-size: 0.72rem; color: var(--text-light); margin-top: 2px; text-transform: uppercase; letter-spacing: 0.4px; }

/* Tab Navigation */
.cap-tabs {
    display: flex; gap: 4px; flex-wrap: wrap;
    background: #f5f8f6; border-radius: 10px;
    padding: 4px; margin-bottom: 16px;
}
.cap-tab {
    flex: 1; min-width: 100px;
    padding: 9px 14px;
    border: none; border-radius: 8px;
    background: transparent; color: var(--text-light);
    font-weight: 600; font-size: 0.82rem; cursor: pointer;
    transition: all 0.2s;
}
.cap-tab.active {
    background: #fff; color: var(--primary-color);
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}
.cap-tab:hover:not(.active) { color: var(--primary-color); background: rgba(255,255,255,0.6); }
.tab-panel { display: none; }
.tab-panel.active { display: block; animation: fadeIn 0.3s ease; }
@keyframes fadeIn { from { opacity:0; transform:translateY(6px); } to { opacity:1; transform:translateY(0); } }

/* Category Cards */
.kat-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 14px; margin-bottom: 16px;
}
.kat-card {
    background: #fff;
    border-radius: 14px;
    padding: 18px;
    box-shadow: 0 2px 12px rgba(0,0,0,0.07);
    position: relative; overflow: hidden;
    border-top: 4px solid;
    transition: transform 0.2s, box-shadow 0.2s;
}
.kat-card:hover { transform: translateY(-3px); box-shadow: 0 8px 24px rgba(0,0,0,0.11); }
.kat-card .kat-bg-icon {
    position: absolute; right: 12px; top: 12px;
    font-size: 3rem; opacity: 0.06; line-height: 1;
}
.kat-card h4 { margin: 0 0 10px; font-size: 0.95rem; }
.kat-stats { display: flex; gap: 16px; margin-top: 10px; }
.kat-stat { text-align: center; }
.kat-stat .ks-val { font-size: 1.1rem; font-weight: 800; }
.kat-stat .ks-lbl { font-size: 0.68rem; color: var(--text-light); text-transform: uppercase; }

/* Progress bar custom */
.prog-track {
    height: 10px; background: #f0f0f0; border-radius: 20px;
    overflow: hidden; margin: 8px 0;
}
.prog-fill {
    height: 100%; border-radius: 20px;
    transition: width 1s cubic-bezier(0.4,0,0.2,1);
}

/* Materi list */
.materi-list { display: flex; flex-direction: column; gap: 10px; }
.materi-item {
    background: #fff;
    border-radius: 12px;
    padding: 14px 18px;
    box-shadow: 0 1px 8px rgba(0,0,0,0.06);
    display: flex; gap: 14px; align-items: center;
    transition: box-shadow 0.2s, transform 0.2s;
    text-decoration: none; color: inherit;
    border: 1px solid transparent;
}
.materi-item:hover {
    box-shadow: 0 4px 20px rgba(0,0,0,0.1);
    transform: translateX(3px);
    border-color: var(--primary-light);
}
.materi-badge {
    width: 44px; height: 44px; border-radius: 12px;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.2rem; flex-shrink: 0;
}
.materi-info { flex: 1; min-width: 0; }
.materi-info h4 { margin: 0 0 4px; font-size: 0.95rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.materi-info small { color: var(--text-light); font-size: 0.78rem; }
.materi-pct {
    font-size: 1rem; font-weight: 800; min-width: 44px;
    text-align: right; line-height: 1.1;
}
.materi-pct small { font-size: 0.68rem; display: block; color: var(--text-light); font-weight: 400; }

/* Chart wrapper */
.chart-box {
    background: #fff; border-radius: 14px;
    padding: 18px; box-shadow: 0 2px 12px rgba(0,0,0,0.07);
}
.chart-box h4 { margin: 0 0 4px; font-size: 0.92rem; color: var(--primary-color); }
.chart-box p.sub { font-size: 0.76rem; color: #999; margin: 0 0 14px; }

/* Filter bar */
.filter-line {
    display: flex; gap: 10px; flex-wrap: wrap;
    align-items: center; margin-bottom: 14px;
}

/* Milestone banner */
.milestone-banner {
    border-radius: 12px; padding: 14px 18px;
    display: flex; align-items: center; gap: 14px;
    margin-bottom: 14px; animation: slideUp 0.4s ease both;
}

/* ===== ACCESS BANNER ===== */
.access-banner {
    border-radius: 14px;
    padding: 0;
    margin-bottom: 16px;
    animation: slideUp 0.4s ease both;
    overflow: hidden;
    box-shadow: 0 4px 20px rgba(0,0,0,0.12);
}
.access-banner-inner {
    display: flex;
    align-items: stretch;
    gap: 0;
    flex-wrap: wrap;
}
.access-banner-icon-col {
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 20px 22px;
    font-size: 2.2rem;
    min-width: 80px;
    flex-shrink: 0;
}
.access-banner-content {
    flex: 1;
    padding: 16px 16px 16px 4px;
    min-width: 0;
}
.access-banner-content h4 {
    margin: 0 0 4px;
    font-size: 1rem;
    font-weight: 700;
}
.access-banner-content p {
    margin: 0 0 10px;
    font-size: 0.83rem;
    opacity: 0.85;
    line-height: 1.5;
}
.access-banner-action {
    display: flex;
    align-items: center;
    padding: 16px 20px;
    flex-shrink: 0;
    flex-wrap: wrap;
    gap: 8px;
}
.btn-input-capaian {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 20px;
    border-radius: 10px;
    font-weight: 700;
    font-size: 0.9rem;
    text-decoration: none;
    border: none;
    cursor: pointer;
    transition: all 0.2s;
    white-space: nowrap;
}
.btn-input-capaian:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(0,0,0,0.2);
}
.countdown-pill {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 4px 10px;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 700;
}
@keyframes pulse-green {
    0%, 100% { box-shadow: 0 4px 20px rgba(102,187,106,0.2); }
    50%       { box-shadow: 0 4px 30px rgba(102,187,106,0.45); }
}
.access-open { animation: slideUp 0.4s ease both, pulse-green 2.5s ease-in-out infinite; }

/* Kalkulasi / Gantt styles */
.pred-hero {
    background: linear-gradient(135deg, #1a237e 0%, #283593 50%, #1565c0 100%);
    border-radius: 14px; padding: 20px 22px;
    color: #fff; margin-top: 14px; margin-bottom: 16px;
    display: flex; align-items: center; gap: 18px;
    position: relative; overflow: hidden; flex-wrap: wrap;
}
.pred-hero::after {
    content: '🎓';
    position: absolute; right: 20px; top: 50%;
    transform: translateY(-50%);
    font-size: 4rem; opacity: 0.15;
}
.pred-hero .ph-val { font-size: 2rem; font-weight: 800; line-height: 1; }
.pred-hero .ph-lbl { font-size: 0.76rem; opacity: 0.75; margin-top: 3px; }
.pred-hero .ph-title { font-size: 0.76rem; opacity: 0.75; margin-bottom: 4px; text-transform: uppercase; letter-spacing: 0.5px; }
.pred-hero .ph-div { width: 1px; height: 50px; background: rgba(255,255,255,0.2); flex-shrink: 0; }
.pred-hero .ph-col { text-align: center; flex: 1; min-width: 100px; }
.gantt-section { background: #fff; border-radius: 14px; padding: 18px; box-shadow: 0 2px 12px rgba(0,0,0,0.07); margin-bottom: 16px; }
.gantt-section h4 { margin: 0 0 4px; font-size: 0.95rem; color: var(--primary-color); }
.gantt-section p.sub { font-size: 0.78rem; color: #999; margin: 0 0 14px; }
.gantt-row-pred { display: flex; align-items: center; margin-bottom: 10px; gap: 10px; }
.gantt-lbl-pred { min-width: 130px; max-width: 130px; font-size: 0.78rem; font-weight: 600; color: #444; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.gantt-track-pred { flex: 1; background: #f0f4f8; border-radius: 20px; height: 20px; position: relative; overflow: hidden; }
.gantt-fill-pred { height: 100%; border-radius: 20px; display: flex; align-items: center; justify-content: flex-end; padding-right: 8px; font-size: 0.68rem; font-weight: 700; color: #fff; transition: width 1.2s cubic-bezier(0.4,0,0.2,1); min-width: 2px; }
.gantt-info-pred { min-width: 100px; max-width: 100px; font-size: 0.72rem; color: #777; text-align: right; }
.sem-history { background: #fff; border-radius: 14px; padding: 18px; box-shadow: 0 2px 12px rgba(0,0,0,0.07); margin-bottom: 16px; }
.pred-card-personal { background: linear-gradient(135deg, #e8f5e9, #f1f8e9); border: 2px solid #66bb6a; border-radius: 14px; padding: 18px; margin-bottom: 16px; display: flex; gap: 18px; align-items: flex-start; flex-wrap: wrap; }
.pred-stat { text-align: center; flex: 1; min-width: 80px; }
.pred-stat .ps-val { font-size: 1.4rem; font-weight: 800; }
.pred-stat .ps-lbl { font-size: 0.7rem; color: #555; margin-top: 2px; text-transform: uppercase; }

/* Responsive */
@media (max-width: 600px) {
    .circle-group { gap: 12px; }
    .circle-card { min-width: 100px; padding: 14px 12px 12px; }
    .kpi-strip { grid-template-columns: 1fr 1fr; }
    .cap-tabs { flex-direction: column; }
    .cap-tab { text-align: left; }
    .access-banner-action { padding: 0 16px 16px; width: 100%; }
    .btn-input-capaian { width: 100%; justify-content: center; }
    .pred-hero .ph-div { display: none; }
    .pred-hero { flex-direction: column; text-align: center; }
}
</style>

{{-- ===== HERO ===== --}}
<div class="cap-hero">
    <div style="position:relative;z-index:2;">
        <h2><i class="fas fa-book-reader"></i> Capaian Materi</h2>
        <p>Pantau progress hafalan dan pembelajaran, {{ $santri->nama_lengkap }} &bull; {{ $santri->kelasPrimary?->kelas?->nama_kelas ?? '-' }}</p>
    </div>
</div>

{{-- ===== CIRCLE CARDS ===== --}}
<div class="circle-group" style="position:relative;z-index:3;margin:0 0 18px;">

    @php
        $avgPct      = min(100, round($rataRataPersentase, 1));
        $alquranPct  = min(100, round($statistikKategori['Al-Qur\'an']['avg'], 1));
        $hadistPct   = min(100, round($statistikKategori['Hadist']['avg'], 1));
        $tambahanPct = min(100, round($statistikKategori['Materi Tambahan']['avg'], 1));
        $r    = 34;
        $circ = round(2 * 3.14159 * $r, 2);
        function dashOffset($pct, $circ) { return round($circ * (1 - $pct/100), 2); }
        $selesaiPct = $totalCapaian > 0 ? min(100, round(($materiSelesai/$totalCapaian)*100, 1)) : 0;
    @endphp

    {{-- Overall --}}
    <div class="circle-card">
        <div class="circle-wrap">
            <svg width="80" height="80" viewBox="0 0 80 80">
                <circle class="circle-track" cx="40" cy="40" r="{{ $r }}"/>
                <circle class="circle-fill" cx="40" cy="40" r="{{ $r }}"
                    stroke="var(--primary-color)"
                    stroke-dasharray="{{ $circ }}"
                    stroke-dashoffset="{{ dashOffset($avgPct, $circ) }}"
                    data-dashoffset="{{ dashOffset($avgPct, $circ) }}"
                    data-dasharray="{{ $circ }}"/>
            </svg>
            <div class="circle-center"><span style="color:var(--primary-color);">{{ $avgPct }}%</span></div>
        </div>
        <div class="circle-label">Rata-rata</div>
        <div class="circle-sub">{{ $totalCapaian }} materi</div>
    </div>

    {{-- Al-Qur'an --}}
    <div class="circle-card">
        <div class="circle-wrap">
            <svg width="80" height="80" viewBox="0 0 80 80">
                <circle class="circle-track" cx="40" cy="40" r="{{ $r }}"/>
                <circle class="circle-fill" cx="40" cy="40" r="{{ $r }}"
                    stroke="var(--success-color)"
                    stroke-dasharray="{{ $circ }}"
                    stroke-dashoffset="{{ dashOffset($alquranPct, $circ) }}"
                    data-dashoffset="{{ dashOffset($alquranPct, $circ) }}"
                    data-dasharray="{{ $circ }}"/>
            </svg>
            <div class="circle-center"><span style="color:var(--success-color);">{{ $alquranPct }}%</span></div>
        </div>
        <div class="circle-label">Al-Qur'an</div>
        <div class="circle-sub">{{ $statistikKategori["Al-Qur'an"]['count'] }} materi</div>
    </div>

    {{-- Hadist --}}
    <div class="circle-card">
        <div class="circle-wrap">
            <svg width="80" height="80" viewBox="0 0 80 80">
                <circle class="circle-track" cx="40" cy="40" r="{{ $r }}"/>
                <circle class="circle-fill" cx="40" cy="40" r="{{ $r }}"
                    stroke="var(--info-color)"
                    stroke-dasharray="{{ $circ }}"
                    stroke-dashoffset="{{ dashOffset($hadistPct, $circ) }}"
                    data-dashoffset="{{ dashOffset($hadistPct, $circ) }}"
                    data-dasharray="{{ $circ }}"/>
            </svg>
            <div class="circle-center"><span style="color:var(--info-color);">{{ $hadistPct }}%</span></div>
        </div>
        <div class="circle-label">Hadist</div>
        <div class="circle-sub">{{ $statistikKategori['Hadist']['count'] }} materi</div>
    </div>

    {{-- Materi Tambahan --}}
    <div class="circle-card">
        <div class="circle-wrap">
            <svg width="80" height="80" viewBox="0 0 80 80">
                <circle class="circle-track" cx="40" cy="40" r="{{ $r }}"/>
                <circle class="circle-fill" cx="40" cy="40" r="{{ $r }}"
                    stroke="var(--warning-color)"
                    stroke-dasharray="{{ $circ }}"
                    stroke-dashoffset="{{ dashOffset($tambahanPct, $circ) }}"
                    data-dashoffset="{{ dashOffset($tambahanPct, $circ) }}"
                    data-dasharray="{{ $circ }}"/>
            </svg>
            <div class="circle-center"><span style="color:var(--warning-color);">{{ $tambahanPct }}%</span></div>
        </div>
        <div class="circle-label">Tambahan</div>
        <div class="circle-sub">{{ $statistikKategori['Materi Tambahan']['count'] }} materi</div>
    </div>

    {{-- Khatam --}}
    <div class="circle-card">
        <div class="circle-wrap">
            <svg width="80" height="80" viewBox="0 0 80 80">
                <circle class="circle-track" cx="40" cy="40" r="{{ $r }}"/>
                <circle class="circle-fill" cx="40" cy="40" r="{{ $r }}"
                    stroke="#9b59b6"
                    stroke-dasharray="{{ $circ }}"
                    stroke-dashoffset="{{ dashOffset($selesaiPct, $circ) }}"
                    data-dashoffset="{{ dashOffset($selesaiPct, $circ) }}"
                    data-dasharray="{{ $circ }}"/>
            </svg>
            <div class="circle-center"><span style="color:#9b59b6;">{{ $materiSelesai }}</span></div>
        </div>
        <div class="circle-label">Khatam</div>
        <div class="circle-sub">dari {{ $totalCapaian }}</div>
    </div>

</div>

{{-- ===== ACCESS BANNER ===== --}}
@if($capaianAccessOpen)
<div class="access-banner access-open" style="background:linear-gradient(135deg, #5EA98C 0%, #5EA98C 60%, #6FBA9D 100%);">
    <div class="access-banner-inner">
        <div class="access-banner-icon-col" style="background:rgba(0,0,0,0.1);color:#fff;">
            <div style="text-align:center;">
                <div style="font-size:1.8rem;line-height:1;">✍️</div>
                <div style="font-size:0.6rem;font-weight:700;opacity:0.8;margin-top:4px;text-transform:uppercase;letter-spacing:0.5px;">AKTIF</div>
            </div>
        </div>
        <div class="access-banner-content" style="color:#fff;">
            <h4><i class="fas fa-unlock-alt" style="margin-right:6px;"></i>Akses Input Capaian Dibuka!</h4>
            <p>
                @if(!empty($capaianAccessConfig['catatan']))
                    📋 <em>{{ $capaianAccessConfig['catatan'] }}</em>
                @else
                    Ustadz/Ustadzah telah membuka akses untuk menginput data capaian kamu. Segera isi sebelum ditutup!
                @endif
            </p>
            <div style="display:flex;gap:8px;flex-wrap:wrap;align-items:center;">
                @if(!empty($capaianAccessConfig['opened_by']))
                <span style="background:rgba(255,255,255,0.18);border-radius:20px;padding:3px 10px;font-size:0.73rem;font-weight:600;">
                    <i class="fas fa-user-shield"></i> {{ $capaianAccessConfig['opened_by'] }}
                </span>
                @endif
                @if(!empty($capaianAccessConfig['opened_at']))
                <span style="background:rgba(255,255,255,0.18);border-radius:20px;padding:3px 10px;font-size:0.73rem;font-weight:600;">
                    <i class="fas fa-clock"></i> {{ \Carbon\Carbon::parse($capaianAccessConfig['opened_at'])->isoFormat('D MMM HH:mm') }}
                </span>
                @endif
                @if($capaianSisaWaktu)
                <span class="countdown-pill" style="background:rgba(241,237,199,0.25);color:#f9f3b5;border:1px solid rgba(255,235,59,0.4);">
                    <i class="fas fa-hourglass-half"></i> Tutup dalam: <strong>{{ $capaianSisaWaktu }}</strong>
                </span>
                @endif
                @if(!empty($capaianAccessConfig['id_semester']))
                @php $semLabel = $semesters->where('id_semester', $capaianAccessConfig['id_semester'])->first(); @endphp
                @if($semLabel)
                <span style="background:rgba(255,255,255,0.18);border-radius:20px;padding:3px 10px;font-size:0.73rem;font-weight:600;">
                    <i class="fas fa-calendar-alt"></i> {{ $semLabel->nama_semester }}
                </span>
                @endif
                @endif
            </div>
        </div>
        <div class="access-banner-action">
            <a href="{{ route('santri.capaian.input.create') }}" class="btn-input-capaian"
               style="background:#fff;color:#43a047;box-shadow:0 4px 16px rgba(0,0,0,0.15);">
                <i class="fas fa-edit"></i> Input Capaian Sekarang
            </a>
        </div>
    </div>
</div>
@else
<div class="access-banner" style="background:#f5f5f5;border:1.5px solid #e0e0e0;">
    <div class="access-banner-inner">
        <div class="access-banner-icon-col" style="background:#eeeeee;color:#bdbdbd;font-size:1.5rem;">🔒</div>
        <div class="access-banner-content" style="color:#757575;">
            <h4 style="color:#555;font-size:0.9rem;"><i class="fas fa-lock" style="margin-right:6px;color:#bdbdbd;"></i>Input Capaian Belum Tersedia</h4>
            <p style="font-size:0.8rem;margin:0;">Ustadz/Ustadzah belum membuka akses input capaian. Nantikan informasinya ya!</p>
        </div>
    </div>
</div>
@endif

{{-- ===== MILESTONE BANNER ===== --}}
@if($avgPct >= 100)
<div class="milestone-banner" style="background:linear-gradient(135deg,#e8f5e9,#c8e6c9);border:2px solid #66bb6a;">
    <span style="font-size:2rem;">🏆</span>
    <div>
        <strong style="color:#2e7d32;">Alhamdulillah, Khatam Semua Materi!</strong>
        <div style="font-size:0.82rem;color:#555;margin-top:2px;">Semua materi telah diselesaikan. Terus pertahankan!</div>
    </div>
</div>
@elseif($avgPct >= 75)
<div class="milestone-banner" style="background:linear-gradient(135deg,#e3f2fd,#bbdefb);border:2px solid #64b5f6;">
    <span style="font-size:2rem;">🔥</span>
    <div>
        <strong style="color:#1565c0;">Hampir Finish! Tinggal {{ 100 - $avgPct }}% lagi</strong>
        <div style="font-size:0.82rem;color:#555;margin-top:2px;">Progress sangat bagus! Pertahankan semangat belajar.</div>
    </div>
</div>
@elseif($avgPct >= 50)
<div class="milestone-banner" style="background:linear-gradient(135deg,#fffde7,#fff9c4);border:2px solid #fdd835;">
    <span style="font-size:2rem;">⚡</span>
    <div>
        <strong style="color:#f57f17;">Setengah Perjalanan! Terus Semangat</strong>
        <div style="font-size:0.82rem;color:#555;margin-top:2px;">Sudah melewati setengah jalan. Sedikit lagi menuju finish!</div>
    </div>
</div>
@endif

{{-- ===== TAB NAVIGATION — 3 tab saja (Grafik dihapus) ===== --}}
<div class="content-box" style="padding:0;overflow:hidden;">
    <div style="padding:14px 18px 0;">
        <div class="cap-tabs" id="capTabs">
            <button class="cap-tab active" data-tab="tab-overview">
                <i class="fas fa-chart-pie"></i> Ringkasan
            </button>
            <button class="cap-tab" data-tab="tab-materi">
                <i class="fas fa-list"></i> Daftar Materi
            </button>
            <button class="cap-tab" data-tab="tab-kalkulasi">
                <i class="fas fa-tasks"></i> Kalkulasi Progress
            </button>
        </div>
    </div>

    <div style="padding:0 18px 18px;">

    {{-- ===================================================================
         TAB 1: RINGKASAN
         Isi: KPI strip (tetap) + Kategori Cards (tetap) +
              [BARU] chart Progress per Kategori +
              [BARU] chart Progress Rata-rata per Semester +
              [BARU] chart Progress per Materi (horizontal bar)
         [DIHAPUS] Total Progress Keseluruhan (progress bar besar)
    =================================================================== --}}
    <div class="tab-panel active" id="tab-overview">

        {{-- KPI Strip — tetap sama --}}
        <div class="kpi-strip" style="margin-top:14px;">
            <div class="kpi-box" style="border-color:var(--primary-color);">
                <i class="fas fa-book-open kpi-icon" style="color:var(--primary-color);"></i>
                <div>
                    <div class="kpi-val" style="color:var(--primary-color);">{{ $totalCapaian }}</div>
                    <div class="kpi-lbl">Total Materi</div>
                </div>
            </div>
            <div class="kpi-box" style="border-color:var(--success-color);">
                <i class="fas fa-check-circle kpi-icon" style="color:var(--success-color);"></i>
                <div>
                    <div class="kpi-val" style="color:var(--success-color);">{{ $materiSelesai }}</div>
                    <div class="kpi-lbl">Selesai</div>
                </div>
            </div>
            <div class="kpi-box" style="border-color:var(--warning-color);">
                <i class="fas fa-hourglass-half kpi-icon" style="color:var(--warning-color);"></i>
                <div>
                    <div class="kpi-val" style="color:var(--warning-color);">{{ $totalCapaian - $materiSelesai }}</div>
                    <div class="kpi-lbl">Berlangsung</div>
                </div>
            </div>
            <div class="kpi-box" style="border-color:#9b59b6;">
                <i class="fas fa-percentage kpi-icon" style="color:#9b59b6;"></i>
                <div>
                    <div class="kpi-val" style="color:#9b59b6;">{{ $avgPct }}%</div>
                    <div class="kpi-lbl">Rata-rata</div>
                </div>
            </div>
        </div>

        {{-- Kategori Cards — tetap sama --}}
        <div class="kat-grid">
            @foreach([
                ['Al-Qur\'an',      'fas fa-book-quran', 'var(--success-color)', '#e8f5e9'],
                ['Hadist',          'fas fa-scroll',     'var(--info-color)',    '#e3f2fd'],
                ['Materi Tambahan', 'fas fa-book',       'var(--warning-color)', '#fffde7'],
            ] as [$kat, $icon, $color, $bg])
            @php $d = $statistikKategori[$kat]; $avg = min(100, round($d['avg'], 1)); @endphp
            <div class="kat-card" style="border-top-color:{{ $color }};">
                <i class="{{ $icon }} kat-bg-icon" style="color:{{ $color }};"></i>
                <h4>
                    <i class="{{ $icon }}" style="color:{{ $color }};margin-right:6px;"></i>{{ $kat }}
                </h4>
                <div style="display:flex;justify-content:space-between;font-size:0.78rem;color:var(--text-light);margin-bottom:4px;">
                    <span>Progress</span>
                    <strong style="color:{{ $color }};">{{ $avg }}%</strong>
                </div>
                <div class="prog-track">
                    <div class="prog-fill" style="width:{{ $avg }}%;background:{{ $color }};"></div>
                </div>
                <div class="kat-stats">
                    <div class="kat-stat">
                        <div class="ks-val" style="color:{{ $color }};">{{ $d['count'] }}</div>
                        <div class="ks-lbl">Materi</div>
                    </div>
                    <div class="kat-stat">
                        <div class="ks-val" style="color:var(--success-color);">{{ $d['selesai'] }}</div>
                        <div class="ks-lbl">Selesai</div>
                    </div>
                    <div class="kat-stat">
                        <div class="ks-val" style="color:var(--warning-color);">{{ $d['count'] - $d['selesai'] }}</div>
                        <div class="ks-lbl">Proses</div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        {{-- Charts: Bar Kategori + Line Semester (2 kolom) --}}
        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));gap:14px;margin-bottom:14px;">

            <div class="chart-box">
                <h4><i class="fas fa-chart-pie"></i> Progress per Kategori</h4>
                <p class="sub">Perbandingan rata-rata progress tiap kelompok materi.</p>
                <canvas id="chartKat" style="max-height:220px;"></canvas>
            </div>

            <div class="chart-box">
                <h4><i class="fas fa-chart-line"></i> Progress Rata-rata per Semester</h4>
                <p class="sub">Trend progress dari semester ke semester.</p>
                <canvas id="chartSemTrend" style="max-height:220px;"></canvas>
            </div>

        </div>

        {{-- Horizontal bar: Progress per Materi (full width) --}}
        <div class="chart-box">
            <h4><i class="fas fa-sort-amount-up"></i> Progress per Materi</h4>
            <p class="sub">Urutan progress dari yang paling sedikit hingga paling banyak.</p>
            <div id="chartMateriWrap"><canvas id="chartMateri"></canvas></div>
        </div>

    </div>{{-- /tab-overview --}}

    {{-- ===================================================================
         TAB 2: DAFTAR MATERI — tidak berubah
    =================================================================== --}}
    <div class="tab-panel" id="tab-materi">

        <div class="filter-line" style="margin-top:14px;">
            <form method="GET" style="display:flex;gap:8px;align-items:center;flex-wrap:wrap;flex:1;">
                <select name="id_semester" class="form-control" style="width:auto;min-width:200px;" onchange="this.form.submit()">
                    <option value="">-- Semua Semester --</option>
                    @foreach($semesters as $sem)
                    <option value="{{ $sem->id_semester }}" {{ $selectedSemester == $sem->id_semester ? 'selected' : '' }}>
                        {{ $sem->nama_semester }}
                        @if($semesterAktif && $sem->id_semester == $semesterAktif->id_semester) ★ @endif
                    </option>
                    @endforeach
                </select>
                @if($selectedSemester)
                <a href="{{ route('santri.capaian.index') }}" class="btn btn-secondary btn-sm"><i class="fas fa-redo"></i> Reset</a>
                @endif
            </form>
            <div style="font-size:0.82rem;color:var(--text-light);">{{ $capaians->count() }} materi ditemukan</div>
            @if($capaianAccessOpen)
            <a href="{{ route('santri.capaian.input.create') }}"
               style="display:inline-flex;align-items:center;gap:6px;padding:7px 14px;
                      background:linear-gradient(135deg,#2e7d32,#43a047);
                      color:#fff;border-radius:8px;font-weight:700;font-size:0.8rem;
                      text-decoration:none;white-space:nowrap;">
                <i class="fas fa-edit"></i> Input Capaian
            </a>
            @endif
        </div>

        @if($capaians->count() > 0)
        @php $grouped = $capaians->groupBy(fn($c) => $c->materi->kategori); @endphp

        @foreach([
            ['Al-Qur\'an',      'fas fa-book-quran', 'var(--success-color)', 'badge-success'],
            ['Hadist',          'fas fa-scroll',     'var(--info-color)',    'badge-info'   ],
            ['Materi Tambahan', 'fas fa-book',       'var(--warning-color)', 'badge-warning'],
        ] as [$kat, $icon, $color, $badge])
        @if(isset($grouped[$kat]) && $grouped[$kat]->count() > 0)
        <div style="margin-bottom:20px;">
            <h4 style="margin:0 0 10px;color:{{ $color }};display:flex;align-items:center;gap:8px;font-size:0.95rem;">
                <i class="{{ $icon }}"></i> {{ $kat }}
                <span class="badge {{ $badge }}" style="font-size:0.72rem;">{{ $grouped[$kat]->count() }} materi</span>
            </h4>
            <div class="materi-list">
                @foreach($grouped[$kat] as $capaian)
                @php
                    $pct = min(100, round($capaian->persentase, 1));
                    if ($pct >= 100)    { $statColor = '#2e7d32'; $statIcon = 'fa-check-circle'; }
                    elseif ($pct >= 75) { $statColor = '#1565c0'; $statIcon = 'fa-fire'; }
                    elseif ($pct >= 50) { $statColor = '#e65100'; $statIcon = 'fa-bolt'; }
                    elseif ($pct >= 25) { $statColor = '#f57f17'; $statIcon = 'fa-seedling'; }
                    else                { $statColor = '#c62828'; $statIcon = 'fa-circle'; }
                @endphp
                <a href="{{ route('santri.capaian.show', $capaian->id) }}" class="materi-item">
                    <div class="materi-badge" style="background:{{ $color }}22;color:{{ $color }};">
                        <i class="{{ $icon }}"></i>
                    </div>
                    <div class="materi-info">
                        <h4>{{ $capaian->materi->nama_kitab }}</h4>
                        <small>{{ count($capaian->pages_array) }}/{{ $capaian->materi->total_halaman }} hal &bull; {{ \Carbon\Carbon::parse($capaian->tanggal_input)->isoFormat('D MMM YYYY') }}</small>
                        <div class="prog-track" style="height:5px;margin-top:6px;">
                            <div class="prog-fill" style="width:{{ $pct }}%;background:{{ $color }};"></div>
                        </div>
                    </div>
                    <div class="materi-pct" style="color:{{ $statColor }};">
                        <i class="fas {{ $statIcon }}" style="font-size:0.75rem;display:block;margin-bottom:2px;"></i>
                        {{ $pct }}%
                        <small>{{ $pct >= 100 ? 'Khatam' : ($pct >= 75 ? 'Hampir' : ($pct >= 50 ? 'Tengah' : 'Proses')) }}</small>
                    </div>
                </a>
                @endforeach
            </div>
        </div>
        @endif
        @endforeach

        @else
        <div class="empty-state" style="margin-top:20px;text-align:center;padding:28px;color:#aaa;">
            <i class="fas fa-book-open" style="font-size:2rem;display:block;margin-bottom:8px;opacity:0.4;"></i>
            <h3>Belum Ada Data</h3>
            <p>Belum ada capaian tercatat{{ $selectedSemester ? ' untuk semester yang dipilih' : '' }}.</p>
            @if($capaianAccessOpen)
            <a href="{{ route('santri.capaian.input.create') }}" class="btn btn-success" style="margin-top:8px;">
                <i class="fas fa-edit"></i> Input Capaian Sekarang
            </a>
            @endif
        </div>
        @endif

    </div>{{-- /tab-materi --}}

    {{-- ===================================================================
         TAB 3: KALKULASI PROGRESS (dulu "Prediksi", tab Grafik dihapus)
         Isi: Header stats + Status card + Gantt estimasi per materi + Trend chart
    =================================================================== --}}
    <div class="tab-panel" id="tab-kalkulasi">

        @php
            $currentPct  = min(100, round($rataRataPersentase, 1));
            $growthRate  = $progressHistory['growth_rate'] ?? 0;
            $historyData = $progressHistory['history'] ?? [];
            $remaining   = 100 - $currentPct;
            $semToFinish = null;
            if ($growthRate > 0 && $currentPct < 100) {
                $semToFinish = ceil($remaining / $growthRate);
            } elseif ($currentPct >= 100) {
                $semToFinish = 0;
            }
            $allCapaiansForPred = $progressHistory['all_capaians'] ?? collect();
        @endphp

        {{-- Header stats — mirip pred-hero admin --}}
        <div class="pred-hero">
            <div class="ph-col">
                <div class="ph-title">Progress Saat Ini</div>
                <div class="ph-val">{{ $currentPct }}%</div>
                <div class="ph-lbl">rata-rata semua materi</div>
            </div>
            <div class="ph-div"></div>
            <div class="ph-col">
                <div class="ph-title">Kecepatan Belajar</div>
                <div class="ph-val" style="font-size:1.6rem;">
                    {{ $growthRate > 0 ? '+' . $growthRate : ($growthRate < 0 ? $growthRate : '—') }}%
                </div>
                <div class="ph-lbl">per semester (rata-rata)</div>
            </div>
            <div class="ph-div"></div>
            <div class="ph-col">
                <div class="ph-title">Estimasi Khatam</div>
                <div class="ph-val" style="font-size:1.6rem;">
                    @if($currentPct >= 100) ✓ Khatam
                    @elseif($semToFinish !== null) {{ $semToFinish }} semester
                    @else Stagnan
                    @endif
                </div>
                <div class="ph-lbl">lagi dari sekarang</div>
            </div>
        </div>

        {{-- Status card --}}
        @if($currentPct >= 100)
        <div style="background:linear-gradient(135deg,#e8f5e9,#c8e6c9);border:2px solid #66bb6a;border-radius:12px;padding:16px;margin-bottom:16px;text-align:center;">
            <div style="font-size:2.5rem;margin-bottom:6px;">🏆</div>
            <strong style="color:#2e7d32;font-size:1.1rem;">Alhamdulillah! Semua Materi Khatam!</strong>
            <p style="color:#555;font-size:0.85rem;margin:6px 0 0;">Semua materi telah diselesaikan 100%. Terus pertahankan semangat!</p>
        </div>
        @elseif($growthRate <= 0)
        <div style="background:linear-gradient(135deg,#fff3e0,#ffe0b2);border:2px solid #ffa726;border-radius:12px;padding:16px;margin-bottom:16px;">
            <strong style="color:#e65100;"><i class="fas fa-exclamation-triangle"></i> Perlu Perhatian</strong>
            <p style="color:#555;font-size:0.85rem;margin:6px 0 0;">Progress tidak meningkat atau menurun antar semester. Yuk tingkatkan semangat belajar!</p>
        </div>
        @else
        <div class="pred-card-personal">
            <div class="pred-stat">
                <div class="ps-val" style="color:#2e7d32;">{{ $semToFinish }}</div>
                <div class="ps-lbl">Semester Lagi</div>
            </div>
            <div class="pred-stat">
                <div class="ps-val" style="color:var(--primary-color);">{{ 100 - $currentPct }}%</div>
                <div class="ps-lbl">Sisa Progress</div>
            </div>
            <div class="pred-stat">
                <div class="ps-val" style="color:#f57f17;">+{{ $growthRate }}%</div>
                <div class="ps-lbl">Growth/Semester</div>
            </div>
            <div style="flex:2;min-width:200px;">
                <div style="font-size:0.82rem;color:#555;font-weight:600;margin-bottom:6px;">Proyeksi Menuju Khatam</div>
                @php $projSteps = min($semToFinish, 8); @endphp
                <div style="display:flex;gap:4px;flex-wrap:wrap;">
                    @for($s = 0; $s <= $projSteps; $s++)
                    @php
                        $projPct = min(100, $currentPct + ($s * $growthRate));
                        $isDone  = $projPct >= 100;
                    @endphp
                    <div style="text-align:center;min-width:44px;">
                        <div style="height:40px;
                            background:{{ $isDone ? '#66bb6a' : 'linear-gradient(180deg,#81c784,#388e3c)' }};
                            width:100%;border-radius:4px 4px 0 0;
                            opacity:{{ max(0.3, ($s / max($projSteps,1)) * 0.7 + 0.3) }};
                            display:flex;align-items:flex-end;justify-content:center;padding-bottom:3px;">
                            <span style="font-size:0.6rem;color:#fff;font-weight:700;">{{ round($projPct) }}%</span>
                        </div>
                        <div style="font-size:0.6rem;color:#999;margin-top:2px;">{{ $s == 0 ? 'Skrg' : 'Sem+'.$s }}</div>
                    </div>
                    @endfor
                </div>
            </div>
        </div>
        @endif

        {{-- Gantt: Estimasi Selesai per Materi --}}
        <div class="gantt-section">
            <h4><i class="fas fa-tasks"></i> Estimasi Selesai per Materi</h4>
            <p class="sub">Seberapa jauh tiap materi dari khatam, dan estimasi berapa semester lagi berdasarkan kecepatan belajarmu.</p>

            @if($allCapaiansForPred->count() > 0)
            @foreach([
                ["Al-Qur'an",       'fas fa-book-quran', '#6FBA9D'],
                ['Hadist',          'fas fa-scroll',     '#81C6E8'],
                ['Materi Tambahan', 'fas fa-book',       '#FFD56B'],
            ] as [$kat, $icon, $color])
            @php $katCaps = $allCapaiansForPred->filter(fn($c) => $c->materi && $c->materi->kategori === $kat); @endphp
            @if($katCaps->count() > 0)
            <div style="margin-bottom:16px;">
                <div style="font-size:0.82rem;font-weight:700;color:{{ $color }};margin-bottom:8px;display:flex;align-items:center;gap:6px;">
                    <i class="{{ $icon }}"></i> {{ $kat }}
                </div>
                @foreach($katCaps->sortBy('persentase') as $cap)
                @php
                    $pctCap  = min(100, round($cap->persentase, 1));
                    $remCap  = 100 - $pctCap;
                    $semEst  = ($growthRate > 0 && $pctCap < 100)
                        ? ceil($remCap / $growthRate)
                        : ($pctCap >= 100 ? 0 : null);
                    $barColor = $pctCap >= 100 ? '#2e7d32,#66bb6a'
                              : ($pctCap >= 75  ? '#1565c0,#64b5f6'
                              : ($pctCap >= 50  ? '#e65100,#ffa726'
                              : '#c62828,#ef5350'));
                @endphp
                <div class="gantt-row-pred">
                    <div class="gantt-lbl-pred" title="{{ $cap->materi->nama_kitab }}">
                        {{ \Illuminate\Support\Str::limit($cap->materi->nama_kitab, 20) }}
                    </div>
                    <div class="gantt-track-pred">
                        <div class="gantt-fill-pred" style="width:{{ $pctCap }}%;background:linear-gradient(90deg,{{ $barColor }});">
                            @if($pctCap > 12){{ $pctCap }}%@endif
                        </div>
                    </div>
                    <div class="gantt-info-pred">
                        @if($pctCap >= 100)
                            <span style="color:#2e7d32;font-weight:700;"><i class="fas fa-check-circle"></i> Khatam</span>
                        @elseif($semEst !== null)
                            <span style="color:#555;">+{{ $semEst }} sem lagi</span>
                        @else
                            <span style="color:#c62828;font-size:0.68rem;"><i class="fas fa-pause-circle"></i> Stagnan</span>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
            @endif
            @endforeach
            @else
            <div style="text-align:center;padding:20px;color:#aaa;">
                <i class="fas fa-chart-line" style="font-size:2rem;display:block;margin-bottom:8px;opacity:0.4;"></i>
                Belum ada data capaian.
            </div>
            @endif
        </div>

        <div style="background:#f0f4ff;border-left:4px solid #3f51b5;border-radius:0 10px 10px 0;padding:12px 16px;font-size:0.8rem;color:#555;">
            <i class="fas fa-info-circle" style="color:#3f51b5;margin-right:6px;"></i>
            <strong>Catatan:</strong> Prediksi dihitung berdasarkan rata-rata pertumbuhan progress per semester. Hasilnya bisa berubah tergantung semangat dan konsistensi belajar. Terus tingkatkan!
        </div>

    </div>{{-- /tab-kalkulasi --}}

    </div>{{-- /padding inner --}}
</div>{{-- /content-box --}}



{{-- ===== SCRIPTS ===== --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
/* ===================== TAB SWITCHING ===================== */
document.querySelectorAll('.cap-tab').forEach(btn => {
    btn.addEventListener('click', function() {
        document.querySelectorAll('.cap-tab').forEach(b => b.classList.remove('active'));
        document.querySelectorAll('.tab-panel').forEach(p => p.classList.remove('active'));
        this.classList.add('active');
        document.getElementById(this.dataset.tab).classList.add('active');
        if (this.dataset.tab === 'tab-kalkulasi' && !window._kalkulasiInited) {
            initKalkulasiChart();
            window._kalkulasiInited = true;
        }
    });
});

/* ===================== CIRCLE SVG ANIMATION ===================== */
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.circle-fill').forEach(el => {
        const da = el.dataset.dasharray;
        const finalOffset = el.dataset.dashoffset;
        el.style.strokeDasharray = da;
        el.style.strokeDashoffset = da;
        setTimeout(() => {
            el.style.transition = 'stroke-dashoffset 1.2s cubic-bezier(0.4,0,0.2,1)';
            el.style.strokeDashoffset = finalOffset;
        }, 200);
    });

    /* Init ringkasan charts on page load (tab aktif default) */
    initRingkasanCharts();
});

/* ===================== TAB 1: RINGKASAN CHARTS ===================== */
function initRingkasanCharts() {

    /* 1. Pie: Progress per Kategori */
    new Chart(document.getElementById('chartKat'), {
        type: 'pie',
        data: {
            labels: ["Al-Qur'an", "Hadist", "Materi Tambahan"],
            datasets: [{
                data: [{{ $alquranPct }}, {{ $hadistPct }}, {{ $tambahanPct }}],
                backgroundColor: ['rgba(111,186,157,0.85)','rgba(129,198,232,0.85)','rgba(255,213,107,0.85)'],
                borderColor:     ['rgba(111,186,157,1)',   'rgba(129,198,232,1)',   'rgba(255,213,107,1)'  ],
                borderWidth: 2, hoverOffset: 8,
            }]
        },
        options: {
            responsive: true, maintainAspectRatio: true,
            plugins: {
                legend: { position: 'bottom', labels: { padding: 14, font: { size: 11 } } },
                tooltip: { callbacks: { label: ctx => ctx.label + ': ' + ctx.parsed.toFixed(1) + '%' } }
            }
        }
    });

    /* 2. Line: Progress Rata-rata per Semester */
    @php
        $semTrendLabels = collect($progressHistory['history'] ?? [])->pluck('sem')->toArray();
        $semTrendValues = collect($progressHistory['history'] ?? [])->pluck('avg')->toArray();
    @endphp
    const semLabels = @json($semTrendLabels);
    const semValues = @json($semTrendValues);
    const semCtx    = document.getElementById('chartSemTrend');
    if (semLabels.length > 0) {
        new Chart(semCtx, {
            type: 'line',
            data: {
                labels: semLabels,
                datasets: [{
                    label: 'Rata-rata Progress',
                    data: semValues,
                    borderColor: 'rgba(111,186,157,1)',
                    backgroundColor: 'rgba(111,186,157,0.15)',
                    pointBackgroundColor: 'rgba(111,186,157,1)',
                    pointRadius: 6, pointHoverRadius: 8,
                    borderWidth: 3, tension: 0.35, fill: true,
                }]
            },
            options: {
                responsive: true, maintainAspectRatio: true,
                scales: {
                    y: { beginAtZero: true, max: 100, ticks: { callback: v => v+'%' }, grid: { color: 'rgba(0,0,0,0.05)' } },
                    x: { grid: { display: false } }
                },
                plugins: {
                    legend: { display: false },
                    tooltip: { callbacks: { label: ctx => ctx.parsed.y.toFixed(1)+'%' } }
                }
            }
        });
    } else {
        /* Tidak ada data history */
        const c = semCtx.getContext('2d');
        semCtx.height = 80;
        c.font = '12px sans-serif'; c.fillStyle = '#ccc'; c.textAlign = 'center';
        c.fillText('Belum ada data semester', semCtx.width / 2, 50);
    }

    /* 3. Horizontal bar: Progress per Materi */
    @php
        $matSorted = $capaians->sortBy('persentase');
        $matLabels = $matSorted->map(fn($c) => \Illuminate\Support\Str::limit($c->materi->nama_kitab, 30))->values();
        $matData   = $matSorted->map(fn($c) => min(100, round($c->persentase, 1)))->values();
        $matColors = $matSorted->map(function($c) {
            $p = $c->persentase;
            if ($p >= 100) return 'rgba(102,187,106,0.8)';
            if ($p >= 75)  return 'rgba(100,181,246,0.8)';
            if ($p >= 50)  return 'rgba(255,213,107,0.8)';
            if ($p >= 25)  return 'rgba(255,171,145,0.8)';
            return 'rgba(255,139,148,0.8)';
        })->values();
    @endphp
    const matWrap = document.getElementById('chartMateriWrap');
    const matEl   = document.getElementById('chartMateri');
    matWrap.style.height = Math.max(200, {{ $capaians->count() }} * 34) + 'px';
    matEl.style.maxHeight = 'none';
    new Chart(matEl, {
        type: 'bar',
        data: {
            labels: @json($matLabels),
            datasets: [{ label: 'Progress (%)', data: @json($matData), backgroundColor: @json($matColors), borderRadius: 6, borderWidth: 0 }]
        },
        options: {
            indexAxis: 'y', responsive: true, maintainAspectRatio: false,
            plugins: { legend: { display: false }, tooltip: { callbacks: { label: ctx => ctx.parsed.x + '%' } } },
            scales: {
                x: { beginAtZero: true, max: 100, ticks: { callback: v => v+'%' } },
                y: { ticks: { font: { size: 11 } } }
            }
        }
    });
}

/* ===================== TAB 3: KALKULASI — tidak ada chart, fungsi dikosongkan ===================== */
function initKalkulasiChart() { /* chart dihapus */ }
</script>

@endsection