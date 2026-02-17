@extends('layouts.app')

@section('content')
<style>
.chart-box { background: #fff; border-radius: 12px; padding: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); margin-bottom: 20px; }
.chart-box h4 { margin: 0 0 16px; color: var(--primary-dark); font-size: 1rem; }
.mini-kpi { display: grid; grid-template-columns: repeat(auto-fit, minmax(140px, 1fr)); gap: 12px; margin-bottom: 20px; }
.mini-kpi-card { padding: 16px; border-radius: 10px; text-align: center; }
.mini-kpi-card.hadir { background: #ECFDF5; color: #065F46; }
.mini-kpi-card.izin { background: #FFFBEB; color: #92400E; }
.mini-kpi-card.sakit { background: #EFF6FF; color: #1E40AF; }
.mini-kpi-card.alpa { background: #FEF2F2; color: #991B1B; }
.mini-kpi-val { font-size: 1.6rem; font-weight: 700; }
.mini-kpi-label { font-size: 0.78rem; margin-top: 4px; }
.progress-bar-lg { background: #e9ecef; border-radius: 10px; height: 24px; overflow: hidden; margin: 12px 0; }
.progress-bar-lg .fill { height: 100%; border-radius: 10px; display: flex; align-items: center; justify-content: center; color: #fff; font-size: 0.82rem; font-weight: 600; }
.insight-box { padding: 12px 16px; border-radius: 8px; margin-bottom: 8px; display: flex; align-items: flex-start; gap: 10px; font-size: 0.85rem; }
.insight-box.i-success { background: #ECFDF5; color: #065F46; }
.insight-box.i-warning { background: #FFFBEB; color: #92400E; }
.insight-box.i-danger { background: #FEF2F2; color: #991B1B; }
.insight-box.i-info { background: #EFF6FF; color: #1E40AF; }
.two-col { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
@media(max-width:768px){ .two-col { grid-template-columns: 1fr; } }
.text-center { text-align: center; }
.progress-inline { display: flex; align-items: center; gap: 8px; }
.progress-bar-mini { flex: 1; background: #e9ecef; border-radius: 8px; height: 8px; overflow: hidden; max-width: 120px; }
.progress-bar-mini .fill { height: 100%; border-radius: 8px; }
</style>

<div class="page-header">
    <h2><i class="fas fa-search-plus"></i> Analisis Kegiatan</h2>
    <a href="{{ route('admin.laporan-kegiatan.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Kembali
    </a>
</div>

{{-- Header Kegiatan --}}
<div class="content-box" style="margin-bottom:20px;">
    <div style="display:flex; justify-content:space-between; align-items:flex-start; flex-wrap:wrap; gap:12px;">
        <div>
            <h3 style="margin:0 0 6px; color:var(--primary-dark);">{{ $kegiatan->nama_kegiatan }}</h3>
            <p style="margin:0; font-size:0.85rem; color:var(--text-light);">
                <span class="badge badge-info">{{ $kegiatan->kategori->nama_kategori ?? '-' }}</span>
                <i class="fas fa-clock" style="margin-left:12px;"></i> {{ $kegiatan->waktu_lengkap }}
                <i class="fas fa-calendar" style="margin-left:12px;"></i> {{ $kegiatan->hari }}
            </p>
            @if(!$kegiatan->isForAllClasses())
                <p style="margin:4px 0 0; font-size:0.82rem;">
                    <i class="fas fa-school"></i> Kelas:
                    @foreach($kegiatan->kelasKegiatan as $kk)
                        <span class="badge badge-success">{{ $kk->nama_kelas }}</span>
                    @endforeach
                </p>
            @else
                <p style="margin:4px 0 0; font-size:0.82rem;"><i class="fas fa-globe"></i> Kegiatan Umum (Semua Kelas)</p>
            @endif
        </div>
        <div style="text-align:right;">
            <p style="margin:0; font-size:0.82rem; color:var(--text-light);">Periode: <strong>{{ $periodeLabel }}</strong></p>
        </div>
    </div>
</div>

{{-- Mini KPI --}}
<div class="mini-kpi">
    <div class="mini-kpi-card hadir">
        <div class="mini-kpi-val">{{ $stats->hadir ?? 0 }}</div>
        <div class="mini-kpi-label"><i class="fas fa-check"></i> Hadir</div>
    </div>
    <div class="mini-kpi-card izin">
        <div class="mini-kpi-val">{{ $stats->izin ?? 0 }}</div>
        <div class="mini-kpi-label"><i class="fas fa-info-circle"></i> Izin</div>
    </div>
    <div class="mini-kpi-card sakit">
        <div class="mini-kpi-val">{{ $stats->sakit ?? 0 }}</div>
        <div class="mini-kpi-label"><i class="fas fa-heartbeat"></i> Sakit</div>
    </div>
    <div class="mini-kpi-card alpa">
        <div class="mini-kpi-val">{{ $stats->alpa ?? 0 }}</div>
        <div class="mini-kpi-label"><i class="fas fa-times"></i> Alpa</div>
    </div>
</div>

{{-- Overall Progress --}}
<div class="content-box" style="margin-bottom:20px;">
    <h4 style="margin:0 0 8px;"><i class="fas fa-percentage"></i> Rata-rata Kehadiran</h4>
    <div class="progress-bar-lg">
        <div class="fill" style="width:{{ $stats->persen }}%; background:{{ $stats->persen >= 85 ? '#10B981' : ($stats->persen >= 70 ? '#FBBF24' : '#EF4444') }};">
            {{ $stats->persen }}%
        </div>
    </div>
</div>

<div class="two-col">
    {{-- Trend Chart --}}
    <div class="chart-box">
        <h4><i class="fas fa-chart-line"></i> Trend 4 Minggu Terakhir</h4>
        <div style="height:250px;">
            <canvas id="kegiatanTrendChart"></canvas>
        </div>
    </div>

    {{-- Punctuality --}}
    <div class="chart-box">
        <h4><i class="fas fa-stopwatch"></i> Ketepatan Waktu (RFID)</h4>
        @if($punctuality && $punctuality->total > 0)
            <div style="height:250px;">
                <canvas id="punctualityChart"></canvas>
            </div>
            <div style="margin-top:12px; font-size:0.82rem; color:var(--text-light); text-align:center;">
                {{ $punctuality->tepat_waktu ?? 0 }} tepat waktu, {{ $punctuality->terlambat ?? 0 }} terlambat dari {{ $punctuality->total }} total
            </div>
        @else
            <div class="insight-box i-info"><i class="fas fa-info-circle"></i> Belum ada data RFID untuk analisis ketepatan waktu.</div>
        @endif
    </div>
</div>

{{-- Breakdown Per Kelas --}}
@if(!empty($breakdownPerKelas) && count($breakdownPerKelas) > 0)
<div class="chart-box">
    <h4><i class="fas fa-school"></i> Breakdown Per Kelas</h4>
    <table class="data-table" style="font-size:0.85rem;">
        <thead><tr><th>Kelas</th><th class="text-center">Total</th><th class="text-center">Hadir</th><th class="text-center" style="min-width:180px;">% Kehadiran</th></tr></thead>
        <tbody>
            @foreach($breakdownPerKelas as $bk)
                <tr style="{{ $bk['persen'] < 70 ? 'background:#FEF2F2;' : '' }}">
                    <td><strong>{{ $bk['kelas'] }}</strong></td>
                    <td class="text-center">{{ $bk['total'] }}</td>
                    <td class="text-center"><span class="badge badge-success">{{ $bk['hadir'] }}</span></td>
                    <td class="text-center">
                        <div class="progress-inline" style="justify-content:center;">
                            <div class="progress-bar-mini"><div class="fill" style="width:{{ $bk['persen'] }}%; background:{{ $bk['persen'] >= 85 ? '#10B981' : ($bk['persen'] >= 70 ? '#FBBF24' : '#EF4444') }};"></div></div>
                            <strong>{{ $bk['persen'] }}%</strong>
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endif

{{-- Santri Tidak Pernah Hadir --}}
@if($santriTidakPernahHadir && $santriTidakPernahHadir->count() > 0)
<div class="chart-box">
    <h4><i class="fas fa-user-times" style="color:#EF4444;"></i> Santri Tidak Pernah Hadir</h4>
    <div style="display:flex; flex-wrap:wrap; gap:8px;">
        @foreach($santriTidakPernahHadir as $sth)
            <a href="{{ route('admin.laporan-kegiatan.detail-santri', $sth->id_santri) }}" class="badge badge-danger" style="padding:6px 12px; font-size:0.82rem; text-decoration:none;">
                {{ $sth->santri->nama_lengkap ?? $sth->id_santri }}
            </a>
        @endforeach
    </div>
</div>
@endif

{{-- Insights & Rekomendasi --}}
@if(!empty($insights))
<div class="chart-box">
    <h4><i class="fas fa-lightbulb"></i> Insight & Rekomendasi</h4>
    @foreach($insights as $insight)
        <div class="insight-box i-{{ $insight['type'] }}">
            <i class="{{ $insight['icon'] }}"></i>
            {{ $insight['text'] }}
        </div>
    @endforeach
</div>
@endif

{{-- Actions --}}
<div class="content-box" style="display:flex; gap:8px; flex-wrap:wrap;">
    <a href="{{ route('admin.absensi-kegiatan.rekap', $kegiatan->kegiatan_id) }}" class="btn btn-info">
        <i class="fas fa-list"></i> Rekap Absensi
    </a>
    <button onclick="window.print()" class="btn btn-secondary"><i class="fas fa-print"></i> Cetak</button>
    <a href="{{ route('admin.laporan-kegiatan.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Kembali</a>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
// Trend Chart
const trend = @json($trend);
new Chart(document.getElementById('kegiatanTrendChart'), {
    type: 'line',
    data: {
        labels: trend.map(t => t.label),
        datasets: [{
            label: '% Kehadiran',
            data: trend.map(t => t.persen),
            borderColor: '#3B82F6',
            backgroundColor: 'rgba(59,130,246,0.1)',
            tension: 0.4, fill: true, pointRadius: 6,
            pointBackgroundColor: trend.map(t => t.persen >= 85 ? '#10B981' : (t.persen >= 70 ? '#FBBF24' : '#EF4444')),
        }]
    },
    options: {
        responsive: true, maintainAspectRatio: false,
        plugins: { legend: { display: false }, tooltip: { callbacks: { label: ctx => ctx.raw + '%' } } },
        scales: { y: { min: 0, max: 100, ticks: { callback: v => v + '%' } }, x: { grid: { display: false } } }
    }
});

// Punctuality Pie
@if($punctuality && $punctuality->total > 0)
new Chart(document.getElementById('punctualityChart'), {
    type: 'doughnut',
    data: {
        labels: ['Tepat Waktu', 'Terlambat'],
        datasets: [{
            data: [{{ $punctuality->tepat_waktu ?? 0 }}, {{ $punctuality->terlambat ?? 0 }}],
            backgroundColor: ['#10B981', '#EF4444'],
            borderWidth: 2, hoverOffset: 8
        }]
    },
    options: {
        responsive: true, maintainAspectRatio: false,
        plugins: { legend: { position: 'bottom', labels: { usePointStyle: true } } }
    }
});
@endif
</script>

<style>
@media print {
    .btn, button { display: none !important; }
    .chart-box, .content-box { box-shadow: none !important; border: 1px solid #e2e8f0; page-break-inside: avoid; }
}
</style>
@endsection
