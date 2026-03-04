{{-- 
============================================================================
LOKASI FILE: resources/views/admin/kelas/kelompok/index.blade.php
============================================================================
CATATAN: Buat folder "kelompok" di dalam folder "kelas" terlebih dahulu
============================================================================
--}}

@extends('layouts.app')

@section('title', 'Kelola Kelompok Kelas')

@section('content')
<div class="page-header">
    <h2><i class="fas fa-folder"></i> Kelola Kelompok Kelas</h2>
    <a href="{{ route('admin.kelas.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Kembali ke Kelola Kelas
    </a>    
</div>

<!-- Flash Messages -->
@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle"></i> {{ session('success') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif

@if (session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif

<!-- Quick Navigation -->
<div class="content-box" style="margin-bottom: 14px; background: linear-gradient(135deg, #E8F7F2 0%, #D4F1E3 100%); border: 2px solid var(--primary-color);">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 11px;">
        <div>
            <h4 style="margin: 0; color: var(--primary-dark);">
                <i class="fas fa-layer-group"></i> Menu Manajemen Kelas
            </h4>
        </div>
        <div style="display: flex; gap: 10px; flex-wrap: wrap;">
        </div>
    </div>
</div>

<!-- Header Actions -->
<div class="content-box" style="margin-bottom: 14px;">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 11px;">
        <!-- Search Form -->
        <form method="GET" action="{{ route('admin.kelas.kelompok.index') }}" style="display: flex; gap: 10px; flex-wrap: wrap; flex-grow: 1;">
            <input type="text" 
                   name="search" 
                   class="form-control" 
                   placeholder="Cari nama kelompok..." 
                   value="{{ request('search') }}"
                   style="max-width: 300px;">
            
            <select name="status" class="form-control" style="max-width: 150px;">
                <option value="">Semua Status</option>
                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Aktif</option>
                <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Tidak Aktif</option>
            </select>
            
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-search"></i> Filter
            </button>
            
            <a href="{{ route('admin.kelas.kelompok.index') }}" class="btn btn-secondary">
                <i class="fas fa-redo"></i> Reset
            </a>
        </form>

        <!-- Action Button -->
        <div>
            <a href="{{ route('admin.kelas.kelompok.create') }}" class="btn btn-success">
                <i class="fas fa-plus"></i> Tambah Kelompok Baru
            </a>
        </div>
    </div>
</div>

<!-- Kelompok List -->
<div class="content-box">
    @if ($kelompokKelas->count() > 0)
        <div class="table-wrapper">
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 50px;">No</th>
                    <th>Kode Kelompok</th>
                    <th>Nama Kelompok</th>
                    <th>Deskripsi</th>
                    <th style="width: 80px;">Urutan</th>
                    <th style="width: 100px;">Total Kelas</th>
                    <th style="width: 100px;">Status</th>
                    <th style="width: 150px;">Aksi</th>
                </tr>
            </thead>
                <tbody>
                    @foreach ($kelompokKelas as $index => $kelompok)
                        <tr>
                            <td>{{ $kelompokKelas->firstItem() + $index }}</td>
                            <td><strong>{{ $kelompok->id_kelompok }}</strong></td>
                            <td>{{ $kelompok->nama_kelompok }}</td>
                            <td>
                                <span class="text-muted" style="font-size: 0.9em;">
                                    {{ $kelompok->deskripsi ?? '-' }}
                                </span>
                            </td>
                            <td class="text-center">{{ $kelompok->urutan }}</td>
                            <td class="text-center">
                                <span class="badge badge-info">
                                    {{ $kelompok->kelas_count }} kelas
                                </span>
                            </td>
                            <td>
                                @if ($kelompok->is_active)
                                    <span class="badge badge-success">
                                        <i class="fas fa-check-circle"></i> Aktif
                                    </span>
                                @else
                                    <span class="badge badge-secondary">
                                        <i class="fas fa-times-circle"></i> Tidak Aktif
                                    </span>
                                @endif
                            </td>
                            <td>
                                <div style="display: flex; gap: 5px;">
                                    <a href="{{ route('admin.kelas.kelompok.edit', $kelompok->id) }}" 
                                       class="btn btn-sm btn-warning"
                                       title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.kelas.kelompok.destroy', $kelompok->id) }}" 
                                          method="POST" 
                                          style="display: inline;"
                                          onsubmit="return confirm('Apakah Anda yakin ingin menghapus kelompok {{ $kelompok->nama_kelompok }}? Semua kelas di kelompok ini akan terhapus!')">
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
        @if ($kelompokKelas->hasPages())
            <div style="margin-top: 14px;">
                {{ $kelompokKelas->links('vendor.pagination.custom') }}
            </div>
        @endif
    @else
        <!-- Empty State -->
        <div class="text-center py-5">
            <i class="fas fa-folder fa-3x text-muted mb-3"></i>
            <h5 class="text-muted">Tidak ada data kelompok kelas</h5>
            <p class="text-muted">
                @if (request()->has('search') || request()->has('status'))
                    Tidak ada kelompok yang sesuai dengan filter.
                @else
                    Belum ada kelompok kelas yang ditambahkan.
                @endif
            </p>
            @if (!request()->has('search') && !request()->has('status'))
                <a href="{{ route('admin.kelas.kelompok.create') }}" class="btn btn-success mt-2">
                    <i class="fas fa-plus"></i> Tambah Kelompok Baru
                </a>
            @else
                <a href="{{ route('admin.kelas.kelompok.index') }}" class="btn btn-secondary mt-2">
                    <i class="fas fa-redo"></i> Reset Filter
                </a>
            @endif
        </div>
    @endif
</div>
@endsection