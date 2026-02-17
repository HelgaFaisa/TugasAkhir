{{-- resources/views/admin/kegiatan/data/partials/detail-modal.blade.php --}}

<div class="modal-kegiatan-detail">
    {{-- Info Kegiatan --}}
    <div style="background: linear-gradient(135deg, {{ $kegiatan->kategori->warna ?? '#6FBAA5' }}, {{ $kegiatan->kategori->warna ?? '#5AA88D' }}); 
                color: white; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
        <h4 style="margin: 0 0 10px 0; display: flex; align-items: center; gap: 10px;">
            <i class="fas {{ $kegiatan->kategori->icon ?? 'fa-calendar' }}"></i>
            {{ $kegiatan->nama_kegiatan }}
        </h4>
        <div style="display: flex; gap: 20px; flex-wrap: wrap; font-size: 0.9rem; opacity: 0.95;">
            <span><i class="fas fa-clock"></i> {{ date('H:i', strtotime($kegiatan->waktu_mulai)) }} - {{ date('H:i', strtotime($kegiatan->waktu_selesai)) }}</span>
            <span><i class="fas fa-tag"></i> {{ $kegiatan->kategori->nama_kategori }}</span>
            <span><i class="fas fa-calendar-day"></i> {{ $kegiatan->hari }}</span>
            <span>
                <i class="fas fa-layer-group"></i> 
                @if($kegiatan->kelasKegiatan->isEmpty())
                    Kegiatan Umum
                @else
                    {{ $kegiatan->kelasKegiatan->pluck('nama_kelas')->implode(', ') }}
                @endif
            </span>
        </div>
        @if($kegiatan->materi)
            <div style="margin-top: 10px; padding-top: 10px; border-top: 1px solid rgba(255,255,255,0.2);">
                <i class="fas fa-book"></i> Materi: {{ $kegiatan->materi }}
            </div>
        @endif
    </div>

    {{-- Statistik Absensi --}}
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(120px, 1fr)); gap: 15px; margin-bottom: 25px;">
        <div style="background: linear-gradient(135deg, #28a745, #218838); color: white; padding: 15px; border-radius: 8px; text-align: center;">
            <div style="font-size: 2rem; font-weight: 700;">{{ $stats['hadir'] }}</div>
            <div style="font-size: 0.85rem; opacity: 0.9; margin-top: 5px;">
                <i class="fas fa-check-circle"></i> Hadir
            </div>
        </div>
        <div style="background: linear-gradient(135deg, #ffc107, #e0a800); color: white; padding: 15px; border-radius: 8px; text-align: center;">
            <div style="font-size: 2rem; font-weight: 700;">{{ $stats['izin'] }}</div>
            <div style="font-size: 0.85rem; opacity: 0.9; margin-top: 5px;">
                <i class="fas fa-info-circle"></i> Izin
            </div>
        </div>
        <div style="background: linear-gradient(135deg, #17a2b8, #138496); color: white; padding: 15px; border-radius: 8px; text-align: center;">
            <div style="font-size: 2rem; font-weight: 700;">{{ $stats['sakit'] }}</div>
            <div style="font-size: 0.85rem; opacity: 0.9; margin-top: 5px;">
                <i class="fas fa-heartbeat"></i> Sakit
            </div>
        </div>
        <div style="background: linear-gradient(135deg, #dc3545, #c82333); color: white; padding: 15px; border-radius: 8px; text-align: center;">
            <div style="font-size: 2rem; font-weight: 700;">{{ $stats['alpa'] }}</div>
            <div style="font-size: 0.85rem; opacity: 0.9; margin-top: 5px;">
                <i class="fas fa-times-circle"></i> Alpa
            </div>
        </div>
        <div style="background: linear-gradient(135deg, #6c757d, #5a6268); color: white; padding: 15px; border-radius: 8px; text-align: center;">
            <div style="font-size: 2rem; font-weight: 700;">{{ $stats['belum_absen'] }}</div>
            <div style="font-size: 0.85rem; opacity: 0.9; margin-top: 5px;">
                <i class="fas fa-hourglass-half"></i> Belum
            </div>
        </div>
    </div>

    {{-- Progress Bar --}}
    <div style="background: #f8f9fa; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
        <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
            <strong style="color: #2c3e50;">Total Kehadiran</strong>
            <strong style="color: {{ $stats['persen_hadir'] >= 85 ? '#28a745' : ($stats['persen_hadir'] >= 70 ? '#ffc107' : '#dc3545') }};">
                {{ $stats['hadir'] }}/{{ $stats['total'] }} ({{ $stats['persen_hadir'] }}%)
            </strong>
        </div>
        <div style="height: 30px; background: #e9ecef; border-radius: 15px; overflow: hidden;">
            <div style="height: 100%; width: {{ $stats['persen_hadir'] }}%; 
                        background: {{ $stats['persen_hadir'] >= 85 ? 'linear-gradient(90deg, #28a745, #20c997)' : ($stats['persen_hadir'] >= 70 ? 'linear-gradient(90deg, #ffc107, #fd7e14)' : 'linear-gradient(90deg, #dc3545, #c82333)') }};
                        display: flex; align-items: center; justify-content: center; 
                        color: white; font-weight: 700; font-size: 0.85rem; transition: width 0.6s;">
                {{ $stats['persen_hadir'] }}%
            </div>
        </div>
    </div>

    {{-- Pie Chart Canvas --}}
    @if($stats['hadir'] + $stats['izin'] + $stats['sakit'] + $stats['alpa'] > 0)
    <div style="margin-bottom: 25px;">
        <h5 style="color: #2c3e50; margin-bottom: 15px;">
            <i class="fas fa-chart-pie"></i> Visualisasi Kehadiran
        </h5>
        <canvas id="detailPieChart" style="max-height: 250px;"></canvas>
    </div>
    @endif

    {{-- Daftar Santri --}}
    <div>
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
            <h5 style="color: #2c3e50; margin: 0;">
                <i class="fas fa-users"></i> Daftar Absensi ({{ $absensis->count() }} dari {{ $stats['total'] }} santri)
            </h5>
            <input type="text" id="searchSantri" placeholder="Cari santri..." 
                   style="padding: 8px 12px; border: 1px solid #ddd; border-radius: 6px; width: 200px;"
                   onkeyup="filterSantri()">
        </div>

        @if($absensis->count() > 0)
            <div style="max-height: 350px; overflow-y: auto; border: 1px solid #e9ecef; border-radius: 8px;">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead style="position: sticky; top: 0; background: white; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
                        <tr style="border-bottom: 2px solid #e9ecef;">
                            <th style="padding: 12px; text-align: left; font-size: 0.85rem; color: #6c757d;">No</th>
                            <th style="padding: 12px; text-align: left; font-size: 0.85rem; color: #6c757d;">ID</th>
                            <th style="padding: 12px; text-align: left; font-size: 0.85rem; color: #6c757d;">Nama Santri</th>
                            <th style="padding: 12px; text-align: center; font-size: 0.85rem; color: #6c757d;">Status</th>
                            <th style="padding: 12px; text-align: center; font-size: 0.85rem; color: #6c757d;">Waktu</th>
                            <th style="padding: 12px; text-align: center; font-size: 0.85rem; color: #6c757d;">Metode</th>
                        </tr>
                    </thead>
                    <tbody id="santriTableBody">
                        @foreach($absensis as $index => $absensi)
                        <tr class="santri-row" style="border-bottom: 1px solid #f1f3f5;" data-nama="{{ strtolower($absensi->santri->nama_lengkap) }}">
                            <td style="padding: 10px;">{{ $index + 1 }}</td>
                            <td style="padding: 10px; font-weight: 600;">{{ $absensi->santri->id_santri }}</td>
                            <td style="padding: 10px;">{{ $absensi->santri->nama_lengkap }}</td>
                            <td style="padding: 10px; text-align: center;">
                                @if($absensi->status == 'Hadir')
                                    <span style="background: #d4edda; color: #155724; padding: 4px 10px; border-radius: 12px; font-size: 0.8rem; font-weight: 600;">
                                        <i class="fas fa-check-circle"></i> Hadir
                                    </span>
                                @elseif($absensi->status == 'Izin')
                                    <span style="background: #fff3cd; color: #856404; padding: 4px 10px; border-radius: 12px; font-size: 0.8rem; font-weight: 600;">
                                        <i class="fas fa-info-circle"></i> Izin
                                    </span>
                                @elseif($absensi->status == 'Sakit')
                                    <span style="background: #d1ecf1; color: #0c5460; padding: 4px 10px; border-radius: 12px; font-size: 0.8rem; font-weight: 600;">
                                        <i class="fas fa-heartbeat"></i> Sakit
                                    </span>
                                @else
                                    <span style="background: #f8d7da; color: #721c24; padding: 4px 10px; border-radius: 12px; font-size: 0.8rem; font-weight: 600;">
                                        <i class="fas fa-times-circle"></i> Alpa
                                    </span>
                                @endif
                            </td>
                            <td style="padding: 10px; text-align: center; font-size: 0.85rem; color: #6c757d;">
                                {{ $absensi->waktu_absen ? date('H:i', strtotime($absensi->waktu_absen)) : '-' }}
                            </td>
                            <td style="padding: 10px; text-align: center;">
                                @if($absensi->metode_absen == 'RFID')
                                    <span style="background: #6FBAA5; color: white; padding: 3px 8px; border-radius: 8px; font-size: 0.75rem;">
                                        <i class="fas fa-id-card"></i> RFID
                                    </span>
                                @else
                                    <span style="background: #6c757d; color: white; padding: 3px 8px; border-radius: 8px; font-size: 0.75rem;">
                                        <i class="fas fa-hand-pointer"></i> Manual
                                    </span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div style="text-align: center; padding: 40px; background: #f8f9fa; border-radius: 8px;">
                <i class="fas fa-inbox" style="font-size: 3rem; color: #cbd5e0; margin-bottom: 15px;"></i>
                <p style="color: #6c757d; margin: 0;">Belum ada absensi untuk kegiatan ini.</p>
            </div>
        @endif
    </div>

    {{-- Action Buttons --}}
    <div style="margin-top: 25px; padding-top: 20px; border-top: 2px solid #e9ecef; display: flex; gap: 10px; justify-content: flex-end;">
        <a href="{{ route('admin.absensi-kegiatan.input', $kegiatan->kegiatan_id) }}?tanggal={{ $tanggal }}" 
           class="btn btn-primary">
            <i class="fas fa-clipboard-check"></i> Input Absensi
        </a>
        <a href="{{ route('admin.riwayat-kegiatan.index') }}?kegiatan_id={{ $kegiatan->kegiatan_id }}&tanggal={{ $tanggal }}" 
           class="btn btn-info">
            <i class="fas fa-chart-bar"></i> Lihat Rekap Lengkap
        </a>
        <button class="btn btn-secondary" onclick="closeModal()">
            <i class="fas fa-times"></i> Tutup
        </button>
    </div>
</div>

{{-- Chart.js Script --}}
@if($stats['hadir'] + $stats['izin'] + $stats['sakit'] + $stats['alpa'] > 0)
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const ctx = document.getElementById('detailPieChart');
new Chart(ctx, {
    type: 'doughnut',
    data: {
        labels: ['Hadir', 'Izin', 'Sakit', 'Alpa'],
        datasets: [{
            data: [
                {{ $stats['hadir'] }},
                {{ $stats['izin'] }},
                {{ $stats['sakit'] }},
                {{ $stats['alpa'] }}
            ],
            backgroundColor: [
                '#28a745',
                '#ffc107',
                '#17a2b8',
                '#dc3545'
            ],
            borderWidth: 3,
            borderColor: '#fff'
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
                    font: { size: 13 },
                    usePointStyle: true
                }
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        let total = {{ $stats['total'] }};
                        let value = context.parsed;
                        let percentage = ((value / total) * 100).toFixed(1);
                        return context.label + ': ' + value + ' (' + percentage + '%)';
                    }
                }
            }
        }
    }
});

// Filter Santri Function
function filterSantri() {
    const searchValue = document.getElementById('searchSantri').value.toLowerCase();
    const rows = document.querySelectorAll('.santri-row');
    
    rows.forEach(row => {
        const nama = row.getAttribute('data-nama');
        if (nama.includes(searchValue)) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}
</script>
@endif