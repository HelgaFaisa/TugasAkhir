@extends('layouts.app')

@section('title', 'Klasifikasi Pelanggaran')

@section('content')
<div class="page-header">
    <h2><i class="fas fa-tags"></i> Klasifikasi Pelanggaran</h2>
</div>

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

<div class="content-box">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h3 style="margin: 0; color: var(--primary-color);">
            <i class="fas fa-list"></i> Daftar Klasifikasi
        </h3>
        <div style="display: flex; gap: 10px;">
            <a href="{{ route('admin.kategori-pelanggaran.index') }}" class="btn btn-info">
                <i class="fas fa-list-ul"></i> Master Pelanggaran
            </a>
            <a href="{{ route('admin.klasifikasi-pelanggaran.create') }}" class="btn btn-primary">
                <i class="fas fa-plus-circle"></i> Tambah Klasifikasi
            </a>
        </div>
    </div>

    @if($data->isNotEmpty())
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 50px;">No</th>
                    <th style="width: 100px;">ID</th>
                    <th>Nama Klasifikasi</th>
                    <th style="width: 120px; text-align: center;">Jumlah Pelanggaran</th>
                    <th style="width: 80px; text-align: center;">Urutan</th>
                    <th style="width: 100px; text-align: center;">Status</th>
                    <th style="width: 200px; text-align: center;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data as $index => $item)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td><span class="badge badge-primary">{{ $item->id_klasifikasi }}</span></td>
                        <td>
                            <strong>{{ $item->nama_klasifikasi }}</strong>
                            @if($item->deskripsi)
                                <br><small style="color: var(--text-light);">{{ Str::limit($item->deskripsi, 80) }}</small>
                            @endif
                        </td>
                        <td style="text-align: center;">
                            <span class="badge badge-info">{{ $item->pelanggarans_count }}</span>
                        </td>
                        <td style="text-align: center;">{{ $item->urutan }}</td>
                        <td style="text-align: center;">
                            @if($item->is_active)
                                <span class="badge badge-success">Aktif</span>
                            @else
                                <span class="badge badge-secondary">Nonaktif</span>
                            @endif
                        </td>
                        <td style="text-align: center;">
                            <div style="display: flex; justify-content: center; gap: 8px;">
                                <a href="{{ route('admin.klasifikasi-pelanggaran.show', $item) }}" 
                                   class="btn btn-sm btn-success" 
                                   title="Detail">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.klasifikasi-pelanggaran.edit', $item) }}" 
                                   class="btn btn-sm btn-warning" 
                                   title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.klasifikasi-pelanggaran.destroy', $item) }}" 
                                      method="POST" 
                                      style="display: inline;"
                                      onsubmit="return confirm('Yakin ingin menghapus?');">
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
    @else
        <div class="empty-state">
            <i class="fas fa-folder-open"></i>
            <h3>Belum ada klasifikasi</h3>
            <p>Mulai dengan menambahkan klasifikasi pelanggaran.</p>
            <a href="{{ route('admin.klasifikasi-pelanggaran.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Tambah Klasifikasi
            </a>
        </div>
    @endif
</div>
@endsection