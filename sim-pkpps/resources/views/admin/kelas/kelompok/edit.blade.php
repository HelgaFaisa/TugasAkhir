@extends('layouts.app')

@section('title', 'Edit Kelompok Kelas')

@section('content')
<div class="page-header">
    <h2><i class="fas fa-edit"></i> Edit Kelompok Kelas</h2>
</div>

<div class="content-box">
    <form action="{{ route('admin.kelas.kelompok.update', $kelompok->id) }}" method="POST">
        @csrf
        @method('PUT')

        <!-- Kode Kelompok (Read-only, Cannot be changed) -->
        <div class="form-group">
            <label for="id_kelompok">
                Kode Kelompok <span class="text-muted">(Tidak dapat diubah)</span>
            </label>
            <input type="text" 
                   class="form-control" 
                   id="id_kelompok" 
                   value="{{ $kelompok->id_kelompok }}" 
                   readonly
                   style="background-color: #e9ecef;">
            <small class="form-text text-muted">
                Kode kelompok tidak dapat diubah setelah dibuat.
            </small>
        </div>

        <!-- Nama Kelompok -->
        <div class="form-group">
            <label for="nama_kelompok">
                Nama Kelompok <span class="text-danger">*</span>
            </label>
            <input type="text" 
                   class="form-control @error('nama_kelompok') is-invalid @enderror" 
                   id="nama_kelompok" 
                   name="nama_kelompok" 
                   value="{{ old('nama_kelompok', $kelompok->nama_kelompok) }}" 
                   placeholder="Contoh: Kelas Pondok, Sekolah Formal, Kelas Umum"
                   required
                   autofocus>
            @error('nama_kelompok')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            <small class="form-text text-muted">
                Nama kelompok harus unik dan menggambarkan kategori kelas.
            </small>
        </div>

        <!-- Deskripsi -->
        <div class="form-group">
            <label for="deskripsi">
                Deskripsi
            </label>
            <textarea class="form-control @error('deskripsi') is-invalid @enderror" 
                      id="deskripsi" 
                      name="deskripsi" 
                      rows="3"
                      placeholder="Deskripsi kelompok kelas (opsional)">{{ old('deskripsi', $kelompok->deskripsi) }}</textarea>
            @error('deskripsi')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            <small class="form-text text-muted">
                Tambahkan penjelasan singkat tentang kelompok kelas ini.
            </small>
        </div>

        <!-- Urutan -->
        <div class="form-group">
            <label for="urutan">
                Urutan <span class="text-danger">*</span>
            </label>
            <input type="number" 
                   class="form-control @error('urutan') is-invalid @enderror" 
                   id="urutan" 
                   name="urutan" 
                   value="{{ old('urutan', $kelompok->urutan) }}" 
                   min="0"
                   required>
            @error('urutan')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            <small class="form-text text-muted">
                Urutan tampilan kelompok (0 = pertama). Semakin kecil semakin awal ditampilkan.
            </small>
        </div>

        <!-- Status Aktif -->
        <div class="form-group">
            <div class="form-check">
                <input type="checkbox" 
                       class="form-check-input" 
                       id="is_active" 
                       name="is_active"
                       value="1"
                       {{ old('is_active', $kelompok->is_active) ? 'checked' : '' }}>
                <label class="form-check-label" for="is_active">
                    Status Aktif
                </label>
            </div>
            <small class="form-text text-muted">
                Kelompok aktif dapat digunakan untuk membuat kelas baru.
            </small>
        </div>

        <hr>

        <!-- Info: Usage Statistics -->
        @if ($kelompok->kelas()->count() > 0)
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> <strong>Info Penggunaan Kelompok:</strong>
                <ul class="mb-0 mt-2">
                    <li>Total Kelas: <strong>{{ $kelompok->kelas()->count() }}</strong></li>
                    <li>Kelas Aktif: <strong>{{ $kelompok->kelas()->where('is_active', true)->count() }}</strong></li>
                    @if ($kelompok->kelas()->count() > 0)
                        <li class="text-warning mt-2">
                            <i class="fas fa-exclamation-triangle"></i> 
                            Jika kelompok dinonaktifkan, kelas-kelas di dalamnya tetap ada tapi tidak dapat digunakan untuk santri baru
                        </li>
                    @endif
                </ul>
            </div>
        @endif

        <!-- Action Buttons -->
        <div class="form-group" style="display: flex; gap: 10px;">
            <button type="submit" class="btn btn-success">
                <i class="fas fa-save"></i> Simpan Perubahan
            </button>
            <a href="{{ route('admin.kelas.kelompok.index') }}" class="btn btn-secondary">
                <i class="fas fa-times"></i> Batal
            </a>
        </div>
    </form>
</div>

@endsection

@section('scripts')
<script>
    // Auto-focus on nama_kelompok input
    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('nama_kelompok').focus();
    });
</script>
@endsection