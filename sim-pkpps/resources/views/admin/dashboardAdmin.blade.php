{{-- views/admin/dashboardAdmin.blade.php --}}
@extends('layouts.app', ['isAdmin' => true])

@section('title', 'Dashboard Admin')

@section('content')
<div class="page-header">
    <h2>Dashboard Admin</h2>
    <p>{{ $hariIni }}, {{ $today->translatedFormat('d F Y') }}</p>
</div>

{{-- 1. KPI Cards --}}
@include('admin.dashboard._kpi-cards', ['kpi' => $kpiCards])

{{-- 2. Jadwal Kegiatan Hari Ini --}}
@include('admin.dashboard._jadwal-kegiatan', ['kegiatan' => $kegiatanHariIni, 'hari' => $hariIni])

{{-- 3. Alert Panel --}}
@include('admin.dashboard._alert-panel', ['alerts' => $alerts])

{{-- Row: Grafik + SPP --}}
<div class="dash-grid-2">
    {{-- 4. Grafik Tren Kehadiran --}}
    @include('admin.dashboard._tren-kehadiran', ['trenKehadiran' => $trenKehadiran])

    {{-- 5. Ringkasan SPP Bulan Ini --}}
    @include('admin.dashboard._ringkasan-spp', ['spp' => $sppBulanIni])
</div>

{{-- 6. Feed Aktivitas Terbaru --}}
@include('admin.dashboard._feed-aktivitas', ['feed' => $feedAktivitas])
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // ── Tren Kehadiran (Line Chart) ──
    const trenCtx = document.getElementById('trenKehadiranChart');
    if (trenCtx) {
        const trenData = @json($trenKehadiran);
        const colors = ['#6FBA9D', '#FF8B94', '#81C6E8', '#FFD56B', '#B39DDB', '#FFAB91'];
        const datasets = Object.keys(trenData.series).map((label, i) => ({
            label: label,
            data: trenData.series[label],
            borderColor: colors[i % colors.length],
            backgroundColor: colors[i % colors.length] + '20',
            tension: 0.3,
            fill: true,
            pointRadius: 4,
            pointHoverRadius: 6,
        }));

        new Chart(trenCtx, {
            type: 'line',
            data: { labels: trenData.labels, datasets },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom', labels: { padding: 16, usePointStyle: true } },
                    tooltip: { callbacks: { label: ctx => ctx.dataset.label + ': ' + ctx.parsed.y + '%' } }
                },
                scales: {
                    y: { beginAtZero: true, max: 100, ticks: { callback: v => v + '%' } }
                }
            }
        });
    }

    // ── Ringkasan SPP (Donut Chart) ──
    const sppCtx = document.getElementById('sppDonutChart');
    if (sppCtx) {
        const sppData = @json($sppBulanIni);
        new Chart(sppCtx, {
            type: 'doughnut',
            data: {
                labels: ['Lunas', 'Belum Lunas'],
                datasets: [{
                    data: [sppData.lunas, sppData.belum],
                    backgroundColor: ['#6FBA9D', '#FF8B94'],
                    borderWidth: 2,
                    borderColor: '#fff',
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '65%',
                plugins: {
                    legend: { position: 'bottom', labels: { padding: 16, usePointStyle: true } },
                }
            }
        });
    }
});
</script>
@endsection