@extends('layouts.app')

@section('title', 'Edit Klasifikasi Pelanggaran')

@section('content')
<div class="page-header">
    <h2><i class="fas fa-edit"></i> Edit Klasifikasi Pelanggaran</h2>
</div>

<div class="content-box">
    <form action="{{ route('admin.klasifikasi-pelanggaran.update', $klasifikasi) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label>
                <i class="fas fa-id-card form-icon"></i>
                ID Klasifikasi
            </label>
            <input type="text" class="form-control" value="{{ $klasifikasi->id_klasifikasi }}" disabled>
        </div>

        <div class="form-group">
            <label for="nama_klasifikasi">
                <i class="fas fa-tag form-icon"></i>
                Nama Klasifikasi <span style="color: var(--danger-color);">*</span>
            </label>
            <input type="text" 
                   name="nama_klasifikasi" 
                   id="nama_klasifikasi"
                   class="form-control @error('nama_klasifikasi') is-invalid @enderror"
                   value="{{ old('nama_klasifikasi', $klasifikasi->nama_klasifikasi) }}"
                   required>
            @error('nama_klasifikasi')
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label for="deskripsi">
                <i class="fas fa-align-left form-icon"></i>
                Deskripsi
            </label>
            <textarea name="deskripsi" 
                      id="deskripsi"
                      class="form-control @error('deskripsi') is-invalid @enderror"
                      rows="4">{{ old('deskripsi', $klasifikasi->deskripsi) }}</textarea>
            @error('deskripsi')
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <div class="form-group">
                <label for="urutan">
                    <i class="fas fa-sort-numeric-up form-icon"></i>
                    Urutan Tampilan
                </label>
                <input type="number" 
                       name="urutan" 
                       id="urutan"
                       class="form-control @error('urutan') is-invalid @enderror"
                       value="{{ old('urutan', $klasifikasi->urutan) }}"
                       min="0">
                @error('urutan')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label>
                    <i class="fas fa-toggle-on form-icon"></i>
                    Status
                </label>
                <div style="margin-top: 12px;">
                    <label style="display: flex; align-items: center; cursor: pointer;">
                        <input type="checkbox" 
                               name="is_active" 
                               value="1" 
                               {{ old('is_active', $klasifikasi->is_active) ? 'checked' : '' }}
                               style="margin-right: 8px;">
                        <span>Aktif</span>
                    </label>
                </div>
            </div>
        </div>

        <div class="btn-group" style="margin-top: 30px;">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Update
            </button>
            <a href="{{ route('admin.klasifikasi-pelanggaran.index') }}" class="btn btn-secondary">
                <i class="fas fa-times"></i> Batal
            </a>
        </div>
    </form>
</div>
@endsection