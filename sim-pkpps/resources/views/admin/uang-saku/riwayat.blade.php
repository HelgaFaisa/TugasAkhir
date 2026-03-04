@extends('layouts.app')

@section('content')
<div class="page-header">
    <h2><i class="fas fa-history"></i> Riwayat Uang Saku - {{ $santri->nama_lengkap }}</h2>
</div>

{{-- Filter Periode --}}
<div class="content-box" style="margin-bottom: 14px;">
    <form method="GET" action="{{ route('admin.uang-saku.riwayat', $santri->id_santri) }}" id="filterPeriode">
        <div style="display: flex; align-items: end; gap: 11px; flex-wrap: wrap;">
            <div class="form-group" style="margin-bottom: 0; flex: 1; min-width: 200px;">
                <label for="tanggal_dari" style="display: block; margin-bottom: 5px; font-weight: 600;">
                    <i class="fas fa-calendar-alt"></i> Dari Tanggal
                </label>
                <input type="date" 
                       name="tanggal_dari" 
                       id="tanggal_dari"
                       class="form-control" 
                       value="{{ $tanggalDari }}"
                       max="{{ date('Y-m-d') }}">
            </div>
            
            <div class="form-group" style="margin-bottom: 0; flex: 1; min-width: 200px;">
                <label for="tanggal_sampai" style="display: block; margin-bottom: 5px; font-weight: 600;">
                    <i class="fas fa-calendar-check"></i> Sampai Tanggal
                </label>
                <input type="date" 
                       name="tanggal_sampai" 
                       id="tanggal_sampai"
                       class="form-control" 
                       value="{{ $tanggalSampai }}"
                       max="{{ date('Y-m-d') }}">
            </div>

            <div style="display: flex; gap: 10px;">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-filter"></i> Terapkan
                </button>
                
                <button type="button" class="btn btn-success" onclick="setBulanIni()">
                    <i class="fas fa-calendar-day"></i> Bulan Ini
                </button>
                
                <a href="{{ route('admin.uang-saku.riwayat', $santri->id_santri) }}" class="btn btn-secondary">
                    <i class="fas fa-redo"></i> Reset
                </a>
            </div>
        </div>
    </form>
</div>

{{-- Info Periode --}}
<div class="info-box" style="margin-bottom: 14px;">
    <p style="margin: 0;">
        <i class="fas fa-info-circle"></i> 
        <strong>Periode:</strong> 
        {{ $periodeDari->format('d F Y') }} - {{ $periodeSampai->format('d F Y') }}
        ({{ $periodeDari->diffInDays($periodeSampai) + 1 }} hari)
    </p>
</div>

<!-- Summary Cards -->
<div class="row-cards">
    <div class="card card-success hover-lift">
        <h3>Total Pemasukan</h3>
        <div class="card-value">Rp {{ number_format($totalPemasukan, 0, ',', '.') }}</div>
        <p style="margin: 10px 0 0 0; font-size: 0.85rem; color: var(--text-light);">
            Periode yang dipilih
        </p>
        <i class="fas fa-arrow-down card-icon"></i>
    </div>
    
    <div class="card card-danger hover-lift">
        <h3>Total Pengeluaran</h3>
        <div class="card-value">Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</div>
        <p style="margin: 10px 0 0 0; font-size: 0.85rem; color: var(--text-light);">
            Periode yang dipilih
        </p>
        <i class="fas fa-arrow-up card-icon"></i>
    </div>
    
    <div class="card card-info hover-lift">
        <h3>Selisih</h3>
        <div class="card-value" style="color: {{ ($totalPemasukan - $totalPengeluaran) >= 0 ? '#6FBA9D' : '#FF8B94' }}">
            Rp {{ number_format($totalPemasukan - $totalPengeluaran, 0, ',', '.') }}
        </div>
        <p style="margin: 10px 0 0 0; font-size: 0.85rem; color: var(--text-light);">
            Pemasukan - Pengeluaran
        </p>
        <i class="fas fa-chart-line card-icon"></i>
    </div>
    
    <div class="card card-primary hover-lift">
        <h3>Saldo Saat Ini</h3>
        <div class="card-value" style="color: {{ $saldoTerakhir >= 0 ? '#6FBA9D' : '#FF8B94' }}">
            Rp {{ number_format($saldoTerakhir, 0, ',', '.') }}
        </div>
        <p style="margin: 10px 0 0 0; font-size: 0.85rem; color: var(--text-light);">
            Total keseluruhan
        </p>
        <i class="fas fa-wallet card-icon"></i>
    </div>
</div>

<!-- Grafik -->
<div class="content-box" style="margin-bottom: 22px;">
    <h3 style="margin-bottom: 14px; color: var(--primary-color);">
        <i class="fas fa-chart-line"></i> Grafik Arus Uang Saku
    </h3>
    <canvas id="chartUangSaku" style="max-height: 400px;"></canvas>
</div>

<!-- Action Buttons -->
<div class="content-box" style="margin-bottom: 14px;">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 11px;">
        <div style="display: flex; gap: 10px; flex-wrap: wrap;">
            <a href="{{ route('admin.uang-saku.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Kembali ke Daftar
            </a>
            <a href="{{ route('admin.santri.show', $santri->id) }}" class="btn btn-primary">
                <i class="fas fa-user"></i> Profil Santri
            </a>
        </div>
        <div style="display: flex; gap: 10px; flex-wrap: wrap;">
            <a href="{{ route('admin.uang-saku.create') }}?id_santri={{ $santri->id_santri }}" class="btn btn-success">
                <i class="fas fa-plus"></i> Tambah Transaksi
            </a>
        </div>
    </div>
</div>

<!-- Tabel Riwayat -->
<div class="content-box">
    <h3 style="margin-bottom: 15px; color: var(--text-color);">
        <i class="fas fa-list"></i> Daftar Transaksi 
        @if($transaksi->total() > 0)
            <span style="color: var(--text-light);">({{ $transaksi->total() }} transaksi)</span>
        @endif
    </h3>
    
    @if($transaksi->count() > 0)
        <div class="table-wrapper">
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 5%;">No</th>
                    <th style="width: 12%;">ID Transaksi</th>
                    <th style="width: 12%;">Tanggal</th>
                    <th style="width: 12%;">Jenis</th>
                    <th style="width: 15%;">Nominal</th>
                    <th style="width: 13%;">Saldo Sebelum</th>
                    <th style="width: 13%;">Saldo Sesudah</th>
                    <th style="width: 12%;">Keterangan</th>
                    <th style="width: 6%;" class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($transaksi as $index => $item)
                    <tr>
                        <td>{{ $transaksi->firstItem() + $index }}</td>
                        <td><strong>{{ $item->id_uang_saku }}</strong></td>
                        <td>{{ $item->tanggal_transaksi->format('d/m/Y') }}</td>
                        <td>
                            @if($item->jenis_transaksi === 'pemasukan')
                                <span class="badge badge-success">
                                    <i class="fas fa-arrow-down"></i> Pemasukan
                                </span>
                            @else
                                <span class="badge badge-danger">
                                    <i class="fas fa-arrow-up"></i> Pengeluaran
                                </span>
                            @endif
                        </td>
                        <td class="nominal-highlight">
                            {{ $item->nominal_format }}
                        </td>
                        <td>
                            Rp {{ number_format($item->saldo_sebelum, 0, ',', '.') }}
                        </td>
                        <td>
                            <strong style="color: {{ $item->saldo_sesudah >= 0 ? '#6FBA9D' : '#FF8B94' }}">
                                {{ $item->saldo_sesudah_format }}
                            </strong>
                        </td>
                        <td>
                            <div class="content-preview">
                                {{ $item->keterangan ?? '-' }}
                            </div>
                        </td>
                        <td class="text-center">
                            <div style="display: flex; gap: 5px; justify-content: center;">
                                <a href="{{ route('admin.uang-saku.show', $item->id) }}" 
                                   class="btn btn-primary btn-sm" 
                                   title="Detail">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.uang-saku.edit', $item->id) }}" 
                                   class="btn btn-warning btn-sm" 
                                   title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        </div>

        <div style="margin-top: 14px;">
            {{ $transaksi->links() }}
        </div>
    @else
        <div class="empty-state">
            <i class="fas fa-calendar-times"></i>
            <h3>Tidak Ada Transaksi</h3>
            <p>Tidak ada transaksi pada periode {{ $periodeDari->format('d F Y') }} - {{ $periodeSampai->format('d F Y') }}</p>
            <a href="{{ route('admin.uang-saku.create') }}?id_santri={{ $santri->id_santri }}" class="btn btn-success">
                <i class="fas fa-plus"></i> Tambah Transaksi
            </a>
        </div>
    @endif
</div>

<!-- Chart.js Library -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

<script>
    // Data untuk grafik dari Laravel
    const dataGrafik = @json($dataGrafik);
    
    // Format data untuk Chart.js (per hari)
    const labels = dataGrafik.map(item => {
        const date = new Date(item.tanggal);
        return date.toLocaleDateString('id-ID', { day: 'numeric', month: 'short' });
    });
    
    const pemasukan = dataGrafik.map(item => parseFloat(item.pemasukan));
    const pengeluaran = dataGrafik.map(item => parseFloat(item.pengeluaran));
    
    // Konfigurasi Chart
    const ctx = document.getElementById('chartUangSaku').getContext('2d');
    const chartUangSaku = new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Pemasukan',
                    data: pemasukan,
                    borderColor: '#6FBA9D',
                    backgroundColor: 'rgba(111, 186, 157, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 5,
                    pointHoverRadius: 7,
                    pointBackgroundColor: '#6FBA9D',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2
                },
                {
                    label: 'Pengeluaran',
                    data: pengeluaran,
                    borderColor: '#FF8B94',
                    backgroundColor: 'rgba(255, 139, 148, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 5,
                    pointHoverRadius: 7,
                    pointBackgroundColor: '#FF8B94',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            interaction: {
                intersect: false,
                mode: 'index'
            },
            plugins: {
                legend: {
                    display: true,
                    position: 'top',
                    labels: {
                        padding: 15,
                        font: {
                            size: 13,
                            family: "'Inter', sans-serif"
                        },
                        usePointStyle: true,
                        pointStyle: 'circle'
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    padding: 12,
                    titleFont: {
                        size: 14,
                        weight: 'bold'
                    },
                    bodyFont: {
                        size: 13
                    },
                    callbacks: {
                        label: function(context) {
                            let label = context.dataset.label || '';
                            if (label) {
                                label += ': ';
                            }
                            label += 'Rp ' + new Intl.NumberFormat('id-ID').format(context.parsed.y);
                            return label;
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return 'Rp ' + new Intl.NumberFormat('id-ID', {
                                notation: 'compact',
                                compactDisplay: 'short'
                            }).format(value);
                        },
                        font: {
                            size: 12
                        }
                    },
                    grid: {
                        color: 'rgba(111, 186, 157, 0.1)',
                        drawBorder: false
                    }
                },
                x: {
                    grid: {
                        display: false,
                        drawBorder: false
                    },
                    ticks: {
                        font: {
                            size: 11
                        },
                        maxRotation: 45,
                        minRotation: 45
                    }
                }
            },
            animation: {
                duration: 1500,
                easing: 'easeInOutQuart'
            }
        }
    });

    // Function untuk set bulan ini
    function setBulanIni() {
        const today = new Date();
        const firstDay = new Date(today.getFullYear(), today.getMonth(), 1);
        const lastDay = new Date(today.getFullYear(), today.getMonth() + 1, 0);
        
        document.getElementById('tanggal_dari').value = firstDay.toISOString().split('T')[0];
        document.getElementById('tanggal_sampai').value = lastDay.toISOString().split('T')[0];
        
        document.getElementById('filterPeriode').submit();
    }

    // Validasi tanggal
    document.getElementById('tanggal_sampai').addEventListener('change', function() {
        const dari = document.getElementById('tanggal_dari').value;
        const sampai = this.value;
        
        if (dari && sampai && sampai < dari) {
            alert('Tanggal sampai tidak boleh lebih kecil dari tanggal dari!');
            this.value = dari;
        }
    });
</script>
@endsection