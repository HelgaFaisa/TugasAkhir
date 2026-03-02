@extends('layouts.app')

@section('title', 'Tambah Kelas Baru')

@section('content')
<div class="page-header">
    <h2><i class="fas fa-plus"></i> Tambah Kelas Baru</h2>
</div>

<div class="content-box">
    <form action="{{ route('admin.kelas.store') }}" method="POST">
        @csrf

        <!-- Kode Kelas (Read-only, Auto-generated) -->
        <div class="form-group">
            <label for="kode_kelas">
                Kode Kelas <span class="text-muted">(Auto-generate)</span>
            </label>
            <input type="text" 
                   class="form-control" 
                   id="kode_kelas" 
                   value="{{ $nextKodeKelas }}" 
                   readonly
                   style="background-color: #e9ecef;">
            <small class="form-text text-muted">
                Kode kelas akan dibuat otomatis oleh sistem.
            </small>
        </div>

        <!-- Nama Kelas -->
        <div class="form-group">
            <label for="nama_kelas">
                Nama Kelas <span class="text-danger">*</span>
            </label>
            <input type="text" 
                   class="form-control @error('nama_kelas') is-invalid @enderror" 
                   id="nama_kelas" 
                   name="nama_kelas" 
                   value="{{ old('nama_kelas') }}" 
                   placeholder="Contoh: Lambatan, cepatan, SD 1, SMP 7"
                   required
                   autofocus>
            @error('nama_kelas')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            <small class="form-text text-muted">
                Nama kelas harus unik dan tidak boleh sama dengan kelas lain.
            </small>
        </div>

        <!-- Kelompok Kelas -->
        <div class="form-group">
            <label for="id_kelompok">
                Kelompok Kelas <span class="text-danger">*</span>
            </label>
            <select class="form-control @error('id_kelompok') is-invalid @enderror" 
                    id="id_kelompok" 
                    name="id_kelompok" 
                    required>
                <option value="">-- Pilih Kelompok Kelas --</option>
                @foreach ($kelompokKelas as $kelompok)
                    <option value="{{ $kelompok->id_kelompok }}" 
                            {{ old('id_kelompok') == $kelompok->id_kelompok ? 'selected' : '' }}>
                        {{ $kelompok->nama_kelompok }}
                    </option>
                @endforeach
            </select>
            @error('id_kelompok')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
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
                Urutan tampilan kelas dalam kelompok (0 = pertama). Semakin kecil semakin awal.
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
                Kelas aktif dapat digunakan untuk santri dan kegiatan. Nonaktifkan jika kelas sudah tidak dipakai.
            </small>
        </div>

        <hr>

        <!-- Action Buttons -->
        <div class="form-group" style="display: flex; gap: 10px;">
            <button type="submit" class="btn btn-success">
                <i class="fas fa-save"></i> Simpan
            </button>
            <a href="{{ route('admin.kelas.index') }}" class="btn btn-secondary">
                <i class="fas fa-times"></i> Batal
            </a>
        </div>
    </form>
</div>

@endsection

@section('scripts')
<script>
    // Auto-focus on nama_kelas input
    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('nama_kelas').focus();
    });
</script>
@endsection
