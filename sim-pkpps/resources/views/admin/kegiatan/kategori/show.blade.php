@extends('layouts.app')

@section('content')
<div class="page-header">
    <h2><i class="fas fa-list-alt"></i> Detail Kategori Kegiatan</h2>
</div>

<div class="content-box">
    <div class="detail-header">
        <h3>{{ $kategoriKegiatan->nama_kategori }}</h3>
        <div style="display: flex; gap: 10px;">
            <a href="{{ route('admin.kategori-kegiatan.edit', $kategoriKegiatan) }}" class="btn btn-warning">
                <i class="fas fa-edit"></i> Edit
            </a>
            <form action="{{ route('admin.kategori-kegiatan.destroy', $kategoriKegiatan) }}" method="POST" style="display: inline;" onsubmit="return confirm('Yakin ingin menghapus kategori ini?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">
                    <i class="fas fa-trash"></i> Hapus
                </button>
            </form>
            <a href="{{ route('admin.kategori-kegiatan.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </div>

    <div class="detail-section">
        <h4><i class="fas fa-info-circle"></i> Informasi Kategori</h4>
        <table class="detail-table">
            <tr>
                <th>ID Kategori</th>
                <td><strong>{{ $kategoriKegiatan->kategori_id }}</strong></td>
            </tr>
            <tr>
                <th>Nama Kategori</th>
                <td>{{ $kategoriKegiatan->nama_kategori }}</td>
            </tr>
            <tr>
                <th>Keterangan</th>
                <td>{{ $kategoriKegiatan->keterangan ?? '-' }}</td>
            </tr>
            <tr>
                <th>Dibuat Pada</th>
                <td>{{ $kategoriKegiatan->created_at->format('d F Y, H:i') }} WIB</td>
            </tr>
            <tr>
                <th>Terakhir Diubah</th>
                <td>{{ $kategoriKegiatan->updated_at->format('d F Y, H:i') }} WIB</td>
            </tr>
        </table>
    </div>
</div>
@endsection