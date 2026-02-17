@extends('layouts.app')

@section('content')
<div class="page-header">
    <h2><i class="fas fa-plus-circle"></i> Tambah Materi Baru</h2>
</div>

<div class="form-container">
    <form action="{{ route('admin.materi.store') }}" method="POST">
        @csrf

        {{-- Info Box --}}
        <div class="info-box">
            <i class="fas fa-info-circle"></i>
            <strong>ID Materi Selanjutnya:</strong> {{ $nextIdMateri }} (Auto-generated)
        </div>

        <div class="row" style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            {{-- Kategori --}}
            <div class="form-group">
                <label><i class="fas fa-layer-group form-icon"></i> Kategori Materi <span style="color: red;">*</span></label>
                <select name="kategori" class="form-control @error('kategori') is-invalid @enderror" required>
                    <option value="">-- Pilih Kategori --</option>
                    <option value="Al-Qur'an" {{ old('kategori') == 'Al-Qur\'an' ? 'selected' : '' }}>Al-Qur'an</option>
                    <option value="Hadist" {{ old('kategori') == 'Hadist' ? 'selected' : '' }}>Hadist</option>
                    <option value="Materi Tambahan" {{ old('kategori') == 'Materi Tambahan' ? 'selected' : '' }}>Materi Tambahan</option>
                </select>
                @error('kategori')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            {{-- Kelas (Dynamic dari tabel kelas) --}}
            <div class="form-group">
                <label><i class="fas fa-users form-icon"></i> Kelas <span style="color: red;">*</span></label>
                <select name="kelas" class="form-control @error('kelas') is-invalid @enderror" required>
                    <option value="">-- Pilih Kelas --</option>
                    @foreach($kelasList as $kls)
                        <option value="{{ $kls->nama_kelas }}" {{ old('kelas') == $kls->nama_kelas ? 'selected' : '' }}>
                            {{ $kls->nama_kelas }}
                        </option>
                    @endforeach
                </select>
                @error('kelas')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>
        </div>

        {{-- Nama Kitab --}}
        <div class="form-group">
            <label><i class="fas fa-book form-icon"></i> Nama Kitab / Materi <span style="color: red;">*</span></label>
            <input type="text" name="nama_kitab" class="form-control @error('nama_kitab') is-invalid @enderror" 
                   value="{{ old('nama_kitab') }}" placeholder="Contoh: K. Sholah, Tafsir Jalalain, Khotbah" required>
            @error('nama_kitab')
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror
        </div>

        <div class="row" style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px;">
            {{-- Halaman Mulai --}}
            <div class="form-group">
                <label><i class="fas fa-file-alt form-icon"></i> Halaman Mulai <span style="color: red;">*</span></label>
                <input type="number" name="halaman_mulai" id="halaman_mulai" 
                       class="form-control @error('halaman_mulai') is-invalid @enderror" 
                       value="{{ old('halaman_mulai', 1) }}" min="1" required 
                       onchange="calculateTotal()">
                @error('halaman_mulai')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            {{-- Halaman Akhir --}}
            <div class="form-group">
                <label><i class="fas fa-file-alt form-icon"></i> Halaman Akhir <span style="color: red;">*</span></label>
                <input type="number" name="halaman_akhir" id="halaman_akhir" 
                       class="form-control @error('halaman_akhir') is-invalid @enderror" 
                       value="{{ old('halaman_akhir') }}" min="1" required 
                       onchange="calculateTotal()">
                @error('halaman_akhir')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            {{-- Total Halaman (Auto) --}}
            <div class="form-group">
                <label><i class="fas fa-calculator form-icon"></i> Total Halaman</label>
                <input type="text" id="total_halaman" class="form-control" 
                       value="0" readonly style="background-color: #f0f0f0; font-weight: bold;">
                <small class="form-text">Auto-calculated</small>
            </div>
        </div>

        {{-- Deskripsi --}}
        <div class="form-group">
            <label><i class="fas fa-align-left form-icon"></i> Deskripsi (Optional)</label>
            <textarea name="deskripsi" class="form-control @error('deskripsi') is-invalid @enderror" 
                      rows="4" placeholder="Deskripsi atau catatan tambahan tentang materi ini...">{{ old('deskripsi') }}</textarea>
            @error('deskripsi')
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror
        </div>

        {{-- Action Buttons --}}
        <div class="btn-group">
            <button type="submit" class="btn btn-success">
                <i class="fas fa-save"></i> Simpan Materi
            </button>
            <a href="{{ route('admin.materi.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </form>
</div>

<script>
// Auto-calculate total halaman
function calculateTotal() {
    const mulai = parseInt(document.getElementById('halaman_mulai').value) || 0;
    const akhir = parseInt(document.getElementById('halaman_akhir').value) || 0;
    
    if (mulai > 0 && akhir >= mulai) {
        const total = akhir - mulai + 1;
        document.getElementById('total_halaman').value = total + ' halaman';
    } else {
        document.getElementById('total_halaman').value = '0 halaman';
    }
}

// Calculate on page load
document.addEventListener('DOMContentLoaded', function() {
    calculateTotal();
});
</script>
@endsection