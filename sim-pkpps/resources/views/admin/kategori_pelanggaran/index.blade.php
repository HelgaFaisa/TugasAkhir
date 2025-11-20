{{-- resources/views/admin/kategori_pelanggaran/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Data Kategori Pelanggaran')

@section('content')
<div class="page-header">
    <h2><i class="fas fa-list-ul"></i> Kategori Pelanggaran</h2>
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

<!-- Form Tambah & Edit -->
<div class="content-box" style="margin-bottom: 30px;">
    <h3 style="margin-bottom: 20px; color: var(--primary-color);">
        @if(isset($kategori))
            <i class="fas fa-edit"></i> Edit Kategori
        @else
            <i class="fas fa-plus-circle"></i> Tambah Kategori
        @endif
    </h3>
    <form action="@if(isset($kategori)){{ route('admin.kategori-pelanggaran.update', $kategori) }}@else{{ route('admin.kategori-pelanggaran.store') }}@endif" method="POST">
        @csrf
        @if(isset($kategori))
            @method('PUT')
        @endif

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
            <div class="form-group">
                <label for="nama_pelanggaran">
                    <i class="fas fa-exclamation-triangle form-icon"></i>
                    Nama Pelanggaran <span style="color: var(--danger-color);">*</span>
                </label>
                <input type="text" 
                       name="nama_pelanggaran" 
                       id="nama_pelanggaran"
                       class="form-control @error('nama_pelanggaran') is-invalid @enderror"
                       value="{{ old('nama_pelanggaran', $kategori->nama_pelanggaran ?? '') }}"
                       placeholder="Contoh: Terlambat Sholat, Tidak Rapi"
                       required>
                @error('nama_pelanggaran')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="poin">
                    <i class="fas fa-star form-icon"></i>
                    Poin Pelanggaran <span style="color: var(--danger-color);">*</span>
                </label>
                <input type="number" 
                       name="poin" 
                       id="poin" 
                       min="1" 
                       max="100"
                       class="form-control @error('poin') is-invalid @enderror"
                       value="{{ old('poin', $kategori->poin ?? '') }}"
                       placeholder="1-100"
                       required>
                @error('poin')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
                <span class="form-text">Poin antara 1-100</span>
            </div>
        </div>

        <div class="btn-group">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i>
                @if(isset($kategori)) Update @else Simpan @endif
            </button>
            @if(isset($kategori))
                <a href="{{ route('admin.kategori-pelanggaran.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Batal
                </a>
            @endif
        </div>
    </form>
</div>

<!-- Tabel Data -->
<div class="content-box">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h3 style="margin: 0; color: var(--primary-color);">
            <i class="fas fa-table"></i> Daftar Kategori Pelanggaran
        </h3>
        <span class="badge badge-info" style="font-size: 0.9em;">
            Total: {{ $data->count() }} Kategori
        </span>
    </div>

    @if($data->isNotEmpty())
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 50px;">No</th>
                    <th style="width: 120px;">ID Kategori</th>
                    <th>Nama Pelanggaran</th>
                    <th style="width: 120px; text-align: center;">Poin</th>
                    <th style="width: 100px; text-align: center;">Digunakan</th>
                    <th style="width: 200px; text-align: center;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data as $index => $item)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>
                            <span class="badge badge-primary">{{ $item->id_kategori }}</span>
                        </td>
                        <td>
                            <strong>{{ $item->nama_pelanggaran }}</strong>
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
                                      onsubmit="return confirm('Yakin ingin menghapus kategori {{ $item->nama_pelanggaran }}?');">
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
    @else
        <div class="empty-state">
            <i class="fas fa-folder-open"></i>
            <h3>Belum ada data kategori pelanggaran</h3>
            <p>Mulai dengan menambahkan kategori pelanggaran baru menggunakan form di atas.</p>
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