@extends('layouts.app')

@section('title', 'Tambah Kelompok Kelas')

@section('content')
<div class="page-header">
    <h2><i class="fas fa-plus"></i> Tambah Kelompok Kelas Baru</h2>
</div>

<div class="content-box">
    <form action="{{ route('admin.kelas.kelompok.store') }}" method="POST">
        @csrf

        <!-- Kode Kelompok (Read-only, Auto-generated) -->
        <div class="form-group">
            <label for="id_kelompok">
                Kode Kelompok <span class="text-muted">(Auto-generate)</span>
            </label>
            <input type="text" 
                   class="form-control" 
                   id="id_kelompok" 
                   value="{{ $nextIdKelompok }}" 
                   readonly
                   style="background-color: #e9ecef;">
            <small class="form-text text-muted">
                Kode kelompok akan dibuat otomatis oleh sistem.
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
                   value="{{ old('nama_kelompok') }}" 
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
                      placeholder="Deskripsi kelompok kelas (opsional)">{{ old('deskripsi') }}</textarea>
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
                   value="{{ old('urutan', 0) }}" 
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
                       {{ old('is_active', true) ? 'checked' : '' }}>
                <label class="form-check-label" for="is_active">
                    Status Aktif
                </label>
            </div>
            <small class="form-text text-muted">
                Kelompok aktif dapat digunakan untuk membuat kelas baru.
            </small>
        </div>

        <hr>

        

        <!-- Action Buttons -->
        <div class="form-group" style="display: flex; gap: 10px;">
            <button type="submit" class="btn btn-success">
                <i class="fas fa-save"></i> Simpan
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