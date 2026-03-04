{{-- resources/views/admin/riwayat_pelanggaran/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Riwayat Pelanggaran')

@section('content')
<div class="page-header">
    <h2><i class="fas fa-history"></i> Riwayat Pelanggaran Santri</h2>
</div>

<!-- Alert Messages -->
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

<!-- Statistik Cards -->
<div class="row-cards">
    <div class="card card-primary">
        <h3><i class="fas fa-list"></i> Total Pelanggaran</h3>
        <div class="card-value">{{ $totalPelanggaran }}</div>
        <p style="margin: 0; color: var(--text-light);">Semua Riwayat</p>
        <i class="fas fa-clipboard-list card-icon"></i>
    </div>

    <div class="card card-warning">
        <h3><i class="fas fa-calendar-alt"></i> Bulan Ini</h3>
        <div class="card-value">{{ $pelanggaranBulanIni }}</div>
        <p style="margin: 0; color: var(--text-light);">{{ \Carbon\Carbon::now()->format('F Y') }}</p>
        <i class="fas fa-calendar-check card-icon"></i>
    </div>

    <div class="card card-danger">
        <h3><i class="fas fa-star"></i> Total Poin</h3>
        <div class="card-value">{{ $totalPoin }}</div>
        <p style="margin: 0; color: var(--text-light);">Akumulasi Poin</p>
        <i class="fas fa-fire card-icon"></i>
    </div>
</div>

<!-- Filter & Search -->
<div class="content-box" style="margin-bottom: 22px;">
    <h3 style="margin-bottom: 14px; color: var(--primary-color);">
        <i class="fas fa-filter"></i> Filter & Pencarian
    </h3>
    
    <form method="GET" action="{{ route('admin.riwayat-pelanggaran.index') }}">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 11px; margin-bottom: 14px;">
            <!-- Search -->
            <div class="form-group" style="margin-bottom: 0;">
                <label for="search">
                    <i class="fas fa-search form-icon"></i>
                    Pencarian
                </label>
                <input type="text" 
                       name="search" 
                       id="search"
                       class="form-control"
                       value="{{ request('search') }}"
                       placeholder="Cari santri, kategori...">
            </div>

            <!-- Filter Santri -->
            <div class="form-group" style="margin-bottom: 0;">
                <label for="id_santri">
                    <i class="fas fa-user form-icon"></i>
                    Santri
                </label>
                <select name="id_santri" id="id_santri" class="form-control">
                    <option value="">-- Semua Santri --</option>
                    @foreach($santriList as $santri)
                        <option value="{{ $santri->id_santri }}" 
                                {{ request('id_santri') == $santri->id_santri ? 'selected' : '' }}>
                            {{ $santri->nama_lengkap }} ({{ $santri->id_santri }})
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Filter Kategori -->
            <div class="form-group" style="margin-bottom: 0;">
                <label for="id_kategori">
                    <i class="fas fa-tags form-icon"></i>
                    Kategori
                </label>
                <select name="id_kategori" id="id_kategori" class="form-control">
                    <option value="">-- Semua Kategori --</option>
                    @foreach($kategoriList as $kategori)
                        <option value="{{ $kategori->id_kategori }}" 
                                {{ request('id_kategori') == $kategori->id_kategori ? 'selected' : '' }}>
                            {{ $kategori->nama_pelanggaran }} ({{ $kategori->poin }} poin)
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Tanggal Mulai -->
            <div class="form-group" style="margin-bottom: 0;">
                <label for="tanggal_mulai">
                    <i class="fas fa-calendar form-icon"></i>
                    Tanggal Mulai
                </label>
                <input type="date" 
                       name="tanggal_mulai" 
                       id="tanggal_mulai"
                       class="form-control"
                       value="{{ request('tanggal_mulai') }}">
            </div>

            <!-- Tanggal Selesai -->
            <div class="form-group" style="margin-bottom: 0;">
                <label for="tanggal_selesai">
                    <i class="fas fa-calendar-check form-icon"></i>
                    Tanggal Selesai
                </label>
                <input type="date" 
                       name="tanggal_selesai" 
                       id="tanggal_selesai"
                       class="form-control"
                       value="{{ request('tanggal_selesai') }}">
            </div>
        </div>

        <div class="btn-group">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-search"></i> Cari
            </button>
            <a href="{{ route('admin.riwayat-pelanggaran.index') }}" class="btn btn-secondary">
                <i class="fas fa-redo"></i> Reset
            </a>
            <label style="display: inline-flex; align-items: center; margin-left: 20px;">
                <input type="checkbox" name="bulan_ini" value="1" {{ request('bulan_ini') ? 'checked' : '' }} style="margin-right: 8px;">
                <span>Bulan Ini Saja</span>
            </label>
        </div>
    </form>
</div>

<!-- Tabel Data -->
<div class="content-box">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 14px;">
        <h3 style="margin: 0; color: var(--primary-color);">
            <i class="fas fa-table"></i> Daftar Riwayat Pelanggaran
        </h3>
        <a href="{{ route('admin.riwayat-pelanggaran.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Tambah Riwayat
        </a>
    </div>

    @if($data->isNotEmpty())
        <div class="table-wrapper">
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 50px;">No</th>
                    <th style="width: 110px;">ID Riwayat</th>
                    <th style="width: 120px;">Tanggal</th>
                    <th>Santri</th>
                    <th>Kategori Pelanggaran</th>
                    <th style="width: 100px; text-align: center;">Poin</th>
                    <th style="width: 200px; text-align: center;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data as $index => $item)
                    <tr>
                        <td>{{ $data->firstItem() + $index }}</td>
                        <td>
                            <span class="badge badge-secondary">{{ $item->id_riwayat }}</span>
                        </td>
                        <td>
                            <i class="fas fa-calendar" style="color: var(--text-light);"></i>
                            {{ \Carbon\Carbon::parse($item->tanggal)->format('d M Y') }}
                        </td>
                        <td>
                            @if($item->santri)
                                <strong>{{ $item->santri->nama_lengkap }}</strong><br>
                                <small style="color: var(--text-light);">
                                    <i class="fas fa-id-card"></i> {{ $item->id_santri }}
                                </small>
                            @else
                                <span style="color: var(--danger-color);">Santri tidak ditemukan</span>
                            @endif
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
        </div>

        <!-- Pagination -->
        <div style="margin-top: 14px;">
            {{ $data->links() }}
        </div>
    @else
        <div class="empty-state">
            <i class="fas fa-folder-open"></i>
            <h3>Belum ada riwayat pelanggaran</h3>
            <p>Silakan tambah riwayat pelanggaran baru menggunakan tombol di atas.</p>
            <a href="{{ route('admin.riwayat-pelanggaran.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Tambah Riwayat
            </a>
        </div>
    @endif
</div>

<script>
// Auto hide alerts after 5 seconds
setTimeout(function() {
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        alert.style.transition = 'opacity 0.5s';
        alert.style.opacity = '0';
        setTimeout(() => alert.remove(), 500);
    });
}, 5000);
</script>
@endsection