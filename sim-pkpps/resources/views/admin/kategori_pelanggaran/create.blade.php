{{-- resources/views/admin/kategori_pelanggaran/create.blade.php --}}
@extends('layouts.app')

@section('title', 'Tambah Kategori Pelanggaran')

@section('content')
<div class="page-header">
    <h2><i class="fas fa-plus-circle"></i> Tambah Kategori Pelanggaran</h2>
</div>

<!-- Breadcrumb -->
<div style="margin-bottom: 20px;">
    <nav style="display: flex; align-items: center; gap: 8px; color: var(--text-light); font-size: 0.9em;">
        <a href="{{ route('admin.kategori-pelanggaran.index') }}" style="color: var(--primary-color); text-decoration: none;">
            <i class="fas fa-list-ul"></i> Kategori Pelanggaran
        </a>
        <i class="fas fa-chevron-right" style="font-size: 0.7em;"></i>
        <span>Tambah</span>
    </nav>
</div>

<div class="content-box">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
        <h3 style="margin: 0; color: var(--primary-color);">
            <i class="fas fa-edit"></i> Form Tambah Kategori
        </h3>
        <div style="background: var(--primary-light); padding: 10px 20px; border-radius: var(--border-radius-sm);">
            <small style="color: var(--text-light);">ID Kategori Berikutnya:</small>
            <strong style="color: var(--primary-dark); font-size: 1.1em;">{{ $nextIdKategori }}</strong>
        </div>
    </div>

    <form action="{{ route('admin.kategori-pelanggaran.store') }}" method="POST">
        @csrf

        <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 20px; margin-bottom: 20px;">
            <!-- Nama Pelanggaran -->
            <div class="form-group">
                <label for="nama_pelanggaran">
                    <i class="fas fa-exclamation-triangle form-icon"></i>
                    Nama Pelanggaran <span style="color: var(--danger-color);">*</span>
                </label>
                <input type="text" 
                       name="nama_pelanggaran" 
                       id="nama_pelanggaran"
                       class="form-control @error('nama_pelanggaran') is-invalid @enderror"
                       value="{{ old('nama_pelanggaran') }}"
                       placeholder="Contoh: Terlambat Sholat, Tidak Rapi, Melanggar Tata Tertib"
                       required>
                @error('nama_pelanggaran')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <!-- Poin -->
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
                       value="{{ old('poin') }}"
                       placeholder="1-100"
                       required>
                @error('poin')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
                <span class="form-text">Poin antara 1-100 (semakin tinggi, semakin berat pelanggarannya)</span>
            </div>
        </div>

        <!-- Info Box -->
        <div style="background: linear-gradient(135deg, #E3F2FD 0%, #D1E9F9 100%); padding: 20px; border-radius: var(--border-radius-sm); border-left: 4px solid var(--info-color); margin-bottom: 25px;">
            <h4 style="margin: 0 0 10px 0; color: var(--text-color);">
                <i class="fas fa-info-circle"></i> Panduan Poin Pelanggaran
            </h4>
            <ul style="margin: 0; padding-left: 20px; line-height: 1.8;">
                <li><strong>1-10 poin:</strong> Pelanggaran ringan (terlambat, tidak rapi)</li>
                <li><strong>11-30 poin:</strong> Pelanggaran sedang (bolos, tidak mengikuti kegiatan)</li>
                <li><strong>31-100 poin:</strong> Pelanggaran berat (berkelahi, mencuri)</li>
            </ul>
        </div>

        <div class="btn-group">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Simpan Kategori
            </button>
            <a href="{{ route('admin.kategori-pelanggaran.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </form>
</div>
@endsection