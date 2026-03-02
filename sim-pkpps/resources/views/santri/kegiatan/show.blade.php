@extends('layouts.app')

@section('title', 'Detail Kegiatan: ' . $kegiatan->nama_kegiatan)

@section('content')
<style>
.kd-header {
    background: linear-gradient(135deg, #0d3b2e 0%, #1a7a5e 60%, #2bbd8e 100%);
    border-radius: 14px;
    padding: 26px 28px;
    color: white;
    margin-bottom: 20px;
    position: relative;
    overflow: hidden;
}
.kd-header::before { content:''; position:absolute; top:-50px; right:-50px; width:180px; height:180px; border-radius:50%; background:rgba(255,255,255,0.05); }
.kd-header-top { display:flex; justify-content:space-between; align-items:flex-start; flex-wrap:wrap; gap:12px; margin-bottom:14px; }
.kd-title { font-size:1.3rem; font-weight:800; margin:0 0 6px; }
.kd-tags  { display:flex; gap:8px; flex-wrap:wrap; }
.kd-tag { background:rgba(255,255,255,0.15); border:1px solid rgba(255,255,255,0.2); padding:4px 12px; border-radius:20px; font-size:0.79rem; font-weight:600; display:inline-flex; align-items:center; gap:5px; }

.kd-btn-back {
    background: rgba(255,255,255,0.18);
    border: 1px solid rgba(255,255,255,0.28);
    color: white;
    padding: 8px 16px;
    border-radius: 9px;
    font-size: 0.82rem;
    font-weight: 600;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 7px;
    transition: background 0.2s;
    white-space: nowrap;
}
.kd-btn-back:hover { background:rgba(255,255,255,0.28); color:white; }

.kd-stats { display:grid; grid-template-columns:repeat(4,1fr); gap:12px; margin-bottom:20px; }
.kd-stat { background:white; border-radius:12px; padding:15px; box-shadow:0 4px 20px rgba(0,0,0,0.07); text-align:center; border-top:3px solid transparent; }
.kd-stat.green  { border-top-color:#2bbd8e; }
.kd-stat.blue   { border-top-color:#3b82f6; }
.kd-stat.orange { border-top-color:#f97316; }
.kd-stat.red    { border-top-color:#e53e3e; }
.kd-stat-icon { width:34px; height:34px; border-radius:8px; display:flex; align-items:center; justify-content:center; font-size:0.88rem; margin:0 auto 7px; }
.green  .kd-stat-icon { background:#d1fae5; color:#059669; }
.blue   .kd-stat-icon { background:#dbeafe; color:#2563eb; }
.orange .kd-stat-icon { background:#ffedd5; color:#ea580c; }
.red    .kd-stat-icon { background:#fee2e2; color:#dc2626; }
.kd-stat-value { font-size:1.9rem; font-weight:800; color:#1a2332; line-height:1; }
.kd-stat-label { font-size:0.77rem; color:#6b7280; margin-top:4px; font-weight:500; }

.kd-two-col { display:grid; grid-template-columns:1fr 1fr; gap:16px; margin-bottom:20px; }
.kd-chart-box { background:white; border-radius:12px; padding:18px; box-shadow:0 4px 20px rgba(0,0,0,0.07); }
.kd-chart-title { font-size:0.86rem; font-weight:700; color:#1a2332; margin-bottom:14px; display:flex; align-items:center; gap:6px; }

.kd-pct-card { background:linear-gradient(135deg,#e8f7f2,#d4f1e3); padding:14px; border-radius:10px; text-align:center; margin-top:12px; }
.kd-pct-label { font-size:0.79rem; color:#6b7280; margin-bottom:2px; }
.kd-pct-val   { font-size:2.1rem; font-weight:800; line-height:1; }
.kd-pct-sub   { font-size:0.76rem; color:#6b7280; }

.kd-table-box { background:white; border-radius:12px; box-shadow:0 4px 20px rgba(0,0,0,0.07); overflow:hidden; }
.kd-table-head { padding:13px 16px; border-bottom:1px solid #e2e8f0; font-size:0.86rem; font-weight:700; color:#1a2332; display:flex; align-items:center; gap:6px; }
.kd-table { width:100%; border-collapse:collapse; }
.kd-table thead tr { background:#f8fafb; }
.kd-table th { padding:10px 13px; text-align:left; font-size:0.77rem; font-weight:700; color:#6b7280; text-transform:uppercase; letter-spacing:0.4px; border-bottom:1px solid #e2e8f0; }
.kd-table td { padding:10px 13px; font-size:0.84rem; border-bottom:1px solid #f8fafc; color:#1a2332; }
.kd-table tbody tr:last-child td { border-bottom:none; }
.kd-table tbody tr:hover { background:#f8fafc; }
.kd-pill { padding:3px 11px; border-radius:20px; font-size:0.77rem; font-weight:700; }
.kd-pill.hadir  { background:#d1fae5; color:#065f46; }
.kd-pill.izin   { background:#dbeafe; color:#1e40af; }
.kd-pill.sakit  { background:#ede9fe; color:#5b21b6; }
.kd-pill.alpa   { background:#fee2e2; color:#991b1b; }

@media (max-width: 680px) {
    .kd-stats    { grid-template-columns:repeat(2,1fr); }
    .kd-two-col  { grid-template-columns:1fr; }
}
</style>

{{-- HEADER --}}
<div class="kd-header">
    <div class="kd-header-top">
        <div>
            <h2 class="kd-title"><i class="fas fa-clipboard-check"></i> {{ $kegiatan->nama_kegiatan }}</h2>
            <div class="kd-tags">
                <span class="kd-tag"><i class="fas fa-tag"></i> {{ $kegiatan->kategori->nama_kategori }}</span>
                <span class="kd-tag"><i class="fas fa-calendar-day"></i> {{ $kegiatan->hari }}</span>
                <span class="kd-tag"><i class="fas fa-clock"></i> {{ date('H:i', strtotime($kegiatan->waktu_mulai)) }}–{{ date('H:i', strtotime($kegiatan->waktu_selesai)) }}</span>
            </div>
        </div>
        {{-- ✅ FIX: Tombol kembali pakai $fromTab agar kembali ke tab yang benar --}}
        <a href="{{ route('santri.kegiatan.index') }}?tab={{ $fromTab ?? 'riwayat' }}" class="kd-btn-back">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    @if($kegiatan->materi)
    <div style="background:rgba(255,255,255,0.12);padding:9px 13px;border-radius:8px;font-size:0.83rem;border:1px solid rgba(255,255,255,0.15);">
        <i class="fas fa-book"></i> <strong>Materi:</strong> {{ $kegiatan->materi }}
    </div>
    @endif
</div>

{{-- STATS --}}
<div class="kd-stats">
    <div class="kd-stat green">
        <div class="kd-stat-icon"><i class="fas fa-check-circle"></i></div>
        <div class="kd-stat-value">{{ $stats['Hadir'] ?? 0 }}</div>
        <div class="kd-stat-label">Hadir</div>
    </div>
    <div class="kd-stat blue">
        <div class="kd-stat-icon"><i class="fas fa-info-circle"></i></div>
        <div class="kd-stat-value">{{ $stats['Izin'] ?? 0 }}</div>
        <div class="kd-stat-label">Izin</div>
    </div>
    <div class="kd-stat orange">
        <div class="kd-stat-icon"><i class="fas fa-heartbeat"></i></div>
        <div class="kd-stat-value">{{ $stats['Sakit'] ?? 0 }}</div>
        <div class="kd-stat-label">Sakit</div>
    </div>
    <div class="kd-stat red">
        <div class="kd-stat-icon"><i class="fas fa-times-circle"></i></div>
        <div class="kd-stat-value">{{ $stats['Alpa'] ?? 0 }}</div>
        <div class="kd-stat-label">Alpa</div>
    </div>
</div>

{{-- CHART + PERSENTASE --}}
<div class="kd-two-col">
    <div class="kd-chart-box">
        <div class="kd-chart-title"><i class="fas fa-chart-line" style="color:#2bbd8e;"></i> Tren Kehadiran (6 Bulan)</div>
        <canvas id="chartTren" style="max-height:220px;"></canvas>
    </div>
    <div class="kd-chart-box">
        <div class="kd-chart-title"><i class="fas fa-chart-pie" style="color:#f5a623;"></i> Distribusi Kehadiran</div>
        <canvas id="chartDonut" style="max-height:170px;"></canvas>
        <div class="kd-pct-card">
            <div class="kd-pct-label">Persentase Kehadiran</div>
            <div class="kd-pct-val" style="color:{{ $persentaseHadir >= 85 ? '#059669' : ($persentaseHadir >= 70 ? '#d97706' : '#dc2626') }};">
                {{ $persentaseHadir }}%
            </div>
            <div class="kd-pct-sub">dari {{ $totalAbsensi }} total absensi</div>
        </div>
    </div>
</div>

{{-- RIWAYAT TABLE --}}
<div class="kd-table-box">
    <div class="kd-table-head">
        <i class="fas fa-history" style="color:#2bbd8e;"></i> Riwayat Lengkap
        <span style="margin-left:auto;background:#e8f7f2;color:#1a7a5e;padding:2px 8px;border-radius:8px;font-size:0.74rem;">{{ $riwayats->total() }} data</span>
    </div>
    @if($riwayats->count() > 0)
        <table class="kd-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Tanggal</th>
                    <th>Hari</th>
                    <th>Waktu Absen</th>
                    <th>Status</th>
                    <th>Metode</th>
                </tr>
            </thead>
            <tbody>
                @foreach($riwayats as $idx => $absensi)
                <tr>
                    <td style="color:#9ca3af;font-size:0.77rem;">{{ $riwayats->firstItem() + $idx }}</td>
                    <td style="font-weight:600;">{{ \Carbon\Carbon::parse($absensi->tanggal)->format('d M Y') }}</td>
                    <td style="color:#6b7280;font-size:0.82rem;">{{ \Carbon\Carbon::parse($absensi->tanggal)->locale('id')->dayName }}</td>
                    <td style="color:#6b7280;font-size:0.82rem;">
                        {{ $absensi->waktu_absen ? \Carbon\Carbon::parse($absensi->waktu_absen)->format('H:i') : '-' }}
                    </td>
                    <td>
                        <span class="kd-pill {{ strtolower($absensi->status) }}">{{ $absensi->status }}</span>
                    </td>
                    <td style="font-size:0.79rem;color:#6b7280;">
                        <i class="fas fa-{{ ($absensi->metode_absen ?? '') === 'RFID' ? 'id-card' : 'hand-pointer' }}"></i>
                        {{ $absensi->metode_absen ?? '-' }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div style="padding:12px 16px;border-top:1px solid #f0f0f0;">{{ $riwayats->links() }}</div>
    @else
        <div style="text-align:center;padding:32px;color:#6b7280;font-size:0.85rem;">
            <i class="fas fa-inbox" style="font-size:2.5rem;opacity:0.2;display:block;margin-bottom:10px;"></i>
            Belum ada riwayat absensi untuk kegiatan ini.
        </div>
    @endif
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    new Chart(document.getElementById('chartTren'), {
        type: 'line',
        data: {
            labels: @json(collect($trendBulanan)->pluck('bulan')),
            datasets: [{
                label: 'Hadir',
                data: @json(collect($trendBulanan)->pluck('hadir')),
                borderColor: '#2bbd8e', backgroundColor: 'rgba(43,189,142,0.12)',
                borderWidth: 3, pointRadius: 5, pointBackgroundColor: '#2bbd8e',
                tension: 0.4, fill: true
            }]
        },
        options: {
            responsive: true, maintainAspectRatio: true,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, ticks: { stepSize: 1 }, grid: { color: 'rgba(0,0,0,0.04)' } },
                x: { grid: { display: false } }
            }
        }
    });

    new Chart(document.getElementById('chartDonut'), {
        type: 'doughnut',
        data: {
            labels: ['Hadir','Izin','Sakit','Alpa'],
            datasets: [{
                data: [{{ $stats['Hadir'] ?? 0 }}, {{ $stats['Izin'] ?? 0 }}, {{ $stats['Sakit'] ?? 0 }}, {{ $stats['Alpa'] ?? 0 }}],
                backgroundColor: ['#2bbd8e','#3b82f6','#f59e0b','#e53e3e'],
                borderWidth: 3, borderColor: '#fff'
            }]
        },
        options: {
            responsive: true, maintainAspectRatio: true, cutout: '60%',
            plugins: {
                legend: { position: 'bottom', labels: { padding: 12, font: { size: 11 } } },
                tooltip: { callbacks: { label: function(ctx) {
                    var total = {{ $totalAbsensi }};
                    var pct = total > 0 ? ((ctx.parsed / total) * 100).toFixed(1) : 0;
                    return ctx.label + ': ' + ctx.parsed + ' (' + pct + '%)';
                }}}
            }
        }
    });
});
</script>
@endsection