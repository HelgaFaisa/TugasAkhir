@extends('layouts.app')

@section('content')
<style>
.detail-header { display: flex; gap: 20px; align-items: flex-start; margin-bottom: 14px; }
.detail-avatar { width: 80px; height: 80px; border-radius: 50%; background: linear-gradient(135deg, #10B981, #34D399); display: flex; align-items: center; justify-content: center; color: #fff; font-size: 2rem; font-weight: 700; flex-shrink: 0; }
.detail-info h3 { margin: 0 0 4px; color: var(--primary-dark); }
.detail-info p { margin: 0; font-size: 0.85rem; color: var(--text-light); }
.mini-kpi { display: grid; grid-template-columns: repeat(auto-fit, minmax(140px, 1fr)); gap: 12px; margin-bottom: 14px; }
.mini-kpi-card { padding: 16px; border-radius: 10px; text-align: center; }
.mini-kpi-card.hadir { background: #ECFDF5; color: #065F46; }
.mini-kpi-card.izin { background: #FFFBEB; color: #92400E; }
.mini-kpi-card.sakit { background: #EFF6FF; color: #1E40AF; }
.mini-kpi-card.alpa { background: #FEF2F2; color: #991B1B; }
.mini-kpi-val { font-size: 1.6rem; font-weight: 700; }
.mini-kpi-label { font-size: 0.78rem; margin-top: 4px; }
.chart-box { background: #fff; border-radius: 12px; padding: 14px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); margin-bottom: 14px; }
.chart-box h4 { margin: 0 0 16px; color: var(--primary-dark); font-size: 1rem; }
.progress-bar-lg { background: #e9ecef; border-radius: 10px; height: 24px; overflow: hidden; margin: 12px 0; }
.progress-bar-lg .fill { height: 100%; border-radius: 10px; transition: width 0.5s; display: flex; align-items: center; justify-content: center; color: #fff; font-size: 0.82rem; font-weight: 600; }
.insight-box { padding: 12px 16px; border-radius: 8px; margin-bottom: 8px; display: flex; align-items: flex-start; gap: 10px; font-size: 0.85rem; }
.insight-box.i-success { background: #ECFDF5; color: #065F46; }
.insight-box.i-warning { background: #FFFBEB; color: #92400E; }
.insight-box.i-danger { background: #FEF2F2; color: #991B1B; }
.insight-box.i-info { background: #EFF6FF; color: #1E40AF; }
.two-col { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
@media(max-width:768px){ .two-col { grid-template-columns: 1fr; } }
.progress-inline { display: flex; align-items: center; gap: 8px; }
.progress-bar-mini { flex: 1; background: #e9ecef; border-radius: 8px; height: 8px; overflow: hidden; max-width: 120px; }
.progress-bar-mini .fill { height: 100%; border-radius: 8px; }
.text-center { text-align: center; }
</style>

<div class="page-header">
    <h2><i class="fas fa-user-chart"></i> Detail Kehadiran Santri</h2>
    <a href="{{ route('admin.laporan-kegiatan.index', request()->query()) }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Kembali
    </a>
</div>

{{-- Header --}}
<div class="content-box" style="margin-bottom: 14px;">
    <div class="detail-header">
        <div class="detail-avatar">{{ strtoupper(substr($santri->nama_lengkap, 0, 1)) }}</div>
        <div class="detail-info">
            <h3>{{ $santri->nama_lengkap }}</h3>
            <p><i class="fas fa-id-card"></i> {{ $santri->id_santri }}</p>
            @if($santri->kelasSantri && $santri->kelasSantri->count() > 0)
                <p><i class="fas fa-school"></i>
                    @foreach($santri->kelasSantri as $ks)
                        <span class="badge badge-info">{{ $ks->kelas->nama_kelas ?? '-' }}</span>
                    @endforeach
                </p>
            @endif
            <p style="margin-top:4px;"><i class="fas fa-calendar"></i> Periode: <strong>{{ $periodeLabel }}</strong></p>
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
<div class="content-box" style="margin-bottom: 14px;">
    <h4 style="margin:0 0 8px; font-size:0.95rem;"><i class="fas fa-chart-bar"></i> Kehadiran Keseluruhan</h4>
    <div class="progress-bar-lg">
        <div class="fill" style="width:{{ $persenKehadiran }}%; background:{{ $persenKehadiran >= 85 ? '#10B981' : ($persenKehadiran >= 70 ? '#FBBF24' : '#EF4444') }};">
            {{ $persenKehadiran }}%
        </div>
    </div>
    @if($persenKehadiran >= 85)
        <div class="insight-box i-success"><i class="fas fa-star"></i> Kehadiran sangat baik. Pertahankan!</div>
    @elseif($persenKehadiran >= 70)
        <div class="insight-box i-warning"><i class="fas fa-exclamation-triangle"></i> Kehadiran cukup, perlu ditingkatkan.</div>
    @else
        <div class="insight-box i-danger"><i class="fas fa-times-circle"></i> Kehadiran di bawah standar. Perlu perhatian khusus.</div>
    @endif
</div>

<div class="two-col">
    {{-- Trend 4 Minggu --}}
    <div class="chart-box">
        <h4><i class="fas fa-chart-line"></i> Trend 4 Minggu Terakhir</h4>
        <div style="height:220px;">
            <canvas id="santriTrendChart"></canvas>
        </div>
    </div>

    {{-- Insights --}}
    <div class="chart-box">
        <h4><i class="fas fa-lightbulb"></i> Insight</h4>
        @if($kegiatanBolos)
            <div class="insight-box i-danger">
                <i class="fas fa-user-times"></i>
                Paling sering Alpa di kegiatan: <strong>{{ $kegiatanBolos->kegiatan->nama_kegiatan ?? '-' }}</strong> ({{ $kegiatanBolos->total_alpa }}x)
            </div>
        @endif
        @if($streak > 0)
            <div class="insight-box i-success">
                <i class="fas fa-fire"></i>
                Streak kehadiran beruntun: <strong>{{ $streak }} kegiatan</strong> ðŸ”¥
            </div>
        @endif
        @if(($stats->total ?? 0) > 0)
            <div class="insight-box i-info">
                <i class="fas fa-calculator"></i>
                Total tercatat: {{ $stats->total }} absensi ({{ $stats->hadir }} hadir, {{ $stats->alpa }} alpa)
            </div>
        @endif
    </div>
</div>

{{-- Kehadiran Per Kegiatan --}}
<div class="chart-box">
    <h4><i class="fas fa-tasks"></i> Kehadiran Per Kegiatan</h4>
    @if($perKegiatan->count() > 0)
        <table class="data-table" style="font-size:0.85rem;">
            <thead>
                <tr>
                    <th>No</th><th>Kegiatan</th>
                    <th class="text-center">Hadir</th><th class="text-center">Izin</th>
                    <th class="text-center">Sakit</th><th class="text-center">Alpa</th>
                    <th class="text-center" style="min-width:180px;">% Kehadiran</th>
                </tr>
            </thead>
            <tbody>
                @foreach($perKegiatan as $i => $kg)
                    <tr style="{{ $kg->persen < 70 ? 'background:#FEF2F2;' : '' }}">
                        <td>{{ $i + 1 }}</td>
                        <td><strong>{{ $kg->nama_kegiatan }}</strong></td>
                        <td class="text-center"><span class="badge badge-success">{{ $kg->hadir }}</span></td>
                        <td class="text-center"><span class="badge badge-warning">{{ $kg->izin }}</span></td>
                        <td class="text-center"><span class="badge badge-info">{{ $kg->sakit }}</span></td>
                        <td class="text-center"><span class="badge badge-danger">{{ $kg->alpa }}</span></td>
                        <td class="text-center">
                            <div class="progress-inline" style="justify-content:center;">
                                <div class="progress-bar-mini"><div class="fill" style="width:{{ $kg->persen }}%; background:{{ $kg->persen >= 85 ? '#10B981' : ($kg->persen >= 70 ? '#FBBF24' : '#EF4444') }};"></div></div>
                                <strong style="color:{{ $kg->persen >= 85 ? '#10B981' : ($kg->persen >= 70 ? '#92400E' : '#EF4444') }};">{{ $kg->persen }}%</strong>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p style="color:var(--text-light); font-size:0.85rem;">Belum ada data kehadiran per kegiatan.</p>
    @endif
</div>

{{-- Riwayat Terbaru --}}
<div class="chart-box">
    <h4><i class="fas fa-history"></i> Riwayat Absensi Terbaru</h4>
    @if($riwayatTerbaru->count() > 0)
        <table class="data-table" style="font-size:0.85rem;">
            <thead><tr><th>Tanggal</th><th>Kegiatan</th><th>Kategori</th><th class="text-center">Status</th></tr></thead>
            <tbody>
                @foreach($riwayatTerbaru as $r)
                    <tr>
                        <td>{{ $r->tanggal->format('d/m/Y') }}</td>
                        <td>{{ $r->kegiatan->nama_kegiatan ?? '-' }}</td>
                        <td>{{ $r->kegiatan->kategori->nama_kategori ?? '-' }}</td>
                        <td class="text-center">{!! $r->status_badge !!}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p style="color:var(--text-light);">Belum ada riwayat.</p>
    @endif
</div>

{{-- Actions --}}
<div class="content-box" style="display:flex; gap:8px; flex-wrap:wrap;">
    <a href="{{ route('admin.riwayat-kegiatan.detail-santri', $santri->id_santri) }}" class="btn btn-info">
        <i class="fas fa-history"></i> Riwayat Lengkap
    </a>
    <button onclick="window.print()" class="btn btn-secondary"><i class="fas fa-print"></i> Cetak Laporan</button>
    <a href="{{ route('admin.laporan-kegiatan.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Kembali ke Laporan</a>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
const trend = @json($trend);
new Chart(document.getElementById('santriTrendChart'), {
    type: 'line',
    data: {
        labels: trend.map(t => t.label),
        datasets: [{
            label: '% Kehadiran',
            data: trend.map(t => t.persen),
            borderColor: '#3B82F6',
            backgroundColor: 'rgba(59,130,246,0.1)',
            tension: 0.4,
            fill: true,
            pointRadius: 6,
            pointBackgroundColor: trend.map(t => t.persen >= 85 ? '#10B981' : (t.persen >= 70 ? '#FBBF24' : '#EF4444')),
        }]
    },
    options: {
        responsive: true, maintainAspectRatio: false,
        plugins: { legend: { display: false }, tooltip: { callbacks: { label: ctx => ctx.raw + '%' } } },
        scales: { y: { min: 0, max: 100, ticks: { callback: v => v + '%' } }, x: { grid: { display: false } } }
    }
});
</script>

<style>
@media print {
    .btn, button, .page-header .btn { display: none !important; }
    .chart-box, .content-box { box-shadow: none !important; border: 1px solid #e2e8f0; page-break-inside: avoid; }
}
</style>
@endsection
