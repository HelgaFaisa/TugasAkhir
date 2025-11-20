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

{{-- Filter & Search Section --}}
<div class="content-box" style="margin-bottom: 20px;">
    <form method="GET" action="{{ route('admin.materi.index') }}" class="filter-form-inline">
        <select name="kategori" class="form-control" style="width: 200px;">
            <option value="">Semua Kategori</option>
            <option value="Al-Qur'an" {{ request('kategori') == 'Al-Qur\'an' ? 'selected' : '' }}>Al-Qur'an</option>
            <option value="Hadist" {{ request('kategori') == 'Hadist' ? 'selected' : '' }}>Hadist</option>
            <option value="Materi Tambahan" {{ request('kategori') == 'Materi Tambahan' ? 'selected' : '' }}>Materi Tambahan</option>
        </select>

        <select name="kelas" class="form-control" style="width: 180px;">
            <option value="">Semua Kelas</option>
            <option value="Lambatan" {{ request('kelas') == 'Lambatan' ? 'selected' : '' }}>Lambatan</option>
            <option value="Cepatan" {{ request('kelas') == 'Cepatan' ? 'selected' : '' }}>Cepatan</option>
            <option value="PB" {{ request('kelas') == 'PB' ? 'selected' : '' }}>PB</option>
        </select>

        <input type="text" name="search" class="form-control" placeholder="Cari nama kitab..." 
               value="{{ request('search') }}" style="width: 250px;">

        <button type="submit" class="btn btn-primary">
            <i class="fas fa-search"></i> Filter
        </button>

        @if(request()->anyFilled(['kategori', 'kelas', 'search']))
            <a href="{{ route('admin.materi.index') }}" class="btn btn-secondary">
                <i class="fas fa-redo"></i> Reset
            </a>
        @endif

        <a href="{{ route('admin.materi.create') }}" class="btn btn-success" style="margin-left: auto;">
            <i class="fas fa-plus"></i> Tambah Materi
        </a>
    </form>
</div>

{{-- Table Section --}}
<div class="content-box">
    @if($materis->count() > 0)
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
                            <div class="btn-group">
                                <a href="{{ route('admin.materi.show', $materi) }}" 
                                   class="btn btn-sm btn-info" title="Detail">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.materi.edit', $materi) }}" 
                                   class="btn btn-sm btn-warning" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.materi.destroy', $materi) }}" 
                                      method="POST" style="display: inline-block;"
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

        {{-- Pagination --}}
        <div style="margin-top: 20px;">
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