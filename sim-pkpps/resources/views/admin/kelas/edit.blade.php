@extends('layouts.app')

@section('title', 'Edit Kelas')

@section('content')
<div class="page-header">
    <h2><i class="fas fa-edit"></i> Edit Kelas</h2>
</div>

<div class="content-box">
    <form action="{{ route('admin.kelas.update', $kela->id) }}" method="POST">
        @csrf
        @method('PUT')

        <!-- Kode Kelas (Read-only, Cannot be changed) -->
        <div class="form-group">
            <label for="kode_kelas">
                Kode Kelas <span class="text-muted">(Tidak dapat diubah)</span>
            </label>
            <input type="text" 
                   class="form-control" 
                   id="kode_kelas" 
                   value="{{ $kela->kode_kelas }}" 
                   readonly
                   style="background-color: #e9ecef;">
            <small class="form-text text-muted">
                Kode kelas tidak dapat diubah setelah dibuat.
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
                   value="{{ old('nama_kelas', $kela->nama_kelas) }}" 
                   placeholder="Contoh: PB, Lambatan, SD 1, SMP 7"
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
                            {{ old('id_kelompok', $kela->id_kelompok) == $kelompok->id_kelompok ? 'selected' : '' }}>
                        {{ $kelompok->nama_kelompok }}
                    </option>
                @endforeach
            </select>
            @error('id_kelompok')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            <small class="form-text text-muted">
                Pilih kelompok kelas: Kelas Pondok, Sekolah Formal, atau Umum.
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
                   value="{{ old('urutan', $kela->urutan) }}" 
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
                       {{ old('is_active', $kelas->is_active) ? 'checked' : '' }}>
                <label class="form-check-label" for="is_active">
                    Status Aktif
                </label>
            </div>
            <small class="form-text text-muted">
                Kelas aktif dapat digunakan untuk santri dan kegiatan. Nonaktifkan jika kelas sudah tidak dipakai.
            </small>
        </div>

        <hr>

        <!-- Info: Usage Statistics -->
        @if ($kela->santriKelas()->count() > 0 || $kela->kegiatans()->count() > 0)
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> <strong>Info Penggunaan Kelas:</strong>
                <ul class="mb-0 mt-2">
                    @if ($kela->santriKelas()->count() > 0)
                        <li>Digunakan oleh <strong>{{ $kela->santriKelas()->count() }} santri</strong></li>
                    @endif
                    @if ($kela->kegiatans()->count() > 0)
                        <li>Memiliki <strong>{{ $kela->kegiatans()->count() }} kegiatan</strong></li>
                    @endif
                </ul>
            </div>
        @endif

        <!-- Action Buttons -->
        <div class="form-group" style="display: flex; gap: 10px;">
            <button type="submit" class="btn btn-success">
                <i class="fas fa-save"></i> Simpan Perubahan
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
