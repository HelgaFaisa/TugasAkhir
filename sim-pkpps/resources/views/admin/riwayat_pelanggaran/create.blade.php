{{-- resources/views/admin/riwayat_pelanggaran/create.blade.php --}}
@extends('layouts.app')

@section('title', 'Tambah Riwayat Pelanggaran')

@section('content')
<div class="page-header">
    <h2><i class="fas fa-plus-circle"></i> Tambah Riwayat Pelanggaran</h2>
</div>

<!-- Breadcrumb -->
<div style="margin-bottom: 20px;">
    <nav style="display: flex; align-items: center; gap: 8px; color: var(--text-light); font-size: 0.9em;">
        <a href="{{ route('admin.riwayat-pelanggaran.index') }}" style="color: var(--primary-color); text-decoration: none;">
            <i class="fas fa-history"></i> Riwayat Pelanggaran
        </a>
        <i class="fas fa-chevron-right" style="font-size: 0.7em;"></i>
        <span>Tambah</span>
    </nav>
</div>

<div class="content-box">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
        <h3 style="margin: 0; color: var(--primary-color);">
            <i class="fas fa-edit"></i> Form Tambah Riwayat
        </h3>
        <div style="background: var(--primary-light); padding: 10px 20px; border-radius: var(--border-radius-sm);">
            <small style="color: var(--text-light);">ID Riwayat Berikutnya:</small>
            <strong style="color: var(--primary-dark); font-size: 1.1em;">{{ $nextIdRiwayat }}</strong>
        </div>
    </div>

    <form action="{{ route('admin.riwayat-pelanggaran.store') }}" method="POST">
        @csrf

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
            <!-- Santri -->
            <div class="form-group">
                <label for="id_santri">
                    <i class="fas fa-user form-icon"></i>
                    Santri <span style="color: var(--danger-color);">*</span>
                </label>
                <select name="id_santri" 
                        id="id_santri" 
                        class="form-control @error('id_santri') is-invalid @enderror" 
                        required>
                    <option value="">-- Pilih Santri --</option>
                    @foreach($santriList as $santri)
                        <option value="{{ $santri->id_santri }}" {{ old('id_santri') == $santri->id_santri ? 'selected' : '' }}>
                            {{ $santri->nama_lengkap }} - {{ $santri->kelas }} ({{ $santri->id_santri }})
                        </option>
                    @endforeach
                </select>
                @error('id_santri')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <!-- Tanggal -->
            <div class="form-group">
                <label for="tanggal">
                    <i class="fas fa-calendar form-icon"></i>
                    Tanggal Pelanggaran <span style="color: var(--danger-color);">*</span>
                </label>
                <input type="date" 
                       name="tanggal" 
                       id="tanggal"
                       class="form-control @error('tanggal') is-invalid @enderror"
                       value="{{ old('tanggal', date('Y-m-d')) }}"
                       required>
                @error('tanggal')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <!-- Kategori Pelanggaran -->
        <div class="form-group">
            <label for="id_kategori">
                <i class="fas fa-tags form-icon"></i>
                Kategori Pelanggaran <span style="color: var(--danger-color);">*</span>
            </label>
            <select name="id_kategori" 
                    id="id_kategori" 
                    class="form-control @error('id_kategori') is-invalid @enderror" 
                    required>
                <option value="">-- Pilih Kategori Pelanggaran --</option>
                @foreach($kategoriList as $kategori)
                    <option value="{{ $kategori->id_kategori }}" 
                            data-poin="{{ $kategori->poin }}"
                            {{ old('id_kategori') == $kategori->id_kategori ? 'selected' : '' }}>
                        {{ $kategori->nama_pelanggaran }} - {{ $kategori->poin }} Poin ({{ $kategori->id_kategori }})
                    </option>
                @endforeach
            </select>
            @error('id_kategori')
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror
            
            <!-- Preview Poin -->
            <div id="poin-preview" style="display: none; margin-top: 12px; padding: 12px; background: var(--danger-color); color: white; border-radius: var(--border-radius-sm); text-align: center;">
                <i class="fas fa-fire"></i> 
                <strong>Poin yang akan ditambahkan: <span id="poin-value">0</span> Poin</strong>
            </div>
        </div>

        <!-- Keterangan -->
        <div class="form-group">
            <label for="keterangan">
                <i class="fas fa-comment form-icon"></i>
                Keterangan Tambahan
            </label>
            <textarea name="keterangan" 
                      id="keterangan" 
                      rows="4"
                      class="form-control @error('keterangan') is-invalid @enderror"
                      placeholder="Jelaskan detail pelanggaran (opsional)">{{ old('keterangan') }}</textarea>
            @error('keterangan')
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror
            <span class="form-text">Maksimal 1000 karakter</span>
        </div>

        <div class="btn-group">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Simpan Riwayat
            </button>
            <a href="{{ route('admin.riwayat-pelanggaran.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </form>
</div>

<script>
// Preview poin saat kategori dipilih
document.getElementById('id_kategori').addEventListener('change', function() {
    const selectedOption = this.options[this.selectedIndex];
    const poin = selectedOption.getAttribute('data-poin');
    const preview = document.getElementById('poin-preview');
    const poinValue = document.getElementById('poin-value');
    
    if (poin) {
        poinValue.textContent = poin;
        preview.style.display = 'block';
    } else {
        preview.style.display = 'none';
    }
});

// Trigger change event jika ada old value
if (document.getElementById('id_kategori').value) {
    document.getElementById('id_kategori').dispatchEvent(new Event('change'));
}
</script>
@endsection