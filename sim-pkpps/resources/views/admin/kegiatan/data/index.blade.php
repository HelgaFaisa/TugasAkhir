@extends('layouts.app')

@section('content')
<div class="page-header">
    <h2><i class="fas fa-calendar-alt"></i> Jadwal Kegiatan Santri</h2>
</div>

@if(session('success'))
    <div class="alert alert-success">
        <i class="fas fa-check-circle"></i> {{ session('success') }}
    </div>
@endif

<div class="content-box">
    <div style="margin-bottom: 20px;">
        <form method="GET" class="filter-form-inline">
            <select name="hari" class="form-control">
                <option value="">-- Semua Hari --</option>
                @foreach($hariList as $h)
                    <option value="{{ $h }}" {{ request('hari') == $h ? 'selected' : '' }}>{{ $h }}</option>
                @endforeach
            </select>

            <select name="kategori_id" class="form-control">
                <option value="">-- Semua Kategori --</option>
                @foreach($kategoris as $kat)
                    <option value="{{ $kat->kategori_id }}" {{ request('kategori_id') == $kat->kategori_id ? 'selected' : '' }}>
                        {{ $kat->nama_kategori }}
                    </option>
                @endforeach
            </select>

            <input type="text" name="search" class="form-control" placeholder="Cari kegiatan..." value="{{ request('search') }}" style="min-width: 200px;">

            <button type="submit" class="btn btn-primary">
                <i class="fas fa-filter"></i> Filter
            </button>

            @if(request()->hasAny(['hari', 'kategori_id', 'search']))
                <a href="{{ route('admin.kegiatan.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Reset
                </a>
            @endif

            <a href="{{ route('admin.kegiatan.create') }}" class="btn btn-success" style="margin-left: auto;">
                <i class="fas fa-plus"></i> Tambah Kegiatan
            </a>
        </form>
    </div>

    @if($kegiatans->count() > 0)
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 50px;">No</th>
                    <th style="width: 100px;">ID</th>
                    <th style="width: 100px;">Hari</th>
                    <th style="width: 120px;">Waktu</th>
                    <th>Nama Kegiatan</th>
                    <th style="width: 150px;">Kategori</th>
                    <th>Materi</th>
                    <th style="width: 180px; text-align: center;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($kegiatans as $index => $kegiatan)
                <tr>
                    <td>{{ $kegiatans->firstItem() + $index }}</td>
                    <td><strong>{{ $kegiatan->kegiatan_id }}</strong></td>
                    <td><span class="badge badge-primary">{{ $kegiatan->hari }}</span></td>
                    <td>{{ date('H:i', strtotime($kegiatan->waktu_mulai)) }} - {{ date('H:i', strtotime($kegiatan->waktu_selesai)) }}</td>
                    <td><strong>{{ $kegiatan->nama_kegiatan }}</strong></td>
                    <td>{{ $kegiatan->kategori->nama_kategori }}</td>
                    <td>{{ Str::limit($kegiatan->materi, 40) ?? '-' }}</td>
                    <td class="text-center">
                        <a href="{{ route('admin.kegiatan.show', $kegiatan) }}" class="btn btn-sm btn-primary" title="Detail">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="{{ route('admin.kegiatan.edit', $kegiatan) }}" class="btn btn-sm btn-warning" title="Edit">
                            <i class="fas fa-edit"></i>
                        </a>
                        <form action="{{ route('admin.kegiatan.destroy', $kegiatan) }}" method="POST" style="display: inline-block;" onsubmit="return confirm('Yakin ingin menghapus kegiatan ini?')">
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
            {{ $kegiatans->links() }}
        </div>
    @else
        <div class="empty-state">
            <i class="fas fa-calendar-times"></i>
            <h3>Belum Ada Kegiatan</h3>
            <p>Silakan tambahkan jadwal kegiatan santri.</p>
            <a href="{{ route('admin.kegiatan.create') }}" class="btn btn-success">
                <i class="fas fa-plus"></i> Tambah Kegiatan
            </a>
        </div>
    @endif
</div>
@endsection