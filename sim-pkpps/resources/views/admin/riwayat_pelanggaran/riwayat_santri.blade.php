{{-- resources/views/admin/riwayat_pelanggaran/riwayat_santri.blade.php --}}
@extends('layouts.app')

@section('title', 'Riwayat Pelanggaran - ' . $santri->nama_lengkap)

@section('content')
<div class="page-header">
    <h2><i class="fas fa-user-times"></i> Riwayat Pelanggaran Santri</h2>
</div>

<!-- Breadcrumb -->
<div style="margin-bottom: 14px;">
    <nav style="display: flex; align-items: center; gap: 8px; color: var(--text-light); font-size: 0.9em;">
        <a href="{{ route('admin.santri.index') }}" style="color: var(--primary-color); text-decoration: none;">
            <i class="fas fa-users"></i> Data Santri
        </a>
        <i class="fas fa-chevron-right" style="font-size: 0.7em;"></i>
        <a href="{{ route('admin.santri.show', $santri) }}" style="color: var(--primary-color); text-decoration: none;">
            {{ $santri->nama_lengkap }}
        </a>
        <i class="fas fa-chevron-right" style="font-size: 0.7em;"></i>
        <span>Riwayat Pelanggaran</span>
    </nav>
</div>

<!-- Statistik Cards -->
<div class="row-cards">
    <div class="card card-primary">
        <h3><i class="fas fa-user"></i> Data Santri</h3>
        <div style="margin-top: 10px;">
            <strong style="font-size: 1.3em; display: block;">{{ $santri->nama_lengkap }}</strong>
            <p style="margin: 5px 0 0 0; color: var(--text-light);">
                {{ $santri->id_santri }} | {{ $santri->kelas }}
            </p>
        </div>
        <i class="fas fa-user-circle card-icon"></i>
    </div>

    <div class="card card-warning">
        <h3><i class="fas fa-list"></i> Total Pelanggaran</h3>
        <div class="card-value">{{ $totalPelanggaran }}</div>
        <p style="margin: 0; color: var(--text-light);">Jumlah Pelanggaran</p>
        <i class="fas fa-clipboard-list card-icon"></i>
    </div>

    <div class="card card-danger">
        <h3><i class="fas fa-fire"></i> Total Poin</h3>
        <div class="card-value">{{ $totalPoin }}</div>
        <p style="margin: 0; color: var(--text-light);">Akumulasi Poin</p>
        <i class="fas fa-star card-icon"></i>
    </div>
</div>

<!-- Tabel Riwayat -->
<div class="content-box">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 14px;">
        <h3 style="margin: 0; color: var(--primary-color);">
            <i class="fas fa-history"></i> Daftar Riwayat Pelanggaran
        </h3>
        <div style="display: flex; gap: 10px;">
            <a href="{{ route('admin.riwayat-pelanggaran.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Tambah Pelanggaran
            </a>
            <a href="{{ route('admin.santri.show', $santri) }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </div>

    @if($riwayat->isNotEmpty())
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 50px;">No</th>
                    <th style="width: 110px;">ID Riwayat</th>
                    <th style="width: 130px;">Tanggal</th>
                    <th>Kategori Pelanggaran</th>
                    <th style="width: 100px; text-align: center;">Poin</th>
                    <th>Keterangan</th>
                    <th style="width: 150px; text-align: center;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($riwayat as $index => $item)
                    <tr>
                        <td>{{ $riwayat->firstItem() + $index }}</td>
                        <td>
                            <span class="badge badge-secondary">{{ $item->id_riwayat }}</span>
                        </td>
                        <td>
                            <i class="fas fa-calendar" style="color: var(--text-light);"></i>
                            {{ \Carbon\Carbon::parse($item->tanggal)->format('d M Y') }}
                        </td>
                        <td>
                            @if($item->kategori)
                                <strong>{{ $item->kategori->nama_pelanggaran }}</strong><br>
                                <small style="color: var(--text-light);">
                                    <i class="fas fa-tag"></i> {{ $item->id_kategori }}
                                </small>
                            @else
                                <span style="color: var(--danger-color);">Kategori tidak ditemukan</span>
                            @endif
                        </td>
                        <td style="text-align: center;">
                            <span class="badge badge-danger" style="font-size: 1em; padding: 8px 12px;">
                                <i class="fas fa-fire"></i> {{ $item->poin }}
                            </span>
                        </td>
                        <td>
                            @if($item->keterangan)
                                <div style="max-width: 300px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                    {{ $item->keterangan }}
                                </div>
                            @else
                                <span style="color: var(--text-light);">-</span>
                            @endif
                        </td>
                        <td style="text-align: center;">
                            <div style="display: flex; justify-content: center; gap: 8px;">
                                <a href="{{ route('admin.riwayat-pelanggaran.show', $item) }}" 
                                   class="btn btn-sm btn-success" 
                                   title="Detail">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.riwayat-pelanggaran.edit', $item) }}" 
                                   class="btn btn-sm btn-warning" 
                                   title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.riwayat-pelanggaran.destroy', $item) }}" 
                                      method="POST" 
                                      style="display: inline;"
                                      onsubmit="return confirm('Yakin ingin menghapus riwayat ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="btn btn-sm btn-danger" 
                                            title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Pagination -->
        <div style="margin-top: 14px;">
            {{ $riwayat->links() }}
        </div>
    @else
        <div class="empty-state">
            <i class="fas fa-check-circle"></i>
            <h3>Belum ada riwayat pelanggaran</h3>
            <p>Santri <strong>{{ $santri->nama_lengkap }}</strong> belum memiliki catatan pelanggaran.</p>
            <a href="{{ route('admin.riwayat-pelanggaran.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Tambah Pelanggaran
            </a>
        </div>
    @endif
</div>

<!-- Info Box -->
@if($totalPoin > 0)
<div class="content-box" style="margin-top: 22px;">
    <h3 style="margin-bottom: 15px; color: var(--primary-color);">
        <i class="fas fa-chart-line"></i> Analisis Pelanggaran
    </h3>
    
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px;">
        <div style="background: var(--primary-light); padding: 14px; border-radius: var(--border-radius-sm); text-align: center;">
            <i class="fas fa-calculator" style="font-size: 2em; color: var(--primary-color); margin-bottom: 10px;"></i>
            <div style="font-size: 1.5em; font-weight: 700; color: var(--primary-dark);">
                {{ $totalPelanggaran > 0 ? number_format($totalPoin / $totalPelanggaran, 1) : 0 }}
            </div>
            <p style="margin: 5px 0 0 0; color: var(--text-light);">Rata-rata Poin/Pelanggaran</p>
        </div>
        
        <div style="background: var(--warning-color); padding: 14px; border-radius: var(--border-radius-sm); text-align: center;">
            <i class="fas fa-exclamation-triangle" style="font-size: 2em; color: #856404; margin-bottom: 10px;"></i>
            <div style="font-size: 1.5em; font-weight: 700; color: #856404;">
                @if($totalPoin >= 50)
                    Berat
                @elseif($totalPoin >= 20)
                    Sedang
                @else
                    Ringan
                @endif
            </div>
            <p style="margin: 5px 0 0 0; color: #856404;">Kategori Pelanggaran</p>
        </div>
        
        <div style="background: var(--danger-color); padding: 14px; border-radius: var(--border-radius-sm); text-align: center;">
            <i class="fas fa-calendar-alt" style="font-size: 2em; color: white; margin-bottom: 10px;"></i>
            <div style="font-size: 1.5em; font-weight: 700; color: white;">
                {{ $riwayat->first() ? \Carbon\Carbon::parse($riwayat->first()->tanggal)->format('d M Y') : '-' }}
            </div>
            <p style="margin: 5px 0 0 0; color: white;">Pelanggaran Terakhir</p>
        </div>
    </div>
</div>
@endif
@endsection