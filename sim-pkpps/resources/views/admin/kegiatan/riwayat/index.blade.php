@extends('layouts.app')

@section('content')
<div class="page-header">
    <h2><i class="fas fa-history"></i> Riwayat Kegiatan & Absensi</h2>
</div>

@if(session('success'))
    <div class="alert alert-success">
        <i class="fas fa-check-circle"></i> {{ session('success') }}
    </div>
@endif

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

<!-- Grafik Kehadiran -->
@if(($stats['Hadir'] ?? 0) + ($stats['Izin'] ?? 0) + ($stats['Sakit'] ?? 0) + ($stats['Alpa'] ?? 0) > 0)
<div class="content-box" style="margin-bottom: 20px;">
    <h3 style="margin: 0 0 20px 0; color: var(--primary-color);">
        <i class="fas fa-chart-pie"></i> Grafik Statistik Kehadiran
    </h3>
    <canvas id="chartKehadiran" style="max-height: 300px;"></canvas>
</div>
@endif

<!-- Filter -->
<div class="content-box">
    <form method="GET" style="margin-bottom: 20px;">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px;">
            <div class="form-group" style="margin: 0;">
                <label for="id_santri" style="font-size: 0.85rem; margin-bottom: 5px;">Santri</label>
                <select name="id_santri" id="id_santri" class="form-control">
                    <option value="">-- Semua Santri --</option>
                    @foreach($santris as $s)
                        <option value="{{ $s->id_santri }}" {{ request('id_santri') == $s->id_santri ? 'selected' : '' }}>
                            {{ $s->nama_lengkap }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group" style="margin: 0;">
                <label for="kategori_id" style="font-size: 0.85rem; margin-bottom: 5px;">Kategori</label>
                <select name="kategori_id" id="kategori_id" class="form-control">
                    <option value="">-- Semua Kategori --</option>
                    @foreach($kategoris as $k)
                        <option value="{{ $k->kategori_id }}" {{ request('kategori_id') == $k->kategori_id ? 'selected' : '' }}>
                            {{ $k->nama_kategori }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group" style="margin: 0;">
                <label for="kegiatan_id" style="font-size: 0.85rem; margin-bottom: 5px;">Kegiatan</label>
                <select name="kegiatan_id" id="kegiatan_id" class="form-control">
                    <option value="">-- Semua Kegiatan --</option>
                    @foreach($kegiatans as $kg)
                        <option value="{{ $kg->kegiatan_id }}" {{ request('kegiatan_id') == $kg->kegiatan_id ? 'selected' : '' }}>
                            {{ $kg->nama_kegiatan }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group" style="margin: 0;">
                <label for="status" style="font-size: 0.85rem; margin-bottom: 5px;">Status</label>
                <select name="status" id="status" class="form-control">
                    <option value="">-- Semua Status --</option>
                    <option value="Hadir" {{ request('status') == 'Hadir' ? 'selected' : '' }}>Hadir</option>
                    <option value="Izin" {{ request('status') == 'Izin' ? 'selected' : '' }}>Izin</option>
                    <option value="Sakit" {{ request('status') == 'Sakit' ? 'selected' : '' }}>Sakit</option>
                    <option value="Alpa" {{ request('status') == 'Alpa' ? 'selected' : '' }}>Alpa</option>
                </select>
            </div>

            <div class="form-group" style="margin: 0;">
                <label for="tanggal_dari" style="font-size: 0.85rem; margin-bottom: 5px;">Tanggal Dari</label>
                <input type="date" name="tanggal_dari" id="tanggal_dari" class="form-control" value="{{ request('tanggal_dari') }}">
            </div>

            <div class="form-group" style="margin: 0;">
                <label for="tanggal_sampai" style="font-size: 0.85rem; margin-bottom: 5px;">Tanggal Sampai</label>
                <input type="date" name="tanggal_sampai" id="tanggal_sampai" class="form-control" value="{{ request('tanggal_sampai') }}">
            </div>

            <div class="form-group" style="margin: 0;">
                <label for="bulan" style="font-size: 0.85rem; margin-bottom: 5px;">Atau Pilih Bulan</label>
                <input type="month" name="bulan" id="bulan" class="form-control" value="{{ request('bulan') }}">
            </div>

            <div style="display: flex; align-items: flex-end; gap: 10px;">
                <button type="submit" class="btn btn-primary" style="flex: 1;">
                    <i class="fas fa-filter"></i> Filter
                </button>
                @if(request()->hasAny(['id_santri', 'kategori_id', 'kegiatan_id', 'status', 'tanggal_dari', 'tanggal_sampai', 'bulan']))
                    <a href="{{ route('admin.riwayat-kegiatan.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i>
                    </a>
                @endif
            </div>
        </div>
    </form>

    @if($riwayats->count() > 0)
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 50px;">No</th>
                    <th style="width: 100px;">Tanggal</th>
                    <th style="width: 100px;">ID Santri</th>
                    <th>Nama Santri</th>
                    <th>Kegiatan</th>
                    <th style="width: 130px;">Kategori</th>
                    <th style="width: 120px; text-align: center;">Status</th>
                    <th style="width: 90px;">Metode</th>
                    <th style="width: 180px; text-align: center;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($riwayats as $index => $riwayat)
                <tr>
                    <td>{{ $riwayats->firstItem() + $index }}</td>
                    <td>{{ $riwayat->tanggal->format('d/m/Y') }}</td>
                    <td><strong>{{ $riwayat->id_santri }}</strong></td>
                    <td>
                        <a href="{{ route('admin.riwayat-kegiatan.detail-santri', $riwayat->id_santri) }}" 
                           style="color: var(--primary-color); text-decoration: none; font-weight: 500;">
                            {{ $riwayat->santri->nama_lengkap }}
                        </a>
                    </td>
                    <td>{{ $riwayat->kegiatan->nama_kegiatan }}</td>
                    <td>{{ $riwayat->kegiatan->kategori->nama_kategori }}</td>
                    <td class="text-center">{!! $riwayat->status_badge !!}</td>
                    <td>
                        @if($riwayat->metode_absen == 'RFID')
                            <span class="badge badge-primary" style="font-size: 0.75rem;"><i class="fas fa-id-card"></i> RFID</span>
                        @else
                            <span class="badge badge-secondary" style="font-size: 0.75rem;"><i class="fas fa-hand-pointer"></i> Manual</span>
                        @endif
                    </td>
                    <td class="text-center">
                        <a href="{{ route('admin.riwayat-kegiatan.show', $riwayat->id) }}" class="btn btn-sm btn-primary" title="Detail">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="{{ route('admin.riwayat-kegiatan.edit', $riwayat->id) }}" class="btn btn-sm btn-warning" title="Edit">
                            <i class="fas fa-edit"></i>
                        </a>
                        <form action="{{ route('admin.riwayat-kegiatan.destroy', $riwayat->id) }}" method="POST" style="display: inline-block;" onsubmit="return confirm('Yakin ingin menghapus riwayat ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger" title="Hapus">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </td>
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
            <h3>Tidak Ada Riwayat</h3>
            <p>Belum ada data riwayat kegiatan dan absensi.</p>
        </div>
    @endif
</div>

@if(($stats['Hadir'] ?? 0) + ($stats['Izin'] ?? 0) + ($stats['Sakit'] ?? 0) + ($stats['Alpa'] ?? 0) > 0)
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const ctx = document.getElementById('chartKehadiran');
new Chart(ctx, {
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
                '#6FBA9D',
                '#FFD56B',
                '#81C6E8',
                '#FF8B94'
            ],
            borderWidth: 2,
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
                    font: { size: 13 }
                }
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        let total = context.dataset.data.reduce((a, b) => a + b, 0);
                        let value = context.parsed;
                        let percentage = ((value / total) * 100).toFixed(1);
                        return context.label + ': ' + value + ' (' + percentage + '%)';
                    }
                }
            }
        }
    }
});
</script>
@endif
@endsection