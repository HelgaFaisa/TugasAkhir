@extends('layouts.app')

@section('content')
<div class="page-header">
    <h2><i class="fas fa-user-clock"></i> Riwayat Kehadiran: {{ $santri->nama_lengkap }}</h2>
</div>

<!-- Info Santri -->
<div class="content-box" style="margin-bottom: 20px;">
    <div style="display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h3 style="margin: 0; color: var(--primary-color);">{{ $santri->nama_lengkap }}</h3>
            <p style="margin: 5px 0 0 0; color: var(--text-light);">
                ID: <strong>{{ $santri->id_santri }}</strong> | 
                Kelas: <strong>{{ $santri->kelas }}</strong> | 
                Status: <span class="badge badge-success">{{ $santri->status }}</span>
            </p>
        </div>
        <a href="{{ route('admin.riwayat-kegiatan.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>
</div>

<!-- Statistik Cards -->
<div class="row-cards">
    <div class="card card-success">
        <h3>Total Hadir</h3>
        <div class="card-value">{{ $stats['Hadir'] ?? 0 }}</div>
        <i class="fas fa-check-circle card-icon"></i>
    </div>
    <div class="card card-warning">
        <h3>Total Izin</h3>
        <div class="card-value">{{ $stats['Izin'] ?? 0 }}</div>
        <i class="fas fa-info-circle card-icon"></i>
    </div>
    <div class="card card-info">
        <h3>Total Sakit</h3>
        <div class="card-value">{{ $stats['Sakit'] ?? 0 }}</div>
        <i class="fas fa-heartbeat card-icon"></i>
    </div>
    <div class="card card-danger">
        <h3>Total Alpa</h3>
        <div class="card-value">{{ $stats['Alpa'] ?? 0 }}</div>
        <i class="fas fa-times-circle card-icon"></i>
    </div>
</div>

<!-- Grafik Kehadiran Per Kategori -->
@if($statsByKategori->count() > 0)
<div class="content-box" style="margin-bottom: 20px;">
    <h3 style="margin: 0 0 20px 0; color: var(--primary-color);">
        <i class="fas fa-chart-bar"></i> Kehadiran Per Kategori
    </h3>
    <canvas id="chartKategori" style="max-height: 300px;"></canvas>
</div>
@endif

<!-- Grafik Tren 30 Hari -->
@if($riwayat30Hari->count() > 0)
<div class="content-box" style="margin-bottom: 20px;">
    <h3 style="margin: 0 0 20px 0; color: var(--primary-color);">
        <i class="fas fa-chart-line"></i> Tren Kehadiran 30 Hari Terakhir
    </h3>
    <canvas id="chartTren" style="max-height: 250px;"></canvas>
</div>
@endif

<!-- Kehadiran Per Kelas Santri -->
@if($statsByKelasSantri->count() > 0)
<div class="content-box" style="margin-bottom: 20px;">
    <h3 style="margin: 0 0 20px 0; color: var(--primary-color);">
        <i class="fas fa-layer-group"></i> Kehadiran Per Kelas
    </h3>
    <table class="data-table">
        <thead>
            <tr>
                <th>Kelompok</th>
                <th>Kelas</th>
                <th style="width: 120px; text-align: center;">Total Kegiatan</th>
                <th style="width: 120px; text-align: center;">Hadir</th>
                <th style="width: 150px; text-align: center;">Persentase</th>
            </tr>
        </thead>
        <tbody>
            @foreach($statsByKelasSantri as $stat)
            <tr>
                <td><span class="badge badge-info">{{ $stat['kelompok'] }}</span></td>
                <td><strong>{{ $stat['kelas'] }}</strong></td>
                <td class="text-center">{{ $stat['total'] }}</td>
                <td class="text-center"><strong>{{ $stat['hadir'] }}</strong></td>
                <td class="text-center">
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <div style="flex: 1; background: #e9ecef; border-radius: 10px; height: 20px; overflow: hidden;">
                            <div style="background: 
                                @if($stat['persen'] >= 85) var(--success-color)
                                @elseif($stat['persen'] >= 70) var(--warning-color)
                                @else var(--danger-color)
                                @endif; 
                                height: 100%; width: {{ $stat['persen'] }}%; 
                                transition: width 0.3s ease;">
                            </div>
                        </div>
                        <strong style="min-width: 45px;">{{ $stat['persen'] }}%</strong>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endif

<!-- Riwayat Lengkap -->
<div class="content-box">
    <h3 style="margin: 0 0 20px 0; color: var(--primary-color);">
        <i class="fas fa-list"></i> Riwayat Lengkap
    </h3>

    @if($riwayats->count() > 0)
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 50px;">No</th>
                    <th style="width: 120px;">Tanggal</th>
                    <th>Kegiatan</th>
                    <th style="width: 150px;">Kategori</th>
                    <th style="width: 130px; text-align: center;">Status</th>
                    <th style="width: 100px;">Waktu</th>
                </tr>
            </thead>
            <tbody>
                @foreach($riwayats as $index => $riwayat)
                <tr>
                    <td>{{ $riwayats->firstItem() + $index }}</td>
                    <td>{{ $riwayat->tanggal->format('d/m/Y') }}</td>
                    <td>{{ $riwayat->kegiatan->nama_kegiatan }}</td>
                    <td>{{ $riwayat->kegiatan->kategori->nama_kategori }}</td>
                    <td class="text-center">{!! $riwayat->status_badge !!}</td>
                    <td>{{ $riwayat->waktu_absen ? date('H:i', strtotime($riwayat->waktu_absen)) : '-' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div style="margin-top: 20px;">
            {{ $riwayats->links() }}
        </div>
    @else
        <div class="empty-state">
            <i class="fas fa-inbox"></i>
            <h3>Belum Ada Riwayat</h3>
            <p>Santri ini belum memiliki riwayat kehadiran.</p>
        </div>
    @endif
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

@if($statsByKategori->count() > 0)
<script>
// Chart Kehadiran Per Kategori
const ctxKategori = document.getElementById('chartKategori');
new Chart(ctxKategori, {
    type: 'bar',
    data: {
        labels: {!! json_encode($statsByKategori->pluck('nama_kategori')) !!},
        datasets: [{
            label: 'Hadir',
            data: {!! json_encode($statsByKategori->pluck('hadir')) !!},
            backgroundColor: '#6FBA9D',
            borderWidth: 0
        }, {
            label: 'Total Kegiatan',
            data: {!! json_encode($statsByKategori->pluck('total')) !!},
            backgroundColor: '#E0F0EC',
            borderWidth: 0
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: { position: 'bottom' }
        },
        scales: {
            y: { beginAtZero: true, ticks: { stepSize: 1 } }
        }
    }
});
</script>
@endif

@if($riwayat30Hari->count() > 0)
<script>
// Chart Tren 30 Hari
const ctxTren = document.getElementById('chartTren');
new Chart(ctxTren, {
    type: 'line',
    data: {
        labels: {!! json_encode($riwayat30Hari->pluck('tanggal')->map(fn($d) => date('d/m', strtotime($d)))) !!},
        datasets: [{
            label: 'Hadir',
            data: {!! json_encode($riwayat30Hari->pluck('hadir')) !!},
            borderColor: '#6FBA9D',
            backgroundColor: 'rgba(111, 186, 157, 0.1)',
            tension: 0.4,
            fill: true
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: { display: false }
        },
        scales: {
            y: { beginAtZero: true, ticks: { stepSize: 1 } }
        }
    }
});
</script>
@endif
@endsection