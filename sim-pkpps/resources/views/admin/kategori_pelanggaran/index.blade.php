@extends('layouts.app')

@section('title', 'Master Pelanggaran')

@section('content')
<div class="page-header">
    <h2><i class="fas fa-list-ul"></i> Kategori Pelanggaran</h2>
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

<!-- Filter -->
<div class="content-box" style="margin-bottom: 14px;">
    <form method="GET" action="{{ route('admin.kategori-pelanggaran.index') }}">
        <div style="display: grid; grid-template-columns: 2fr 1fr auto; gap: 11px; align-items: end;">
            <div class="form-group" style="margin-bottom: 0;">
                <label for="id_klasifikasi">
                    <i class="fas fa-filter form-icon"></i>
                    Filter Klasifikasi
                </label>
                <select name="id_klasifikasi" id="id_klasifikasi" class="form-control">
                    <option value="">-- Semua Klasifikasi --</option>
                    @foreach($klasifikasiList as $kl)
                        <option value="{{ $kl->id_klasifikasi }}" 
                                {{ request('id_klasifikasi') == $kl->id_klasifikasi ? 'selected' : '' }}>
                            {{ $kl->nama_klasifikasi }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group" style="margin-bottom: 0;">
                <label for="is_active">
                    <i class="fas fa-toggle-on form-icon"></i>
                    Status
                </label>
                <select name="is_active" id="is_active" class="form-control">
                    <option value="">-- Semua Status --</option>
                    <option value="1" {{ request('is_active') == '1' ? 'selected' : '' }}>Aktif</option>
                    <option value="0" {{ request('is_active') == '0' ? 'selected' : '' }}>Nonaktif</option>
                </select>
            </div>

            <div class="btn-group" style="margin-bottom: 0;">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search"></i> Filter
                </button>
                <a href="{{ route('admin.kategori-pelanggaran.index') }}" class="btn btn-secondary">
                    <i class="fas fa-redo"></i> Reset
                </a>
            </div>
        </div>
    </form>
</div>

<!-- Tabel Data -->
<div class="content-box">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 14px;">
        <h3 style="margin: 0; color: var(--primary-color);">
            <i class="fas fa-table"></i> Daftar Pelanggaran
        </h3>
        <div style="display: flex; gap: 10px;">
            <a href="{{ route('admin.klasifikasi-pelanggaran.index') }}" class="btn btn-warning">
                <i class="fas fa-tags"></i> Klasifikasi Pelanggaran
            </a>
            <a href="{{ route('admin.kategori-pelanggaran.create') }}" class="btn btn-primary">
                <i class="fas fa-plus-circle"></i> Tambah Pelanggaran
            </a>
        </div>
    </div>

    @if($data->isNotEmpty())
        <div class="table-wrapper">
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 50px;">No</th>
                    <th style="width: 100px;">ID</th>
                    <th style="width: 150px;">Klasifikasi</th>
                    <th>Nama Pelanggaran</th>
                    <th style="width: 80px; text-align: center;">Poin</th>
                    <th style="width: 100px; text-align: center;">Digunakan</th>
                    <th style="width: 100px; text-align: center;">Status</th>
                    <th style="width: 200px; text-align: center;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data as $index => $item)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td><span class="badge badge-primary">{{ $item->id_kategori }}</span></td>
                        <td>
                            @if($item->klasifikasi)
                                <span class="badge badge-info">{{ $item->klasifikasi->nama_klasifikasi }}</span>
                            @else
                                <span class="badge badge-secondary">-</span>
                            @endif
                        </td>
                        <td>
                            <strong>{{ $item->nama_pelanggaran }}</strong>
                            @if($item->kafaroh)
                                <br><small style="color: var(--text-light);">
                                    <i class="fas fa-hands"></i> Kafaroh: {{ Str::limit($item->kafaroh, 50) }}
                                </small>
                            @endif
                        </td>
                        <td style="text-align: center;">
                            <span class="badge badge-danger" style="font-size: 0.9em;">
                                <i class="fas fa-star"></i> {{ $item->poin }}
                            </span>
                        </td>
                        <td style="text-align: center;">
                            <span class="badge badge-secondary">
                                {{ $item->riwayatPelanggaran->count() }}x
                            </span>
                        </td>
                        <td style="text-align: center;">
                            @if($item->is_active)
                                <span class="badge badge-success">Aktif</span>
                            @else
                                <span class="badge badge-secondary">Nonaktif</span>
                            @endif
                        </td>
                        <td style="text-align: center;">
                            <div style="display: flex; justify-content: center; gap: 8px;">
                                <a href="{{ route('admin.kategori-pelanggaran.show', $item) }}" 
                                   class="btn btn-sm btn-success" 
                                   title="Detail">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.kategori-pelanggaran.edit', $item) }}" 
                                   class="btn btn-sm btn-warning" 
                                   title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.kategori-pelanggaran.destroy', $item) }}" 
                                      method="POST" 
                                      style="display: inline;"
                                      onsubmit="return confirm('Yakin ingin menghapus pelanggaran {{ $item->nama_pelanggaran }}?');">
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
    @else
        <div class="empty-state">
            <i class="fas fa-folder-open"></i>
            <h3>Belum ada data pelanggaran</h3>
            <p>Silakan tambah pelanggaran baru menggunakan tombol di atas.</p>
            <a href="{{ route('admin.kategori-pelanggaran.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Tambah Pelanggaran
            </a>
        </div>
    @endif
</div>

<script>
// Auto hide alerts after 5 seconds
setTimeout(function() {
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        alert.style.transition = 'opacity 0.5s';
        alert.style.opacity = '0';
        setTimeout(() => alert.remove(), 500);
    });
}, 5000);
</script>
@endsection