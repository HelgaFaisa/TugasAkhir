{{-- 
============================================================================
LOKASI FILE: resources/views/admin/kelas/index.blade.php
============================================================================
--}}

@extends('layouts.app')

@section('title', 'Kelola Kelas Santri')

@section('content')
<div class="page-header">
    <h2><i class="fas fa-layer-group"></i> Kelola Kelas Santri</h2>
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

<!-- Quick Navigation Menu -->
<div class="content-box" style="margin-bottom: 20px; background: linear-gradient(135deg, #E8F7F2 0%, #D4F1E3 100%); border: 2px solid var(--primary-color);">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;">
        <div>
            <h4 style="margin: 0; color: var(--primary-dark); display: flex; align-items: center; gap: 10px;">
                <i class="fas fa-layer-group"></i>
                Menu Manajemen Kelas
            </h4>
            <p style="margin: 5px 0 0 0; color: var(--text-light); font-size: 0.9rem;">
                Kelola kelompok kelas, daftar kelas, dan kenaikan kelas tahunan
            </p>
        </div>
        <div style="display: flex; gap: 10px; flex-wrap: wrap;">
            <a href="{{ route('admin.kelas.kelompok.index') }}" class="btn btn-info">
                <i class="fas fa-folder"></i> Kelompok Kelas
            </a>
            <a href="{{ route('admin.kelas.index') }}" class="btn btn-primary">
                <i class="fas fa-chalkboard"></i> Daftar Kelas
            </a>
            <a href="{{ route('admin.kelas.kenaikan.index') }}" class="btn btn-success">
                <i class="fas fa-graduation-cap"></i> Kenaikan Kelas
            </a>
        </div>
    </div>
</div>

<!-- Header Actions -->
<div class="content-box" style="margin-bottom: 20px;">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;">
        <!-- Search & Filter Form -->
        <form method="GET" action="{{ route('admin.kelas.index') }}" style="display: flex; gap: 10px; flex-wrap: wrap; flex-grow: 1;">
            <input type="text" 
                   name="search" 
                   class="form-control" 
                   placeholder="Cari nama atau kode kelas..." 
                   value="{{ request('search') }}"
                   style="max-width: 300px;">
            
            <select name="kelompok" class="form-control" style="max-width: 200px;">
                <option value="">Semua Kelompok</option>
                @foreach ($kelompokKelas as $kelompok)
                    <option value="{{ $kelompok->id_kelompok }}" 
                            {{ request('kelompok') == $kelompok->id_kelompok ? 'selected' : '' }}>
                        {{ $kelompok->nama_kelompok }}
                    </option>
                @endforeach
            </select>
            
            <select name="status" class="form-control" style="max-width: 150px;">
                <option value="">Semua Status</option>
                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Aktif</option>
                <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Tidak Aktif</option>
            </select>
            
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-search"></i> Filter
            </button>
            
            <a href="{{ route('admin.kelas.index') }}" class="btn btn-secondary">
                <i class="fas fa-redo"></i> Reset
            </a>
        </form>

        <!-- Action Button -->
        <div>
            <a href="{{ route('admin.kelas.create') }}" class="btn btn-success">
                <i class="fas fa-plus"></i> Tambah Kelas Baru
            </a>
        </div>
    </div>
</div>

<!-- Kelas List -->
<div class="content-box">
    @if ($kelas->count() > 0)
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 50px;">No</th>
                    <th>Kode Kelas</th>
                    <th>Nama Kelas</th>
                    <th>Kelompok</th>
                    <th style="width: 80px;">Urutan</th>
                    <th style="width: 100px;">Status</th>
                    <th style="width: 150px;">Aksi</th>
                </tr>
            </thead>
                <tbody>
                    @foreach ($kelas as $index => $item)
                        <tr>
                            <td>{{ $kelas->firstItem() + $index }}</td>
                            <td><strong>{{ $item->kode_kelas }}</strong></td>
                            <td>{{ $item->nama_kelas }}</td>
                            <td>
                                <span class="badge badge-info">
                                    {{ $item->kelompok->nama_kelompok }}
                                </span>
                            </td>
                            <td class="text-center">{{ $item->urutan }}</td>
                            <td>
                                @if ($item->is_active)
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
                                    <a href="{{ route('admin.kelas.edit', $item->id) }}" 
                                       class="btn btn-sm btn-warning"
                                       title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.kelas.destroy', $item->id) }}" 
                                          method="POST" 
                                          style="display: inline;"
                                          onsubmit="return confirm('Apakah Anda yakin ingin menghapus kelas {{ $item->nama_kelas }}?')">
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
        @if ($kelas->hasPages())
            <div style="margin-top: 20px;">
                {{ $kelas->links('vendor.pagination.custom') }}
            </div>
        @endif
    @else
        <!-- Empty State -->
        <div class="text-center py-5">
            <i class="fas fa-layer-group fa-3x text-muted mb-3"></i>
            <h5 class="text-muted">Tidak ada data kelas</h5>
            <p class="text-muted">
                @if (request()->has('search') || request()->has('kelompok') || request()->has('status'))
                    Tidak ada kelas yang sesuai dengan filter.
                @else
                    Belum ada kelas yang ditambahkan.
                @endif
            </p>
            @if (!request()->has('search') && !request()->has('kelompok') && !request()->has('status'))
                <a href="{{ route('admin.kelas.create') }}" class="btn btn-success mt-2">
                    <i class="fas fa-plus"></i> Tambah Kelas Baru
                </a>
            @else
                <a href="{{ route('admin.kelas.index') }}" class="btn btn-secondary mt-2">
                    <i class="fas fa-redo"></i> Reset Filter
                </a>
            @endif
        </div>
    @endif
</div>
@endsection