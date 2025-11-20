@extends('layouts.app')

@section('content')
<div class="page-header">
    <h2><i class="fas fa-calendar-alt"></i> Detail Kegiatan</h2>
</div>

<div class="content-box">
    <div class="detail-header">
        <h3>{{ $kegiatan->nama_kegiatan }}</h3>
        <div style="display: flex; gap: 10px;">
            <a href="{{ route('admin.kegiatan.edit', $kegiatan) }}" class="btn btn-warning">
                <i class="fas fa-edit"></i> Edit
            </a>
            <form action="{{ route('admin.kegiatan.destroy', $kegiatan) }}" method="POST" style="display: inline;" onsubmit="return confirm('Yakin ingin menghapus kegiatan ini?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">
                    <i class="fas fa-trash"></i> Hapus
                </button>
            </form>
            <a href="{{ route('admin.kegiatan.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </div>

    <div class="detail-section">
        <h4><i class="fas fa-info-circle"></i> Informasi Kegiatan</h4>
        <table class="detail-table">
            <tr>
                <th>ID Kegiatan</th>
                <td><strong>{{ $kegiatan->kegiatan_id }}</strong></td>
            </tr>
            <tr>
                <th>Kategori</th>
                <td>
                    <span class="badge badge-primary badge-lg">{{ $kegiatan->kategori->nama_kategori }}</span>
                </td>
            </tr>
            <tr>
                <th>Nama Kegiatan</th>
                <td><strong>{{ $kegiatan->nama_kegiatan }}</strong></td>
            </tr>
            <tr>
                <th>Hari</th>
                <td><span class="badge badge-info badge-lg">{{ $kegiatan->hari }}</span></td>
            </tr>
            <tr>
                <th>Waktu Pelaksanaan</th>
                <td>
                    <i class="fas fa-clock" style="color: var(--primary-color);"></i> 
                    {{ date('H:i', strtotime($kegiatan->waktu_mulai)) }} - {{ date('H:i', strtotime($kegiatan->waktu_selesai)) }} WIB
                </td>
            </tr>
            <tr>
                <th>Materi/Topik</th>
                <td>{{ $kegiatan->materi ?? '-' }}</td>
            </tr>
            <tr>
                <th>Keterangan</th>
                <td>{{ $kegiatan->keterangan ?? '-' }}</td>
            </tr>
            <tr>
                <th>Dibuat Pada</th>
                <td>{{ $kegiatan->created_at->format('d F Y, H:i') }} WIB</td>
            </tr>
            <tr>
                <th>Terakhir Diubah</th>
                <td>{{ $kegiatan->updated_at->format('d F Y, H:i') }} WIB</td>
            </tr>
        </table>
    </div>
</div>
@endsection