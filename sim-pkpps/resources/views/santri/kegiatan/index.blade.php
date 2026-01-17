@extends('layouts.app')

@section('title', 'Riwayat Kegiatan & Absensi')

@section('content')
<div class="page-header">
    <h2><i class="fas fa-calendar-check"></i> Riwayat Kegiatan & Absensi</h2>
    <p style="margin: 5px 0 0 0; color: var(--text-light);">
        {{ $santri->nama_lengkap }} - Kelas {{ $santri->kelas }}
    </p>
</div>

{{-- ✅ JADWAL KEGIATAN HARI INI --}}
<div class="content-box" style="margin-bottom: 25px; background: linear-gradient(135deg, #E8F7F2 0%, #D4F1E3 100%); border: 2px solid var(--primary-color);">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
        <h3 style="margin: 0; color: var(--primary-dark);">
            <i class="fas fa-clock"></i> Jadwal Kegiatan Hari Ini ({{ ucfirst($hariIni) }})
        </h3>
        <span class="badge badge-primary badge-lg">
            <i class="fas fa-calendar-day"></i> {{ \Carbon\Carbon::now()->locale('id')->isoFormat('D MMMM YYYY') }}
        </span>
    </div>
    
    @if($jadwalHariIni->count() > 0)
        <div style="display: flex; flex-direction: column; gap: 12px;">
            @foreach($jadwalHariIni as $jadwal)
            <div style="background: white; padding: 18px; border-radius: var(--border-radius-sm); border-left: 4px solid {{ isset($absensiHariIni[$jadwal->kegiatan_id]) ? 'var(--success-color)' : 'var(--warning-color)' }}; display: flex; justify-content: space-between; align-items: center; box-shadow: var(--shadow-sm);">
                <div style="flex: 1;">
                    <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 8px;">
                        <span class="badge badge-info">{{ $jadwal->kategori->nama_kategori }}</span>
                        <h4 style="margin: 0; font-size: 1.1rem; color: var(--text-color);">{{ $jadwal->nama_kegiatan }}</h4>
                    </div>
                    <div style="display: flex; align-items: center; gap: 15px; font-size: 0.9rem; color: var(--text-light);">
                        <span><i class="fas fa-clock"></i> {{ date('H:i', strtotime($jadwal->waktu_mulai)) }} - {{ date('H:i', strtotime($jadwal->waktu_selesai)) }}</span>
                        @if($jadwal->materi)
                        <span><i class="fas fa-book"></i> {{ $jadwal->materi }}</span>
                        @endif
                    </div>
                </div>
                <div>
                    @if(isset($absensiHariIni[$jadwal->kegiatan_id]))
                        <span class="badge badge-success badge-lg">
                            <i class="fas fa-check-circle"></i> {{ $absensiHariIni[$jadwal->kegiatan_id] }}
                        </span>
                    @else
                        <span class="badge badge-warning badge-lg">
                            <i class="fas fa-hourglass-half"></i> Belum Absen
                        </span>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    @else
        <div class="empty-state" style="padding: 40px; text-align: center;">
            <i class="fas fa-calendar-times" style="font-size: 3rem; color: #ccc;"></i>
            <p style="margin-top: 15px; color: var(--text-light);">Tidak ada jadwal kegiatan untuk hari ini.</p>
        </div>
    @endif
</div>

{{-- ✅ STATISTIK KEHADIRAN 30 HARI TERAKHIR --}}
<div class="row-cards" style="margin-bottom: 25px;">
    <div class="card card-success">
        <h3><i class="fas fa-check-circle"></i> Total Kehadiran</h3>
        <div class="card-value">{{ $stats30Hari['Hadir'] ?? 0 }}</div>
        <div class="card-icon"><i class="fas fa-check-circle"></i></div>
        <p style="margin-top: 10px; font-size: 0.85rem; color: var(--text-light);">30 hari terakhir</p>
    </div>
    
    <div class="card card-info">
        <h3><i class="fas fa-percentage"></i> Persentase Kehadiran</h3>
        <div class="card-value">{{ $persentaseKehadiran }}%</div>
        <div class="card-icon"><i class="fas fa-chart-line"></i></div>
        <div style="margin-top: 10px;">
            <div class="progress-bar">
                <div class="progress-fill" style="width: {{ $persentaseKehadiran }}%; background: var(--info-color);"></div>
            </div>
        </div>
    </div>
    
    <div class="card card-warning">
        <h3><i class="fas fa-exclamation-triangle"></i> Izin / Sakit / Alpa</h3>
        <div class="card-value">{{ ($stats30Hari['Izin'] ?? 0) + ($stats30Hari['Sakit'] ?? 0) + ($stats30Hari['Alpa'] ?? 0) }}</div>
        <div class="card-icon"><i class="fas fa-exclamation-triangle"></i></div>
        <p style="margin-top: 10px; font-size: 0.85rem; color: var(--text-light);">
            Izin: {{ $stats30Hari['Izin'] ?? 0 }} | Sakit: {{ $stats30Hari['Sakit'] ?? 0 }} | Alpa: {{ $stats30Hari['Alpa'] ?? 0 }}
        </p>
    </div>
    
    <div class="card card-primary">
        <h3><i class="fas fa-list-check"></i> Total Kegiatan</h3>
        <div class="card-value">{{ $totalKegiatan30Hari }}</div>
        <div class="card-icon"><i class="fas fa-list-check"></i></div>
        <p style="margin-top: 10px; font-size: 0.85rem; color: var(--text-light);">30 hari terakhir</p>
    </div>
</div>

{{-- ✅ GRAFIK KEHADIRAN MINGGUAN & PER KATEGORI (SIDE BY SIDE) --}}
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(450px, 1fr)); gap: 25px; margin-bottom: 25px;">
    
    {{-- GRAFIK 1: Kehadiran per Minggu (LINE CHART) --}}
    <div class="content-box">
        <h3 style="margin-bottom: 20px; color: var(--primary-dark);">
            <i class="fas fa-chart-line"></i> Tren Kehadiran (4 Minggu Terakhir)
        </h3>
        <canvas id="chartTrenKehadiran" style="max-height: 300px;"></canvas>
    </div>
    
    {{-- GRAFIK 2: Kehadiran per Kategori (BAR CHART) --}}
    <div class="content-box">
        <h3 style="margin-bottom: 20px; color: var(--primary-dark);">
            <i class="fas fa-chart-bar"></i> Kehadiran per Kategori Kegiatan
        </h3>
        <canvas id="chartKategori" style="max-height: 300px;"></canvas>
    </div>
</div>

{{-- ✅ FILTER & RIWAYAT ABSENSI --}}
<div class="content-box">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 15px;">
        <h3 style="margin: 0; color: var(--text-color);">
            <i class="fas fa-history"></i> Riwayat Absensi Lengkap
        </h3>
        
        {{-- Form Filter --}}
        <form method="GET" action="{{ route('santri.kegiatan.index') }}" style="display: flex; gap: 10px; flex-wrap: wrap;">
            <input type="month" name="bulan" class="form-control" value="{{ request('bulan') }}" 
                   style="max-width: 200px;" placeholder="Pilih Bulan">
            <select name="status" class="form-control" style="max-width: 150px;">
                <option value="">Semua Status</option>
                <option value="Hadir" {{ request('status') == 'Hadir' ? 'selected' : '' }}>Hadir</option>
                <option value="Izin" {{ request('status') == 'Izin' ? 'selected' : '' }}>Izin</option>
                <option value="Sakit" {{ request('status') == 'Sakit' ? 'selected' : '' }}>Sakit</option>
                <option value="Alpa" {{ request('status') == 'Alpa' ? 'selected' : '' }}>Alpa</option>
            </select>
            <button type="submit" class="btn btn-primary btn-sm">
                <i class="fas fa-filter"></i> Filter
            </button>
            <a href="{{ route('santri.kegiatan.index') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-redo"></i> Reset
            </a>
        </form>
    </div>
    
    @if($riwayats->count() > 0)
        <div class="table-container">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Tanggal</th>
                        <th>Kegiatan</th>
                        <th>Kategori</th>
                        <th>Waktu Absen</th>
                        <th>Status</th>
                        <th>Metode</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($riwayats as $index => $absensi)
                    <tr>
                        <td>{{ $riwayats->firstItem() + $index }}</td>
                        <td>{{ $absensi->tanggal_formatted }}</td>
                        <td><strong>{{ $absensi->kegiatan->nama_kegiatan }}</strong></td>
                        <td><span class="badge badge-info">{{ $absensi->kegiatan->kategori->nama_kategori }}</span></td>
                        <td>{{ $absensi->waktu_absen_formatted }}</td>
                        <td>
                            <span class="badge {{ $absensi->status_badge_class }}">
                                <i class="fas fa-{{ $absensi->status == 'Hadir' ? 'check' : ($absensi->status == 'Izin' ? 'info-circle' : ($absensi->status == 'Sakit' ? 'heartbeat' : 'times')) }}-circle"></i>
                                {{ $absensi->status }}
                            </span>
                        </td>
                        <td><span class="badge badge-secondary">{{ $absensi->metode_absen }}</span></td>
                        <td class="text-center">
                            <a href="{{ route('santri.kegiatan.show', $absensi->kegiatan_id) }}" class="btn btn-sm btn-primary" title="Lihat Detail Kegiatan">
                                <i class="fas fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        {{-- Pagination --}}
        <div style="margin-top: 20px;">
            {{ $riwayats->links() }}
        </div>
    @else
        <div class="empty-state">
            <i class="fas fa-inbox"></i>
            <h3>Belum Ada Riwayat Absensi</h3>
            <p>Riwayat absensi Anda akan muncul di sini setelah mengikuti kegiatan.</p>
        </div>
    @endif
</div>

{{-- Chart.js Script --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    
    // ============================================
    // GRAFIK 1: Tren Kehadiran per Minggu (LINE CHART)
    // ============================================
    const ctxTren = document.getElementById('chartTrenKehadiran');
    if (ctxTren) {
        new Chart(ctxTren.getContext('2d'), {
            type: 'line',
            data: {
                labels: [
                    @foreach($dataGrafikMingguan as $data)
                    '{{ $data["minggu"] }}',
                    @endforeach
                ],
                datasets: [
                    {
                        label: 'Hadir',
                        data: [
                            @foreach($dataGrafikMingguan as $data)
                            {{ $data['hadir'] }},
                            @endforeach
                        ],
                        borderColor: 'rgba(111, 186, 157, 1)',
                        backgroundColor: 'rgba(111, 186, 157, 0.2)',
                        borderWidth: 3,
                        pointRadius: 5,
                        pointBackgroundColor: 'rgba(111, 186, 157, 1)',
                        tension: 0.4,
                        fill: true
                    },
                    {
                        label: 'Total Kegiatan',
                        data: [
                            @foreach($dataGrafikMingguan as $data)
                            {{ $data['total'] }},
                            @endforeach
                        ],
                        borderColor: 'rgba(129, 198, 232, 1)',
                        backgroundColor: 'rgba(129, 198, 232, 0.1)',
                        borderWidth: 2,
                        pointRadius: 4,
                        pointBackgroundColor: 'rgba(129, 198, 232, 1)',
                        tension: 0.4,
                        fill: true,
                        borderDash: [5, 5]
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                        labels: {
                            font: { size: 12, weight: '600' },
                            padding: 15
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.dataset.label + ': ' + context.parsed.y;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1,
                            font: { size: 12, weight: '600' }
                        },
                        grid: { color: 'rgba(0, 0, 0, 0.05)' }
                    },
                    x: {
                        ticks: { font: { size: 12, weight: '600' } },
                        grid: { display: false }
                    }
                }
            }
        });
    }
    
    // ============================================
    // GRAFIK 2: Kehadiran per Kategori (BAR CHART)
    // ============================================
    const ctxKategori = document.getElementById('chartKategori');
    if (ctxKategori) {
        new Chart(ctxKategori.getContext('2d'), {
            type: 'bar',
            data: {
                labels: [
                    @foreach($statsByKategori as $stat)
                    '{{ $stat->nama_kategori }}',
                    @endforeach
                ],
                datasets: [{
                    label: 'Kehadiran',
                    data: [
                        @foreach($statsByKategori as $stat)
                        {{ $stat->hadir }},
                        @endforeach
                    ],
                    backgroundColor: [
                        'rgba(111, 186, 157, 0.8)',
                        'rgba(129, 198, 232, 0.8)',
                        'rgba(255, 213, 107, 0.8)',
                        'rgba(255, 139, 148, 0.8)',
                        'rgba(179, 157, 219, 0.8)',
                    ],
                    borderColor: [
                        'rgba(111, 186, 157, 1)',
                        'rgba(129, 198, 232, 1)',
                        'rgba(255, 213, 107, 1)',
                        'rgba(255, 139, 148, 1)',
                        'rgba(179, 157, 219, 1)',
                    ],
                    borderWidth: 2,
                    borderRadius: 8,
                    borderSkipped: false
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return 'Hadir: ' + context.parsed.y + ' kali';
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1,
                            font: { size: 12, weight: '600' }
                        },
                        grid: { color: 'rgba(0, 0, 0, 0.05)' }
                    },
                    x: {
                        ticks: { 
                            font: { size: 11 },
                            maxRotation: 45,
                            minRotation: 45
                        },
                        grid: { display: false }
                    }
                }
            }
        });
    }
});
</script>
@endsection