@extends('layouts.app')

@section('content')
<div class="page-header">
    <h2><i class="fas fa-list-alt"></i> Kategori Kegiatan</h2>
</div>

@if(session('success'))
    <div class="alert alert-success">
        <i class="fas fa-check-circle"></i> {{ session('success') }}
    </div>
@endif

<div class="content-box">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <form method="GET" style="flex: 1; max-width: 400px;">
            <div style="display: flex; gap: 10px;">
                <input type="text" name="search" class="form-control" placeholder="Cari kategori..." value="{{ request('search') }}">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search"></i>
                </button>
                @if(request('search'))
                    <a href="{{ route('admin.kategori-kegiatan.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i>
                    </a>
                @endif
            </div>
        </form>
        <a href="{{ route('admin.kategori-kegiatan.create') }}" class="btn btn-success">
            <i class="fas fa-plus"></i> Tambah Kategori
        </a>
    </div>

    @if($kategoris->count() > 0)
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 60px;">No</th>
                    <th style="width: 120px;">ID Kategori</th>
                    <th>Nama Kategori</th>
                    <th>Keterangan</th>
                    <th style="width: 150px;">Dibuat</th>
                    <th style="width: 180px; text-align: center;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($kategoris as $index => $kategori)
                <tr>
                    <td>{{ $kategoris->firstItem() + $index }}</td>
                    <td><strong>{{ $kategori->kategori_id }}</strong></td>
                    <td>{{ $kategori->nama_kategori }}</td>
                    <td>{{ Str::limit($kategori->keterangan, 50) ?? '-' }}</td>
                    <td>{{ $kategori->created_at->format('d M Y') }}</td>
                    <td class="text-center">
                        <a href="{{ route('admin.kategori-kegiatan.show', $kategori) }}" class="btn btn-sm btn-primary" title="Detail">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="{{ route('admin.kategori-kegiatan.edit', $kategori) }}" class="btn btn-sm btn-warning" title="Edit">
                            <i class="fas fa-edit"></i>
                        </a>
                        <form action="{{ route('admin.kategori-kegiatan.destroy', $kategori) }}" method="POST" style="display: inline-block;" onsubmit="return confirm('Yakin ingin menghapus kategori ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger" title="Hapus">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div style="margin-top: 20px;">
            {{ $kategoris->links() }}
        </div>
    @else
        <div class="empty-state">
            <i class="fas fa-folder-open"></i>
            <h3>Belum Ada Kategori</h3>
            <p>Silakan tambahkan kategori kegiatan baru.</p>
            <a href="{{ route('admin.kategori-kegiatan.create') }}" class="btn btn-success">
                <i class="fas fa-plus"></i> Tambah Kategori
            </a>
        </div>
    @endif
</div>
@endsection