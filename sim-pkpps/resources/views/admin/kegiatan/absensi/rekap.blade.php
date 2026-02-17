@extends('layouts.app')

@section('content')
<div class="page-header">
    <h2><i class="fas fa-chart-bar"></i> Rekap Absensi: {{ $kegiatan->nama_kegiatan }}</h2>
</div>

<div class="row-cards">
    <div class="card card-success">
        <h3>Hadir</h3>
        <div class="card-value">{{ $stats['Hadir'] ?? 0 }}</div>
        <i class="fas fa-check-circle card-icon"></i>
    </div>
    <div class="card card-warning">
        <h3>Izin</h3>
        <div class="card-value">{{ $stats['Izin'] ?? 0 }}</div>
        <i class="fas fa-info-circle card-icon"></i>
    </div>
    <div class="card card-info">
        <h3>Sakit</h3>
        <div class="card-value">{{ $stats['Sakit'] ?? 0 }}</div>
        <i class="fas fa-heartbeat card-icon"></i>
    </div>
    <div class="card card-danger">
        <h3>Alpa</h3>
        <div class="card-value">{{ $stats['Alpa'] ?? 0 }}</div>
        <i class="fas fa-times-circle card-icon"></i>
    </div>
</div>

<div class="content-box">
    <div style="margin-bottom: 20px;">
        <form method="GET" class="filter-form-inline">
            <input type="date" name="tanggal" class="form-control" value="{{ request('tanggal') }}">
            <input type="month" name="bulan" class="form-control" value="{{ request('bulan') }}" placeholder="Pilih Bulan">
            
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-filter"></i> Filter
            </button>

            @if(request()->hasAny(['tanggal', 'bulan']))
                <a href="{{ route('admin.absensi-kegiatan.rekap', $kegiatan->kegiatan_id) }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Reset
                </a>
            @endif

            <a href="{{ route('admin.absensi-kegiatan.index') }}" class="btn btn-secondary" style="margin-left: auto;">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </form>
    </div>

    @if($absensis->count() > 0)
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 50px;">No</th>
                    <th style="width: 100px;">Tanggal</th>
                    <th style="width: 100px;">ID Santri</th>
                    <th>Nama Santri</th>
                    <th style="width: 80px;">Kelas</th>
                    <th style="width: 120px; text-align: center;">Status</th>
                    <th style="width: 100px;">Metode</th>
                    <th style="width: 100px;">Waktu</th>
                </tr>
            </thead>
            <tbody>
                @foreach($absensis as $index => $absensi)
                <tr>
                    <td>{{ $absensis->firstItem() + $index }}</td>
                    <td>{{ $absensi->tanggal->format('d/m/Y') }}</td>
                    <td><strong>{{ $absensi->id_santri }}</strong></td>
                    <td>{{ $absensi->santri->nama_lengkap }}</td>
                    <td>
                        @php
                            $kelasName = $absensi->santri->kelas_name ?? $absensi->santri->kelas ?? '-';
                        @endphp
                        <span class="badge badge-secondary">{{ $kelasName }}</span>
                    </td>
                    <td class="text-center">{!! $absensi->status_badge !!}</td>
                    <td>
                        @if($absensi->metode_absen == 'RFID')
                            <span class="badge badge-primary"><i class="fas fa-id-card"></i> RFID</span>
                        @else
                            <span class="badge badge-secondary"><i class="fas fa-hand-pointer"></i> Manual</span>
                        @endif
                    </td>
                    <td>{{ $absensi->waktu_absen ? date('H:i', strtotime($absensi->waktu_absen)) : '-' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div style="margin-top: 20px;">
            {{ $absensis->links() }}
        </div>
    @else
        <div class="empty-state">
            <i class="fas fa-clipboard"></i>
            <h3>Belum Ada Data Absensi</h3>
            <p>Silakan input absensi terlebih dahulu.</p>
            <a href="{{ route('admin.absensi-kegiatan.input', $kegiatan->kegiatan_id) }}" class="btn btn-success">
                <i class="fas fa-clipboard-check"></i> Input Absensi
            </a>
        </div>
    @endif
</div>
@endsection