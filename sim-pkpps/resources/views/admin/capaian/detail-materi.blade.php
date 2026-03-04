@extends('layouts.app')

@section('content')
<div class="page-header">
    <h2><i class="fas fa-book-open"></i> Detail Capaian per Materi</h2>
</div>

{{-- Materi Info Card --}}
<div class="content-box" style="margin-bottom: 14px;">
    <div style="display: flex; align-items: center; gap: 20px;">
        <div class="icon-wrapper icon-wrapper-lg">
            <i class="fas fa-book"></i>
        </div>
        <div style="flex: 1;">
            <h3 style="margin: 0 0 5px 0;">{{ $materi->nama_kitab }}</h3>
            <p style="margin: 0; color: var(--text-light);">
                {!! $materi->kategori_badge !!} | 
                {!! $materi->kelas_badge !!} | 
                <strong>Total:</strong> {{ $materi->total_halaman }} halaman
            </p>
        </div>
        <div style="text-align: right;">
            <a href="{{ route('admin.materi.show', $materi) }}" class="btn btn-info">
                <i class="fas fa-eye"></i> Detail Materi
            </a>
        </div>
    </div>
</div>

{{-- Filter Semester --}}
<div class="content-box" style="margin-bottom: 14px;">
    <form method="GET" action="{{ route('admin.capaian.detail-materi', $materi->id_materi) }}" class="filter-form-inline">
        <select name="id_semester" class="form-control" style="width: 250px;">
            <option value="">Semua Semester</option>
            @foreach($semesters as $semester)
                <option value="{{ $semester->id_semester }}" {{ $selectedSemester == $semester->id_semester ? 'selected' : '' }}>
                    {{ $semester->nama_semester }} @if($semester->is_active) Ã¢Ëœâ€¦ @endif
                </option>
            @endforeach
        </select>

        <button type="submit" class="btn btn-primary">
            <i class="fas fa-filter"></i> Filter
        </button>
    </form>
</div>

{{-- Statistik Cards --}}
<div class="row-cards">
    <div class="card card-info">
        <h3>Total Santri</h3>
        <div class="card-value">{{ $totalSantri }}</div>
        <p class="text-muted">Santri yang belajar materi ini</p>
        <i class="fas fa-users card-icon"></i>
    </div>
    <div class="card card-success">
        <h3>Selesai 100%</h3>
        <div class="card-value">{{ $santriSelesai }}</div>
        <p class="text-muted">{{ $totalSantri > 0 ? number_format(($santriSelesai/$totalSantri)*100, 1) : 0 }}% dari total santri</p>
        <i class="fas fa-check-circle card-icon"></i>
    </div>
    <div class="card card-warning">
        <h3>Sedang Belajar</h3>
        <div class="card-value">{{ $santriMulai }}</div>
        <p class="text-muted">Progress 1-99%</p>
        <i class="fas fa-book-reader card-icon"></i>
    </div>
    <div class="card card-primary">
        <h3>Rata-rata Progress</h3>
        <div class="card-value">{{ number_format($rataRataPersentase, 1) }}%</div>
        <p class="text-muted">Progress keseluruhan</p>
        <i class="fas fa-chart-line card-icon"></i>
    </div>
</div>

{{-- Grafik Distribusi --}}
<div class="content-box" style="margin-bottom: 14px;">
    <h4 style="margin: 0 0 20px 0; color: var(--primary-dark);">
        <i class="fas fa-chart-bar"></i> Distribusi Progress Santri
    </h4>
    <canvas id="chartDistribusi" style="max-height: 250px;"></canvas>
</div>

{{-- Tabel Detail Capaian Santri --}}
<div class="content-box">
    <h4 style="margin: 0 0 20px 0; color: var(--primary-dark);">
        <i class="fas fa-list"></i> Detail Capaian per Santri
    </h4>
    
    @if($capaians->count() > 0)
        <div class="table-wrapper">
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 5%;">No</th>
                    <th style="width: 10%;">NIS</th>
                    <th style="width: 25%;">Nama Santri</th>
                    <th style="width: 10%;">Kelas</th>
                    <th style="width: 15%;">Semester</th>
                    <th style="width: 10%;">Halaman</th>
                    <th style="width: 15%;">Progress</th>
                    <th class="text-center" style="width: 10%;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($capaians as $index => $capaian)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $capaian->santri->nis }}</td>
                        <td>
                            <strong>{{ $capaian->santri->nama_lengkap }}</strong>
                        </td>
                        <td>
                            <span class="badge badge-secondary">{{ $capaian->santri->kelas }}</span>
                        </td>
                        <td>
                            <small>{{ $capaian->semester->nama_semester }}</small>
                        </td>
                        <td class="text-center">
                            <span class="badge badge-info">
                                {{ $capaian->jumlah_halaman_selesai }} / {{ $materi->total_halaman }}
                            </span>
                        </td>
                        <td>
                            <div class="progress-bar" style="height: 25px;">
                                <div class="progress-fill" 
                                     style="width: {{ $capaian->persentase }}%; 
                                            background: linear-gradient(90deg, 
                                                {{ $capaian->persentase >= 75 ? 'var(--success-color), var(--primary-color)' : ($capaian->persentase >= 50 ? 'var(--warning-color), var(--accent-peach)' : 'var(--danger-color), var(--secondary-color)') }}); 
                                            display: flex; 
                                            align-items: center; 
                                            justify-content: center; 
                                            color: white; 
                                            font-weight: bold;">
                                    {{ number_format($capaian->persentase, 1) }}%
                                </div>
                            </div>
                        </td>
                        <td class="text-center">
                            <div class="btn-group">
                                <a href="{{ route('admin.capaian.show', $capaian) }}" 
                                   class="btn btn-sm btn-info" title="Detail">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.capaian.edit', $capaian) }}" 
                                   class="btn btn-sm btn-warning" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        </div>
    @else
        <div class="empty-state">
            <i class="fas fa-clipboard-list"></i>
            <h3>Belum Ada Capaian</h3>
            <p>Belum ada santri yang mencatat capaian untuk materi ini.</p>
        </div>
    @endif
</div>

{{-- Chart.js Script --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Chart: Distribusi Progress
const ctxDistribusi = document.getElementById('chartDistribusi').getContext('2d');
const chartDistribusi = new Chart(ctxDistribusi, {
    type: 'bar',
    data: {
        labels: ['0-25%', '26-50%', '51-75%', '76-99%', '100%'],
        datasets: [{
            label: 'Jumlah Santri',
            data: [
                {{ $distribusi['0-25%'] }},
                {{ $distribusi['26-50%'] }},
                {{ $distribusi['51-75%'] }},
                {{ $distribusi['76-99%'] }},
                {{ $distribusi['100%'] }}
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
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        return 'Jumlah Santri: ' + context.parsed.y + ' santri';
                    }
                }
            }
        }
    }
});
</script>
@endsection