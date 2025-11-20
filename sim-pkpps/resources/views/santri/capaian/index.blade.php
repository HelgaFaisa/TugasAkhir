@extends('layouts.app')

@section('title', 'Capaian Al-Qur\'an & Hadist')

@section('content')
<div class="page-header">
    <h2><i class="fas fa-book-reader"></i> Capaian Al-Qur'an & Hadist</h2>
    <p style="margin: 5px 0 0 0; color: var(--text-light);">
        Pantau progres hafalan dan pembelajaran Anda
    </p>
</div>

{{-- Alert Success --}}
@if(session('success'))
<div class="alert alert-success">
    <i class="fas fa-check-circle"></i> {{ session('success') }}
</div>
@endif

{{-- Cards Statistik --}}
<div class="row-cards">
    {{-- Total Capaian --}}
    <div class="card card-primary">
        <h3><i class="fas fa-list-check"></i> Total Materi</h3>
        <div class="card-value">{{ $totalCapaian }}</div>
        <div class="card-icon"><i class="fas fa-list-check"></i></div>
    </div>
    
    {{-- Rata-rata Progress --}}
    <div class="card card-info">
        <h3><i class="fas fa-chart-line"></i> Rata-rata Progress</h3>
        <div class="card-value">{{ number_format($rataRataPersentase, 1) }}%</div>
        <div class="card-icon"><i class="fas fa-chart-line"></i></div>
        <div style="margin-top: 10px;">
            <div class="progress-bar">
                <div class="progress-fill" style="width: {{ $rataRataPersentase }}%; background: var(--info-color);"></div>
            </div>
        </div>
    </div>
    
    {{-- Materi Selesai --}}
    <div class="card card-success">
        <h3><i class="fas fa-check-circle"></i> Materi Selesai</h3>
        <div class="card-value">{{ $materiSelesai }}</div>
        <div class="card-icon"><i class="fas fa-check-circle"></i></div>
        @if($totalCapaian > 0)
        <p style="margin-top: 10px; color: var(--text-light); font-size: 0.9rem;">
            {{ number_format(($materiSelesai / $totalCapaian) * 100, 1) }}% dari total materi
        </p>
        @endif
    </div>
    
    {{-- Kelas --}}
    <div class="card card-secondary">
        <h3><i class="fas fa-graduation-cap"></i> Kelas</h3>
        <div class="card-value-small">{{ $santri->kelas }}</div>
        <div class="card-icon"><i class="fas fa-graduation-cap"></i></div>
        <p style="margin-top: 10px; color: var(--text-light); font-size: 0.9rem;">
            NIS: {{ $santri->nis ?? '-' }}
        </p>
    </div>
</div>

{{-- Statistik per Kategori --}}
<div class="content-box" style="margin-bottom: 25px;">
    <h3 style="margin-bottom: 20px; color: var(--primary-dark);">
        <i class="fas fa-chart-pie"></i> Statistik per Kategori
    </h3>
    
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 20px;">
        @foreach($statistikKategori as $kategori => $data)
        <div style="background: linear-gradient(135deg, #FFFFFF 0%, #FEFFFE 100%); border-radius: var(--border-radius-sm); padding: 20px; box-shadow: var(--shadow-sm); border-left: 4px solid 
            @if($kategori == 'Al-Qur\'an') var(--success-color)
            @elseif($kategori == 'Hadist') var(--info-color)
            @else var(--warning-color)
            @endif
        ;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                <h4 style="margin: 0; font-size: 1rem; color: var(--text-color);">{{ $kategori }}</h4>
                <span style="background: 
                    @if($kategori == 'Al-Qur\'an') rgba(111, 186, 157, 0.1)
                    @elseif($kategori == 'Hadist') rgba(129, 198, 232, 0.1)
                    @else rgba(255, 213, 107, 0.1)
                    @endif
                ; padding: 5px 10px; border-radius: 20px; font-size: 0.85rem; font-weight: 600; color: 
                    @if($kategori == 'Al-Qur\'an') var(--success-color)
                    @elseif($kategori == 'Hadist') var(--info-color)
                    @else var(--warning-color)
                    @endif
                ;">
                    {{ $data['count'] }} Materi
                </span>
            </div>
            
            <div style="margin-bottom: 12px;">
                <div style="display: flex; justify-content: space-between; margin-bottom: 5px;">
                    <span style="font-size: 0.85rem; color: var(--text-light);">Progress</span>
                    <span style="font-size: 0.85rem; font-weight: 600; color: var(--text-color);">{{ number_format($data['avg'], 1) }}%</span>
                </div>
                <div class="progress-bar">
                    <div class="progress-fill" style="width: {{ $data['avg'] }}%; background: 
                        @if($kategori == 'Al-Qur\'an') var(--success-color)
                        @elseif($kategori == 'Hadist') var(--info-color)
                        @else var(--warning-color)
                        @endif
                    ;"></div>
                </div>
            </div>
            
            <div style="display: flex; justify-content: space-between; font-size: 0.85rem; color: var(--text-light);">
                <span><i class="fas fa-check-circle" style="color: var(--success-color); margin-right: 5px;"></i> {{ $data['selesai'] }} Selesai</span>
                <span><i class="fas fa-hourglass-half" style="color: var(--warning-color); margin-right: 5px;"></i> {{ $data['count'] - $data['selesai'] }} Berlangsung</span>
            </div>
        </div>
        @endforeach
    </div>
</div>

{{-- Grafik --}}
<div class="content-box" style="margin-bottom: 25px;">
    <h3 style="margin-bottom: 20px; color: var(--primary-dark);">
        <i class="fas fa-chart-bar"></i> Visualisasi Progress
    </h3>
    
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); gap: 25px;">
        {{-- Chart Kategori --}}
        <div style="background: white; padding: 20px; border-radius: var(--border-radius-sm); box-shadow: var(--shadow-sm);">
            <h4 style="margin: 0 0 15px 0; font-size: 0.95rem; color: var(--text-color); text-align: center;">Progress per Kategori</h4>
            <canvas id="chartKategori" style="max-height: 300px;"></canvas>
        </div>
        
        {{-- Chart Distribusi --}}
        <div style="background: white; padding: 20px; border-radius: var(--border-radius-sm); box-shadow: var(--shadow-sm);">
            <h4 style="margin: 0 0 15px 0; font-size: 0.95rem; color: var(--text-color); text-align: center;">Distribusi Persentase</h4>
            <canvas id="chartDistribusi" style="max-height: 300px;"></canvas>
        </div>
    </div>
</div>

{{-- Filter & Daftar Capaian --}}
<div class="content-box">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 15px;">
        <h3 style="margin: 0; color: var(--primary-dark);">
            <i class="fas fa-list"></i> Daftar Capaian
        </h3>
        
        {{-- Filter Semester --}}
        <form method="GET" style="display: flex; gap: 10px; align-items: center;">
            <select name="id_semester" class="form-control" style="width: auto; min-width: 200px;" onchange="this.form.submit()">
                <option value="">-- Semua Semester --</option>
                @foreach($semesters as $semester)
                <option value="{{ $semester->id_semester }}" {{ $selectedSemester == $semester->id_semester ? 'selected' : '' }}>
                    {{ $semester->nama_semester }}
                    @if($semesterAktif && $semester->id_semester == $semesterAktif->id_semester) 
                        <span style="color: var(--success-color);">● Aktif</span> 
                    @endif
                </option>
                @endforeach
            </select>
        </form>
    </div>
    
    @if($capaians->count() > 0)
    <div class="table-container">
        <table class="data-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Materi</th>
                    <th>Kategori</th>
                    <th>Halaman Selesai</th>
                    <th>Progress</th>
                    <th>Tanggal Input</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($capaians as $index => $capaian)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>
                        <strong>{{ $capaian->materi->nama_kitab }}</strong>
                    </td>
                    <td>
                        <span class="badge 
                            @if($capaian->materi->kategori == 'Al-Qur\'an') badge-success
                            @elseif($capaian->materi->kategori == 'Hadist') badge-info
                            @else badge-warning
                            @endif
                        ">
                            {{ $capaian->materi->kategori }}
                        </span>
                    </td>
                    <td>
                        <span style="font-size: 0.9rem; color: var(--text-light);">
                            {{ count($capaian->pages_array) }} dari {{ $capaian->materi->total_halaman }} halaman
                        </span>
                    </td>
                    <td>
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <div class="progress-bar" style="flex: 1; height: 8px;">
                                <div class="progress-fill" style="width: {{ $capaian->persentase }}%; background: 
                                    @if($capaian->persentase >= 100) var(--success-color)
                                    @elseif($capaian->persentase >= 75) var(--info-color)
                                    @elseif($capaian->persentase >= 50) var(--warning-color)
                                    @else var(--danger-color)
                                    @endif
                                ;"></div>
                            </div>
                            <span style="font-weight: 600; min-width: 50px; text-align: right; color: var(--text-color);">
                                {{ number_format($capaian->persentase, 1) }}%
                            </span>
                        </div>
                    </td>
                    <td>{{ \Carbon\Carbon::parse($capaian->tanggal_input)->format('d M Y') }}</td>
                    <td class="text-center">
                        <a href="{{ route('santri.capaian.show', $capaian->id) }}" class="btn btn-sm btn-primary">
                            <i class="fas fa-eye"></i> Detail
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @else
    <div class="empty-state">
        <i class="fas fa-book-open"></i>
        <h3>Belum Ada Data Capaian</h3>
        <p>
            @if($selectedSemester)
                Tidak ada data capaian untuk semester yang dipilih.
            @else
                Belum ada data capaian yang tercatat untuk Anda.
            @endif
        </p>
    </div>
    @endif
</div>

{{-- Chart.js Script --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Chart Kategori
    const ctxKategori = document.getElementById('chartKategori').getContext('2d');
    new Chart(ctxKategori, {
        type: 'bar',
        data: {
            labels: ['Al-Qur\'an', 'Hadist', 'Materi Tambahan'],
            datasets: [{
                label: 'Progress (%)',
                data: [
                    {{ $statistikKategori['Al-Qur\'an']['avg'] }},
                    {{ $statistikKategori['Hadist']['avg'] }},
                    {{ $statistikKategori['Materi Tambahan']['avg'] }}
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
                    display: false
                }
            },
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
            }
        }
    });
    
    // Chart Distribusi
    const ctxDistribusi = document.getElementById('chartDistribusi').getContext('2d');
    new Chart(ctxDistribusi, {
        type: 'doughnut',
        data: {
            labels: ['0-25%', '26-50%', '51-75%', '76-99%', '100%'],
            datasets: [{
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
                borderColor: '#fff',
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
});
</script>
@endsection