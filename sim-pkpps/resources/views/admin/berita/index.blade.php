@extends('layouts.app')

@section('title', 'Daftar Berita')

@section('content')
<div class="page-header">
    <h2><i class="fas fa-newspaper"></i> Daftar Berita</h2>
</div>

<!-- Header Actions -->
<div class="content-box" style="margin-bottom: 20px;">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;">
        <!-- Search & Filter Form -->
        <form method="GET" action="{{ route('admin.berita.index') }}" style="display: flex; gap: 10px; flex-wrap: wrap; flex-grow: 1;">
            <input type="text" 
                   name="search" 
                   class="form-control" 
                   placeholder="Cari judul, penulis, atau ID..." 
                   value="{{ request('search') }}"
                   style="max-width: 300px;">
            
            <select name="status" class="form-control" style="max-width: 150px;">
                <option value="">Semua Status</option>
                <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                <option value="published" {{ request('status') == 'published' ? 'selected' : '' }}>Published</option>
            </select>
            
            <select name="target" class="form-control" style="max-width: 150px;">
                <option value="">Semua Target</option>
                <option value="semua" {{ request('target') == 'semua' ? 'selected' : '' }}>Semua Santri</option>
                <option value="kelas_tertentu" {{ request('target') == 'kelas_tertentu' ? 'selected' : '' }}>Kelas Tertentu</option>
            </select>
            
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-search"></i> Filter
            </button>
            
            <a href="{{ route('admin.berita.index') }}" class="btn btn-secondary">
                <i class="fas fa-redo"></i> Reset
            </a>
        </form>

        <!-- Action Buttons -->
        <div style="display: flex; gap: 10px;">
            <a href="{{ route('admin.berita.statistik') }}" class="btn btn-secondary">
                <i class="fas fa-chart-bar"></i> Statistik
            </a>
            <a href="{{ route('admin.berita.create') }}" class="btn btn-success">
                <i class="fas fa-plus"></i> Tambah Berita
            </a>
        </div>
    </div>
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

<!-- Tabel Berita -->
<div class="content-box">
    @if($berita->count() > 0)
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 80px;">ID</th>
                    <th>Judul & Konten</th>
                    <th style="width: 150px;">Penulis</th>
                    <th style="width: 120px;">Tanggal</th>
                    <th style="width: 100px;">Status</th>
                    <th style="width: 150px;">Target</th>
                    <th style="width: 150px;" class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($berita as $item)
                <tr>
                    <td><strong>{{ $item->id_berita }}</strong></td>
                    <td>
                        <div style="max-width: 250px;">
                            <strong style="color: var(--primary-color);">{{ $item->judul }}</strong>
                            <br>
                            <small class="text-muted">{{ Str::limit(strip_tags($item->konten), 80) }}</small>
                        </div>
                    </td>
                    <td>{{ $item->penulis }}</td>
                    <td>{{ $item->tanggal_formatted }}</td>
                    <td>
                        <span class="badge {{ $item->status_badge }}">
                            @if($item->status === 'published')
                                <i class="fas fa-check-circle"></i> Published
                            @else
                                <i class="fas fa-edit"></i> Draft
                            @endif
                        </span>
                    </td>
                    <td>
                        @php
                            $badgeClass = match($item->target_berita) {
                                'semua' => 'badge-primary',
                                'kelas_tertentu' => 'badge-info',
                                default => 'badge-secondary'
                            };
                        @endphp
                        <span class="badge {{ $badgeClass }}">
                            {{ $item->target_audience }}
                        </span>
                    </td>
                    <td class="text-center">
                        <div style="display: flex; gap: 5px; justify-content: center;">
                            <a href="{{ route('admin.berita.show', $item->id_berita) }}" 
                               class="btn btn-primary btn-sm" 
                               title="Lihat Detail">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('admin.berita.edit', $item->id_berita) }}" 
                               class="btn btn-warning btn-sm" 
                               title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('admin.berita.destroy', $item->id_berita) }}" 
                                  method="POST" 
                                  style="display: inline;"
                                  onsubmit="return confirm('Yakin ingin menghapus berita ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm" title="Hapus">
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
        <div style="margin-top: 20px; display: flex; justify-content: center;">
            {{ $berita->appends(request()->query())->links() }}
        </div>
    @else
        <div style="text-align: center; padding: 60px 20px;">
            <i class="fas fa-newspaper" style="font-size: 4em; color: #ccc; margin-bottom: 20px;"></i>
            <h3 style="color: var(--text-light);">Belum Ada Berita</h3>
            <p style="color: var(--text-light); margin-bottom: 25px;">
                Mulai tambahkan berita pertama untuk santri pesantren.
            </p>
            <a href="{{ route('admin.berita.create') }}" class="btn btn-success">
                <i class="fas fa-plus"></i> Tambah Berita Pertama
            </a>
        </div>
    @endif
</div>
@endsection