@extends('layouts.app')

@section('content')
<div class="page-header">
    <h2><i class="fas fa-plus-circle"></i> Tambah Kategori Kegiatan</h2>
</div>

<div class="form-container">
    <form action="{{ route('admin.kategori-kegiatan.store') }}" method="POST">
        @csrf

        <div class="form-group">
            <label for="kategori_id">
                <i class="fas fa-hashtag form-icon"></i>
                ID Kategori (Otomatis)
            </label>
            <input type="text" class="form-control" value="{{ $nextId }}" disabled>
            <small class="form-text">ID akan dibuat otomatis saat disimpan</small>
        </div>

        <div class="form-group">
            <label for="nama_kategori">
                <i class="fas fa-tag form-icon"></i>
                Nama Kategori <span style="color: red;">*</span>
            </label>
            <input type="text" 
                   name="nama_kategori" 
                   id="nama_kategori" 
                   class="form-control @error('nama_kategori') is-invalid @enderror" 
                   value="{{ old('nama_kategori') }}" 
                   placeholder="Contoh: Kajian Kitab, Olahraga, Kesenian"
                   required>
            @error('nama_kategori')
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label for="keterangan">
                <i class="fas fa-align-left form-icon"></i>
                Keterangan
            </label>
            <textarea name="keterangan" 
                      id="keterangan" 
                      class="form-control @error('keterangan') is-invalid @enderror" 
                      rows="4"
                      placeholder="Deskripsi singkat tentang kategori ini...">{{ old('keterangan') }}</textarea>
            @error('keterangan')
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror
        </div>

        <div class="btn-group">
            <button type="submit" class="btn btn-success">
                <i class="fas fa-save"></i> Simpan
            </button>
            <a href="{{ route('admin.kategori-kegiatan.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </form>
</div>
@endsection