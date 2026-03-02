@extends('layouts.app')

@section('title', 'Riwayat Kesehatan Santri')

@section('content')
<div class="page-header">
    <h2><i class="fas fa-history"></i> Riwayat Kesehatan Santri</h2>
</div>

<!-- Informasi Santri Card -->
<div class="content-box" style="background: linear-gradient(135deg, var(--primary-color), var(--primary-dark)); color: white; margin-bottom: 22px;">
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 20px; align-items: center;">
        <div>
            <h3 style="margin: 0; font-size: 1.5em;">
                <i class="fas fa-user-circle"></i> {{ $santri->nama_lengkap }}
            </h3>
            <p style="margin: 5px 0 0 0; opacity: 0.9;">{{ $santri->id_santri }}</p>
        </div>
        
        <div style="opacity: 0.95;">
            <strong>Kelas:</strong> {{ $santri->kelas }}<br>
            <strong>Jenis Kelamin:</strong> {{ $santri->jenis_kelamin }}
        </div>
        
        <div style="opacity: 0.95;">
            <strong>Status:</strong> {{ $santri->status_badge }}<br>
            <strong>NIS:</strong> {{ $santri->nis ?: '-' }}
        </div>
        
        <div style="opacity: 0.95;">
            <strong>Total Riwayat:</strong> {{ $riwayatKesehatan->total() }} kali<br>
            <strong>Orang Tua:</strong> {{ $santri->nama_orang_tua ?: '-' }}
        </div>
    </div>
</div>

<!-- Statistik Kesehatan -->
<div class="row-cards" style="margin-bottom: 22px;">
    <div class="card card-danger">
        <h3><i class="fas fa-bed"></i> Sedang Dirawat</h3>
        <p class="card-value">{{ $riwayatKesehatan->where('status', 'dirawat')->count() }}</p>
        <div class="card-icon"><i class="fas fa-bed"></i></div>
    </div>
    
    <div class="card card-success">
        <h3><i class="fas fa-check-circle"></i> Sembuh</h3>
        <p class="card-value">{{ $riwayatKesehatan->where('status', 'sembuh')->count() }}</p>
        <div class="card-icon"><i class="fas fa-check-circle"></i></div>
    </div>
    
    <div class="card card-warning">
        <h3><i class="fas fa-home"></i> Izin Pulang</h3>
        <p class="card-value">{{ $riwayatKesehatan->where('status', 'izin')->count() }}</p>
        <div class="card-icon"><i class="fas fa-home"></i></div>
    </div>
    
    <div class="card card-info">
        <h3><i class="fas fa-calendar-days"></i> Rata-rata Dirawat</h3>
        <p class="card-value-small">
            @if($riwayatKesehatan->count() > 0)
                {{ round($riwayatKesehatan->avg(function($item) { return $item->lama_dirawat; }), 1) }} hari
            @else
                0 hari
            @endif
        </p>
        <div class="card-icon"><i class="fas fa-calendar-days"></i></div>
    </div>
</div>

<!-- Navigation Buttons -->
<div class="content-box">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 14px;">
        <h3 style="margin: 0; color: var(--primary-color);">
            <i class="fas fa-list"></i> Riwayat Kesehatan Lengkap
        </h3>
        <div style="display: flex; gap: 10px;">
            <a href="{{ route('admin.kesehatan-santri.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Tambah Data Kesehatan
            </a>
            <a href="{{ route('admin.kesehatan-santri.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </div>
    
    @if($riwayatKesehatan->count() > 0)
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 5%;">No</th>
                    <th style="width: 10%;">ID Kesehatan</th>
                    <th style="width: 12%;">Tanggal Masuk</th>
                    <th style="width: 25%;">Keluhan</th>
                    <th style="width: 18%;">Catatan</th>
                    <th style="width: 12%;">Tanggal Keluar</th>
                    <th style="width: 8%;">Status</th>
                    <th style="width: 5%;">Lama</th>
                    <th style="width: 15%;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($riwayatKesehatan as $index => $data)
                <tr style="{{ $data->status == 'dirawat' ? 'background-color: #FFEBEE;' : '' }}">
                    <td class="text-center">{{ $riwayatKesehatan->firstItem() + $index }}</td>
                    <td><strong>{{ $data->id_kesehatan }}</strong></td>
                    <td>
                        <strong>{{ $data->tanggal_masuk_formatted }}</strong><br>
                        <small style="color: #7F8C8D;">{{ $data->tanggal_masuk->format('D') }}</small>
                    </td>
                    <td>
                        <div title="{{ $data->keluhan }}">
                            {{ Str::limit($data->keluhan, 80) }}
                        </div>
                    </td>
                    <td>
                        @if($data->catatan)
                            <div title="{{ $data->catatan }}">
                                {{ Str::limit($data->catatan, 60) }}
                            </div>
                        @else
                            <span style="color: #BDC3C7; font-style: italic;">-</span>
                        @endif
                    </td>
                    <td>
                        @if($data->tanggal_keluar)
                            <strong>{{ $data->tanggal_keluar_formatted }}</strong><br>
                            <small style="color: #7F8C8D;">{{ $data->tanggal_keluar->format('D') }}</small>
                        @else
                            <span style="color: #E74C3C; font-weight: bold;">Belum keluar</span>
                        @endif
                    </td>
                    <td class="text-center">
                        <span class="btn btn-{{ $data->status_badge_color }} btn-sm" 
                              style="cursor: default; padding: 5px 10px;">
                            {{ ucfirst($data->status) }}
                        </span>
                    </td>
                    <td class="text-center">
                        <strong>{{ $data->lama_dirawat }}</strong> hari
                    </td>
                    <td>
                        <div style="display: flex; gap: 5px; flex-wrap: wrap;">
                            <a href="{{ route('admin.kesehatan-santri.show', $data) }}" 
                               class="btn btn-primary btn-sm"
                               title="Detail">
                                <i class="fas fa-eye"></i>
                            </a>
                            
                            <a href="{{ route('admin.kesehatan-santri.edit', $data) }}" 
                               class="btn btn-warning btn-sm"
                               title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            
                            <a href="{{ route('admin.kesehatan-santri.cetak-surat', $data) }}" 
                               class="btn btn-secondary btn-sm"
                               title="Cetak Surat"
                               target="_blank">
                                <i class="fas fa-print"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        
        <!-- Pagination -->
        <div style="margin-top: 14px; display: flex; justify-content: center;">
            {{ $riwayatKesehatan->links() }}
        </div>
        
    @else
        <div style="text-align: center; padding: 36px; color: #7F8C8D;">
            <i class="fas fa-heartbeat" style="font-size: 3em; margin-bottom: 15px; color: #BDC3C7;"></i>
            <h3>Belum Ada Riwayat Kesehatan</h3>
            <p>Santri {{ $santri->nama_lengkap }} belum memiliki riwayat kesehatan.</p>
            <a href="{{ route('admin.kesehatan-santri.create') }}" class="btn btn-primary" style="margin-top: 15px;">
                <i class="fas fa-plus"></i> Tambah Data Kesehatan
            </a>
        </div>
    @endif
</div>

<!-- Info Tambahan -->
@if($riwayatKesehatan->count() > 0)
<div class="content-box" style="background: linear-gradient(135deg, #F8FBF9 0%, #E8F7F2 100%); border-left: 4px solid var(--primary-color);">
    <h4 style="color: var(--primary-color); margin-bottom: 14px;">
        <i class="fas fa-info-circle"></i> Informasi Tambahan
    </h4>
    
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px;">
        <div>
            <strong><i class="fas fa-calendar-plus" style="color: var(--primary-color);"></i> Kunjungan Pertama:</strong><br>
            <span style="font-size: 1.1em;">{{ $riwayatKesehatan->sortBy('tanggal_masuk')->first()->tanggal_masuk->format('d M Y') }}</span>
        </div>
        
        <div>
            <strong><i class="fas fa-calendar-check" style="color: var(--primary-color);"></i> Kunjungan Terakhir:</strong><br>
            <span style="font-size: 1.1em;">{{ $riwayatKesehatan->sortByDesc('tanggal_masuk')->first()->tanggal_masuk->format('d M Y') }}</span>
        </div>
        
        <div>
            <strong><i class="fas fa-bed" style="color: var(--primary-color);"></i> Lama Dirawat Terlama:</strong><br>
            <span style="font-size: 1.1em;">{{ $riwayatKesehatan->max('lama_dirawat') }} hari</span>
        </div>
        
        <div>
            <strong><i class="fas fa-notes-medical" style="color: var(--primary-color);"></i> Total Hari Dirawat:</strong><br>
            <span style="font-size: 1.1em;">{{ $riwayatKesehatan->sum('lama_dirawat') }} hari</span>
        </div>
    </div>
</div>
@endif

@endsection