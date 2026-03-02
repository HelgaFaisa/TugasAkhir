{{-- resources/views/admin/dashboardAdmin.blade.php --}}
@extends('layouts.app', ['isAdmin' => true])

@section('title', 'Dashboard Admin')

@section('content')
<style>
@import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');

.dash-root * { font-family: 'Plus Jakarta Sans', sans-serif; box-sizing: border-box; }

/* ─── Header ─── */
.dash-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 24px;
    flex-wrap: wrap;
    gap: 10px;
}
.dash-header-left h1 {
    font-size: 1.55rem;
    font-weight: 800;
    color: #0f172a;
    margin: 0 0 2px;
    line-height: 1.2;
}
.dash-header-left p {
    font-size: .8rem;
    color: #94a3b8;
    margin: 0;
    font-weight: 500;
}
.dash-live-pill {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    background: linear-gradient(135deg,#f0fdf4,#dcfce7);
    border: 1px solid #86efac;
    color: #15803d;
    font-size: .72rem;
    font-weight: 700;
    padding: 6px 14px;
    border-radius: 30px;
    letter-spacing: .03em;
}
.dash-live-dot {
    width: 7px; height: 7px;
    border-radius: 50%;
    background: #22c55e;
    box-shadow: 0 0 0 3px rgba(34,197,94,.25);
    animation: _pulse 2s infinite;
}
@keyframes _pulse { 0%,100%{box-shadow:0 0 0 3px rgba(34,197,94,.25)} 50%{box-shadow:0 0 0 6px rgba(34,197,94,.05)} }

/* ─── KPI Strip ─── */
.dash-kpi-strip {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(155px,1fr));
    gap: 12px;
    margin-bottom: 20px;
}
.dash-kpi-card {
    background: #fff;
    border-radius: 14px;
    padding: 16px 18px;
    border: 1.5px solid #f1f5f9;
    box-shadow: 0 1px 8px rgba(15,23,42,.05);
    position: relative;
    overflow: hidden;
    transition: transform .2s, box-shadow .2s;
    cursor: default;
}
.dash-kpi-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 24px rgba(15,23,42,.10);
}
.dash-kpi-card::after {
    content:'';
    position:absolute; bottom:0; left:0; right:0; height:3px;
    border-radius: 0 0 14px 14px;
}
.kpi-blue::after   { background: linear-gradient(90deg,#3b82f6,#93c5fd); }
.kpi-teal::after   { background: linear-gradient(90deg,#0d9488,#2dd4bf); }
.kpi-rose::after   { background: linear-gradient(90deg,#f43f5e,#fb7185); }
.kpi-amber::after  { background: linear-gradient(90deg,#f59e0b,#fcd34d); }
.kpi-violet::after { background: linear-gradient(90deg,#7c3aed,#c4b5fd); }
.kpi-green::after  { background: linear-gradient(90deg,#16a34a,#4ade80); }

.dash-kpi-icon {
    width: 36px; height: 36px;
    border-radius: 9px;
    display: flex; align-items: center; justify-content: center;
    font-size: .9rem;
    margin-bottom: 10px;
}
.kpi-blue   .dash-kpi-icon { background:#eff6ff; color:#2563eb; }
.kpi-teal   .dash-kpi-icon { background:#f0fdfa; color:#0f766e; }
.kpi-rose   .dash-kpi-icon { background:#fff1f2; color:#e11d48; }
.kpi-amber  .dash-kpi-icon { background:#fffbeb; color:#d97706; }
.kpi-violet .dash-kpi-icon { background:#f5f3ff; color:#7c3aed; }
.kpi-green  .dash-kpi-icon { background:#f0fdf4; color:#16a34a; }

.dash-kpi-num {
    font-size: 2rem;
    font-weight: 800;
    color: #0f172a;
    line-height: 1;
    margin-bottom: 3px;
}
.dash-kpi-title { font-size: .73rem; color: #64748b; font-weight: 600; }
.dash-kpi-sub   { font-size: .66rem; color: #94a3b8; margin-top: 2px; }

/* ─── Alerts ─── */
.dash-alerts-row {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(260px,1fr));
    gap: 12px;
    margin-bottom: 20px;
}
.dash-alert-box {
    border-radius: 12px;
    padding: 14px 16px;
    border-left: 4px solid;
}
.dab-red    { background:#fff1f2; border-color:#f43f5e; }
.dab-amber  { background:#fffbeb; border-color:#f59e0b; }
.dab-blue   { background:#eff6ff; border-color:#3b82f6; }
.dash-alert-box h5 {
    font-size: .72rem; font-weight: 800;
    text-transform: uppercase; letter-spacing:.05em;
    margin: 0 0 8px;
    display: flex; align-items: center; gap: 6px;
}
.dab-red h5   { color:#be123c; }
.dab-amber h5 { color:#b45309; }
.dab-blue h5  { color:#1d4ed8; }
.dab-list { margin:0;padding:0;list-style:none; }
.dab-list li {
    font-size:.76rem; color:#334155;
    padding: 5px 0;
    border-bottom:1px dashed rgba(0,0,0,.07);
    display:flex; align-items:center; gap:7px; flex-wrap:wrap;
}
.dab-list li:last-child { border-bottom:none; }
.dab-chip {
    font-size:.62rem; font-weight:700;
    padding:2px 7px; border-radius:20px;
    white-space:nowrap; flex-shrink:0;
}
.chip-red   { background:#fecdd3; color:#9f1239; }
.chip-amber { background:#fde68a; color:#78350f; }
.chip-blue  { background:#bfdbfe; color:#1e3a8a; }

/* ─── Jadwal Table ─── */
.dash-jadwal-section { margin-bottom: 20px; }
.dash-section-hd {
    font-size:.78rem; font-weight:800;
    text-transform:uppercase; letter-spacing:.07em;
    color:#475569;
    margin:0 0 10px;
    display:flex; align-items:center; gap:8px;
}
.dash-section-hd::after {
    content:''; flex:1; height:1px;
    background:linear-gradient(90deg,#e2e8f0,transparent);
}
.dash-table-card {
    background:#fff;
    border-radius:14px;
    border:1.5px solid #f1f5f9;
    box-shadow:0 1px 8px rgba(15,23,42,.05);
    overflow:hidden;
}
.dash-table { width:100%; border-collapse:collapse; font-size:.78rem; }
.dash-table thead th {
    background:#f8fafc;
    color:#64748b; font-weight:700; font-size:.7rem;
    text-transform:uppercase; letter-spacing:.04em;
    padding:10px 16px;
    border-bottom:1.5px solid #e2e8f0;
    text-align:left; white-space:nowrap;
}
.dash-table tbody td {
    padding:10px 16px;
    border-bottom:1px solid #f8fafc;
    vertical-align:middle;
    color:#1e293b;
}
.dash-table tbody tr:last-child td { border-bottom:none; }
.dash-table tbody tr:hover { background:#fafbfc; }
.dash-table tbody tr.tr-warn { background:#fffbeb; }

.status-pill {
    display:inline-flex; align-items:center; gap:5px;
    font-size:.67rem; font-weight:700;
    padding:3px 9px; border-radius:20px;
}
.sp-live   { background:#dcfce7; color:#15803d; }
.sp-done   { background:#f1f5f9; color:#64748b; }
.sp-soon   { background:#dbeafe; color:#1d4ed8; }
.sp-warn   { background:#fef2f2; color:#dc2626; }
.sp-live::before, .sp-soon::before {
    content:''; width:5px; height:5px; border-radius:50%;
    display:inline-block;
}
.sp-live::before { background:#22c55e; animation:_pulse 2s infinite; }
.sp-soon::before { background:#3b82f6; }

.mini-prog { background:#e2e8f0; border-radius:4px; height:5px; width:72px; overflow:hidden; margin-bottom:2px; }
.mini-prog-fill { height:100%; border-radius:4px; background:linear-gradient(90deg,#0d9488,#2dd4bf); }

/* ─── Middle 2-col: SPP kiri + Kas kanan ─── */
.dash-mid-row {
    display:grid;
    grid-template-columns:1fr 1fr;
    gap:14px;
    margin-bottom:20px;
}
@media(max-width:700px){ .dash-mid-row{ grid-template-columns:1fr; } }

.dash-panel {
    background:#fff;
    border-radius:16px;
    padding:20px;
    border:1.5px solid #f1f5f9;
    box-shadow:0 1px 8px rgba(15,23,42,.05);
    display:flex; flex-direction:column;
}
.dash-panel-title {
    font-size:.75rem; font-weight:800;
    text-transform:uppercase; letter-spacing:.06em;
    color:#475569;
    margin:0 0 16px;
    display:flex; align-items:center; gap:7px;
}
.dash-panel-title i { font-size:.85rem; }

/* SPP Panel */
.spp-ring-row {
    display:flex; gap:16px; align-items:center; flex-wrap:wrap; margin-bottom:14px;
}
.spp-ring-box { position:relative; width:110px; height:110px; flex-shrink:0; }
.spp-ring-center {
    position:absolute; inset:0;
    display:flex; flex-direction:column;
    align-items:center; justify-content:center;
    pointer-events:none;
}
.spp-ring-pct {
    font-size:1.6rem; font-weight:800; color:#0f172a; line-height:1;
}
.spp-ring-lbl { font-size:.58rem; color:#94a3b8; font-weight:600; }
.spp-stats { display:flex; flex-direction:column; gap:7px; flex:1; min-width:110px; }
.spp-stat-row { display:flex; align-items:center; justify-content:space-between; gap:6px; }
.spp-stat-dot { width:8px;height:8px;border-radius:50%;flex-shrink:0; }
.spp-stat-name { font-size:.7rem; color:#64748b; flex:1; }
.spp-stat-val  { font-size:.78rem; font-weight:700; color:#0f172a; }
.spp-prog-wrap { margin-top:2px; }
.spp-prog-labels { display:flex; justify-content:space-between; font-size:.65rem; color:#94a3b8; margin-bottom:4px; }
.spp-prog-track { background:#f1f5f9; border-radius:8px; height:6px; overflow:hidden; }
.spp-prog-fill  { height:100%; border-radius:8px; background:linear-gradient(90deg,#0d9488,#5eead4); }

/* Kas Panel */
.kas-bars { display:flex; flex-direction:column; gap:12px; }
.kas-bar-item { }
.kas-bar-header { display:flex; justify-content:space-between; align-items:center; margin-bottom:5px; }
.kas-bar-name { font-size:.72rem; color:#64748b; font-weight:600; display:flex; align-items:center; gap:6px; }
.kas-bar-name span { width:8px;height:8px;border-radius:50%;display:inline-block;flex-shrink:0; }
.kas-bar-val  { font-size:.78rem; font-weight:700; color:#0f172a; }
.kas-bar-track { background:#f1f5f9; border-radius:6px; height:10px; overflow:hidden; }
.kas-bar-fill  { height:100%; border-radius:6px; transition:width .7s cubic-bezier(.4,0,.2,1); }
.kas-sisa {
    margin-top:14px;
    padding:11px 14px;
    border-radius:10px;
    display:flex; justify-content:space-between; align-items:center;
}
.kas-sisa.pos { background:#f0fdf4; border:1px solid #bbf7d0; }
.kas-sisa.neg { background:#fff1f2; border:1px solid #fecdd3; }
.kas-sisa-lbl { font-size:.72rem; font-weight:700; color:#475569; display:flex; align-items:center; gap:6px; }
.kas-sisa-num { font-size:1.05rem; font-weight:800; }
.kas-sisa-num.pos { color:#16a34a; }
.kas-sisa-num.neg { color:#dc2626; }

/* Quick links shared */
.dash-qlinks { display:flex; gap:7px; margin-top:14px; flex-wrap:wrap; }
.dash-qlink {
    flex:1; min-width:90px;
    text-align:center; padding:7px 5px;
    border-radius:8px; font-size:.69rem; font-weight:700;
    text-decoration:none; white-space:nowrap;
    transition:transform .15s, opacity .15s;
    border:1px solid;
}
.dash-qlink:hover { transform:translateY(-1px); opacity:.85; }
.ql-red    { background:#fff1f2; color:#be123c; border-color:#fecdd3; }
.ql-amber  { background:#fffbeb; color:#b45309; border-color:#fde68a; }
.ql-blue   { background:#eff6ff; color:#1d4ed8; border-color:#bfdbfe; }

/* ─── Tren full width ─── */
.dash-tren-section { margin-bottom: 8px; }
.dash-tren-card {
    background:#fff;
    border-radius:16px;
    border:1.5px solid #f1f5f9;
    box-shadow:0 1px 8px rgba(15,23,42,.05);
    padding:20px 22px;
}
.dash-tren-header {
    display:flex; justify-content:space-between; align-items:center; margin-bottom:16px; flex-wrap:wrap; gap:8px;
}
.dash-tren-header-title {
    font-size:.75rem; font-weight:800;
    text-transform:uppercase; letter-spacing:.06em; color:#475569;
    display:flex; align-items:center; gap:7px; margin:0;
}
.dash-tren-canvas { height:240px; }

/* ─── Empty ─── */
.dash-empty { text-align:center; padding:30px; color:#cbd5e1; }
.dash-empty i { font-size:2rem; display:block; margin-bottom:8px; }
.dash-empty p { font-size:.8rem; margin:0; }
</style>

<div class="dash-root">

{{-- ───────── HEADER ───────── --}}
<div class="dash-header">
    <div class="dash-header-left">
        <h1>
            @if(auth()->user()->role === 'super_admin') Super Admin
            @elseif(auth()->user()->role === 'akademik') Akademik
            @else Pamong @endif
        </h1>
        <p><i class="fas fa-calendar-day" style="margin-right:4px;"></i>{{ $hariIni }}, {{ $today->translatedFormat('d F Y') }}</p>
    </div>
    <span class="dash-live-pill">
        <span class="dash-live-dot"></span> Live Dashboard
    </span>
</div>

{{-- ───────── KPI CARDS ───────── --}}
@include('admin.dashboard._kpi-cards', ['kpi' => $kpiCards])

{{-- ───────── ALERTS ───────── --}}
@include('admin.dashboard._alert-panel', ['alerts' => $alerts])

{{-- ───────── JADWAL KEGIATAN ───────── --}}
@include('admin.dashboard._jadwal-kegiatan', ['kegiatan' => $kegiatanHariIni, 'hari' => $hariIni])

{{-- ───────── MIDDLE ROW: SPP + KAS ───────── --}}
@if(auth()->user()->isSuperAdmin())
    @include('admin.dashboard._ringkasan-spp', ['spp' => $sppBulanIni])
@endif

{{-- ───────── TREN KEHADIRAN (FULL WIDTH) ───────── --}}
@include('admin.dashboard._tren-kehadiran', ['trenKehadiran' => $trenKehadiran])

</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {

    // ── Tren Kehadiran ────────────────────────────────────────────────────
    var trenCtx = document.getElementById('trenKehadiranChart');
    if (trenCtx) {
        var trenData   = @json($trenKehadiran);
        var palette    = ['#0d9488','#3b82f6','#f59e0b','#e11d48','#7c3aed','#10b981'];
        var datasets   = [];
        Object.keys(trenData.series).forEach(function (key, i) {
            var c = palette[i % palette.length];
            datasets.push({
                label            : key,
                data             : trenData.series[key],
                borderColor      : c,
                backgroundColor  : c + '15',
                borderWidth      : 2.5,
                tension          : 0.42,
                fill             : true,
                pointRadius      : 5,
                pointHoverRadius : 7,
                pointBackgroundColor : c,
                pointBorderColor     : '#fff',
                pointBorderWidth     : 2.5
            });
        });

        new Chart(trenCtx, {
            type: 'line',
            data: { labels: trenData.labels, datasets: datasets },
            options: {
                responsive: true, maintainAspectRatio: false,
                interaction: { mode: 'index', intersect: false },
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { padding:20, usePointStyle:true, font:{size:12,family:'Plus Jakarta Sans'} }
                    },
                    tooltip: {
                        backgroundColor:'#0f172a', titleFont:{size:12,weight:'700'},
                        bodyFont:{size:11}, padding:12, cornerRadius:10,
                        callbacks:{ label: function(c){ return '  '+c.dataset.label+': '+c.parsed.y+'%'; } }
                    }
                },
                scales: {
                    x: { grid:{display:false}, ticks:{font:{size:11,family:'Plus Jakarta Sans'},color:'#94a3b8'} },
                    y: {
                        beginAtZero:true, max:100,
                        grid:{color:'#f1f5f9',drawBorder:false},
                        ticks:{ callback:function(v){return v+'%';}, font:{size:10},color:'#94a3b8' }
                    }
                }
            }
        });
    }

    // ── SPP + Kas charts diinisialisasi dari _ringkasan-spp.blade.php ────
    // (script ada di dalam partial itu sendiri, sudah polling Chart.js)
});
</script>
@endsection