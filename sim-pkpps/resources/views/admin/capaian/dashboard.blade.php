@extends('layouts.app')

@section('content')
<div class="page-header">
    <h2><i class="fas fa-chart-pie"></i> Dashboard Capaian Al-Qur'an & Hadist</h2>
</div>

{{-- Filter Section --}}
<div class="content-box" style="margin-bottom: 20px;">
    <form method="GET" action="{{ route('admin.capaian.dashboard') }}" class="filter-form-inline">
        <select name="id_santri" class="form-control" style="width: 250px;">
            <option value="">Semua Santri</option>
            @foreach($santris as $santri)
                <option value="{{ $santri->id_santri }}" {{ $idSantri == $santri->id_santri ? 'selected' : '' }}>
                    {{ $santri->nama_lengkap }} ({{ $santri->kelas }})
                </option>
            @endforeach
        </select>

        <select name="id_semester" class="form-control" style="width: 200px;">
            <option value="">Semua Semester</option>
            @foreach($semesters as $semester)
                <option value="{{ $semester->id_semester }}" {{ $selectedSemester == $semester->id_semester ? 'selected' : '' }}>
                    {{ $semester->nama_semester }} @if($semester->is_active) ★ @endif
                </option>
            @endforeach
        </select>

        <select name="kelas" class="form-control" style="width: 150px;">
            <option value="">Semua Kelas</option>
            <option value="Lambatan" {{ $kelas == 'Lambatan' ? 'selected' : '' }}>Lambatan</option>
            <option value="Cepatan" {{ $kelas == 'Cepatan' ? 'selected' : '' }}>Cepatan</option>
            <option value="PB" {{ $kelas == 'PB' ? 'selected' : '' }}>PB</option>
        </select>

        <button type="submit" class="btn btn-primary">
            <i class="fas fa-filter"></i> Filter
        </button>

        @if($idSantri || $kelas)
            <a href="{{ route('admin.capaian.dashboard') }}" class="btn btn-secondary">
                <i class="fas fa-redo"></i> Reset
            </a>
        @endif
    </form>
</div>

{{-- Statistik Cards --}}
<div class="row-cards">
    <div class="card card-info">
        <h3>Total Capaian</h3>
        <div class="card-value">{{ $totalCapaian }}</div>
        <p class="text-muted">Data capaian tercatat</p>
        <i class="fas fa-clipboard-list card-icon"></i>
    </div>
    <div class="card card-success">
        <h3>Total Santri</h3>
        <div class="card-value">{{ $totalSantri }}</div>
        <p class="text-muted">Santri aktif dengan capaian</p>
        <i class="fas fa-users card-icon"></i>
    </div>
    <div class="card card-primary">
        <h3>Rata-rata Progress</h3>
        <div class="card-value">{{ number_format($rataRataPersentase, 1) }}%</div>
        <p class="text-muted">Progress keseluruhan</p>
        <i class="fas fa-chart-line card-icon"></i>
    </div>
    <div class="card card-warning">
        <h3>Selesai 100%</h3>
        <div class="card-value">{{ $capaianSelesai }}</div>
        <p class="text-muted">Materi yang diselesaikan</p>
        <i class="fas fa-trophy card-icon"></i>
    </div>
</div>

{{-- Statistik Per Kategori --}}
<div class="row-cards">
    @foreach($statistikKategori as $kategori => $stats)
        <div class="card card-{{ $kategori == 'Al-Qur\'an' ? 'primary' : ($kategori == 'Hadist' ? 'success' : 'info') }}">
            <h3>{{ $kategori }}</h3>
            <div class="card-value-small">{{ number_format($stats['avg'], 1) }}%</div>
            <p class="text-muted">
                {{ $stats['count'] }} capaian | {{ $stats['selesai'] }} selesai
            </p>
            <i class="fas fa-{{ $kategori == 'Al-Qur\'an' ? 'book-quran' : ($kategori == 'Hadist' ? 'scroll' : 'book') }} card-icon"></i>
        </div>
    @endforeach
</div>

{{-- Grafik Section --}}
<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
    {{-- Grafik Pie - Progress per Kategori --}}
    <div class="content-box">
        <h4 style="margin: 0 0 20px 0; color: var(--primary-dark);">
            <i class="fas fa-chart-pie"></i> Progress per Kategori
        </h4>
        <canvas id="chartKategori" style="max-height: 300px;"></canvas>
    </div>

    {{-- Grafik Bar - Distribusi Persentase --}}
    <div class="content-box">
        <h4 style="margin: 0 0 20px 0; color: var(--primary-dark);">
            <i class="fas fa-chart-bar"></i> Distribusi Progress Santri
        </h4>
        <canvas id="chartDistribusi" style="max-height: 300px;"></canvas>
    </div>
</div>

{{-- Grafik Line - Trend Progress --}}
<div class="content-box" style="margin-bottom: 20px;">
    <h4 style="margin: 0 0 20px 0; color: var(--primary-dark);">
        <i class="fas fa-chart-line"></i> Trend Progress dari Waktu ke Waktu
    </h4>
    <canvas id="chartTrend" style="max-height: 250px;"></canvas>
</div>

{{-- Top 10 Santri --}}
<div class="content-box" style="margin-bottom: 20px;">
    <h4 style="margin: 0 0 20px 0; color: var(--primary-dark);">
        <i class="fas fa-trophy"></i> Top 10 Santri dengan Progress Tertinggi
    </h4>
    @if($topSantri->count() > 0)
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 5%;">Rank</th>
                    <th style="width: 10%;">NIS</th>
                    <th style="width: 30%;">Nama Santri</th>
                    <th style="width: 10%;">Kelas</th>
                    <th style="width: 25%;">Rata-rata Progress</th>
                    <th class="text-center" style="width: 20%;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($topSantri as $index => $item)
                    <tr>
                        <td class="text-center">
                            @if($index < 3)
                                <span style="font-size: 1.5rem;">
                                    @if($index == 0) 🥇
                                    @elseif($index == 1) 🥈
                                    @else 🥉
                                    @endif
                                </span>
                            @else
                                <strong>{{ $index + 1 }}</strong>
                            @endif
                        </td>
                        <td>{{ $item->santri->nis }}</td>
                        <td><strong>{{ $item->santri->nama_lengkap }}</strong></td>
                        <td><span class="badge badge-secondary">{{ $item->santri->kelas }}</span></td>
                        <td>
                            <div class="progress-bar" style="height: 25px;">
                                <div class="progress-fill" style="width: {{ $item->rata_rata }}%; background: linear-gradient(90deg, var(--primary-color), var(--success-color)); display: flex; align-items: center; justify-content: center; color: white; font-weight: bold;">
                                    {{ number_format($item->rata_rata, 1) }}%
                                </div>
                            </div>
                        </td>
                        <td class="text-center">
                            <a href="{{ route('admin.capaian.riwayat-santri', $item->id_santri) }}" class="btn btn-sm btn-info">
                                <i class="fas fa-eye"></i> Lihat Detail
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div class="empty-state">
            <i class="fas fa-chart-line"></i>
            <p>Belum ada data untuk ditampilkan</p>
        </div>
    @endif
</div>

{{-- Materi dengan Progress Terendah (Perlu Perhatian) --}}
@if($materiTerendah->count() > 0)
<div class="content-box">
    <h4 style="margin: 0 0 20px 0; color: var(--danger-color);">
        <i class="fas fa-exclamation-triangle"></i> Materi yang Perlu Perhatian (Progress < 50%)
    </h4>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 30%;">Nama Materi</th>
                <th style="width: 15%;">Kategori</th>
                <th style="width: 10%;">Kelas</th>
                <th style="width: 10%;">Jumlah Santri</th>
                <th style="width: 20%;">Rata-rata Progress</th>
                <th class="text-center" style="width: 15%;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($materiTerendah as $item)
                <tr>
                    <td><strong>{{ $item->materi->nama_kitab }}</strong></td>
                    <td>{!! $item->materi->kategori_badge !!}</td>
                    <td>{!! $item->materi->kelas_badge !!}</td>
                    <td class="text-center">{{ $item->jumlah_santri }} santri</td>
                    <td>
                        <div class="progress-bar" style="height: 20px;">
                            <div class="progress-fill" style="width: {{ $item->rata_rata }}%; background: linear-gradient(90deg, var(--danger-color), var(--warning-color)); display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; font-size: 0.85rem;">
                                {{ number_format($item->rata_rata, 1) }}%
                            </div>
                        </div>
                    </td>
                    <td class="text-center">
                        <a href="{{ route('admin.capaian.detail-materi', $item->id_materi) }}" class="btn btn-sm btn-warning">
                            <i class="fas fa-eye"></i> Detail
                        </a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endif

{{-- Quick Actions --}}
<div style="margin-top: 30px; display: flex; gap: 10px; justify-content: center;">
    <a href="{{ route('admin.capaian.rekap-kelas') }}" class="btn btn-primary">
        <i class="fas fa-table"></i> Rekap per Kelas
    </a>
    <a href="{{ route('admin.capaian.create') }}" class="btn btn-success">
        <i class="fas fa-plus"></i> Input Capaian Baru
    </a>
    <a href="{{ route('admin.materi.index') }}" class="btn btn-info">
        <i class="fas fa-book"></i> Master Materi
    </a>
</div>

{{-- Chart.js Script --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Chart 1: Pie Chart - Progress per Kategori
const ctxKategori = document.getElementById('chartKategori').getContext('2d');
const chartKategori = new Chart(ctxKategori, {
    type: 'pie',
    data: {
        labels: ['Al-Qur\'an', 'Hadist', 'Materi Tambahan'],
        datasets: [{
            label: 'Rata-rata Progress (%)',
            data: [
                {{ number_format($statistikKategori['Al-Qur\'an']['avg'], 2) }},
                {{ number_format($statistikKategori['Hadist']['avg'], 2) }},
                {{ number_format($statistikKategori['Materi Tambahan']['avg'], 2) }}
            ],
            backgroundColor: [
                'rgba(111, 186, 157, 0.8)',
                'rgba(129, 198, 232, 0.8)',
                'rgba(255, 213, 107, 0.8)',
            ],
            borderColor: [
                'rgba(111, 186, 157, 1)',
                'rgba(129, 198, 232, 1)',
                'rgba(255, 213, 107, 1)',
            ],
            borderWidth: 2
        }]
        },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: {
                position: 'bottom',
                labels: {
                    padding: 15,
                    font: {
                        size: 12
                    }
                }
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        return context.label + ': ' + context.parsed.toFixed(2) + '%';
                    }
                }
            }
        }
    }
});

// Chart 2: Bar Chart - Distribusi Persentase
const ctxDistribusi = document.getElementById('chartDistribusi').getContext('2d');
const chartDistribusi = new Chart(ctxDistribusi, {
    type: 'bar',
    data: {
        labels: ['0-25%', '26-50%', '51-75%', '76-99%', '100%'],
        datasets: [{
            label: 'Jumlah Santri',
            data: [
                {{ $distribusiPersentase['0-25%'] }},
                {{ $distribusiPersentase['26-50%'] }},
                {{ $distribusiPersentase['51-75%'] }},
                {{ $distribusiPersentase['76-99%'] }},
                {{ $distribusiPersentase['100%'] }}
            ],
            backgroundColor: [
                'rgba(255, 139, 148, 0.8)',
                'rgba(255, 171, 145, 0.8)',
                'rgba(255, 213, 107, 0.8)',
                'rgba(129, 198, 232, 0.8)',
                'rgba(111, 186, 157, 0.8)',
            ],
            borderColor: [
                'rgba(255, 139, 148, 1)',
                'rgba(255, 171, 145, 1)',
                'rgba(255, 213, 107, 1)',
                'rgba(129, 198, 232, 1)',
                'rgba(111, 186, 157, 1)',
            ],
            borderWidth: 2
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    stepSize: 1
                }
            }
        },
        plugins: {
            legend: {
                display: false
            }
        }
    }
});

// Chart 3: Line Chart - Trend Progress (Load via AJAX)
fetch('{{ route("admin.capaian.api.grafik-data") }}?type=trend&id_semester={{ $selectedSemester }}&kelas={{ $kelas }}')
    .then(response => response.json())
    .then(data => {
        const ctxTrend = document.getElementById('chartTrend').getContext('2d');
        const chartTrend = new Chart(ctxTrend, {
            type: 'line',
            data: data,
            options: {
                responsive: true,
                maintainAspectRatio: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100,
                        ticks: {
                            callback: function(value) {
                                return value + '%';
                            }
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: true,
                        position: 'bottom'
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.dataset.label + ': ' + context.parsed.y.toFixed(2) + '%';
                            }
                        }
                    }
                }
            }
        });
    })
    .catch(error => console.error('Error loading trend chart:', error));
</script>
@endsection