@extends('layouts.app')

@section('content')
<div class="page-header">
    <h2><i class="fas fa-book-open"></i> Master Materi Al-Qur'an & Hadist</h2>
</div>

{{-- Alert Messages --}}
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

{{-- Filter Kategori Buttons --}}
<div class="content-box" style="margin-bottom: 14px;">
    <div style="display: flex; gap: 10px; justify-content: center; flex-wrap: wrap;">
        <a href="{{ route('admin.materi.index', ['kategori' => 'Al-Qur\'an'] + request()->except('kategori')) }}" 
           class="btn {{ request('kategori') == 'Al-Qur\'an' ? 'btn-primary' : 'btn-outline-primary' }}" 
           style="min-width: 150px;">
            <i class="fas fa-book-quran"></i> Al-Qur'an
        </a>
        <a href="{{ route('admin.materi.index', ['kategori' => 'Hadist'] + request()->except('kategori')) }}" 
           class="btn {{ request('kategori') == 'Hadist' ? 'btn-primary' : 'btn-outline-primary' }}" 
           style="min-width: 150px;">
            <i class="fas fa-book"></i> Hadist
        </a>
        <a href="{{ route('admin.materi.index', ['kategori' => 'Materi Tambahan'] + request()->except('kategori')) }}" 
           class="btn {{ request('kategori') == 'Materi Tambahan' ? 'btn-primary' : 'btn-outline-primary' }}" 
           style="min-width: 150px;">
            <i class="fas fa-graduation-cap"></i> Materi Tambahan
        </a>
        @if(request('kategori'))
            <a href="{{ route('admin.materi.index', request()->except('kategori')) }}" 
               class="btn btn-secondary" 
               style="min-width: 150px;">
                <i class="fas fa-list"></i> Semua Kategori
            </a>
        @endif
    </div>
</div>

{{-- Filter & Search Section --}}
<div class="content-box" style="margin-bottom: 14px;">
    <form method="GET" action="{{ route('admin.materi.index') }}" class="filter-form-inline">
        <input type="hidden" name="kategori" value="{{ request('kategori') }}">
        
        <select name="kelas" class="form-control" style="width: 180px;">
            <option value="">Semua Kelas</option>
            @foreach($kelasList as $kls)
                <option value="{{ $kls->nama_kelas }}" {{ request('kelas') == $kls->nama_kelas ? 'selected' : '' }}>{{ $kls->nama_kelas }}</option>
            @endforeach
        </select>

        <input type="text" name="search" class="form-control" placeholder="Cari nama kitab..." 
               value="{{ request('search') }}" style="width: 250px;">

        <button type="submit" class="btn btn-primary">
            <i class="fas fa-search"></i> Filter
        </button>

        @if(request()->anyFilled(['kelas', 'search']))
            <a href="{{ route('admin.materi.index', request()->only('kategori')) }}" class="btn btn-secondary">
                <i class="fas fa-redo"></i> Reset
            </a>
        @endif

        <div style="margin-left: auto; display: flex; gap: 10px;">
            <a href="{{ route('admin.semester.index') }}" class="btn btn-info">
                <i class="fas fa-calendar-alt"></i> Manajemen Semester
            </a>
            <a href="{{ route('admin.materi.create') }}" class="btn btn-success">
                <i class="fas fa-plus"></i> Tambah Materi
            </a>
        </div>
    </form>
</div>

{{-- Table Section --}}
<div class="content-box">
    @if($materis->count() > 0)
        <div class="table-wrapper">
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 5%;">No</th>
                    <th style="width: 10%;">ID Materi</th>
                    <th style="width: 15%;">Kategori</th>
                    <th style="width: 10%;">Kelas</th>
                    <th style="width: 25%;">Nama Kitab</th>
                    <th style="width: 15%;">Halaman</th>
                    <th style="width: 10%;">Total Hal</th>
                    <th class="text-center" style="width: 10%;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($materis as $index => $materi)
                    <tr>
                        <td>{{ $materis->firstItem() + $index }}</td>
                        <td><strong>{{ $materi->id_materi }}</strong></td>
                        <td>{!! $materi->kategori_badge !!}</td>
                        <td>{!! $materi->kelas_badge !!}</td>
                        <td>{{ $materi->nama_kitab }}</td>
                        <td>
                            <span class="badge badge-info">
                                {{ $materi->halaman_mulai }} - {{ $materi->halaman_akhir }}
                            </span>
                        </td>
                        <td class="text-center">
                            <strong>{{ $materi->total_halaman }}</strong> hal
                        </td>
                        <td class="text-center">
                            <div style="display: flex; justify-content: center; align-items: center; gap: 8px;">
                                <a href="{{ route('admin.materi.show', $materi) }}" 
                                   class="btn btn-sm btn-info" title="Detail">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.materi.edit', $materi) }}" 
                                   class="btn btn-sm btn-warning" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.materi.destroy', $materi) }}" 
                                      method="POST" style="margin: 0;"
                                      onsubmit="return confirm('Yakin ingin menghapus materi {{ $materi->nama_kitab }}?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" title="Hapus">
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

        {{-- Pagination --}}
        <div style="margin-top: 14px;">
            {{ $materis->links() }}
        </div>
    @else
        <div class="empty-state">
            <i class="fas fa-book-open"></i>
            <h3>Belum Ada Data Materi</h3>
            <p>Silakan tambahkan materi pembelajaran Al-Qur'an, Hadist, atau Materi Tambahan.</p>
            <a href="{{ route('admin.materi.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Tambah Materi Pertama
            </a>
        </div>
    @endif
</div>
@endsection