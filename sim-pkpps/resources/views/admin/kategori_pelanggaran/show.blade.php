{{-- resources/views/admin/kategori_pelanggaran/show.blade.php --}}
@extends('layouts.app')

@section('title', 'Detail Kategori Pelanggaran')

@section('content')
<div class="page-header">
    <h2><i class="fas fa-info-circle"></i> Detail Kategori Pelanggaran</h2>
</div>

<!-- Breadcrumb -->
<div style="margin-bottom: 20px;">
    <nav style="display: flex; align-items: center; gap: 8px; color: var(--text-light); font-size: 0.9em;">
        <a href="{{ route('admin.kategori-pelanggaran.index') }}" style="color: var(--primary-color); text-decoration: none;">
            <i class="fas fa-list-ul"></i> Kategori Pelanggaran
        </a>
        <i class="fas fa-chevron-right" style="font-size: 0.7em;"></i>
        <span>Detail</span>
    </nav>
</div>

<div style="display: grid; grid-template-columns: 2fr 1fr; gap: 30px;">
    <!-- Detail Kategori -->
    <div>
        <div class="content-box">
            <div class="detail-header">
                <h3><i class="fas fa-clipboard-list"></i> Informasi Kategori</h3>
                <div style="display: flex; gap: 10px;">
                    <a href="{{ route('admin.kategori-pelanggaran.edit', $kategori) }}" class="btn btn-warning btn-sm">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                    <a href="{{ route('admin.kategori-pelanggaran.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                </div>
            </div>

            <table class="detail-table">
                <tr>
                    <th><i class="fas fa-tag"></i> ID Kategori</th>
                    <td>
                        <span class="badge badge-primary" style="font-size: 1em;">
                            {{ $kategori->id_kategori }}
                        </span>
                    </td>
                </tr>
                <tr>
                    <th><i class="fas fa-exclamation-triangle"></i> Nama Pelanggaran</th>
                    <td><strong style="font-size: 1.1em;">{{ $kategori->nama_pelanggaran }}</strong></td>
                </tr>
                <tr>
                    <th><i class="fas fa-star"></i> Poin</th>
                    <td>
                        <span class="badge badge-danger" style="font-size: 1.1em; padding: 8px 16px;">
                            <i class="fas fa-fire"></i> {{ $kategori->poin }} Poin
                        </span>
                    </td>
                </tr>
                <tr>
                    <th><i class="fas fa-calendar-plus"></i> Tanggal Dibuat</th>
                    <td>{{ $kategori->created_at->format('d F Y, H:i') }} WIB</td>
                </tr>
                <tr>
                    <th><i class="fas fa-calendar-check"></i> Terakhir Diperbarui</th>
                    <td>{{ $kategori->updated_at->format('d F Y, H:i') }} WIB</td>
                </tr>
            </table>
        </div>

        <!-- Riwayat Pelanggaran Terkait -->
        @if($kategori->riwayatPelanggaran->isNotEmpty())
        <div class="content-box" style="margin-top: 30px;">
            <h3 style="margin-bottom: 20px; color: var(--primary-color);">
                <i class="fas fa-history"></i> Riwayat Pelanggaran Terkait
            </h3>
            
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 50px;">No</th>
                        <th>Tanggal</th>
                        <th>Santri</th>
                        <th style="text-align: center;">Poin</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($kategori->riwayatPelanggaran->take(10) as $index => $riwayat)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ \Carbon\Carbon::parse($riwayat->tanggal)->format('d M Y') }}</td>
                        <td>
                            @if($riwayat->santri)
                                <strong>{{ $riwayat->santri->nama_lengkap }}</strong><br>
                                <small style="color: var(--text-light);">{{ $riwayat->id_santri }}</small>
                            @else
                                <span style="color: var(--danger-color);">Santri tidak ditemukan</span>
                            @endif
                        </td>
                        <td style="text-align: center;">
                            <span class="badge badge-danger">{{ $riwayat->poin }}</span>
                        </td>
                        <td>{{ $riwayat->keterangan ?? '-' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            @if($kategori->riwayatPelanggaran->count() > 10)
            <div style="text-align: center; margin-top: 20px;">
                <a href="{{ route('admin.riwayat-pelanggaran.index') }}" class="btn btn-primary">
                    <i class="fas fa-list"></i> Lihat Semua ({{ $kategori->riwayatPelanggaran->count() }} Riwayat)
                </a>
            </div>
            @endif
        </div>
        @endif
    </div>

    <!-- Sidebar -->
    <div>
        <!-- Statistik -->
        <div class="card card-primary">
            <h3 style="margin-bottom: 15px;">
                <i class="fas fa-chart-bar"></i> Statistik
            </h3>
            <div style="text-align: center;">
                <div class="card-value">{{ $kategori->riwayatPelanggaran->count() }}</div>
                <p style="margin: 0; color: var(--text-light);">Total Pelanggaran</p>
            </div>
            
            @if($kategori->riwayatPelanggaran->count() > 0)
            <div style="margin-top: 20px; padding-top: 20px; border-top: 1px solid var(--primary-light);">
                <div style="text-align: center; margin-bottom: 15px;">
                    <div class="card-value-small" style="color: var(--danger-color);">
                        {{ $kategori->riwayatPelanggaran->sum('poin') }}
                    </div>
                    <p style="margin: 0; color: var(--text-light);">Total Poin Terkumpul</p>
                </div>
                
                <div style="background: var(--primary-light); padding: 12px; border-radius: var(--border-radius-sm); text-align: center;">
                    <small style="color: var(--text-color); display: block; margin-bottom: 5px;">
                        <i class="fas fa-calendar"></i> Pelanggaran Terakhir
                    </small>
                    <strong style="color: var(--primary-dark);">
                        {{ $kategori->riwayatPelanggaran->sortByDesc('tanggal')->first()->tanggal->format('d M Y') }}
                    </strong>
                </div>
            </div>
            @endif
        </div>

        <!-- Quick Actions -->
        <div class="card card-success" style="margin-top: 20px;">
            <h3 style="margin-bottom: 15px;">
                <i class="fas fa-bolt"></i> Aksi Cepat
            </h3>
            <div style="display: flex; flex-direction: column; gap: 10px;">
                <a href="{{ route('admin.kategori-pelanggaran.edit', $kategori) }}" class="btn btn-warning" style="width: 100%;">
                    <i class="fas fa-edit"></i> Edit Kategori
                </a>
                <a href="{{ route('admin.riwayat-pelanggaran.create') }}" class="btn btn-primary" style="width: 100%;">
                    <i class="fas fa-plus"></i> Tambah Riwayat
                </a>
                <a href="{{ route('admin.kategori-pelanggaran.index') }}" class="btn btn-secondary" style="width: 100%;">
                    <i class="fas fa-list"></i> Semua Kategori
                </a>
            </div>
        </div>

        <!-- Info Box -->
        <div class="card card-info" style="margin-top: 20px;">
            <h3 style="margin-bottom: 10px;">
                <i class="fas fa-info-circle"></i> Informasi
            </h3>
            <p style="font-size: 0.9em; line-height: 1.6; margin: 0; color: var(--text-color);">
                Kategori ini telah digunakan <strong>{{ $kategori->riwayatPelanggaran->count() }} kali</strong> 
                dalam sistem pencatatan pelanggaran santri.
            </p>
        </div>
    </div>
</div>
@endsection