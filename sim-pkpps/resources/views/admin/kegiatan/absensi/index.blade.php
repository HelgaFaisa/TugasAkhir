@extends('layouts.app')

@section('content')
<div class="page-header">
    <h2><i class="fas fa-clipboard-check"></i> Absensi Kegiatan</h2>
</div>

@if(session('success'))
    <div class="alert alert-success">
        <i class="fas fa-check-circle"></i> {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger">
        <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
    </div>
@endif

<div class="content-box">
    <div style="margin-bottom: 20px;">
        <form method="GET" class="filter-form-inline">
            <select name="hari" class="form-control">
                <option value="">-- Semua Hari --</option>
                @foreach($hariList as $h)
                    <option value="{{ $h }}" {{ request('hari') == $h ? 'selected' : '' }}>{{ $h }}</option>
                @endforeach
            </select>

            <button type="submit" class="btn btn-primary">
                <i class="fas fa-filter"></i> Filter
            </button>

            @if(request('hari'))
                <a href="{{ route('admin.absensi-kegiatan.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Reset
                </a>
            @endif
        </form>
    </div>

    @if($kegiatans->count() > 0)
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 50px;">No</th>
                    <th style="width: 100px;">Hari</th>
                    <th style="width: 120px;">Waktu</th>
                    <th>Nama Kegiatan</th>
                    <th style="width: 150px;">Kategori</th>
                    <th style="width: 250px; text-align: center;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($kegiatans as $index => $kegiatan)
                <tr>
                    <td>{{ $kegiatans->firstItem() + $index }}</td>
                    <td><span class="badge badge-primary">{{ $kegiatan->hari }}</span></td>
                    <td>{{ date('H:i', strtotime($kegiatan->waktu_mulai)) }} - {{ date('H:i', strtotime($kegiatan->waktu_selesai)) }}</td>
                    <td><strong>{{ $kegiatan->nama_kegiatan }}</strong></td>
                    <td>{{ $kegiatan->kategori->nama_kategori }}</td>
                    <td class="text-center">
                        <a href="{{ route('admin.absensi-kegiatan.input', $kegiatan->kegiatan_id) }}" class="btn btn-sm btn-success" title="Input Absensi">
                            <i class="fas fa-clipboard-check"></i> Input
                        </a>
                        <a href="{{ route('admin.absensi-kegiatan.rekap', $kegiatan->kegiatan_id) }}" class="btn btn-sm btn-primary" title="Rekap Absensi">
                            <i class="fas fa-chart-bar"></i> Rekap
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div style="margin-top: 20px;">
            {{ $kegiatans->links() }}
        </div>
    @else
        <div class="empty-state">
            <i class="fas fa-calendar-times"></i>
            <h3>Belum Ada Kegiatan</h3>
            <p>Silakan tambahkan kegiatan terlebih dahulu.</p>
            <a href="{{ route('admin.kegiatan.create') }}" class="btn btn-success">
                <i class="fas fa-plus"></i> Tambah Kegiatan
            </a>
        </div>
    @endif
</div>
@endsection