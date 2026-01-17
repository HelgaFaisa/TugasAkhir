@extends('layouts.app')

@section('title', 'Detail Riwayat Kegiatan')

@section('content')
<div class="page-header">
    <h2><i class="fas fa-clipboard-list"></i> Detail Riwayat Kegiatan</h2>
    <p style="margin: 5px 0 0 0; color: var(--text-light);">
        {{ $santri->nama_lengkap }}
    </p>
</div>

{{-- ✅ INFO KEGIATAN --}}
<div class="content-box" style="margin-bottom: 25px; background: linear-gradient(135deg, #FFFFFF 0%, #F8FBF9 100%); border: 2px solid var(--primary-light);">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px; margin-bottom: 20px;">
        <div>
            <h3 style="margin: 0 0 8px 0; color: var(--primary-dark); font-size: 1.5rem;">
                {{ $kegiatan->nama_kegiatan }}
            </h3>
            <div style="display: flex; align-items: center; gap: 12px; flex-wrap: wrap;">
                <span class="badge badge-info badge-lg">
                    <i class="fas fa-tag"></i> {{ $kegiatan->kategori->nama_kategori }}
                </span>
                <span class="badge badge-primary badge-lg">
                    <i class="fas fa-calendar-day"></i> {{ $kegiatan->hari }}
                </span>
                <span class="badge badge-secondary badge-lg">
                    <i class="fas fa-clock"></i> {{ date('H:i', strtotime($kegiatan->waktu_mulai)) }} - {{ date('H:i', strtotime($kegiatan->waktu_selesai)) }}
                </span>
            </div>
        </div>
        <a href="{{ route('santri.kegiatan.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>
    
    @if($kegiatan->materi)
    <div style="padding: 15px; background: var(--primary-light); border-radius: var(--border-radius-sm); margin-bottom: 15px;">
        <strong><i class="fas fa-book"></i> Materi:</strong> {{ $kegiatan->materi }}
    </div>
    @endif
    
    @if($kegiatan->keterangan)
    <div style="padding: 15px; background: #FFF8E1; border-radius: var(--border-radius-sm); border-left: 4px solid var(--warning-color);">
        <strong><i class="fas fa-info-circle"></i> Keterangan:</strong><br>
        {{ $kegiatan->keterangan }}
    </div>
    @endif
</div>

{{-- ✅ STATISTIK KEHADIRAN UNTUK KEGIATAN INI --}}
<div class="row-cards" style="margin-bottom: 25px;">
    <div class="card card-success">
        <h3><i class="fas fa-check-circle"></i> Total Hadir</h3>
        <div class="card-value">{{ $stats['Hadir'] ?? 0 }}</div>
        <div class="card-icon"><i class="fas fa-check-circle"></i></div>
    </div>
    
    <div class="card card-info">
        <h3><i class="fas fa-info-circle"></i> Izin</h3>
        <div class="card-value">{{ $stats['Izin'] ?? 0 }}</div>
        <div class="card-icon"><i class="fas fa-info-circle"></i></div>
    </div>
    
    <div class="card card-warning">
        <h3><i class="fas fa-heartbeat"></i> Sakit</h3>
        <div class="card-value">{{ $stats['Sakit'] ?? 0 }}</div>
        <div class="card-icon"><i class="fas fa-heartbeat"></i></div>
    </div>
    
    <div class="card card-danger">
        <h3><i class="fas fa-times-circle"></i> Alpa</h3>
        <div class="card-value">{{ $stats['Alpa'] ?? 0 }}</div>
        <div class="card-icon"><i class="fas fa-times-circle"></i></div>
    </div>
</div>

{{-- ✅ GRAFIK PIE: Distribusi Status Kehadiran --}}
<div class="content-box" style="margin-bottom: 25px;">
    <h3 style="margin-bottom: 20px; color: var(--primary-dark); text-align: center;">
        <i class="fas fa-chart-pie"></i> Distribusi Status Kehadiran
    </h3>
    
    <div style="max-width: 400px; margin: 0 auto;">
        <canvas id="chartDistribusiStatus" style="max-height: 350px;"></canvas>
    </div>
    
    <div style="margin-top: 25px; text-align: center; padding-top: 20px; border-top: 1px solid #f0f0f0;">
        <div style="display: inline-block; padding: 15px 30px; background: linear-gradient(135deg, #E8F7F2 0%, #D4F1E3 100%); border-radius: var(--border-radius); box-shadow: var(--shadow-sm);">
            <div style="font-size: 0.9rem; color: var(--text-light); margin-bottom: 5px;">Persentase Kehadiran</div>
            <div style="font-size: 2.5rem; font-weight: 700; color: var(--success-color);">{{ $persentaseHadir }}%</div>
            <div style="font-size: 0.85rem; color: var(--text-light); margin-top: 5px;">dari {{ $totalAbsensi }} total absensi</div>
        </div>
    </div>
</div>

{{-- ✅ RIWAYAT ABSENSI LENGKAP --}}
<div class="content-box">
    <h3 style="margin-bottom: 20px; color: var(--text-color);">
        <i class="fas fa-history"></i> Riwayat Absensi Lengkap
    </h3>
    
    @if($riwayats->count() > 0)
        <div class="table-container">
            <table class="data-table">
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
                    @foreach($riwayats as $index => $absensi)
                    <tr>
                        <td>{{ $riwayats->firstItem() + $index }}</td>
                        <td>{{ $absensi->tanggal_formatted }}</td>
                        <td>{{ \Carbon\Carbon::parse($absensi->tanggal)->locale('id')->dayName }}</td>
                        <td>{{ $absensi->waktu_absen_formatted }}</td>
                        <td>
                            <span class="badge {{ $absensi->status_badge_class }}">
                                <i class="fas fa-{{ $absensi->status == 'Hadir' ? 'check' : ($absensi->status == 'Izin' ? 'info-circle' : ($absensi->status == 'Sakit' ? 'heartbeat' : 'times')) }}-circle"></i>
                                {{ $absensi->status }}
                            </span>
                        </td>
                        <td><span class="badge badge-secondary">{{ $absensi->metode_absen }}</span></td>
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
            <h3>Belum Ada Riwayat</h3>
            <p>Anda belum pernah mengikuti kegiatan ini atau belum ada data absensi yang tercatat.</p>
        </div>
    @endif
</div>

{{-- Chart.js Script --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    
    // ============================================
    // GRAFIK PIE: Distribusi Status Kehadiran
    // ============================================
    const ctxPie = document.getElementById('chartDistribusiStatus');
    if (ctxPie) {
        new Chart(ctxPie.getContext('2d'), {
            type: 'pie',
            data: {
                labels: ['Hadir', 'Izin', 'Sakit', 'Alpa'],
                datasets: [{
                    data: [
                        {{ $stats['Hadir'] ?? 0 }},
                        {{ $stats['Izin'] ?? 0 }},
                        {{ $stats['Sakit'] ?? 0 }},
                        {{ $stats['Alpa'] ?? 0 }}
                    ],
                    backgroundColor: [
                        'rgba(111, 186, 157, 0.9)',
                        'rgba(129, 198, 232, 0.9)',
                        'rgba(255, 213, 107, 0.9)',
                        'rgba(255, 139, 148, 0.9)',
                    ],
                    borderColor: '#fff',
                    borderWidth: 3
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 20,
                            font: {
                                size: 13,
                                weight: '600'
                            }
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.parsed || 0;
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                                return label + ': ' + value + ' kali (' + percentage + '%)';
                            }
                        }
                    }
                }
            }
        });
    }
});
</script>
@endsection